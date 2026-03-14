<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::withCount('recipes')->orderBy('name')->paginate(50);
        return view('admin.tags.index', compact('tags'));
    }

    public function create(): RedirectResponse
    {
        // Create is handled inline on the index page
        return redirect()->route('admin.etiquetas.index');
    }

    public function show(Tag $tag): RedirectResponse
    {
        return redirect()->route('admin.etiquetas.index');
    }

    public function edit(Tag $tag): View
    {
        $tags    = Tag::withCount('recipes')->orderBy('name')->paginate(50);
        $editTag = $tag;
        return view('admin.tags.index', compact('tags', 'editTag'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
        Tag::create($data);
        return redirect()->route('admin.etiquetas.index')->with('success', 'Etiqueta creada.');
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
        $tag->update($data);
        return redirect()->route('admin.etiquetas.index')->with('success', 'Etiqueta «' . $tag->name . '» actualizada.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();
        return redirect()->route('admin.etiquetas.index')->with('success', 'Etiqueta eliminada.');
    }
}
