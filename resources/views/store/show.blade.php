@extends('layouts.app')

@section('seo_head')
<title>{{ $book->title }} | Libros | Marvin Baptista</title>
<meta name="description" content="{{ Str::limit(strip_tags($book->description ?? 'Libro de cocina recomendado por Marvin Baptista.'), 160) }}">
<link rel="canonical" href="{{ route('store.show', $book->id) }}">
<meta property="og:type" content="product">
<meta property="og:title" content="{{ $book->title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($book->description ?? 'Libro de cocina recomendado por Marvin Baptista.'), 160) }}">
<meta property="og:url" content="{{ route('store.show', $book->id) }}">
<meta property="og:site_name" content="Marvin Baptista">
@if($book->cover_image_url)
<meta property="og:image" content="{{ $book->cover_image_url }}">
<meta property="og:image:alt" content="{{ $book->title }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $book->title }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($book->description ?? ''), 160) }}">
@if($book->cover_image_url)
<meta name="twitter:image" content="{{ $book->cover_image_url }}">
@endif
@endsection

@section('schema_org')
@php
$bookSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Book',
    'name'        => $book->title,
    'author'      => ['@type' => 'Person', 'name' => $book->author ?? 'Desconocido'],
    'description' => Str::limit(strip_tags($book->description ?? ''), 300),
    'url'         => route('store.show', $book->id),
];
if ($book->cover_image_url) $bookSchema['image'] = $book->cover_image_url;
if ($book->asin)            $bookSchema['isbn']  = $book->asin;

$bookBreadcrumb = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tienda', 'item' => route('store.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $book->title, 'item' => route('store.show', $book->id)],
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($bookSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($bookBreadcrumb, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-8 flex-wrap" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('store.index') }}" class="hover:text-zinc-700 transition-colors">Tienda</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-medium line-clamp-1">{{ $book->title }}</span>
    </nav>

    <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-10 mb-14 overflow-hidden">

        {{-- Book Cover --}}
        <div class="lg:col-span-2 flex justify-center min-w-0">
            <div class="relative">
                @if($book->cover_image_url)
                <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}"
                     class="max-w-xs w-full rounded-2xl shadow-2xl"
                     loading="eager">
                @else
                <div class="w-64 h-80 bg-zinc-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-20 h-20 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                @endif
            </div>
        </div>

        {{-- Book Info --}}
        <div class="lg:col-span-3 space-y-5 min-w-0">
            @if($book->cuisine_type)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                {{ $book->cuisine_type }}
            </span>
            @endif

            <h1 class="text-3xl lg:text-4xl font-bold text-zinc-900 leading-tight"
                style="font-family: 'Playfair Display', serif;">
                {{ $book->title }}
            </h1>

            @if($book->author)
            <p class="text-lg text-zinc-500">Por <strong class="text-zinc-700 font-semibold">{{ $book->author }}</strong></p>
            @endif

            @if($book->description)
            <div class="max-w-none text-zinc-700 leading-relaxed text-base
                        [overflow-wrap:anywhere] [word-break:break-word]
                        [&_p]:mb-3 [&_p:last-child]:mb-0
                        [&_br]:block
                        [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-3
                        [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-3
                        [&_li]:mb-1
                        [&_strong]:font-semibold
                        [&_em]:italic
                        [&_a]:text-amber-600 [&_a]:underline [&_a:hover]:text-amber-500">
                {!! $book->description !!}
            </div>
            @endif

            {{-- Affiliate CTA --}}
            <div class="space-y-3 pt-4">
                <a href="{{ $book->getAffiliateUrl('US') }}" target="_blank" rel="noopener noreferrer sponsored"
                   class="inline-flex items-center gap-2 px-7 py-3.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl font-semibold text-base transition-all hover:shadow-md"
                   data-ga-event="book_cta_click" data-ga-category="affiliate" data-ga-label="{{ $book->title }}" data-ga-item-id="{{ $book->asin }}">
                    Ver en Amazon
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                <p class="text-xs text-zinc-400 italic leading-relaxed">
                    Como afiliado de Amazon, recibo una pequeña comisión por las compras realizadas a través de estos enlaces, sin costo adicional para ti.
                </p>
            </div>
        </div>
    </div>

    {{-- Related Books --}}
    @if(isset($relatedBooks) && $relatedBooks->count() > 0)
    <section aria-label="Otros libros relacionados">
        <h2 class="text-2xl font-bold text-zinc-900 mb-6" style="font-family: 'Playfair Display', serif;">
            Otros Libros que te Pueden Interesar
        </h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($relatedBooks as $related)
            <a href="{{ route('store.show', $related->id) }}"
               class="group flex flex-col bg-white rounded-2xl border border-zinc-100 overflow-hidden hover:shadow-md transition-all">
                <div class="aspect-[3/4] bg-zinc-50 flex items-center justify-center p-3">
                    @if($related->cover_image_url)
                    <img src="{{ $related->cover_image_url }}" alt="{{ $related->title }}"
                         loading="lazy"
                         class="max-h-full object-contain rounded group-hover:scale-105 transition-transform duration-300">
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-zinc-900 text-sm line-clamp-2 leading-snug group-hover:text-amber-600 transition-colors">{{ $related->title }}</h3>
                    @if($related->author)
                    <p class="text-xs text-zinc-400 mt-1">{{ $related->author }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection

@push('scripts')
<script>
/* ── view_item: evento GA4 Ecommerce para libros ────────────────────────────
   Permite rastrear qué libros se consultan y correlacionar con clics de CTA
   ──────────────────────────────────────────────────────────────────────── */
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
    event:         'view_item',
    item_id:       '{{ $book->asin }}',
    item_name:     '{{ addslashes($book->title) }}',
    item_category: '{{ addslashes($book->cuisine_type ?? '') }}',
    item_brand:    '{{ addslashes($book->author ?? '') }}'
});
</script>
@endpush
