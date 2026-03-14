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

        // Búsqueda por texto (name="search" en la vista)
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('subtitle', 'like', "%{$term}%");
            });
        }

        // Dificultad: array de valores (name="difficulty[]" en la vista)
        $difficulties = array_filter((array) $request->input('difficulty', []));
        if (count($difficulties)) {
            $query->whereIn('difficulty', $difficulties);
        }

        // País: array de valores (name="country[]" en la vista)
        $countries = array_filter((array) $request->input('country', []));
        if (count($countries)) {
            $query->whereIn('origin_country', $countries);
        }

        // Ordenación
        match ($request->input('sort', 'latest')) {
            'popular' => $query->orderByDesc('view_count'),
            'rating'  => $query->orderByDesc('schema_rating_value'),
            default   => $query->latest('published_at'),
        };

        $recipes = $query->paginate(12)->withQueryString();

        $categories = Category::topLevel()
            ->with('children')
            ->withCount(['recipes' => fn($q) => $q->published()])
            ->ordered()
            ->get();

        $availableCountries = Recipe::published()
            ->whereNotNull('origin_country')
            ->distinct()
            ->pluck('origin_country')
            ->sort()
            ->values();

        $seo = [
            'title'       => 'Todas las Recetas - ' . config('app.name'),
            'description' => 'Explora nuestra colección de recetas latinoamericanas y mediterráneas. Encuentra recetas fáciles, rápidas y deliciosas.',
            'canonical'   => route('recipes.index'),
        ];

        return view('recipes.index', [
            'recipes'    => $recipes,
            'categories' => $categories,
            'countries'  => $availableCountries,
            'seo'        => $seo,
        ]);
    }
}
