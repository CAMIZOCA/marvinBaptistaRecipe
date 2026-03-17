<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmazonBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        if (isset($data['description'])) {
            $data['description'] = $this->sanitizeDescription($data['description']);
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image_url'] = url(Storage::url(
                $request->file('cover_image')->store('books', 'public')
            ));
        }
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
        if (isset($data['description'])) {
            $data['description'] = $this->sanitizeDescription($data['description']);
        }
        if ($request->hasFile('cover_image')) {
            // Delete old file if it was uploaded locally
            if ($book->cover_image_url) {
                $parsed = parse_url($book->cover_image_url, PHP_URL_PATH);
                if ($parsed && str_contains($parsed, '/storage/books/')) {
                    Storage::disk('public')->delete('books/' . basename($parsed));
                }
            }
            $data['cover_image_url'] = url(Storage::url(
                $request->file('cover_image')->store('books', 'public')
            ));
        }
        $book->update($data);
        return redirect()->route('admin.libros.index')->with('success', 'Libro actualizado.');
    }

    public function destroy(AmazonBook $book): RedirectResponse
    {
        $book->delete();
        return redirect()->route('admin.libros.index')->with('success', 'Libro eliminado.');
    }

    public function toggleActive(AmazonBook $book): RedirectResponse
    {
        $book->update(['is_active' => !$book->is_active]);
        return redirect()->route('admin.libros.index');
    }

    private function sanitizeDescription(?string $html): ?string
    {
        if (!$html) return $html;
        // Normalize non-breaking spaces (&nbsp; / \u00A0) to regular spaces so text wraps naturally
        $html = str_replace(['&nbsp;', "\u{00A0}", "\xc2\xa0"], ' ', $html);
        // Collapse runs of multiple spaces into one
        $html = preg_replace('/ {2,}/', ' ', $html);
        return strip_tags($html, '<p><br><strong><em><u><ol><ul><li><a><h2><h3>');
    }

    private function validateBook(Request $request): array
    {
        return $request->validate([
            'cover_image' => ['nullable', 'image', 'max:3072'],
            'asin' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'cover_image_url' => ['nullable', 'string', 'max:500'],
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
