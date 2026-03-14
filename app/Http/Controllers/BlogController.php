<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Recipe;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);

        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get(['id', 'slug', 'title', 'featured_image', 'category', 'published_at']);

        // Related recipes: pick 6 published recipes, prioritise same-category matches via post category keywords
        $relatedRecipes = Recipe::published()
            ->inRandomOrder()
            ->take(6)
            ->get(['id', 'slug', 'title', 'featured_image', 'image_alt', 'prep_time_minutes', 'cook_time_minutes', 'difficulty']);

        return view('blog.show', compact('post', 'recentPosts', 'relatedRecipes'));
    }
}
