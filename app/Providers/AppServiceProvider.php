<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\Setting;
use App\Observers\RecipeObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Recipe::observe(RecipeObserver::class);

        // Share categories and settings with ALL views that use the public layout
        View::composer(['components.public.header', 'components.public.footer', 'home.index'], function ($view) {
            static $shared = null;
            if ($shared === null) {
                $shared = [
                    'categories' => Category::whereNull('parent_id')
                        ->withCount(['recipes' => fn($q) => $q->published()])
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get()
                        ->filter(fn($c) => $c->recipes_count > 0)
                        ->values(),
                    'settings' => [
                        'social_instagram' => Setting::get('social_instagram', ''),
                        'social_youtube'   => Setting::get('social_youtube', ''),
                        'social_pinterest' => Setting::get('social_pinterest', ''),
                        'social_facebook'  => Setting::get('social_facebook', ''),
                    ],
                ];
            }
            $view->with('headerCategories', $shared['categories']);
            $view->with('footerCategories', $shared['categories']);
            $view->with('settings', $shared['settings']);
        });
    }
}
