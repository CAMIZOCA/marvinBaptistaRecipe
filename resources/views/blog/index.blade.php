@extends('layouts.app')

@section('seo_head')
<title>Blog de Cocina — Técnicas, Historia y Consejos | Marvin Baptista</title>
<meta name="description" content="Artículos sobre técnicas culinarias, historia de la gastronomía latinoamericana y mediterránea, ingredientes y consejos de chef para cocinar mejor.">
<link rel="canonical" href="{{ route('blog.index') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="Blog de Cocina | Marvin Baptista">
<meta property="og:url" content="{{ route('blog.index') }}">
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <header class="mb-12 max-w-2xl">
        <p class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3">Blog</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900 mb-3" style="font-family: 'Playfair Display', serif;">
            Cocina, Historia & Técnicas
        </h1>
        <p class="text-zinc-500 text-lg leading-relaxed">
            Artículos para entender mejor los sabores, las tradiciones y las técnicas de la cocina latinoamericana y mediterránea.
        </p>
    </header>

    @if($posts->count() > 0)

    {{-- Featured post (first) --}}
    @php $featured = $posts->first(); @endphp
    <article class="group mb-12 bg-white rounded-3xl overflow-hidden border border-zinc-100 shadow-sm hover:shadow-lg transition-all duration-300 lg:flex">
        <div class="lg:w-1/2 aspect-video lg:aspect-auto overflow-hidden bg-gradient-to-br from-amber-50 via-orange-50 to-amber-100">
            @if($featured->featured_image)
            <img src="{{ $featured->featured_image }}"
                 alt="{{ $featured->image_alt ?? $featured->title }}"
                 loading="eager"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            @else
            <div class="w-full h-full min-h-[280px] flex flex-col items-center justify-center gap-3">
                <svg class="w-20 h-20 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span class="text-amber-400 font-semibold text-sm">{{ $featured->category ?? 'Blog de Cocina' }}</span>
            </div>
            @endif
        </div>
        <div class="lg:w-1/2 p-8 flex flex-col justify-center">
            @if($featured->category)
            <span class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3 inline-block">{{ $featured->category }}</span>
            @endif
            <h2 class="text-2xl font-bold text-zinc-900 group-hover:text-amber-600 transition-colors mb-3 leading-snug" style="font-family: 'Playfair Display', serif;">
                <a href="{{ route('blog.show', $featured->slug) }}">{{ $featured->title }}</a>
            </h2>
            <p class="text-zinc-500 leading-relaxed mb-5 line-clamp-3">{{ $featured->short_excerpt }}</p>
            <div class="flex items-center justify-between">
                @if($featured->published_at)
                <time class="text-xs text-zinc-400">{{ $featured->published_at->translatedFormat('d \d\e F, Y') }}</time>
                @endif
                <a href="{{ route('blog.show', $featured->slug) }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors group/link">
                    Leer artículo
                    <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </article>

    {{-- Rest of posts grid --}}
    @if($posts->count() > 1)
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts->skip(1) as $post)
        <article class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-zinc-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">

            {{-- Image --}}
            <div class="aspect-video overflow-hidden shrink-0">
                @if($post->featured_image)
                <img src="{{ $post->featured_image }}"
                     alt="{{ $post->image_alt ?? $post->title }}"
                     loading="lazy"
                     decoding="async"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                {{-- Placeholder gradient with category icon --}}
                @php
                    $gradients = [
                        'Historia & Cultura' => 'from-rose-50 to-pink-100',
                        'Técnicas'           => 'from-blue-50 to-indigo-100',
                        'Ingredientes'       => 'from-green-50 to-emerald-100',
                        'Nutrición'          => 'from-teal-50 to-cyan-100',
                        'Equipos de Cocina'  => 'from-zinc-100 to-slate-200',
                        'Consejos'           => 'from-violet-50 to-purple-100',
                        'default'            => 'from-amber-50 to-orange-100',
                    ];
                    $iconColors = [
                        'Historia & Cultura' => 'text-rose-300',
                        'Técnicas'           => 'text-blue-300',
                        'Ingredientes'       => 'text-green-300',
                        'Nutrición'          => 'text-teal-300',
                        'Equipos de Cocina'  => 'text-zinc-400',
                        'Consejos'           => 'text-violet-300',
                        'default'            => 'text-amber-300',
                    ];
                    $cat = $post->category ?? 'default';
                    $grad = $gradients[$cat] ?? $gradients['default'];
                    $iconColor = $iconColors[$cat] ?? $iconColors['default'];
                @endphp
                <div class="w-full h-full bg-gradient-to-br {{ $grad }} flex flex-col items-center justify-center gap-2 min-h-[160px]">
                    <svg class="w-12 h-12 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    @if($post->category)
                    <span class="{{ str_replace('text-', 'text-', $iconColor) }} text-xs font-semibold opacity-80">{{ $post->category }}</span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="p-5 flex flex-col flex-1">
                @if($post->category)
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">{{ $post->category }}</span>
                @endif

                <h2 class="text-base font-bold text-zinc-900 group-hover:text-amber-600 transition-colors line-clamp-2 leading-snug mb-2">
                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                </h2>

                <p class="text-sm text-zinc-500 line-clamp-3 leading-relaxed flex-1">
                    {{ $post->short_excerpt }}
                </p>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-zinc-100">
                    @if($post->published_at)
                    <time class="text-xs text-zinc-400">{{ $post->published_at->translatedFormat('d M, Y') }}</time>
                    @endif
                    <a href="{{ route('blog.show', $post->slug) }}"
                       class="text-xs font-semibold text-amber-600 hover:text-amber-700 transition-colors flex items-center gap-1 group/link">
                        Leer más
                        <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @endif

    {{-- Pagination --}}
    @if($posts->hasPages())
    <div class="mt-12 flex justify-center">{{ $posts->links() }}</div>
    @endif

    @else
    <div class="text-center py-24 text-zinc-400">
        <svg class="w-20 h-20 mx-auto mb-5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
        <p class="text-xl font-semibold text-zinc-500">Próximamente nuevos artículos</p>
        <p class="text-sm mt-2">Mientras tanto, explora nuestras recetas.</p>
        <a href="{{ route('recipes.index') }}"
           class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-xl text-sm transition-colors">
            Ver recetas →
        </a>
    </div>
    @endif

</div>
@endsection
