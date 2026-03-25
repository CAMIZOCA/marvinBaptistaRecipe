<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <sitemap>
        <loc>{{ config('app.url') }}/recipe-sitemap.xml</loc>
        @if($recipesLastmod)
        <lastmod>{{ \Carbon\Carbon::parse($recipesLastmod)->format('Y-m-d') }}</lastmod>
        @endif
    </sitemap>

    <sitemap>
        <loc>{{ config('app.url') }}/blog-sitemap.xml</loc>
        @if($blogLastmod)
        <lastmod>{{ \Carbon\Carbon::parse($blogLastmod)->format('Y-m-d') }}</lastmod>
        @endif
    </sitemap>

    <sitemap>
        <loc>{{ config('app.url') }}/page-sitemap.xml</loc>
        @if($pagesLastmod)
        <lastmod>{{ \Carbon\Carbon::parse($pagesLastmod)->format('Y-m-d') }}</lastmod>
        @endif
    </sitemap>

</sitemapindex>
