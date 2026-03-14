<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function show(Request $request, Category $category, Category $subcategory): View
    {
        // Verify subcategory belongs to category
        abort_unless($subcategory->parent_id === $category->id, 404);

        $recipes = $subcategory->recipes()
            ->published()
            ->with('categories')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $seo = [
            'title' => ($subcategory->seo_title ?? $subcategory->name) . ' - ' . config('app.name'),
            'description' => $subcategory->seo_description ?? "Recetas de {$subcategory->name}.",
            'canonical' => route('subcategory.show', [$category->slug, $subcategory->slug]),
        ];

        return view('categories.show', ['category' => $subcategory, 'recipes' => $recipes, 'seo' => $seo, 'parent' => $category]);
    }
}
