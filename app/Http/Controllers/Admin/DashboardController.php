<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_recipes' => Recipe::count(),
            'published_recipes' => Recipe::published()->count(),
            'draft_recipes' => Recipe::draft()->count(),
            'ai_pending' => Recipe::aiPending()->where('is_published', true)->count(),
            'total_views' => Recipe::sum('view_count'),
            'total_books' => AmazonBook::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
        ];

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
            'stats', 'latestRecipes', 'topRecipes', 'recentlyPublished'
        ));
    }
}
