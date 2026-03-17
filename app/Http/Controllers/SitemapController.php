<?php

namespace App\Http\Controllers;

use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\IngredientIndex;
use App\Models\Recipe;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $recipes = Recipe::published()
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->get();

        $categories = Category::select('slug', 'updated_at')->get();
        $ingredients = IngredientIndex::select('slug', 'updated_at')->get();
        $books = AmazonBook::select('slug', 'updated_at')->latest('updated_at')->get();

        $content = view('sitemap.index', compact('recipes', 'categories', 'ingredients', 'books'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /admin\n\n";
        $content .= "Sitemap: " . config('app.url') . "/sitemap.xml\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
