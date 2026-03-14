<?php

namespace App\Providers;

use App\Models\Recipe;
use App\Observers\RecipeObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Recipe::observe(RecipeObserver::class);
    }
}
