@extends('admin.layouts.app')
@section('page-title', isset($post) && $post->exists ? 'Editar Artículo' : 'Nuevo Artículo')

@section('content')
@php $isEdit = isset($post) && $post->exists; @endphp

<form method="POST"
      action="{{ $isEdit ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
      class="p-6 space-y-6">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.blog.index') }}"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-zinc-100">
            {{ $isEdit ? 'Editar: '.$post->title : 'Nuevo Artículo' }}
        </h1>
    </div>

    @if($errors->any())
    <div class="bg-red-900/30 border border-red-700 text-red-300 rounded-xl px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-emerald-900/30 border border-emerald-700 text-emerald-300 rounded-xl px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Título --}}
            <div>
                <label class="block text-sm font-semibold text-zinc-300 mb-1.5">Título *</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                       class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-4 py-2.5 text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            {{-- Extracto --}}
            <div>
                <label class="block text-sm font-semibold text-zinc-300 mb-1.5">
                    Extracto
                    <span class="text-zinc-500 font-normal">(resumen que aparece en listados y recetas)</span>
                </label>
                <textarea name="excerpt" rows="3"
                          class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-4 py-2.5 text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            {{-- Contenido --}}
            <div>
                <label class="block text-sm font-semibold text-zinc-300 mb-1.5">Contenido</label>
                <textarea name="content" rows="16"
                          class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-4 py-2.5 text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono text-sm resize-y">{{ old('content', $post->content) }}</textarea>
                <p class="text-xs text-zinc-500 mt-1">Puedes usar HTML para dar formato al contenido.</p>
            </div>

            {{-- SEO --}}
            <div class="bg-zinc-700/30 border border-zinc-600 rounded-2xl p-5 space-y-4">
                <h3 class="font-semibold text-zinc-200 text-sm">SEO</h3>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">SEO Title</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $post->seo_title) }}"
                           class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-3 py-2 text-zinc-100 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">Meta Description</label>
                    <textarea name="seo_description" rows="2" maxlength="160"
                              class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-3 py-2 text-zinc-100 text-sm focus:outline-none focus:border-indigo-500 resize-none">{{ old('seo_description', $post->seo_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Publicar --}}
            <div class="bg-zinc-800 border border-zinc-700 rounded-2xl p-5 space-y-4">
                <h3 class="font-semibold text-zinc-200">Publicación</h3>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1"
                           {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-zinc-500 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-zinc-800">
                    <span class="text-sm font-medium text-zinc-300">Publicado</span>
                </label>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">Fecha de publicación</label>
                    <input type="datetime-local" name="published_at"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                           class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-3 py-2 text-zinc-100 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <button type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-sm transition-colors">
                    {{ $isEdit ? 'Guardar cambios' : 'Crear artículo' }}
                </button>
            </div>

            {{-- Imagen y metadatos --}}
            <div class="bg-zinc-800 border border-zinc-700 rounded-2xl p-5 space-y-4">
                <h3 class="font-semibold text-zinc-200">Imagen y metadatos</h3>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">URL de imagen destacada</label>
                    <input type="text" name="featured_image" value="{{ old('featured_image', $post->featured_image) }}"
                           placeholder="https://..."
                           class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-3 py-2 text-zinc-100 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                @if($isEdit && $post->featured_image)
                <div class="rounded-xl overflow-hidden bg-zinc-700 aspect-video">
                    <img src="{{ $post->featured_image }}" alt="" class="w-full h-full object-cover">
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">Alt de imagen</label>
                    <input type="text" name="image_alt" value="{{ old('image_alt', $post->image_alt) }}"
                           class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-3 py-2 text-zinc-100 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">Categoría del artículo</label>
                    <input type="text" name="category" value="{{ old('category', $post->category) }}"
                           placeholder="Ej: Técnicas, Ingredientes, Historia..."
                           class="w-full bg-zinc-700 border border-zinc-600 rounded-xl px-3 py-2 text-zinc-100 text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            {{-- Danger zone --}}
            @if($isEdit)
            <div class="bg-red-950/30 border border-red-900/50 rounded-2xl p-4">
                <h4 class="text-sm font-semibold text-red-400 mb-3">Zona de peligro</h4>
                <form method="POST" action="{{ route('admin.blog.destroy', $post) }}"
                      onsubmit="return confirm('¿Eliminar este artículo permanentemente?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-900/40 hover:bg-red-900 border border-red-800 text-red-300 rounded-xl text-sm font-medium transition-colors">
                        Eliminar artículo
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</form>
@endsection
