<?php

namespace App\Http\Controllers;

use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $data = Cache::remember('home_page_data', 3600, function () {
            $heroRecipes = Recipe::published()
                ->with('categories')
                ->orderByDesc('view_count')
                ->take(3)
                ->get();

            $featuredRecipes = Recipe::published()
                ->with('categories')
                ->orderByDesc('view_count')
                ->take(6)
                ->get();

            $latestRecipes = Recipe::published()
                ->with('categories')
                ->latest('published_at')
                ->take(3)
                ->get();

            $quickRecipes = Recipe::published()
                ->with('categories')
                ->where(function ($q) {
                    $q->whereRaw('(COALESCE(prep_time_minutes, 0) + COALESCE(cook_time_minutes, 0)) <= 30');
                })
                ->latest('published_at')
                ->take(4)
                ->get();

            $featuredCategories = Category::topLevel()
                ->withCount(['recipes' => fn($q) => $q->published()])
                ->ordered()
                ->get()
                ->filter(fn($c) => $c->recipes_count > 0)
                ->take(8)
                ->values();

            $totalRecipes  = Recipe::published()->count();
            $totalCountries = Recipe::published()->whereNotNull('origin_country')->distinct()->count('origin_country');

            return compact(
                'heroRecipes', 'featuredRecipes', 'latestRecipes', 'quickRecipes',
                'featuredCategories', 'totalRecipes', 'totalCountries'
            );
        });

        extract($data);

        // Selección personalizada de libros (fuera del cache, por request)
        $carouselBooks = $this->selectCarouselBooks($request);

        return view('home.index', compact(
            'heroRecipes', 'featuredRecipes', 'latestRecipes', 'quickRecipes',
            'featuredCategories', 'carouselBooks', 'totalRecipes', 'totalCountries'
        ));
    }

    private function selectCarouselBooks(Request $request): \Illuminate\Database\Eloquent\Collection
    {
        // Prioridad 1: país del visitante (Accept-Language)
        $cuisineFromLang = $this->cuisineTypeFromLanguage($request->header('Accept-Language', ''));
        if ($cuisineFromLang) {
            $books = AmazonBook::where('is_active', true)
                ->where('cuisine_type', $cuisineFromLang)
                ->inRandomOrder()->take(3)->get();
            if ($books->count() === 3) return $books;
        }

        // Prioridad 2: última receta vista (sesión)
        $lastCountry = session('last_recipe_country');
        if ($lastCountry) {
            $cuisineFromSession = $this->cuisineTypeFromOriginCountry($lastCountry);
            if ($cuisineFromSession && $cuisineFromSession !== $cuisineFromLang) {
                $books = AmazonBook::where('is_active', true)
                    ->where('cuisine_type', $cuisineFromSession)
                    ->inRandomOrder()->take(3)->get();
                if ($books->count() === 3) return $books;
            }
        }

        // Fallback: 3 libros aleatorios activos
        return AmazonBook::where('is_active', true)->inRandomOrder()->take(3)->get();
    }

    private function cuisineTypeFromLanguage(string $acceptLanguage): ?string
    {
        $lang = strtolower($acceptLanguage);
        $map = [
            'es-mx' => 'mexicana', 'es-gt' => 'mexicana', 'es-hn' => 'mexicana',
            'es-sv' => 'mexicana', 'es-ni' => 'mexicana', 'es-cr' => 'mexicana', 'es-pa' => 'mexicana',
            'es-co' => 'colombiana', 'es-pe' => 'peruana', 'es-ec' => 'ecuatoriana',
            'es-ar' => 'argentina',  'es-es' => 'española',
            'es-ve' => 'internacional', 'es-bo' => 'internacional',
            'es-py' => 'internacional', 'es-uy' => 'internacional', 'es-cl' => 'internacional',
            'pt-br' => 'mediterránea', 'pt-pt' => 'mediterránea',
        ];
        foreach ($map as $tag => $cuisine) {
            if (str_contains($lang, $tag)) return $cuisine;
        }
        return null;
    }

    private function cuisineTypeFromOriginCountry(string $country): ?string
    {
        return match($country) {
            'México', 'Mexico'   => 'mexicana',
            'España', 'Espana'   => 'española',
            'Perú', 'Peru'       => 'peruana',
            'Ecuador'            => 'ecuatoriana',
            'Argentina'          => 'argentina',
            'Colombia'           => 'colombiana',
            'Italia', 'Italy'    => 'mediterránea',
            default              => null,
        };
    }
}
