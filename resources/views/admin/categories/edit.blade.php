@extends('admin.layouts.app')

@section('page-title', isset($category) ? 'Editar Categoría' : 'Nueva Categoría')

@section('content')
@php
    $isEdit = isset($category) && $category->exists;
    $formAction = $isEdit
        ? route('admin.categorias.index').'/'.$category->id
        : route('admin.categorias.index');
@endphp

<div class="p-6 space-y-6 max-w-2xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categorias.index') }}"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-zinc-100">
            {{ $isEdit ? 'Editar: '.$category->name : 'Nueva Categoría' }}
        </h1>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-900/50 border border-red-700 rounded-xl text-red-300 text-sm space-y-1">
        @foreach($errors->all() as $error)
        <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Información</h2>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
                       placeholder="Postres Latinoamericanos..."
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Categoría Padre</label>
                <select name="parent_id"
                        class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">— Sin padre (categoría principal) —</option>
                    @foreach($parentCategories ?? [] as $parent)
                        @if(!$isEdit || $parent->id !== $category->id)
                        <option value="{{ $parent->id }}"
                                {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Descripción</label>
                <textarea name="description" rows="3"
                          placeholder="Descripción de la categoría..."
                          class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('description', $category->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">URL de Imagen</label>
                @if(isset($category->image) && $category->image)
                <div class="mb-3">
                    <img src="{{ $category->image }}" alt="{{ $category->name }}"
                         class="w-24 h-24 object-cover rounded-xl">
                </div>
                @endif
                <input type="url" name="image" value="{{ old('image', $category->image ?? '') }}"
                       placeholder="https://..."
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Orden</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0"
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">SEO</h2>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">SEO Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $category->seo_title ?? '') }}"
                       maxlength="60"
                       placeholder="Título para motores de búsqueda..."
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">SEO Description</label>
                <textarea name="seo_description" rows="2" maxlength="160"
                          placeholder="Meta descripción (máx. 160 caracteres)..."
                          class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('seo_description', $category->seo_description ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                {{ $isEdit ? 'Actualizar' : 'Crear Categoría' }}
            </button>
            <a href="{{ route('admin.categorias.index') }}"
               class="px-6 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
