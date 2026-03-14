<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Services\RecipeEnhancer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RecipeAiController extends Controller
{
    public function __construct(private RecipeEnhancer $enhancer) {}

    public function enhance(Recipe $recipe): JsonResponse
    {
        $userId = auth()->id();
        $key = 'ai-enhance:' . $userId;
        $config = config('ai.rate_limit');

        if (RateLimiter::tooManyAttempts($key, $config['attempts'])) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Has alcanzado el límite de mejoras con IA. Intenta en " . ceil($seconds / 60) . " minutos.",
            ], 429);
        }

        RateLimiter::hit($key, $config['decay_seconds']);

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
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'error' => 'Ocurrió un error inesperado. Por favor intenta nuevamente.',
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
}
