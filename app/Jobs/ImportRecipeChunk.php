<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\Tag;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportRecipeChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        private array $rows,
        private int $userId,
    ) {}

    public function handle(): void
    {
        foreach ($this->rows as $row) {
            if ($this->batch()?->cancelled()) {
                return;
            }

            try {
                $this->importRow($row);
            } catch (\Exception $e) {
                // Log individual row error but continue processing
                logger()->error('CSV Import row failed: ' . $e->getMessage(), ['row' => $row]);
            }
        }
    }

    private function importRow(array $row): void
    {
        DB::transaction(function () use ($row) {
            // Create recipe
            $recipe = Recipe::create([
                'title' => $row['title'] ?? 'Sin título',
                'subtitle' => $row['subtitle'] ?? null,
                'description' => $row['description'] ?? null,
                'origin_country' => $row['origin_country'] ?? null,
                'prep_time_minutes' => is_numeric($row['prep_time'] ?? null) ? (int)$row['prep_time'] : null,
                'cook_time_minutes' => is_numeric($row['cook_time'] ?? null) ? (int)$row['cook_time'] : null,
                'servings' => is_numeric($row['servings'] ?? null) ? (int)$row['servings'] : null,
                'difficulty' => in_array($row['difficulty'] ?? '', ['easy', 'medium', 'hard'])
                    ? $row['difficulty']
                    : 'medium',
                'story' => $row['story'] ?? null,
                'tips_secrets' => $row['tips'] ?? null,
                'is_published' => false,
                'user_id' => $this->userId,
            ]);

            // Categories
            if (!empty($row['category'])) {
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($row['category'])],
                    ['name' => $row['category']]
                );
                $recipe->categories()->attach($category->id, ['is_primary' => true]);
            }

            // Tags (comma-separated)
            if (!empty($row['tags'])) {
                $tagNames = array_map('trim', explode(',', $row['tags']));
                $tagIds = [];
                foreach ($tagNames as $tagName) {
                    if (!$tagName) continue;
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
                $recipe->tags()->sync($tagIds);
            }

            // Ingredients: format "amount|unit|name|group" separated by ";"
            if (!empty($row['ingredients'])) {
                $ingredientLines = array_map('trim', explode(';', $row['ingredients']));
                foreach ($ingredientLines as $pos => $line) {
                    if (!$line) continue;
                    $parts = array_map('trim', explode('|', $line));
                    RecipeIngredient::create([
                        'recipe_id' => $recipe->id,
                        'order_position' => $pos + 1,
                        'amount' => is_numeric($parts[0] ?? null) ? (float)$parts[0] : null,
                        'unit' => $parts[1] ?? null,
                        'ingredient_name' => $parts[2] ?? $line,
                        'ingredient_group' => $parts[3] ?? null,
                    ]);
                }
            }

            // Steps: format "title|description" separated by ";"
            if (!empty($row['steps'])) {
                $stepLines = array_map('trim', explode(';', $row['steps']));
                foreach ($stepLines as $num => $line) {
                    if (!$line) continue;
                    $parts = array_map('trim', explode('|', $line));
                    RecipeStep::create([
                        'recipe_id' => $recipe->id,
                        'step_number' => $num + 1,
                        'title' => count($parts) > 1 ? $parts[0] : null,
                        'description' => count($parts) > 1 ? $parts[1] : $parts[0],
                    ]);
                }
            }
        });
    }
}
