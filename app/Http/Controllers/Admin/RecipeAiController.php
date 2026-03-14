<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Setting;
use App\Services\RecipeEnhancer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

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
            return response()->json([
                'error'      => $e->getMessage(),
                'error_type' => 'config',
            ], 422);
        } catch (\Exception $e) {
            report($e);
            $detail = app()->isLocal() ? ' Detalle: ' . $e->getMessage() : '';
            return response()->json([
                'error'      => 'Ocurrió un error inesperado al llamar a la IA.' . $detail,
                'error_type' => 'server',
            ], 500);
        }
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
}
