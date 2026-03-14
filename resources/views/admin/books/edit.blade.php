@extends('admin.layouts.app')

@section('page-title', isset($book) ? 'Editar Libro' : 'Nuevo Libro')

@section('content')
@php
    $isEdit = isset($book) && $book->exists;
    $formAction = $isEdit
        ? route('admin.libros.index').'/'.$book->id
        : route('admin.libros.index');
@endphp

<div class="p-6 space-y-6 max-w-4xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.libros.index') }}"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-zinc-100">
            {{ $isEdit ? 'Editar: '.$book->title : 'Nuevo Libro' }}
        </h1>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-900/50 border border-red-700 rounded-xl text-red-300 text-sm space-y-1">
        @foreach($errors->all() as $error)
        <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- Affiliate note --}}
    <div class="flex items-start gap-3 p-4 bg-blue-900/20 border border-blue-700/40 rounded-xl text-blue-300 text-sm">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>El tag de afiliado de Amazon se configura en <a href="{{ route('admin.settings.index') }}" class="underline hover:text-blue-200">Ajustes → Amazon</a>. No es necesario incluirlo aquí en la URL.</p>
    </div>

    <form method="POST" action="{{ $formAction }}" class="space-y-6">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Left column --}}
            <div class="space-y-5">
                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Información Principal</h2>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">ASIN de Amazon *</label>
                        <input type="text" name="asin" value="{{ old('asin', $book->asin ?? '') }}"
                               placeholder="B08XYZ1234"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono"
                               required>
                        <p class="text-xs text-zinc-500 mt-1">El código ASIN se encuentra en la URL de Amazon.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Título del Libro *</label>
                        <input type="text" name="title" value="{{ old('title', $book->title ?? '') }}"
                               placeholder="El Gran Libro de la Cocina..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Autor</label>
                        <input type="text" name="author" value="{{ old('author', $book->author ?? '') }}"
                               placeholder="Nombre del autor..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Tipo de Cocina</label>
                        <input type="text" name="cuisine_type" value="{{ old('cuisine_type', $book->cuisine_type ?? '') }}"
                               placeholder="Mexicana, Mediterránea, Italiana..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Palabras clave de coincidencia</label>
                        <input type="text" name="keywords_match" value="{{ old('keywords_match', $book->keywords_match ?? '') }}"
                               placeholder="pollo, arroz, fácil..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <p class="text-xs text-zinc-500 mt-1">Keywords separadas por coma para asociación automática a recetas.</p>
                    </div>
                </div>

                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Configuración</h2>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-zinc-300">Libro activo</label>
                            <p class="text-xs text-zinc-500 mt-0.5">Visible en el sitio y disponible para recetas</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $book->is_active ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-zinc-600 peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Orden de visualización</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $book->sort_order ?? 0) }}" min="0"
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            {{-- Right column --}}
            <div class="space-y-5">
                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Imágenes y URLs</h2>

                    @if(isset($book->cover_image_url) && $book->cover_image_url)
                    <div class="flex justify-center">
                        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}"
                             class="max-h-40 object-contain rounded-lg shadow-lg">
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">URL de Portada</label>
                        <input type="url" name="cover_image_url" value="{{ old('cover_image_url', $book->cover_image_url ?? '') }}"
                               placeholder="https://..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">URLs de Amazon por país</label>

                        @foreach([
                            ['name' => 'amazon_url_us', 'label' => '🇺🇸 Estados Unidos (.com)'],
                            ['name' => 'amazon_url_mx', 'label' => '🇲🇽 México (.com.mx)'],
                            ['name' => 'amazon_url_es', 'label' => '🇪🇸 España (.es)'],
                            ['name' => 'amazon_url_ar', 'label' => '🇦🇷 Argentina (.com.ar)'],
                        ] as $amazonField)
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">{{ $amazonField['label'] }}</label>
                            <input type="url" name="{{ $amazonField['name'] }}"
                                   value="{{ old($amazonField['name'], $book->{$amazonField['name']} ?? '') }}"
                                   placeholder="https://www.amazon.com/..."
                                   class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Descripción</h2>
                    <textarea name="description" rows="6"
                              placeholder="Descripción del libro..."
                              class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('description', $book->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $isEdit ? 'Actualizar Libro' : 'Guardar Libro' }}
            </button>
            <a href="{{ route('admin.libros.index') }}"
               class="px-6 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
