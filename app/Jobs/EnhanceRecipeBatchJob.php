<?php

namespace App\Jobs;

use App\Models\Recipe;
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
    public bool $failOnTimeout = true;

    public function __construct(
        public readonly int $recipeId,
        public readonly string $batchCacheKey,
        public readonly int $totalCount,
    ) {}

    public function handle(RecipeEnhancer $enhancer): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $recipe = Recipe::with(['ingredients', 'steps', 'faqs', 'categories', 'tags'])->find($this->recipeId);
        if (!$recipe) {
            $this->updateProgress('error', "Receta ID {$this->recipeId} no encontrada");
            return;
        }

        $this->updateProgress('processing', "Procesando: {$recipe->title}");

        try {
            // Usa el mismo prompt y validaciones del flujo manual (Prompt + aplicar).
            $result = $enhancer->enhance($recipe);

            if (!$result || !is_array($result)) {
                $this->updateProgress('error', "Error al mejorar: {$recipe->title}");
                return;
            }

            // Aplica exactamente el mismo flujo de guardado que el editor manual.
            $enhancer->applyEnhancement($recipe, $result);

            Cache::forget("recipe_page:{$recipe->slug}");

            $this->updateProgress('done', "Completada: {$recipe->title}");
        } catch (\Throwable $e) {
            Log::error("EnhanceRecipeBatchJob error for recipe {$this->recipeId}: " . $e->getMessage());
            $this->updateProgress('error', "Error en: {$recipe->title} - " . $e->getMessage());
        }
    }

    /**
     * Handle failures that bypass normal try/catch (for example worker timeout).
     */
    public function failed(\Throwable $e): void
    {
        $title = (string) Recipe::query()->whereKey($this->recipeId)->value('title');
        $title = $title !== '' ? $title : "ID {$this->recipeId}";

        Log::error("EnhanceRecipeBatchJob failed for recipe {$this->recipeId}: " . $e->getMessage());
        $this->updateProgress('error', "Error en: {$title} - " . $e->getMessage());
    }

    private function updateProgress(string $status, string $message): void
    {
        $lockKey = $this->batchCacheKey . ':lock';

        // Avoid race conditions between parallel queue jobs updating the same progress key.
        Cache::lock($lockKey, 10)->block(5, function () use ($status, $message) {
            $progress = Cache::get($this->batchCacheKey, [
                'processed' => 0,
                'errors' => [],
                'log' => [],
                'total' => $this->totalCount,
                'done' => false,
                'status' => 'running',
                'started_at' => now()->toIso8601String(),
                'last_update_at' => now()->toIso8601String(),
            ]);

            if ($status === 'error') {
                $progress['errors'][] = $message;
            }

            if (in_array($status, ['done', 'error'], true)) {
                $progress['processed']++;
            }

            $progress['log'][] = [
                'status' => $status,
                'msg' => $message,
                'time' => now()->format('H:i:s'),
            ];
            $progress['status'] = 'running';

            if ($progress['processed'] >= $this->totalCount) {
                $progress['done'] = true;
                $progress['status'] = 'completed';
            }

            $progress['last_update_at'] = now()->toIso8601String();

            Cache::put($this->batchCacheKey, $progress, 14400); // 4 hours
        });
    }
}
