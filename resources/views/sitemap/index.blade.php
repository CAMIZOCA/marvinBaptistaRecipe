<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    {{-- Homepage --}}
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Recipes listing --}}
    <url>
        <loc>{{ route('recipes.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Store --}}
    <url>
        <loc>{{ route('store.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- Individual Recipes --}}
    @foreach($recipes as $recipe)
    <url>
        <loc>{{ route('recipe.show', $recipe->slug) }}</loc>
        <lastmod>{{ $recipe->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if($recipe->featured_image)
        <image:image>
            <image:loc>{{ $recipe->featured_image }}</image:loc>
            <image:title>{{ htmlspecialchars($recipe->title) }}</image:title>
            @if($recipe->image_alt)
            <image:caption>{{ htmlspecialchars($recipe->image_alt) }}</image:caption>
            @endif
        </image:image>
        @endif
    </url>
    @endforeach

    {{-- Categories --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ route('category.show', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- Books --}}
    @foreach($books ?? [] as $book)
    <url>
        <loc>{{ route('store.show', $book->id) }}</loc>
        <lastmod>{{ $book->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

    {{-- Pages --}}
    @foreach($pages ?? [] as $page)
    @if($page->is_published)
    <url>
        <loc>{{ route('page.show', $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    @endif
    @endforeach

</urlset>
