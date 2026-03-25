<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    {{-- ═══════════════════════════════════════════════════════════
         ÍNDICE DEL BLOG
    ═══════════════════════════════════════════════════════════ --}}

    <url>
        <loc>{{ route('blog.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    {{-- ═══════════════════════════════════════════════════════════
         ARTÍCULOS INDIVIDUALES
    ═══════════════════════════════════════════════════════════ --}}

    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.show', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
        @if($post->featured_image)
        <image:image>
            <image:loc>{{ Str::startsWith($post->featured_image, 'http') ? $post->featured_image : asset(ltrim($post->featured_image, '/')) }}</image:loc>
            <image:title>{{ htmlspecialchars($post->title) }}</image:title>
            @if($post->image_alt)
            <image:caption>{{ htmlspecialchars($post->image_alt) }}</image:caption>
            @endif
        </image:image>
        @endif
    </url>
    @endforeach

</urlset>
