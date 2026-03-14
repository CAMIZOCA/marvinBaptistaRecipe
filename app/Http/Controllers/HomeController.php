<?php

namespace App\Http\Controllers;

use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
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

            $featuredBook = AmazonBook::where('is_active', true)
                ->inRandomOrder()
                ->first();

            $totalRecipes  = Recipe::published()->count();
            $totalCountries = Recipe::published()->whereNotNull('origin_country')->distinct()->count('origin_country');

            return compact(
                'heroRecipes', 'featuredRecipes', 'latestRecipes', 'quickRecipes',
                'featuredCategories', 'featuredBook', 'totalRecipes', 'totalCountries'
            );
        });

        extract($data);

        return view('home.index', compact(
            'heroRecipes', 'featuredRecipes', 'latestRecipes', 'quickRecipes',
            'featuredCategories', 'featuredBook', 'totalRecipes', 'totalCountries'
        ));
    }
}
