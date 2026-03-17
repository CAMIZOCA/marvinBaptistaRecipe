<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\EnhanceRecipeBatchJob;
use App\Models\Recipe;
use App\Models\Setting;
use App\Services\RecipeEnhancer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class RecipeAiController extends Controller
{
    public function __construct(private RecipeEnhancer $enhancer) {}

    public function enhance(Recipe $recipe): JsonResponse
    {
        $userId = auth()->id();
        $key    = 'ai-enhance:' . $userId;
        $config = config('ai.rate_limit');

        // Skip rate limiting in local/development environment
        if (!app()->isLocal()) {
            if (RateLimiter::tooManyAttempts($key, $config['attempts'])) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'error'      => "Has alcanzado el límite de mejoras con IA. Intenta en " . ceil($seconds / 60) . " minutos.",
                    'error_type' => 'rate_limit',
                    'retry_in'   => $seconds,
                ], 429);
            }
            RateLimiter::hit($key, $config['decay_seconds']);
        }

        try {
            $result = $this->enhancer->enhance($recipe);

            return response()->json([
                'success' => true,
                'current' => [
                    'seo_title' => $recipe->seo_title,
                    'seo_description' => $recipe->seo_description,
                    'story' => $recipe->story,
                    'tips_secrets' => $recipe->tips_secrets,
                    'faq_count' => $recipe->faqs()->count(),
                ],
                'suggested' => $result,
            ]);
        } catch (\RuntimeException $e) {
            // "JSON malformado" is a model-quality issue, not a config issue — don't show settings link
            $isConfigError = !str_contains($e->getMessage(), 'JSON') && !str_contains($e->getMessage(), 'inválida');
            return response()->json([
                'error'      => $e->getMessage(),
                'error_type' => $isConfigError ? 'config' : 'server',
            ], 422);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $isTimeout = str_contains($e->getMessage(), 'timed out') || str_contains($e->getMessage(), 'cURL error 28');
            return response()->json([
                'error'      => $isTimeout
                    ? 'La IA local tardó demasiado en responder (timeout). Prueba un modelo más ligero o aumenta el límite en Ajustes → IA → "Tiempo límite de respuesta".'
                    : 'No se pudo conectar con la IA local. Verifica que el servidor esté corriendo en la URL configurada.',
                'error_type' => 'server',
            ], 504);
        } catch (\Exception $e) {
            report($e);
            $detail = app()->isLocal() ? ' Detalle: ' . $e->getMessage() : '';
            return response()->json([
                'error'      => 'Ocurrió un error inesperado al llamar a la IA.' . $detail,
                'error_type' => 'server',
            ], 500);
        }
    }

    /* ─── Per-field AI enhancement ───────────────────────────────── */

    /**
     * Generate a single AI field for a recipe.
     * Called in parallel from the frontend for each field — faster, debuggable.
     */
    public function enhanceField(Request $request, Recipe $recipe): JsonResponse
    {
        $allowed = ['seo_title', 'seo_description', 'story', 'tips_secrets',
                    'faq', 'amazon_keywords', 'internal_link_suggestions'];

        $request->validate([
            'field' => ['required', 'string', 'in:' . implode(',', $allowed)],
        ]);

        $field = $request->string('field')->toString();

        try {
            $value   = $this->enhancer->enhanceSingleField($recipe, $field);
            $current = $this->getCurrentFieldValue($recipe, $field);

            return response()->json([
                'success' => true,
                'field'   => $field,
                'value'   => $value,
                'current' => $current,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success'    => false,
                'field'      => $field,
                'error'      => $e->getMessage(),
                'error_type' => 'server',
            ], 422);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $isTimeout = str_contains($e->getMessage(), 'timed out') || str_contains($e->getMessage(), 'cURL error 28');
            return response()->json([
                'success'    => false,
                'field'      => $field,
                'error'      => $isTimeout
                    ? 'Timeout al generar este campo. Prueba un modelo más ligero o aumenta el tiempo límite en Ajustes → IA.'
                    : 'No se pudo conectar con la IA local para este campo.',
                'error_type' => 'server',
            ], 504);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success'    => false,
                'field'      => $field,
                'error'      => app()->isLocal() ? $e->getMessage() : 'Error inesperado al generar este campo.',
                'error_type' => 'server',
            ], 500);
        }
    }

    private function getCurrentFieldValue(Recipe $recipe, string $field): mixed
    {
        return match ($field) {
            'seo_title'       => $recipe->seo_title,
            'seo_description' => $recipe->seo_description,
            'story'           => $recipe->story,
            'tips_secrets'    => $recipe->tips_secrets,
            'faq'             => $recipe->faqs()->get(['question', 'answer'])->toArray(),
            default           => null,
        };
    }

    public function testApiKey(Request $request): JsonResponse
    {
        // Use provider from request (current UI selection) or fall back to saved DB value
        $provider = $request->input('provider', Setting::get('ai_provider', 'anthropic'));

        return $provider === 'local'
            ? $this->testLocalAi($request)
            : $this->testAnthropic();
    }

    private function testAnthropic(): JsonResponse
    {
        $apiKey = Setting::get('anthropic_api_key') ?: config('services.anthropic.key');
        $model  = Setting::get('anthropic_model')   ?: config('ai.anthropic.model', 'claude-haiku-3-5');

        if (blank($apiKey)) {
            return response()->json(['ok' => false, 'message' => 'No hay clave API de Anthropic configurada. Guarda la clave primero.'], 422);
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(15)
            ->post(config('ai.anthropic.api_url', 'https://api.anthropic.com/v1/messages'), [
                'model'      => $model,
                'max_tokens' => 5,
                'messages'   => [['role' => 'user', 'content' => 'Reply with "ok"']],
            ]);

            if ($response->successful()) {
                $modelUsed = $response->json('model', $model);
                return response()->json(['ok' => true, 'message' => "Conexión exitosa con Anthropic. Modelo: {$modelUsed}"]);
            }

            $status = $response->status();
            $err    = $response->json('error.message', $response->body());
            $hint   = match(true) {
                $status === 401 => 'Clave API inválida o sin permisos.',
                $status === 403 => 'Acceso denegado. Verifica los permisos de tu clave.',
                $status === 429 => 'Límite de peticiones alcanzado. Intenta en unos minutos.',
                default         => "Error {$status}: {$err}",
            };

            return response()->json(['ok' => false, 'message' => $hint], 422);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'No se pudo conectar con Anthropic: ' . $e->getMessage()], 500);
        }
    }

    private function testLocalAi(Request $request): JsonResponse
    {
        // Prefer values sent from the UI form (not yet saved) over DB values
        $baseUrl = rtrim($request->input('local_ai_url', Setting::get('local_ai_url', '')), '/');
        $model   = $request->input('local_ai_model', Setting::get('local_ai_model', 'llama3.2'));
        $key     = $request->input('local_ai_api_key', Setting::get('local_ai_api_key', 'local'));

        if (blank($baseUrl)) {
            return response()->json(['ok' => false, 'message' => 'No hay URL de IA local configurada.'], 422);
        }

        $endpoint = str_ends_with($baseUrl, '/chat/completions')
            ? $baseUrl
            : rtrim($baseUrl, '/') . '/v1/chat/completions';

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . ($key ?: 'local'),
            ])
            ->timeout(20)
            ->post($endpoint, [
                'model'      => $model,
                'stream'     => false,
                'max_tokens' => 5,
                'messages'   => [['role' => 'user', 'content' => 'Reply with "ok"']],
            ]);

            if ($response->successful()) {
                $modelUsed = $response->json('model', $model);
                return response()->json(['ok' => true, 'message' => "Conexión exitosa con IA local. Modelo: {$modelUsed}"]);
            }

            return response()->json([
                'ok'      => false,
                'message' => "Error {$response->status()}: {$response->body()}",
            ], 422);
        } catch (\Exception $e) {
            $msg = str_contains($e->getMessage(), 'Connection refused')
                ? "No se pudo conectar a {$baseUrl}. ¿Está corriendo Ollama / LM Studio?"
                : 'Error: ' . $e->getMessage();
            return response()->json(['ok' => false, 'message' => $msg], 500);
        }
    }

    public function saveEnhancement(Request $request, Recipe $recipe): JsonResponse
    {
        $request->validate([
            'fields' => ['required', 'array'],
            'fields.seo_title' => ['nullable', 'string', 'max:60'],
            'fields.seo_description' => ['nullable', 'string', 'max:160'],
            'fields.story' => ['nullable', 'string'],
            'fields.tips_secrets' => ['nullable', 'string'],
            'fields.faq' => ['nullable', 'array'],
            'fields.amazon_keywords' => ['nullable', 'array'],
        ]);

        try {
            $this->enhancer->applyEnhancement($recipe, $request->fields);

            return response()->json([
                'success' => true,
                'message' => '¡Mejoras aplicadas exitosamente!',
                'ai_enhanced_at' => $recipe->fresh()->ai_enhanced_at?->diffForHumans(),
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => 'Error al guardar las mejoras.'], 500);
        }
    }

    /**
     * Build and return the full AI prompt for a recipe (no AI call).
     * Lets the user copy it and paste it into ChatGPT / Claude / Gemini.
     */
    public function getPrompt(Recipe $recipe): JsonResponse
    {
        $recipe->load('ingredients', 'steps', 'categories', 'tags');

        $prompt = $this->enhancer->buildUserPrompt($recipe);

        $current = [
            'seo_title'                 => $recipe->seo_title,
            'seo_description'           => $recipe->seo_description,
            'story'                     => $recipe->story,
            'tips_secrets'              => $recipe->tips_secrets,
            'faq'                       => $recipe->faqs()->get(['question', 'answer'])->toArray(),
            'amazon_keywords'           => null,
            'internal_link_suggestions' => null,
        ];

        return response()->json(['prompt' => $prompt, 'current' => $current]);
    }

    /**
     * Inicia mejora IA en lote para múltiples recetas.
     */
    public function enhanceBatch(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1', 'max:50'],
            'ids.*' => ['integer', 'exists:recipes,id'],
        ]);

        $ids      = $request->ids;
        $batchId  = Str::uuid()->toString();
        $cacheKey = "ai_batch:{$batchId}";
        $total    = count($ids);

        // Inicializar caché de progreso
        Cache::put($cacheKey, [
            'processed' => 0,
            'errors'    => [],
            'log'       => [],
            'total'     => $total,
            'done'      => false,
        ], 1800);

        // Despachar un job por receta
        foreach ($ids as $recipeId) {
            EnhanceRecipeBatchJob::dispatch($recipeId, $cacheKey, $total)
                ->onQueue('ai');
        }

        return response()->json([
            'batch_id' => $batchId,
            'total'    => $total,
            'message'  => "Lote iniciado: {$total} receta(s) en cola.",
        ]);
    }

    /**
     * Retorna el progreso actual de un lote IA.
     */
    public function batchProgress(string $batchId): JsonResponse
    {
        // Validar formato UUID para evitar cache key injection
        if (!preg_match('/^[0-9a-f\-]{36}$/i', $batchId)) {
            return response()->json(['error' => 'ID de lote inválido'], 400);
        }

        $progress = Cache::get("ai_batch:{$batchId}");

        if (!$progress) {
            return response()->json(['error' => 'Lote no encontrado o expirado'], 404);
        }

        return response()->json($progress);
    }
}
