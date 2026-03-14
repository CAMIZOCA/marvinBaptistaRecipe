<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Request $request, Category $category): View
    {
        // If category has children, show hub page
        if ($category->children()->exists()) {
            $children = $category->children()->withCount(['recipes' => fn($q) => $q->published()])->get();
            return view('categories.hub', compact('category', 'children'));
        }

        $page = request()->get('page', 1);
        $cacheKey = "category:{$category->slug}:page:{$page}";

        $recipes = Cache::remember($cacheKey, 3600, function () use ($category) {
            return $category->recipes()
                ->published()
                ->with('categories')
                ->latest('published_at')
                ->paginate(12)
                ->withQueryString();
        });

        $seo = [
            'title' => ($category->seo_title ?? $category->name) . ' - ' . config('app.name'),
            'description' => $category->seo_description ?? "Recetas de {$category->name}. Encuentra las mejores recetas con ingredientes tradicionales.",
            'canonical' => route('category.show', $category->slug),
        ];

        return view('categories.show', compact('category', 'recipes', 'seo'));
    }
}
