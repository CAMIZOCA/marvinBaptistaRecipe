<?php

namespace App\Observers;

use App\Models\Recipe;
use Illuminate\Support\Facades\Cache;

class RecipeObserver
{
    public function saved(Recipe $recipe): void
    {
        $this->flushCache($recipe);
    }

    public function deleted(Recipe $recipe): void
    {
        $this->flushCache($recipe);
    }

    public function restored(Recipe $recipe): void
    {
        $this->flushCache($recipe);
    }

    private function flushCache(Recipe $recipe): void
    {
        Cache::forget("recipe_page:{$recipe->slug}");
    }
}
