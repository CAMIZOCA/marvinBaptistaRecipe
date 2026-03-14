<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('parent', 'children')
            ->topLevel()
            ->withCount('recipes')
            ->ordered()
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::topLevel()->ordered()->get();
        $category = new Category();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:60'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        Category::create($data);
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit(Category $category): View
    {
        $parents = Category::topLevel()->where('id', '!=', $category->id)->ordered()->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:60'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $category->update($data);
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada.');
    }
}
