<?php

namespace App\Http\Controllers;

use App\Models\AmazonBook;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $query = AmazonBook::where('is_active', true);

        if ($request->filled('tipo')) {
            $query->where('cuisine_type', $request->tipo);
        }

        $books = $query->orderBy('sort_order')->orderBy('title')->paginate(24)->withQueryString();

        $cuisineTypes = AmazonBook::where('is_active', true)
            ->whereNotNull('cuisine_type')
            ->distinct()
            ->pluck('cuisine_type')
            ->sort()
            ->values();

        $seo = [
            'title' => 'Tienda de Libros de Cocina - ' . config('app.name'),
            'description' => 'Libros de cocina latinoamericana y mediterránea. Amplía tu cocina con los mejores recetarios.',
            'canonical' => route('store.index'),
        ];

        return view('store.index', compact('books', 'cuisineTypes', 'seo'));
    }

    public function show(AmazonBook $book): View
    {
        $relatedBooks = AmazonBook::where('is_active', true)
            ->where('id', '!=', $book->id)
            ->where('cuisine_type', $book->cuisine_type)
            ->take(4)
            ->get();

        // Detect user country from Accept-Language header
        $lang = request()->header('Accept-Language', 'en');
        $country = $this->detectCountry($lang);

        $affiliateUrl = $book->getAffiliateUrl($country, config('services.amazon.affiliate_tag'));

        $seo = [
            'title' => $book->title . ' - ' . config('app.name'),
            'description' => "Conoce el libro '{$book->title}' de {$book->author}. Disponible en Amazon.",
            'canonical' => route('store.show', $book->slug),
            'og_image' => $book->cover_image_url,
        ];

        return view('store.show', compact('book', 'relatedBooks', 'affiliateUrl', 'seo'));
    }

    private function detectCountry(string $acceptLanguage): string
    {
        $lang = strtolower($acceptLanguage);
        if (str_contains($lang, 'es-mx') || str_contains($lang, 'es-gt') || str_contains($lang, 'es-co')) return 'MX';
        if (str_contains($lang, 'es-ar')) return 'AR';
        if (str_contains($lang, 'es-es')) return 'ES';
        return 'US';
    }
}
