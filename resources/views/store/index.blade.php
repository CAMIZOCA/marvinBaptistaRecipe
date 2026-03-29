@extends('layouts.app')

@section('seo_head')
@php $hasFilter = request()->filled('tipo'); @endphp
@if($hasFilter)
<meta name="robots" content="noindex,follow">
@endif
<title>Tienda de Libros de Cocina | Marvin Baptista</title>
<meta name="description" content="Descubre los mejores libros de cocina latinoamericana y mediterránea. Seleccionados con amor para llevar tu cocina al siguiente nivel.">
<link rel="canonical" href="{{ route('store.index') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="Tienda de Libros | Marvin Baptista">
<meta property="og:description" content="Descubre los mejores libros de cocina latinoamericana y mediterránea. Seleccionados con amor para llevar tu cocina al siguiente nivel.">
<meta property="og:url" content="{{ route('store.index') }}">
<meta property="og:image" content="{{ $settings['default_og_image'] ?? asset('images/og-default.jpg') }}">
<meta property="og:image:alt" content="Tienda de Libros de Cocina — Marvin Baptista">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Tienda de Libros | Marvin Baptista">
<meta name="twitter:description" content="Descubre los mejores libros de cocina latinoamericana y mediterránea. Seleccionados con amor para llevar tu cocina al siguiente nivel.">
<meta name="twitter:image" content="{{ $settings['default_og_image'] ?? asset('images/og-default.jpg') }}">
@endsection

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-900 py-20 px-4">
    <div class="max-w-4xl mx-auto text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-full text-sm font-semibold mb-2">
            Libros Seleccionados
        </div>
        <h1 class="text-4xl sm:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
            Libros de Cocina
        </h1>
        <p class="text-zinc-300 text-lg max-w-xl mx-auto leading-relaxed">
            Una selección personal de libros que han transformado mi manera de cocinar. Disponibles en Amazon.
        </p>
        <p class="text-xs text-zinc-500 mt-2">
            * Los enlaces son de afiliado. Recibo una pequeña comisión sin costo adicional para ti.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Filter Tabs by Cuisine --}}
    @if(isset($cuisineTypes) && count($cuisineTypes) > 0)
    <div class="flex flex-wrap gap-2 mb-8" role="tablist" aria-label="Filtrar por tipo de cocina">
        <a href="{{ route('store.index') }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ !request('tipo') ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
            Todos
        </a>
        @foreach($cuisineTypes as $cuisine)
        <a href="{{ route('store.index', ['tipo' => $cuisine]) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ request('tipo') === $cuisine ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
            {{ ucfirst($cuisine) }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- Books Grid --}}
    @if(isset($books) && $books->count() > 0)
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($books as $book)
        <article class="bg-white rounded-2xl border border-zinc-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 overflow-hidden group">
            <a href="{{ route('store.show', $book->slug) }}" class="block"
               data-ga-event="book_card_click" data-ga-category="store" data-ga-label="{{ $book->title }}" data-ga-item-id="{{ $book->asin }}">
                <div class="aspect-[3/4] bg-zinc-50 flex items-center justify-center p-4 overflow-hidden">
                    @if($book->cover_image_url)
                    <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}"
                         loading="lazy"
                         class="max-h-full w-auto object-contain group-hover:scale-105 transition-transform duration-500 shadow-lg rounded">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-zinc-100 rounded-xl">
                        <svg class="w-16 h-16 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    @endif
                </div>
            </a>
            <div class="p-5 space-y-3">
                @if($book->cuisine_type)
                <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">{{ $book->cuisine_type }}</span>
                @endif
                <h2 class="font-bold text-zinc-900 leading-snug line-clamp-2">
                    <a href="{{ route('store.show', $book->slug) }}" class="hover:text-amber-600 transition-colors">
                        {{ $book->title }}
                    </a>
                </h2>
                @if($book->author)
                <p class="text-sm text-zinc-500">{{ $book->author }}</p>
                @endif
                <a href="{{ $book->getAffiliateUrl('US') }}"
                   target="_blank" rel="noopener noreferrer sponsored"
                   class="block w-full text-center py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-sm font-semibold transition-colors"
                   data-ga-event="book_cta_click" data-ga-category="affiliate" data-ga-label="{{ $book->title }}" data-ga-item-id="{{ $book->asin }}">
                    Ver en Amazon
                </a>
            </div>
        </article>
        @endforeach
    </div>

    @if($books->hasPages())
    <div class="mt-10 flex justify-center">
        {{ $books->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    <div class="text-center py-20">
        <svg class="w-16 h-16 text-zinc-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <h2 class="text-xl font-semibold text-zinc-600 mb-2">No hay libros disponibles</h2>
        <p class="text-zinc-400">Vuelve pronto, estamos preparando nuevas recomendaciones.</p>
    </div>
    @endif

</div>

@endsection
