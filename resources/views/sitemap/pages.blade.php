<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- ═══════════════════════════════════════════════════════════
         PÁGINAS PRINCIPALES DE NAVEGACIÓN
    ═══════════════════════════════════════════════════════════ --}}

    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ route('recipes.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ route('store.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- ═══════════════════════════════════════════════════════════
         LIBROS / TIENDA
    ═══════════════════════════════════════════════════════════ --}}

    @foreach($books as $book)
    <url>
        <loc>{{ route('store.show', $book->slug) }}</loc>
        <lastmod>{{ $book->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

    {{-- ═══════════════════════════════════════════════════════════
         PÁGINAS ESTÁTICAS
    ═══════════════════════════════════════════════════════════ --}}

    @foreach($pages as $page)
    <url>
        <loc>{{ route('page.show', $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    @endforeach

</urlset>
