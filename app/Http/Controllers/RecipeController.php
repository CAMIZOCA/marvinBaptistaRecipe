<?php

namespace App\Http\Controllers;

use App\Jobs\IncrementRecipeViewCount;
use App\Models\Post;
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

        // Sidebar / mobile (3 from same category)
        $relatedRecipes = Recipe::published()
            ->whereHas('categories', function ($q) use ($recipe) {
                $q->whereIn('categories.id', $recipe->categories->pluck('id'));
            })
            ->where('id', '!=', $recipe->id)
            ->take(3)
            ->get();

        // "Te puede interesar" grid — 16 recipes: same category first, then fill with others
        $categoryIds = $recipe->categories->pluck('id');
        $sameCatIds  = Recipe::published()
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds))
            ->where('id', '!=', $recipe->id)
            ->inRandomOrder()
            ->take(16)
            ->pluck('id');

        $suggestedRecipes = Recipe::published()
            ->where('id', '!=', $recipe->id)
            ->orderByRaw('CASE WHEN id IN (' . ($sameCatIds->isNotEmpty() ? $sameCatIds->implode(',') : '0') . ') THEN 0 ELSE 1 END')
            ->inRandomOrder()
            ->take(16)
            ->get(['id', 'slug', 'title', 'featured_image', 'image_alt', 'prep_time_minutes', 'cook_time_minutes', 'difficulty']);

        // Related blog articles
        $relatedPosts = Post::published()
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get(['id', 'slug', 'title', 'excerpt', 'content', 'featured_image', 'image_alt', 'category', 'published_at']);

        $seo = [
            'title' => $recipe->seo_title ?? $recipe->title . ' - ' . config('app.name'),
            'description' => $recipe->seo_description ?? '',
            'canonical' => route('recipe.show', $recipe->slug),
            'og_type' => 'article',
            'og_image' => $recipe->featured_image ? asset($recipe->featured_image) : null,
        ];

        $breadcrumbSchema = $recipe->toBreadcrumbSchema(config('app.url'));

        return view('recipes.show', compact('recipe', 'relatedRecipes', 'suggestedRecipes', 'relatedPosts', 'seo', 'breadcrumbSchema'));
    }
}
