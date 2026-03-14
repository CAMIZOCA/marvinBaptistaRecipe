@extends('layouts.app')

@section('seo_head')
<title>Recetas | Marvin Baptista</title>
<meta name="description" content="Todas las recetas de cocina latinoamericana y mediterránea. Filtra por dificultad, país de origen y más.">
<link rel="canonical" href="{{ route('recipes.index') }}">
@if(isset($recipes))
@if(!$recipes->onFirstPage())
<link rel="prev" href="{{ $recipes->previousPageUrl() }}">
@endif
@if($recipes->hasMorePages())
<link rel="next" href="{{ $recipes->nextPageUrl() }}">
@endif
@endif
<meta property="og:title" content="Todas las Recetas | Marvin Baptista">
<meta property="og:url" content="{{ route('recipes.index') }}">
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-medium" aria-current="page">Recetas</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ===== SIDEBAR FILTERS ===== --}}
        <aside class="lg:w-64 shrink-0 space-y-6" aria-label="Filtros">
            <form method="GET" action="{{ route('recipes.index') }}" id="filter-form">

                {{-- Search --}}
                <div class="bg-white rounded-2xl border border-zinc-100 p-4 shadow-sm space-y-3">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Buscar</h2>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Buscar receta..."
                               class="w-full pl-10 pr-4 py-2.5 border border-zinc-200 text-zinc-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                </div>

                {{-- Difficulty --}}
                <div class="bg-white rounded-2xl border border-zinc-100 p-4 shadow-sm space-y-3">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Dificultad</h2>
                    @foreach(['facil' => 'Fácil', 'media' => 'Media', 'dificil' => 'Difícil'] as $val => $label)
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="difficulty[]" value="{{ $val }}"
                               {{ in_array($val, request()->input('difficulty', [])) ? 'checked' : '' }}
                               class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                        <span class="text-sm text-zinc-600 group-hover:text-zinc-900">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Country --}}
                @if(isset($countries) && count($countries) > 0)
                <div class="bg-white rounded-2xl border border-zinc-100 p-4 shadow-sm space-y-3">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">País de Origen</h2>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                        @foreach($countries as $country)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="country[]" value="{{ $country }}"
                                   {{ in_array($country, request()->input('country', [])) ? 'checked' : '' }}
                                   class="rounded border-zinc-300 text-amber-500 focus:ring-amber-400">
                            <span class="text-sm text-zinc-600 group-hover:text-zinc-900">{{ $country }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-sm font-semibold transition-colors">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'difficulty', 'country', 'sort']))
                    <a href="{{ route('recipes.index') }}"
                       title="Limpiar filtros"
                       class="py-2.5 px-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-600 rounded-xl text-sm transition-colors">
                        ✕
                    </a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900" style="font-family: 'Playfair Display', serif;">
                        {{ request()->hasAny(['search', 'difficulty', 'country']) ? 'Resultados de búsqueda' : 'Todas las Recetas' }}
                    </h1>
                    @if(isset($recipes))
                    <p class="text-sm text-zinc-500 mt-0.5">{{ $recipes->total() }} recetas encontradas</p>
                    @endif
                </div>
                <select name="sort" form="filter-form" onchange="this.form.submit()"
                        class="text-sm border border-zinc-200 rounded-xl px-3 py-2 text-zinc-600 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <option value="latest" {{ request('sort','latest') === 'latest' ? 'selected' : '' }}>Más recientes</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Más vistas</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Mejor valoradas</option>
                </select>
            </div>

            @if(isset($recipes) && $recipes->count() > 0)
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($recipes as $recipe)
                <x-public.recipe-card :recipe="$recipe"/>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($recipes->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $recipes->appends(request()->query())->links() }}
            </div>
            @endif

            @else
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <svg class="w-16 h-16 text-zinc-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-lg font-semibold text-zinc-700 mb-2">Sin resultados</h2>
                <p class="text-zinc-500 text-sm max-w-sm">No encontramos recetas con los filtros seleccionados. Intenta con otros términos.</p>
                <a href="{{ route('recipes.index') }}"
                   class="mt-5 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-sm font-semibold transition-colors">
                    Ver todas las recetas
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
