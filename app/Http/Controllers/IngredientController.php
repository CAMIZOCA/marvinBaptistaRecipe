<?php

namespace App\Http\Controllers;

use App\Models\IngredientIndex;
use App\Models\Recipe;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function show(IngredientIndex $ingredient): View
    {
        $recipes = Recipe::published()
            ->whereHas('ingredients', fn($q) => $q->where('ingredient_name', 'like', '%' . $ingredient->name . '%'))
            ->with('categories')
            ->paginate(12);

        $seo = [
            'title' => ($ingredient->seo_title ?? 'Recetas con ' . $ingredient->name) . ' - ' . config('app.name'),
            'description' => $ingredient->seo_description ?? "Descubre todas las recetas que llevan {$ingredient->name}. Recetas fáciles y deliciosas.",
            'canonical' => route('ingredient.show', $ingredient->slug),
        ];

        return view('ingredients.show', compact('ingredient', 'recipes', 'seo'));
    }
}
