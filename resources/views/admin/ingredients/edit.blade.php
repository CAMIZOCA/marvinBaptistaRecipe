@extends('admin.layouts.app')

@section('page-title', isset($ingredient) ? 'Editar Ingrediente' : 'Nuevo Ingrediente')

@section('content')
@php
    $isEdit = isset($ingredient) && $ingredient->exists;
    $formAction = $isEdit ? '/admin/ingredientes/'.$ingredient->id : '/admin/ingredientes';
@endphp

<div class="p-6 space-y-6 max-w-2xl">

    <div class="flex items-center gap-3">
        <a href="/admin/ingredientes"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-zinc-100">
            {{ $isEdit ? 'Editar: '.$ingredient->name : 'Nuevo Ingrediente' }}
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

        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Información</h2>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $ingredient->name ?? '') }}"
                       placeholder="Aguacate, Harina de trigo..."
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Descripción</label>
                <input id="ingredient_description" type="hidden" name="description"
                       value="{{ old('description', $ingredient->description ?? '') }}">
                <trix-editor input="ingredient_description"
                             class="trix-content bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl min-h-28 focus:outline-none focus:ring-2 focus:ring-amber-500"></trix-editor>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">URL de Imagen</label>
                @if(isset($ingredient->image) && $ingredient->image)
                <div class="mb-3">
                    <img src="{{ $ingredient->image }}" alt="{{ $ingredient->name }}"
                         class="w-20 h-20 object-cover rounded-xl">
                </div>
                @endif
                <input type="url" name="image" value="{{ old('image', $ingredient->image ?? '') }}"
                       placeholder="https://..."
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">SEO</h2>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">SEO Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $ingredient->seo_title ?? '') }}"
                       maxlength="60"
                       placeholder="Título para Google..."
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">SEO Description</label>
                <textarea name="seo_description" rows="2" maxlength="160"
                          placeholder="Meta descripción..."
                          class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('seo_description', $ingredient->seo_description ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                {{ $isEdit ? 'Actualizar' : 'Crear Ingrediente' }}
            </button>
            <a href="/admin/ingredientes"
               class="px-6 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
@endpush
@endsection
