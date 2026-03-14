@extends('layouts.app')

@section('seo_head')
<title>{{ $category->seo_title ?? $category->name }} | Marvin Baptista</title>
<meta name="description" content="{{ $category->seo_description ?? 'Explora las subcategorías de '.$category->name.' en Marvin Baptista.' }}">
<link rel="canonical" href="{{ route('category.show', $category->slug) }}">
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('recipes.index') }}" class="hover:text-zinc-700 transition-colors">Recetas</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-medium" aria-current="page">{{ $category->name }}</span>
    </nav>

    {{-- Hero Header --}}
    <header class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl p-10 mb-12 overflow-hidden">
        @if($category->image)
        <div class="absolute inset-0 opacity-10">
            <img src="{{ $category->image }}" alt="" class="w-full h-full object-cover">
        </div>
        @endif
        <div class="relative z-10">
            <h1 class="text-4xl sm:text-5xl font-bold text-zinc-900 mb-3" style="font-family: 'Playfair Display', serif;">
                {{ $category->name }}
            </h1>
            @if($category->description)
            <p class="text-lg text-zinc-600 max-w-2xl leading-relaxed">{{ $category->description }}</p>
            @endif
        </div>
    </header>

    {{-- Subcategory Cards --}}
    @if($category->children?->count() > 0)
    <section aria-label="Subcategorías de {{ $category->name }}">
        <h2 class="text-xl font-bold text-zinc-800 mb-6">Explora por subcategoría</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($category->children as $child)
            <a href="{{ route('category.show', $child->slug) }}"
               class="group relative bg-white border border-zinc-100 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="aspect-video bg-zinc-100 overflow-hidden">
                    @if($child->image)
                    <img src="{{ $child->image }}" alt="{{ $child->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-50 to-orange-100">
                        <svg class="w-10 h-10 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-zinc-900 group-hover:text-amber-600 transition-colors">{{ $child->name }}</h3>
                    <p class="text-sm text-zinc-400 mt-0.5">{{ $child->recipes_count ?? 0 }} recetas</p>
                    @if($child->description)
                    <p class="text-sm text-zinc-500 mt-2 line-clamp-2">{{ $child->description }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Also show some recipes from this category --}}
    @if(isset($recipes) && $recipes->count() > 0)
    <section class="mt-14" aria-label="Recetas destacadas en {{ $category->name }}">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-zinc-900" style="font-family: 'Playfair Display', serif;">
                Recetas Destacadas
            </h2>
            <a href="{{ route('recipes.index') }}?category={{ $category->slug }}"
               class="text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors">
                Ver todas →
            </a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($recipes->take(6) as $recipe)
            <x-public.recipe-card :recipe="$recipe"/>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
