<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IngredientIndex;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientAdminController extends Controller
{
    public function index(): View
    {
        $ingredients = IngredientIndex::orderBy('name')->paginate(30);
        return view('admin.ingredients.index', compact('ingredients'));
    }

    public function create(): View
    {
        $ingredient = new IngredientIndex();
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateIngredient($request);
        IngredientIndex::create($data);
        return redirect()->route('admin.ingredientes.index')->with('success', 'Ingrediente creado.');
    }

    public function edit(IngredientIndex $ingredient): View
    {
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, IngredientIndex $ingredient): RedirectResponse
    {
        $data = $this->validateIngredient($request);
        $ingredient->update($data);
        return redirect()->route('admin.ingredientes.index')->with('success', 'Ingrediente actualizado.');
    }

    public function destroy(IngredientIndex $ingredient): RedirectResponse
    {
        $ingredient->delete();
        return redirect()->route('admin.ingredientes.index')->with('success', 'Ingrediente eliminado.');
    }

    private function validateIngredient(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:60'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'image' => ['nullable', 'string'],
        ]);
    }
}
