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
use Illuminate\Support\Facades\DB;
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
        $provider = (string) $request->input('provider', Setting::get('ai_provider', 'anthropic'));
        $allowedProviders = ['anthropic', 'openai', 'gemini', 'gemma', 'deepinfra', 'local'];

        if (!in_array($provider, $allowedProviders, true)) {
            return response()->json([
                'ok' => false,
                'message' => "Proveedor de IA inválido: '{$provider}'. Guarda de nuevo la configuración de IA.",
            ], 422);
        }

        return match ($provider) {
            'local'  => $this->testLocalAi($request),
            'openai' => $this->testOpenAi($request),
            'gemini' => $this->testGemini($request),
            'gemma'  => $this->testGemma($request),
            'deepinfra' => $this->testDeepinfra($request),
            default  => $this->testAnthropic(),
        };
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
        $baseUrlInput = trim((string) $request->input('local_ai_url', ''));
        $modelInput = trim((string) $request->input('local_ai_model', ''));
        $keyInput = (string) $request->input('local_ai_api_key', '');

        $baseUrl = rtrim($baseUrlInput !== '' ? $baseUrlInput : (string) Setting::get('local_ai_url', ''), '/');
        $model = $modelInput !== '' ? $modelInput : (string) Setting::get('local_ai_model', 'llama3.2');
        $key = $keyInput !== '' ? $keyInput : (string) Setting::get('local_ai_api_key', 'local');

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

    private function testOpenAi(Request $request): JsonResponse
    {
        $apiKeyInput = trim((string) $request->input('openai_api_key', ''));
        $modelInput = trim((string) $request->input('openai_model', ''));
        $apiKey = $apiKeyInput !== '' ? $apiKeyInput : (string) Setting::get('openai_api_key', config('services.openai.key'));
        $model = $modelInput !== '' ? $modelInput : (string) Setting::get('openai_model', config('ai.openai.model', 'gpt-4.1-mini'));
        $baseUrl = (string) config('ai.openai.api_url', config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions'));

        if (blank($apiKey)) {
            return response()->json(['ok' => false, 'message' => 'No hay clave API de OpenAI configurada.'], 422);
        }

        return $this->testOpenAiCompatibleProvider(
            providerName: 'OpenAI',
            endpoint: $this->normalizeOpenAiCompatibleUrl($baseUrl),
            model: $model,
            apiKey: $apiKey,
            timeout: (int) config('ai.openai.timeout', config('services.openai.timeout', 20)),
        );
    }

    private function testGemini(Request $request): JsonResponse
    {
        $apiKeyInput = trim((string) $request->input('gemini_api_key', ''));
        $modelInput = trim((string) $request->input('gemini_model', ''));
        $apiKey = $apiKeyInput !== '' ? $apiKeyInput : (string) Setting::get('gemini_api_key', config('services.gemini.key'));
        $model = $modelInput !== '' ? $modelInput : (string) Setting::get('gemini_model', config('ai.gemini.model', 'gemini-2.5-flash'));
        $baseUrl = (string) config('ai.gemini.api_url', config('services.gemini.api_url', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'));

        if (blank($apiKey)) {
            return response()->json(['ok' => false, 'message' => 'No hay clave API de Gemini configurada.'], 422);
        }

        return $this->testOpenAiCompatibleProvider(
            providerName: 'Gemini',
            endpoint: $this->normalizeOpenAiCompatibleUrl($baseUrl),
            model: $model,
            apiKey: $apiKey,
            timeout: (int) config('ai.gemini.timeout', config('services.gemini.timeout', 20)),
        );
    }

    private function testGemma(Request $request): JsonResponse
    {
        $baseUrlInput = trim((string) $request->input('gemma_api_url', ''));
        $modelInput = trim((string) $request->input('gemma_model', ''));
        $apiKeyInput = (string) $request->input('gemma_api_key', '');
        $timeoutInput = (int) $request->input('gemma_timeout', 0);

        $baseUrl = $baseUrlInput !== '' ? $baseUrlInput : (string) Setting::get('gemma_api_url', config('ai.gemma.api_url', 'http://localhost:11434'));
        $model = $modelInput !== '' ? $modelInput : (string) Setting::get('gemma_model', config('ai.gemma.model', 'gemma3:4b'));
        $apiKey = $apiKeyInput !== '' ? $apiKeyInput : (string) Setting::get('gemma_api_key', config('services.gemma.key', 'local'));
        $timeout = $timeoutInput > 0 ? $timeoutInput : (int) Setting::get('gemma_timeout', config('ai.gemma.timeout', 300));

        if (blank($baseUrl)) {
            return response()->json(['ok' => false, 'message' => 'No hay URL para Gemma configurada.'], 422);
        }

        return $this->testOpenAiCompatibleProvider(
            providerName: 'Gemma',
            endpoint: $this->normalizeOpenAiCompatibleUrl($baseUrl),
            model: $model,
            apiKey: $apiKey ?: 'local',
            timeout: $timeout > 0 ? $timeout : 20,
        );
    }

    private function testDeepinfra(Request $request): JsonResponse
    {
        $baseUrlInput = trim((string) $request->input('deepinfra_api_url', ''));
        $modelInput = trim((string) $request->input('deepinfra_model', ''));
        $apiKeyInput = trim((string) $request->input('deepinfra_api_key', ''));
        $timeoutInput = (int) $request->input('deepinfra_timeout', 0);

        $baseUrl = $baseUrlInput !== ''
            ? $baseUrlInput
            : (string) Setting::get('deepinfra_api_url', config('ai.deepinfra.api_url', 'https://api.deepinfra.com/v1/openai/chat/completions'));
        $model = $modelInput !== ''
            ? $modelInput
            : (string) Setting::get('deepinfra_model', config('ai.deepinfra.model', 'meta-llama/Llama-3.3-70B-Instruct'));
        $apiKey = $apiKeyInput !== ''
            ? $apiKeyInput
            : (string) Setting::get('deepinfra_api_key', config('services.deepinfra.key'));
        $timeout = $timeoutInput > 0
            ? $timeoutInput
            : (int) Setting::get('deepinfra_timeout', config('ai.deepinfra.timeout', 180));

        if (blank($apiKey)) {
            return response()->json(['ok' => false, 'message' => 'No hay clave API de DeepInfra configurada.'], 422);
        }

        return $this->testOpenAiCompatibleProvider(
            providerName: 'DeepInfra',
            endpoint: $this->normalizeOpenAiCompatibleUrl($baseUrl),
            model: $model,
            apiKey: $apiKey,
            timeout: $timeout > 0 ? $timeout : 20,
        );
    }

    private function normalizeOpenAiCompatibleUrl(string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');
        if (str_ends_with($baseUrl, '/chat/completions')) {
            return $baseUrl;
        }

        if (str_ends_with($baseUrl, '/openai') || str_ends_with($baseUrl, '/v1/openai')) {
            return $baseUrl . '/chat/completions';
        }

        $baseUrl .= '/v1/chat/completions';

        return $baseUrl;
    }

    private function testOpenAiCompatibleProvider(
        string $providerName,
        string $endpoint,
        string $model,
        string $apiKey,
        int $timeout = 20
    ): JsonResponse {
        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . ($apiKey ?: 'local'),
            ])
            ->timeout($timeout > 0 ? $timeout : 20)
            ->post($endpoint, [
                'model'      => $model,
                'stream'     => false,
                'max_tokens' => 5,
                'messages'   => [['role' => 'user', 'content' => 'Reply with "ok"']],
            ]);

            if ($response->successful()) {
                $modelUsed = $response->json('model', $model);
                return response()->json(['ok' => true, 'message' => "Conexión exitosa con {$providerName}. Modelo: {$modelUsed}"]);
            }

            return response()->json([
                'ok'      => false,
                'message' => "Error {$response->status()}: {$response->body()}",
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => "No se pudo conectar con {$providerName}: " . $e->getMessage(),
            ], 500);
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
            'ids'   => ['nullable', 'array', 'min:1', 'max:2000'],
            'ids.*' => ['integer', 'exists:recipes,id'],
            'mode'  => ['nullable', 'string', 'in:ids,pending'],
            'limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'category_slug' => ['nullable', 'string', 'max:120'],
        ]);

        if ($validationError = $this->validateBatchAiConfiguration()) {
            return $validationError;
        }

        $ids = $this->resolveBatchRecipeIds($request);
        if (empty($ids)) {
            return response()->json([
                'error' => 'No hay recetas pendientes para procesar con IA.',
                'error_type' => 'empty',
            ], 422);
        }

        $batchId  = Str::uuid()->toString();
        $cacheKey = "ai_batch:{$batchId}";
        $total    = count($ids);
        $promptConfig = $this->resolvePromptConfigurationSummary();
        $recipesPreview = Recipe::whereIn('id', $ids)
            ->orderBy('id')
            ->get(['id', 'title'])
            ->map(fn ($r) => ['id' => $r->id, 'title' => $r->title])
            ->values()
            ->all();

        // Inicializar caché de progreso
        Cache::put($cacheKey, [
            'processed' => 0,
            'errors'    => [],
            'log'       => [[
                'status' => 'info',
                'msg' => $promptConfig['message'],
                'time' => now()->format('H:i:s'),
            ]],
            'total'     => $total,
            'done'      => false,
            'status'    => 'running',
            'started_at' => now()->toIso8601String(),
            'last_update_at' => now()->toIso8601String(),
            'recipes'   => $recipesPreview,
            'prompt_config' => $promptConfig,
        ], 14400);

        // Despachar un job por receta
        $queueName = config('queue.connections.' . config('queue.default') . '.queue', 'default');
        $pendingBefore = DB::table('jobs')
            ->where('queue', $queueName)
            ->whereNull('reserved_at')
            ->count();
        foreach ($ids as $recipeId) {
            EnhanceRecipeBatchJob::dispatch($recipeId, $cacheKey, $total)
                ->onQueue($queueName);
        }

        return response()->json([
            'batch_id' => $batchId,
            'total'    => $total,
            'recipes'  => $recipesPreview,
            'queue_name' => $queueName,
            'pending_before' => $pendingBefore,
            'prompt_config' => $promptConfig,
            'warning' => $pendingBefore > 0
                ? "Hay {$pendingBefore} job(s) pendientes en cola antes de este lote. Inicia/valida queue:work para que comience a procesar."
                : null,
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

        if (($progress['done'] ?? false) !== true && ($progress['status'] ?? 'running') === 'running') {
            $lastUpdate = $progress['last_update_at'] ?? null;
            $staleAfterSeconds = 6 * 60;

            if (is_string($lastUpdate)) {
                try {
                    $secondsSinceUpdate = now()->diffInSeconds(\Carbon\Carbon::parse($lastUpdate));
                    if ($secondsSinceUpdate >= $staleAfterSeconds) {
                        $progress['done'] = true;
                        $progress['status'] = 'failed';
                        $progress['errors'][] = 'Lote detenido por inactividad: no hubo actualizaciones de progreso en varios minutos.';
                        $progress['log'][] = [
                            'status' => 'error',
                            'msg' => 'Lote cerrado automaticamente por inactividad prolongada.',
                            'time' => now()->format('H:i:s'),
                        ];
                        $progress['last_update_at'] = now()->toIso8601String();
                        Cache::put("ai_batch:{$batchId}", $progress, 14400);
                    }
                } catch (\Throwable) {
                    // Ignore parse errors and return current progress.
                }
            }
        }

        return response()->json($progress);
    }

    /**
     * Validate minimal AI configuration before dispatching a long batch.
     */
    private function validateBatchAiConfiguration(): ?JsonResponse
    {
        $provider = (string) Setting::get('ai_provider', 'anthropic');
        $allowedProviders = ['anthropic', 'openai', 'gemini', 'gemma', 'deepinfra', 'local'];

        if (!in_array($provider, $allowedProviders, true)) {
            return response()->json([
                'error' => "Proveedor de IA inválido en ajustes: '{$provider}'. Ve a Ajustes > IA, selecciona uno válido y guarda.",
                'error_type' => 'config',
            ], 422);
        }

        if ($provider === 'local') {
            $url = trim((string) Setting::get('local_ai_url', ''));
            if ($url === '') {
                return response()->json([
                    'error' => 'No hay URL de IA local configurada. Ve a Ajustes > IA y guarda la URL antes de iniciar el lote.',
                    'error_type' => 'config',
                ], 422);
            }
            return null;
        }

        if ($provider === 'gemma') {
            $url = trim((string) Setting::get('gemma_api_url', config('ai.gemma.api_url', '')));
            if ($url === '') {
                return response()->json([
                    'error' => 'No hay URL configurada para Gemma. Ve a Ajustes > IA y guarda la URL antes de iniciar el lote.',
                    'error_type' => 'config',
                ], 422);
            }
            return null;
        }

        if ($provider === 'deepinfra') {
            $url = trim((string) Setting::get('deepinfra_api_url', config('ai.deepinfra.api_url', '')));
            if ($url === '') {
                return response()->json([
                    'error' => 'No hay URL configurada para DeepInfra. Ve a Ajustes > IA y guarda la URL antes de iniciar el lote.',
                    'error_type' => 'config',
                ], 422);
            }

            $key = trim((string) (Setting::get('deepinfra_api_key') ?: config('services.deepinfra.key')));
            if ($key === '') {
                return response()->json([
                    'error' => 'No hay clave API configurada para DeepInfra. Ve a Ajustes > IA y guarda la clave antes de iniciar el lote.',
                    'error_type' => 'config',
                ], 422);
            }

            return null;
        }

        $apiKey = match ($provider) {
            'openai' => Setting::get('openai_api_key') ?: config('services.openai.key'),
            'gemini' => Setting::get('gemini_api_key') ?: config('services.gemini.key'),
            default  => Setting::get('anthropic_api_key') ?: config('services.anthropic.key'),
        };

        if (blank($apiKey)) {
            $providerLabel = match ($provider) {
                'openai' => 'OpenAI',
                'gemini' => 'Gemini',
                default  => 'Anthropic',
            };
            return response()->json([
                'error' => "No hay clave API configurada para {$providerLabel}. Ve a Ajustes > IA y guarda la clave antes de iniciar el lote.",
                'error_type' => 'config',
            ], 422);
        }

        return null;
    }

    /**
     * Resolve recipe IDs for batch enhancement.
     * - mode=ids: uses explicit IDs from UI selection.
     * - mode=pending: fetches recipes without ai_enhanced_at using optional filters.
     */
    private function resolveBatchRecipeIds(Request $request): array
    {
        $mode = $request->input('mode', 'ids');

        if ($mode === 'pending') {
            $limit = (int) $request->input('limit', 500);
            $query = Recipe::query()
                ->whereNull('ai_enhanced_at')
                ->orderBy('id');

            if ($request->filled('category_slug')) {
                $slug = $request->string('category_slug')->toString();
                $query->whereHas('categories', fn ($q) => $q->where('slug', $slug));
            }

            if ($limit === 0) {
                return $query->pluck('id')->all();
            }

            return $query->limit($limit)->pluck('id')->all();
        }

        return collect($request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Returns a summary of active prompt customization settings used by RecipeEnhancer.
     */
    private function resolvePromptConfigurationSummary(): array
    {
        $provider = (string) Setting::get('ai_provider', 'anthropic');
        $model = match ($provider) {
            'local'  => (Setting::get('local_ai_model', 'llama3.2') ?: 'llama3.2'),
            'openai' => (Setting::get('openai_model') ?: config('ai.openai.model', 'gpt-4.1-mini')),
            'gemini' => (Setting::get('gemini_model') ?: config('ai.gemini.model', 'gemini-2.5-flash')),
            'gemma'  => (Setting::get('gemma_model') ?: config('ai.gemma.model', 'gemma3:4b')),
            'deepinfra' => (Setting::get('deepinfra_model') ?: config('ai.deepinfra.model', 'meta-llama/Llama-3.3-70B-Instruct')),
            'anthropic' => (Setting::get('anthropic_model') ?: config('ai.anthropic.model', 'claude-sonnet-4-6')),
            default  => 'unknown',
        };

        $fields = [
            'seo_title',
            'seo_description',
            'story',
            'tips_secrets',
            'faq',
            'amazon_keywords',
            'internal_links',
        ];

        $overrides = [];
        foreach ($fields as $field) {
            $custom = trim((string) Setting::get('ai_prompt_' . $field, ''));
            $overrides[$field] = $custom !== '';
        }

        $active = array_keys(array_filter($overrides, fn ($v) => $v === true));
        $activeLabel = empty($active) ? 'ninguna' : implode(', ', $active);

        return [
            'provider' => $provider,
            'model' => $model,
            'overrides' => $overrides,
            'message' => "Prompt config -> provider: {$provider}, model: {$model}, overrides activos: {$activeLabel}",
        ];
    }
}
