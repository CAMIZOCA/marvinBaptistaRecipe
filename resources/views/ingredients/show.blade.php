@extends('layouts.app')

@section('seo_head')
<title>{{ $ingredient->seo_title ?? 'Recetas con '.$ingredient->name }} | Marvin Baptista</title>
<meta name="description" content="{{ $ingredient->seo_description ?? 'Descubre todas las recetas con '.$ingredient->name.' en Marvin Baptista.' }}">
<link rel="canonical" href="/ingredientes/{{ $ingredient->slug }}">
<meta property="og:title" content="Recetas con {{ $ingredient->name }} | Marvin Baptista">
@if($ingredient->image)
<meta property="og:image" content="{{ $ingredient->image }}">
@endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('recipes.index') }}" class="hover:text-zinc-700 transition-colors">Recetas</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-medium" aria-current="page">{{ $ingredient->name }}</span>
    </nav>

    {{-- Header --}}
    <header class="flex items-start gap-6 mb-10">
        @if($ingredient->image)
        <div class="w-24 h-24 rounded-2xl overflow-hidden bg-zinc-100 shrink-0">
            <img src="{{ $ingredient->image }}" alt="{{ $ingredient->name }}"
                 class="w-full h-full object-cover">
        </div>
        @endif
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900" style="font-family: 'Playfair Display', serif;">
                Recetas con {{ $ingredient->name }}
            </h1>
            @if($ingredient->description)
            <div class="mt-3 prose prose-zinc max-w-2xl">
                {!! $ingredient->description !!}
            </div>
            @endif
            @if(isset($recipes))
            <p class="mt-2 text-sm text-zinc-400">{{ $recipes->total() }} recetas</p>
            @endif
        </div>
    </header>

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
    <div class="text-center py-16">
        <h2 class="text-lg font-semibold text-zinc-600 mb-2">Sin recetas aún</h2>
        <p class="text-zinc-400 text-sm">No encontramos recetas con este ingrediente.</p>
        <a href="{{ route('recipes.index') }}" class="mt-4 inline-block text-amber-600 hover:text-amber-700 font-medium">
            Ver todas las recetas →
        </a>
    </div>
    @endif

</div>
@endsection
