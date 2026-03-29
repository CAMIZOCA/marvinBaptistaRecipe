<?php

namespace App\Http\Controllers;

use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\IngredientIndex;
use App\Models\Page;
use App\Models\Post;
use App\Models\Recipe;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $recipesLastmod     = Recipe::published()->max('updated_at');
        $blogLastmod        = Post::published()->max('updated_at');
        $pagesLastmod       = Page::where('is_published', true)->max('updated_at');

        $content = view('sitemap.index', compact('recipesLastmod', 'blogLastmod', 'pagesLastmod'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function recipes(): Response
    {
        $recipes     = Recipe::published()
            ->select('slug', 'updated_at', 'featured_image', 'image_alt', 'title')
            ->latest('updated_at')
            ->get();

        $categories  = Category::select('slug', 'updated_at')
            ->whereHas('recipes', fn($q) => $q->published())
            ->get();
        $ingredients = IngredientIndex::select('slug', 'updated_at', 'name')->get()
            ->filter(fn($ing) => Recipe::published()
                ->whereHas('ingredients', fn($q) => $q->where('ingredient_name', 'like', '%'.$ing->name.'%'))
                ->exists()
            );

        $content = view('sitemap.recipes', compact('recipes', 'categories', 'ingredients'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function blog(): Response
    {
        $posts = Post::published()
            ->select('slug', 'updated_at', 'featured_image', 'image_alt', 'title')
            ->latest('updated_at')
            ->get();

        $content = view('sitemap.blog', compact('posts'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function pages(): Response
    {
        $pages = Page::where('is_published', true)->select('slug', 'updated_at')->get();
        $books = AmazonBook::select('slug', 'updated_at')->latest('updated_at')->get();

        $content = view('sitemap.pages', compact('pages', 'books'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /admin',
            '',
            'Sitemap: '.rtrim(config('app.url'), '/').'/sitemap.xml',
            '',
        ]);

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
