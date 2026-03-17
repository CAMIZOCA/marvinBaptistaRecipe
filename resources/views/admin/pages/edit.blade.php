@extends('admin.layouts.app')

@section('page-title', isset($page) ? 'Editar Página' : 'Nueva Página')

@section('content')
@php
    $isEdit = isset($page) && $page->exists;
    $formAction = $isEdit ? '/admin/paginas/'.$page->id : '/admin/paginas';
@endphp

<div class="p-6 space-y-6 max-w-4xl">

    <div class="flex items-center gap-3">
        <a href="/admin/paginas"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-zinc-100">
            {{ $isEdit ? 'Editar: '.$page->title : 'Nueva Página' }}
        </h1>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-900/50 border border-red-700 rounded-xl text-red-300 text-sm space-y-1">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Título *</label>
                        <input type="text" name="title" value="{{ old('title', $page->title ?? '') }}"
                               placeholder="Sobre Mí, Política de Privacidad..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 text-xl font-semibold rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Contenido</label>
                        <input id="page_content" type="hidden" name="content" value="{{ old('content', $page->content ?? '') }}">
                        <trix-editor input="page_content" class="trix-content bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl min-h-64 focus:outline-none focus:ring-2 focus:ring-amber-500"></trix-editor>
                    </div>
                </div>

                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">SEO</h2>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">SEO Title</label>
                        <input type="text" name="seo_title" value="{{ old('seo_title', $page->seo_title ?? '') }}"
                               maxlength="60"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">SEO Description</label>
                        <textarea name="seo_description" rows="2" maxlength="160"
                                  class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('seo_description', $page->seo_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                {{-- SEO / Content Analyzer --}}
                <div id="seo-analyzer-panel" data-type="page" class="rounded-xl border border-zinc-600 overflow-hidden">
                    <div id="seo-panel-header"
                         class="px-4 py-3 flex items-center gap-2.5 bg-zinc-700/50 cursor-pointer select-none"
                         onclick="(function(){var l=document.getElementById('seo-checks-list'),c=document.getElementById('seo-chevron');if(!l||!c)return;var h=l.classList.toggle('hidden');c.style.transform=h?'rotate(-90deg)':''})()">
                        <div id="seo-traffic-light"
                             class="w-3.5 h-3.5 rounded-full shrink-0 bg-zinc-600 transition-colors duration-300"></div>
                        <span class="text-sm font-semibold text-zinc-200 flex-1">Análisis de contenido</span>
                        <span id="seo-score-badge"
                              class="text-xs font-bold px-2 py-0.5 rounded-full bg-zinc-700 text-zinc-400 tabular-nums">—</span>
                        <svg id="seo-chevron" class="w-4 h-4 text-zinc-500 transition-transform duration-200 shrink-0"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <div id="seo-checks-list" class="p-3 space-y-0.5 bg-zinc-800/60">
                        <p class="text-xs text-zinc-500 py-2 text-center">Analizando…</p>
                    </div>
                </div>

                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Publicación</h2>

                    <div class="flex items-center justify-between">
                        <label class="text-sm text-zinc-300">Publicada</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1"
                                   {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-zinc-600 peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    @if($isEdit)
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1">Slug</label>
                        <p class="text-sm font-mono text-zinc-400 bg-zinc-700 px-3 py-2 rounded-lg">/{{ $page->slug }}</p>
                    </div>
                    @endif
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                        {{ $isEdit ? 'Actualizar Página' : 'Crear Página' }}
                    </button>
                    <a href="/admin/paginas"
                       class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
@endpush
@endsection
