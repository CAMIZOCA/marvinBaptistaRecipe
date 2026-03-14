<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $posts = Post::orderByDesc('created_at')->paginate(20);
        return view('admin.blog.index', compact('posts'));
    }

    public function create(): View
    {
        $post = new Post();
        return view('admin.blog.edit', compact('post'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(Str::slug($request->title));
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;

        Post::create($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artículo creado exitosamente.');
    }

    public function edit(Post $post): View
    {
        return view('admin.blog.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validated($request);
        if ($data['is_published'] && ! $post->published_at) {
            $data['published_at'] = now();
        } elseif (! $data['is_published']) {
            $data['published_at'] = null;
        }

        $post->update($data);

        return back()->with('success', '¡Artículo actualizado exitosamente!');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();
        return redirect()->route('admin.blog.index')
            ->with('success', 'Artículo eliminado.');
    }

    public function togglePublished(Post $post): RedirectResponse
    {
        $post->update([
            'is_published' => ! $post->is_published,
            'published_at' => ! $post->is_published ? now() : $post->published_at,
        ]);
        return back()->with('success', $post->is_published ? 'Artículo publicado.' : 'Artículo despublicado.');
    }

    /* ─── Helpers ───────────────────────────────────────────── */

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'excerpt'         => ['nullable', 'string'],
            'content'         => ['nullable', 'string'],
            'featured_image'  => ['nullable', 'url', 'max:500'],
            'image_alt'       => ['nullable', 'string', 'max:255'],
            'category'        => ['nullable', 'string', 'max:100'],
            'seo_title'       => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'is_published'    => ['boolean'],
            'published_at'    => ['nullable', 'date'],
        ]);
    }

    private function uniqueSlug(string $base, int $postId = 0): string
    {
        $slug = $base;
        $i    = 1;
        while (Post::where('slug', $slug)->where('id', '!=', $postId)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
