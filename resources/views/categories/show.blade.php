@extends('layouts.app')

@section('seo_head')
<title>{{ $category->seo_title ?? $category->name }} | Marvin Baptista</title>
<meta name="description" content="{{ $category->seo_description ?? 'Recetas de '.$category->name.' en Marvin Baptista.' }}">
<link rel="canonical" href="{{ route('category.show', $category->slug) }}">
<meta property="og:title" content="{{ $category->name }} | Marvin Baptista">
<meta property="og:url" content="{{ route('category.show', $category->slug) }}">
@if(isset($category) && $category->image)
<meta property="og:image" content="{{ Str::startsWith($category->image, 'http') ? $category->image : asset($category->image) }}">
<meta property="og:image:alt" content="{{ $category->name }}">
@endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-6 flex-wrap" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('recipes.index') }}" class="hover:text-zinc-700 transition-colors">Recetas</a>
        @if($category->parent)
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('category.show', $category->parent->slug) }}" class="hover:text-zinc-700 transition-colors">{{ $category->parent->name }}</a>
        @endif
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-medium" aria-current="page">{{ $category->name }}</span>
    </nav>

    {{-- Category Header --}}
    <header class="mb-10">
        <div class="flex items-start gap-5">
            @if($category->image)
            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-zinc-100 shrink-0">
                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
            </div>
            @endif
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900" style="font-family: 'Playfair Display', serif;">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                <p class="mt-2 text-zinc-500 text-lg leading-relaxed max-w-2xl">{{ $category->description }}</p>
                @endif
                <p class="mt-2 text-sm text-zinc-400">{{ $recipes->total() ?? 0 }} recetas</p>
            </div>
        </div>
    </header>

    {{-- Subcategories (if any) --}}
    @if($category->children?->count() > 0)
    <div class="mb-8">
        <div class="flex flex-wrap gap-3">
            @foreach($category->children as $child)
            <a href="{{ route('category.show', $child->slug) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 hover:border-amber-300 hover:bg-amber-50 text-zinc-700 rounded-xl text-sm font-medium transition-all">
                {{ $child->name }}
                <span class="text-xs text-zinc-400">({{ $child->recipes_count ?? 0 }})</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recipe Grid --}}
    @if(isset($recipes) && $recipes->count() > 0)
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($recipes as $recipe)
        <x-public.recipe-card :recipe="$recipe"/>
        @endforeach
    </div>

    @if($recipes->hasPages())
    <div class="mt-10 flex justify-center">
        {{ $recipes->links() }}
    </div>
    @endif
    @else
    <div class="flex flex-col items-center justify-center py-20">
        <h2 class="text-lg font-semibold text-zinc-600 mb-2">Sin recetas en esta categoría</h2>
        <a href="{{ route('recipes.index') }}" class="text-amber-600 hover:text-amber-700 font-medium">Ver todas las recetas →</a>
    </div>
    @endif

</div>
@endsection
