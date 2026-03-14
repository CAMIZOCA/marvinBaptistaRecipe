<?php

namespace App\Http\Controllers;

use App\Jobs\IncrementRecipeViewCount;
use App\Models\Recipe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function show(string $slug): View
    {
        $recipe = Cache::remember(
            "recipe_page:{$slug}",
            86400,
            function () use ($slug) {
                return Recipe::published()
                    ->with([
                        'ingredients',
                        'steps',
                        'faqs',
                        'categories.parent',
                        'tags',
                        'books' => fn($q) => $q->where('is_active', true),
                    ])
                    ->where('slug', $slug)
                    ->firstOrFail();
            }
        );

        // Increment view count asynchronously (non-blocking)
        dispatch(new IncrementRecipeViewCount($recipe->id));

        $relatedRecipes = Recipe::published()
            ->whereHas('categories', function ($q) use ($recipe) {
                $q->whereIn('categories.id', $recipe->categories->pluck('id'));
            })
            ->where('id', '!=', $recipe->id)
            ->take(3)
            ->get();

        $seo = [
            'title' => $recipe->seo_title ?? $recipe->title . ' - ' . config('app.name'),
            'description' => $recipe->seo_description ?? '',
            'canonical' => route('recipe.show', $recipe->slug),
            'og_type' => 'article',
            'og_image' => $recipe->featured_image ? asset($recipe->featured_image) : null,
        ];

        $breadcrumbSchema = $recipe->toBreadcrumbSchema(config('app.url'));

        return view('recipes.show', compact('recipe', 'relatedRecipes', 'seo', 'breadcrumbSchema'));
    }
}
