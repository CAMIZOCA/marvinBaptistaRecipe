@extends('layouts.app')

@section('seo_head')
<title>{{ $post->seo_title ?? $post->title }} | Blog | Marvin Baptista</title>
<meta name="description" content="{{ $post->seo_description ?? $post->short_excerpt }}">
<link rel="canonical" href="{{ route('blog.show', $post->slug) }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $post->seo_title ?? $post->title }}">
<meta property="og:description" content="{{ $post->seo_description ?? $post->short_excerpt }}">
<meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
@if($post->featured_image)
<meta property="og:image" content="{{ $post->featured_image }}">
@endif
<meta property="og:site_name" content="Marvin Baptista">
@if($post->published_at)
<meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
@endif
<meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── LAYOUT: article + sidebar ─────────────────────────── --}}
    <div class="grid lg:grid-cols-3 gap-12">

        {{-- ════ MAIN ARTICLE ════ --}}
        <article class="lg:col-span-2">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-6 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('blog.index') }}" class="hover:text-zinc-700 transition-colors">Blog</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-zinc-800 font-medium line-clamp-1" aria-current="page">{{ Str::limit($post->title, 55) }}</span>
            </nav>

            {{-- Category + Title --}}
            @if($post->category)
            <span class="inline-block text-xs font-bold text-amber-600 uppercase tracking-widest mb-3 bg-amber-50 px-3 py-1 rounded-full">
                {{ $post->category }}
            </span>
            @endif

            <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900 mb-4 leading-tight"
                style="font-family: 'Playfair Display', serif;">
                {{ $post->title }}
            </h1>

            {{-- Meta --}}
            <div class="flex items-center gap-4 text-sm text-zinc-400 mb-7">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Marvin Baptista
                </span>
                @if($post->published_at)
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <time datetime="{{ $post->published_at->toIso8601String() }}">
                        {{ $post->published_at->translatedFormat('d \d\e F, Y') }}
                    </time>
                </span>
                @endif
            </div>

            {{-- Featured Image --}}
            <div class="rounded-3xl overflow-hidden mb-8 bg-gradient-to-br from-amber-50 via-orange-50 to-amber-100" style="aspect-ratio:16/9;">
                @if($post->featured_image)
                <img src="{{ $post->featured_image }}"
                     alt="{{ $post->image_alt ?? $post->title }}"
                     loading="eager"
                     decoding="async"
                     class="w-full h-full object-cover"
                     width="800" height="450">
                @else
                <div class="w-full h-full flex flex-col items-center justify-center gap-3 p-8">
                    <svg class="w-16 h-16 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <span class="text-amber-400 font-medium text-sm">{{ $post->category ?? 'Blog de Cocina' }}</span>
                </div>
                @endif
            </div>

            {{-- Excerpt highlight --}}
            @if($post->excerpt)
            <p class="text-lg text-zinc-600 leading-relaxed mb-8 border-l-4 border-amber-400 pl-5 italic">
                {{ $post->excerpt }}
            </p>
            @endif

            {{-- Content --}}
            <div class="prose prose-zinc prose-lg max-w-none leading-relaxed
                        prose-headings:font-bold prose-headings:text-zinc-900
                        prose-a:text-amber-600 prose-a:no-underline hover:prose-a:underline
                        prose-img:rounded-2xl prose-blockquote:border-amber-400">
                {!! $post->content !!}
            </div>

            {{-- Share --}}
            <div class="mt-10 pt-8 border-t border-zinc-100 flex items-center gap-3 flex-wrap">
                <span class="text-sm font-semibold text-zinc-500">Compartir:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" aria-label="Facebook">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('blog.show', $post->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="p-2 bg-zinc-900 hover:bg-zinc-700 text-white rounded-lg transition-colors" aria-label="X / Twitter">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://wa.me/?text={{ urlencode($post->title.' - '.route('blog.show', $post->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="p-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors" aria-label="WhatsApp">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>

        </article>

        {{-- ════ SIDEBAR ════ --}}
        <aside class="hidden lg:block space-y-6 sticky top-24 self-start">

            {{-- Sobre el autor --}}
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 text-center">
                <div class="w-16 h-16 bg-amber-200 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-amber-900 mb-1">Marvin Baptista</h3>
                <p class="text-xs text-amber-700 leading-relaxed">Chef y escritor gastronómico especializado en cocina latinoamericana y mediterránea.</p>
                <a href="{{ route('page.show', 'sobre-mi') }}"
                   class="mt-3 inline-block text-xs font-semibold text-amber-600 hover:text-amber-800 transition-colors">
                    Conoce más →
                </a>
            </div>

            {{-- Artículos recientes --}}
            @if($recentPosts->count() > 0)
            <div class="bg-white border border-zinc-100 rounded-2xl p-5 space-y-4">
                <h3 class="font-bold text-zinc-900 text-sm">Artículos Recientes</h3>
                <div class="space-y-3">
                    @foreach($recentPosts as $recent)
                    <a href="{{ route('blog.show', $recent->slug) }}"
                       class="flex gap-3 group">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gradient-to-br from-amber-50 to-orange-100 shrink-0 flex items-center justify-center">
                            @if($recent->featured_image)
                            <img src="{{ $recent->featured_image }}" alt="{{ $recent->title }}"
                                 loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @if($recent->category)
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">{{ $recent->category }}</span>
                            @endif
                            <p class="text-xs font-semibold text-zinc-800 group-hover:text-amber-600 transition-colors line-clamp-2 leading-snug mt-0.5">
                                {{ $recent->title }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
                <a href="{{ route('blog.index') }}"
                   class="block text-center text-xs font-semibold text-amber-600 hover:text-amber-700 transition-colors pt-2 border-t border-zinc-100">
                    Ver todos los artículos →
                </a>
            </div>
            @endif

        </aside>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         RECETAS RELACIONADAS — 6 cards
    ═════════════════════════════════════════════════════════════ --}}
    @if(isset($relatedRecipes) && $relatedRecipes->count() > 0)
    <section class="mt-16 pt-12 border-t border-zinc-100" aria-label="Recetas relacionadas">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-zinc-900" style="font-family: 'Playfair Display', serif;">
                    Recetas que te pueden gustar
                </h2>
                <p class="text-sm text-zinc-400 mt-1">Pon en práctica lo que acabas de aprender</p>
            </div>
            <a href="{{ route('recipes.index') }}"
               class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors group">
                Ver todas
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($relatedRecipes as $recipe)
            <a href="{{ route('recipe.show', $recipe->slug) }}"
               class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-zinc-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="aspect-square overflow-hidden bg-gradient-to-br from-amber-50 to-orange-100 shrink-0">
                    @if($recipe->featured_image)
                    <img src="{{ $recipe->featured_image }}"
                         alt="{{ $recipe->image_alt ?? $recipe->title }}"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="p-2.5">
                    <h3 class="text-xs font-semibold text-zinc-900 group-hover:text-amber-600 transition-colors line-clamp-2 leading-snug">
                        {{ $recipe->title }}
                    </h3>
                    @php $rTime = ($recipe->prep_time_minutes ?? 0) + ($recipe->cook_time_minutes ?? 0); @endphp
                    @if($rTime > 0)
                    <span class="text-[10px] text-zinc-400 mt-1 flex items-center gap-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $rTime }} min
                    </span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
