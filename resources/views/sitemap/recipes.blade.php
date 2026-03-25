<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    {{-- ═══════════════════════════════════════════════════════════
         RECETAS INDIVIDUALES
    ═══════════════════════════════════════════════════════════ --}}

    @foreach($recipes as $recipe)
    <url>
        <loc>{{ route('recipe.show', $recipe->slug) }}</loc>
        <lastmod>{{ $recipe->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if($recipe->featured_image)
        <image:image>
            <image:loc>{{ Str::startsWith($recipe->featured_image, 'http') ? $recipe->featured_image : asset(ltrim($recipe->featured_image, '/')) }}</image:loc>
            <image:title>{{ htmlspecialchars($recipe->title) }}</image:title>
            @if($recipe->image_alt)
            <image:caption>{{ htmlspecialchars($recipe->image_alt) }}</image:caption>
            @endif
        </image:image>
        @endif
    </url>
    @endforeach

    {{-- ═══════════════════════════════════════════════════════════
         CATEGORÍAS
    ═══════════════════════════════════════════════════════════ --}}

    @foreach($categories as $category)
    <url>
        <loc>{{ route('category.show', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- ═══════════════════════════════════════════════════════════
         ÍNDICE DE INGREDIENTES
    ═══════════════════════════════════════════════════════════ --}}

    @foreach($ingredients as $ingredient)
    <url>
        <loc>{{ route('ingredient.show', $ingredient->slug) }}</loc>
        <lastmod>{{ $ingredient->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    @endforeach

</urlset>
