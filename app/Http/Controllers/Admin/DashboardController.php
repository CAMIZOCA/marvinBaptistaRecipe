<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Recipe;
use App\Models\Setting;
use App\Models\User;
use App\Services\GoogleApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_recipes'     => Recipe::count(),
            'published_recipes' => Recipe::published()->count(),
            'draft_recipes'     => Recipe::draft()->count(),
            'ai_pending'        => Recipe::aiPending()->where('is_published', true)->count(),
            'total_views'       => Recipe::sum('view_count'),
            'total_books'       => AmazonBook::where('is_active', true)->count(),
            'total_categories'  => Category::count(),
            'total_users'       => User::count(),
        ];

        // ── Páginas publicadas en sitemap ──────────────────────────
        $stats['sitemap_pages'] =
            Recipe::published()->count()
            + Post::published()->count()
            + Page::where('is_published', true)->count()
            + AmazonBook::where('is_active', true)->count()
            + Category::count();

        // ── Google Analytics + Search Console ─────────────────────
        $google      = app(GoogleApiService::class);
        $propertyId  = Setting::get('ga4_property_id', '');
        $siteUrl     = Setting::get('search_console_site_url', '');

        $ga4Stats = ($google->isConfigured() && $propertyId)
            ? $google->ga4Stats($propertyId)
            : null;

        $scStats = ($google->isConfigured() && $siteUrl)
            ? $google->searchConsoleStats($siteUrl)
            : null;

        $googleConfigured = $google->isConfigured();

        // ── Tablas ────────────────────────────────────────────────
        $latestRecipes = Recipe::with('categories')
            ->latest()
            ->take(10)
            ->get();

        $topRecipes = Recipe::published()
            ->orderByDesc('view_count')
            ->take(5)
            ->get();

        $recentlyPublished = Recipe::published()
            ->where('published_at', '>=', now()->subDays(30))
            ->count();

        return view('admin.dashboard.index', compact(
            'stats', 'latestRecipes', 'topRecipes', 'recentlyPublished',
            'ga4Stats', 'scStats', 'googleConfigured'
        ));
    }
}
