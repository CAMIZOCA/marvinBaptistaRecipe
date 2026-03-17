@extends('admin.layouts.app')

@section('page-title', isset($book) ? 'Editar Libro' : 'Nuevo Libro')

@section('content')
@php
    $isEdit = isset($book) && $book->exists;
    $formAction = $isEdit
        ? route('admin.libros.update', $book)
        : route('admin.libros.store');
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

    <form id="book-form" method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-6">
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

                    {{-- Cover preview --}}
                    <div id="cover-preview-wrap" class="{{ (old('cover_image_url', $book->cover_image_url ?? '')) ? '' : 'hidden' }} flex justify-center">
                        <img id="cover-preview"
                             src="{{ old('cover_image_url', $book->cover_image_url ?? '') }}"
                             alt="Portada"
                             class="max-h-40 object-contain rounded-lg shadow-lg">
                    </div>

                    {{-- File upload --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Subir Portada</label>
                        <label class="flex items-center gap-3 px-4 py-3 bg-zinc-700 border border-dashed border-zinc-500 rounded-xl cursor-pointer hover:border-amber-500 transition-colors group">
                            <svg class="w-5 h-5 text-zinc-400 group-hover:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span id="cover-file-label" class="text-sm text-zinc-400 group-hover:text-zinc-200 truncate">Seleccionar imagen (JPG, PNG, WebP — máx. 3 MB)</span>
                            <input type="file" name="cover_image" accept="image/*" class="sr-only" id="cover-file-input">
                        </label>
                        @error('cover_image')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- URL manual --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">O ingresar URL de Portada</label>
                        <input type="text" name="cover_image_url" id="cover-url-input"
                               value="{{ old('cover_image_url', $book->cover_image_url ?? '') }}"
                               placeholder="https://..."
                               class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <p class="text-xs text-zinc-500 mt-1">Si subes un archivo, éste tiene prioridad sobre la URL.</p>
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

                <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-3">
                    <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Descripción</h2>

                    {{-- Quill editor container --}}
                    <div id="quill-editor"
                         class="bg-zinc-700 rounded-xl text-zinc-200 min-h-[160px]"
                         style="font-size:0.875rem; line-height:1.6">
                    </div>

                    {{-- Hidden input that carries the HTML to the server --}}
                    <input type="hidden" name="description" id="description-input">

                    <p class="text-xs text-zinc-500">Soporta negritas, cursiva, listas y enlaces.</p>
                </div>
            </div>
        </div>

        {{-- Quill WYSIWYG --}}
        <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
        <style>
            #quill-editor .ql-toolbar { background:#3f3f46; border-color:#52525b; border-radius:0.75rem 0.75rem 0 0; }
            #quill-editor .ql-container { border-color:#52525b; border-radius:0 0 0.75rem 0.75rem; min-height:140px; }
            #quill-editor .ql-editor { color:#e4e4e7; min-height:140px; }
            #quill-editor .ql-editor.ql-blank::before { color:#71717a; font-style:normal; }
            #quill-editor .ql-stroke { stroke:#a1a1aa !important; }
            #quill-editor .ql-fill  { fill:#a1a1aa !important; }
            #quill-editor .ql-picker-label { color:#a1a1aa; }
            #quill-editor button:hover .ql-stroke,
            #quill-editor button.ql-active .ql-stroke { stroke:#f59e0b !important; }
            #quill-editor button:hover .ql-fill,
            #quill-editor button.ql-active .ql-fill { fill:#f59e0b !important; }
        </style>
        <script>
            (function () {
                const quill = new Quill('#quill-editor', {
                    theme: 'snow',
                    placeholder: 'Descripción del libro...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });

                // Pre-load existing content
                const existing = @json(old('description', $book->description ?? ''));
                if (existing) quill.root.innerHTML = existing;

                // Before submit: copy editor HTML to hidden input
                document.getElementById('book-form').addEventListener('submit', function () {
                    document.getElementById('description-input').value = quill.getSemanticHTML();
                });
            })();
        </script>

        {{-- URL preview update --}}
        <script>
            const fileInput = document.getElementById('cover-file-input');
            const urlInput  = document.getElementById('cover-url-input');
            const preview   = document.getElementById('cover-preview');
            const previewWrap = document.getElementById('cover-preview-wrap');
            const fileLabel = document.getElementById('cover-file-label');

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                fileLabel.textContent = file.name;
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    previewWrap.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });

            urlInput.addEventListener('input', function () {
                if (!fileInput.files.length && this.value) {
                    preview.src = this.value;
                    previewWrap.classList.remove('hidden');
                }
            });
        </script>

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
