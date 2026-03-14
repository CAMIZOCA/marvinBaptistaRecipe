<?php

namespace App\Jobs;

use App\Models\Recipe;
use App\Models\RecipeFaq;
use App\Services\RecipeEnhancer;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EnhanceRecipeBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $tries = 2;
    public int $timeout = 300;

    public function __construct(
        public readonly int    $recipeId,
        public readonly string $batchCacheKey,
        public readonly int    $totalCount,
    ) {}

    public function handle(RecipeEnhancer $enhancer): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $recipe = Recipe::with(['ingredients', 'steps', 'faqs', 'categories'])->find($this->recipeId);
        if (!$recipe) {
            $this->updateProgress('error', "Receta ID {$this->recipeId} no encontrada");
            return;
        }

        $this->updateProgress('processing', "Procesando: {$recipe->title}");

        try {
            $result = $enhancer->enhance($recipe);

            if (!$result || isset($result['error'])) {
                $this->updateProgress('error', $result['error'] ?? "Error al mejorar: {$recipe->title}");
                return;
            }

            $suggested = $result['suggested'] ?? [];

            // Guardar automáticamente todos los campos mejorados
            $updateData = [];
            foreach (['seo_title', 'seo_description', 'story', 'tips_secrets'] as $field) {
                if (!empty($suggested[$field])) {
                    $updateData[$field] = $suggested[$field];
                }
            }
            if (!empty($updateData)) {
                $updateData['ai_enhanced_at'] = now();
                $recipe->update($updateData);
            }

            // Guardar FAQs si vienen
            if (!empty($suggested['faq']) && is_array($suggested['faq'])) {
                $recipe->faqs()->delete();
                foreach ($suggested['faq'] as $i => $faq) {
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        RecipeFaq::create([
                            'recipe_id'  => $recipe->id,
                            'question'   => $faq['question'],
                            'answer'     => $faq['answer'],
                            'sort_order' => $i + 1,
                        ]);
                    }
                }
            }

            // Invalidar caché de la receta
            Cache::forget("recipe_page:{$recipe->slug}");

            $this->updateProgress('done', "Completada: {$recipe->title}");

        } catch (\Throwable $e) {
            Log::error("EnhanceRecipeBatchJob error for recipe {$this->recipeId}: " . $e->getMessage());
            $this->updateProgress('error', "Error en: {$recipe->title} — " . $e->getMessage());
        }
    }

    private function updateProgress(string $status, string $message): void
    {
        $progress = Cache::get($this->batchCacheKey, [
            'processed' => 0,
            'errors'    => [],
            'log'       => [],
            'total'     => $this->totalCount,
            'done'      => false,
        ]);

        if ($status === 'error') {
            $progress['errors'][] = $message;
        }
        if (in_array($status, ['done', 'error'])) {
            $progress['processed']++;
        }
        $progress['log'][] = ['status' => $status, 'msg' => $message, 'time' => now()->format('H:i:s')];

        if ($progress['processed'] >= $this->totalCount) {
            $progress['done'] = true;
        }

        Cache::put($this->batchCacheKey, $progress, 1800); // 30 min
    }
}
