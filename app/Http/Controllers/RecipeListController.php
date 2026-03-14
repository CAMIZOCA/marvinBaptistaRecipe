<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecipeListController extends Controller
{
    public function index(Request $request): View
    {
        $query = Recipe::published()->with('categories');

        if ($request->filled('dificultad')) {
            $query->byDifficulty($request->dificultad);
        }
        if ($request->filled('pais')) {
            $query->byCountry($request->pais);
        }
        if ($request->filled('buscar')) {
            $query->where('title', 'like', '%' . $request->buscar . '%');
        }

        $recipes = $query->latest('published_at')->paginate(12)->withQueryString();

        $categories = Category::topLevel()->with('children')->withCount(['recipes' => fn($q) => $q->published()])->ordered()->get();
        $countries = Recipe::published()->whereNotNull('origin_country')->distinct()->pluck('origin_country')->sort()->values();

        $seo = [
            'title' => 'Todas las Recetas - ' . config('app.name'),
            'description' => 'Explora nuestra colección de recetas latinoamericanas y mediterráneas. Encuentra recetas fáciles, rápidas y deliciosas.',
            'canonical' => route('recipes.index'),
        ];

        return view('recipes.index', compact('recipes', 'categories', 'countries', 'seo'));
    }
}
