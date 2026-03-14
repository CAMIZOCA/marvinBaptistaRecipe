<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageAdminController extends Controller
{
    public function index(): View
    {
        $pages = Page::orderBy('title')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        $page = new Page(['is_published' => false]);
        return view('admin.pages.edit', compact('page'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePage($request);
        $data['is_published'] = $request->boolean('is_published');
        Page::create($data);
        return redirect()->route('admin.paginas.index')->with('success', 'Página creada.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validatePage($request);
        $data['is_published'] = $request->boolean('is_published');
        $page->update($data);
        return redirect()->route('admin.paginas.index')->with('success', 'Página actualizada.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();
        return redirect()->route('admin.paginas.index')->with('success', 'Página eliminada.');
    }

    private function validatePage(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:60'],
            'seo_description' => ['nullable', 'string', 'max:160'],
        ]);
    }
}
