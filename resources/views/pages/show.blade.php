@extends('layouts.app')

@section('seo_head')
<title>{{ $page->seo_title ?? $page->title }} | Marvin Baptista</title>
<meta name="description" content="{{ $page->seo_description ?? Str::limit(strip_tags($page->content ?? ''), 160) }}">
<link rel="canonical" href="{{ route('page.show', $page->slug) }}">
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-medium" aria-current="page">{{ $page->title }}</span>
    </nav>

    {{-- Page Title --}}
    <header class="mb-8 pb-8 border-b border-zinc-100">
        <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900 leading-tight"
            style="font-family: 'Playfair Display', serif;">
            {{ $page->title }}
        </h1>
        @if($page->updated_at)
        <p class="text-sm text-zinc-400 mt-3">
            Última actualización: {{ $page->updated_at->format('d \\d\\e F \\d\\e Y') }}
        </p>
        @endif
    </header>

    {{-- Page Content --}}
    <div class="prose prose-zinc prose-lg max-w-none
                prose-headings:font-bold prose-headings:text-zinc-900
                prose-a:text-amber-600 prose-a:no-underline hover:prose-a:underline
                prose-img:rounded-2xl prose-img:shadow-md">
        {!! $page->content !!}
    </div>

</div>
@endsection
