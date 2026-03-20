<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Services\RecipeEnhancer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class EnhanceRecipesBatch extends Command
{
    protected $signature = 'recipes:enhance-batch
                            {--limit=0 : Máximo de recetas a procesar (0 = sin límite)}
                            {--delay=2 : Segundos de pausa entre llamadas a la API}
                            {--category= : Filtrar por slug de categoría}
                            {--all : Procesar todas, incluyendo las ya mejoradas}
                            {--dry-run : Mostrar qué se procesaría sin hacer llamadas a la API}';

    protected $description = 'Mejora recetas en lote usando la IA configurada (Anthropic/local)';

    public function handle(RecipeEnhancer $enhancer): int
    {
        $limit   = (int) $this->option('limit');
        $delay   = (int) $this->option('delay');
        $all     = $this->option('all');
        $dryRun  = $this->option('dry-run');
        $category = $this->option('category');

        // Build query
        $query = Recipe::with(['ingredients', 'steps', 'faqs', 'categories'])
            ->orderBy('id');

        if (!$all) {
            $query->whereNull('ai_enhanced_at');
        }

        if ($category) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $category));
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No hay recetas pendientes de mejora con IA.');
            if (!$all) {
                $this->line('  Usa --all para procesar también las ya mejoradas.');
            }
            return self::SUCCESS;
        }

        $this->info("Recetas a procesar: <comment>{$total}</comment>");

        if ($dryRun) {
            $this->warn('[DRY-RUN] No se harán llamadas a la API. Recetas que se procesarían:');
            $query->select('id', 'title', 'ai_enhanced_at')->each(function ($recipe) {
                $status = $recipe->ai_enhanced_at ? '(ya mejorada ' . $recipe->ai_enhanced_at->format('d/m/Y') . ')' : '(sin mejorar)';
                $this->line("  [{$recipe->id}] {$recipe->title} {$status}");
            });
            return self::SUCCESS;
        }

        $this->newLine();
        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%\n");
        $bar->setMessage('Iniciando...');
        $bar->start();

        $errors  = [];
        $success = 0;

        $query->each(function (Recipe $recipe) use ($enhancer, $bar, $delay, &$errors, &$success) {
            $bar->setMessage("Procesando: {$recipe->title}");

            try {
                $enhanced = $enhancer->enhance($recipe);
                $enhancer->applyEnhancement($recipe, $enhanced);
                Cache::forget("recipe_page:{$recipe->slug}");
                $success++;
            } catch (\Throwable $e) {
                $errors[] = "[{$recipe->id}] {$recipe->title}: " . $e->getMessage();
            }

            $bar->advance();

            if ($delay > 0) {
                sleep($delay);
            }
        });

        $bar->setMessage('Completado.');
        $bar->finish();
        $this->newLine(2);

        $this->info("Mejoradas con éxito: <comment>{$success}</comment>");

        if (!empty($errors)) {
            $this->warn('Errores (' . count($errors) . '):');
            foreach ($errors as $err) {
                $this->error("  {$err}");
            }
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
