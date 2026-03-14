<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmazonBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = AmazonBook::withCount('recipes')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(20);

        return view('admin.books.index', compact('books'));
    }

    public function create(): View
    {
        $book = new AmazonBook();
        return view('admin.books.edit', compact('book'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBook($request);
        AmazonBook::create($data);
        return redirect()->route('admin.libros.index')->with('success', 'Libro creado exitosamente.');
    }

    public function edit(AmazonBook $book): View
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, AmazonBook $book): RedirectResponse
    {
        $data = $this->validateBook($request);
        $book->update($data);
        return redirect()->route('admin.libros.index')->with('success', 'Libro actualizado.');
    }

    public function destroy(AmazonBook $book): RedirectResponse
    {
        $book->delete();
        return redirect()->route('admin.libros.index')->with('success', 'Libro eliminado.');
    }

    private function validateBook(Request $request): array
    {
        return $request->validate([
            'asin' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'cover_image_url' => ['nullable', 'url', 'max:500'],
            'amazon_url_us' => ['nullable', 'url', 'max:500'],
            'amazon_url_mx' => ['nullable', 'url', 'max:500'],
            'amazon_url_es' => ['nullable', 'url', 'max:500'],
            'amazon_url_ar' => ['nullable', 'url', 'max:500'],
            'cuisine_type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'keywords_match' => ['nullable', 'string'], // comma-separated, will convert to array
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'asin.required' => 'El ASIN de Amazon es obligatorio.',
            'title.required' => 'El título del libro es obligatorio.',
        ]);
    }
}
