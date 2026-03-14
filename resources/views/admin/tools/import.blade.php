@extends('admin.layouts.app')

@section('page-title', 'Importar Recetas')

@section('content')
<div class="p-6 space-y-6 max-w-3xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.recipes.index') }}"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Importar Recetas</h1>
            <p class="text-zinc-400 text-sm mt-0.5">Sube un archivo CSV para importar múltiples recetas de una vez</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-900/50 border border-red-700 rounded-xl text-red-300 text-sm space-y-1">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
    @endif

    {{-- Instructions --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-zinc-200 mb-2">Instrucciones</h2>
                <ul class="text-sm text-zinc-400 space-y-1">
                    <li>• Descarga la plantilla CSV y rellena los campos requeridos</li>
                    <li>• Columnas requeridas: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-amber-300">title, description</code></li>
                    <li>• Columnas opcionales: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">subtitle, prep_time, cook_time, servings, difficulty, origin_country, seo_title, seo_description</code></li>
                    <li>• El archivo debe estar en codificación UTF-8</li>
                    <li>• Máximo 500 recetas por importación</li>
                </ul>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="/admin/recetas/importar/plantilla"
               class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar Plantilla CSV
            </a>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-5">
        <h2 class="text-sm font-semibold text-zinc-300">Subir Archivo</h2>

        <form method="POST" action="{{ route('admin.recipes.import.index') }}" enctype="multipart/form-data"
              id="import-form" class="space-y-4">
            @csrf

            <div id="file-drop-zone"
                 class="relative flex flex-col items-center justify-center px-6 py-12 border-2 border-dashed border-zinc-600 rounded-xl hover:border-amber-500 transition-colors cursor-pointer group">
                <input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       required>
                <svg class="w-12 h-12 text-zinc-500 group-hover:text-amber-400 mb-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-zinc-300 font-medium" id="file-label">Arrastra tu CSV aquí o haz click para seleccionar</p>
                <p class="text-zinc-500 text-sm mt-1">Solo archivos .csv — máx. 10MB</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="overwrite_existing" value="1"
                               class="rounded border-zinc-500 bg-zinc-700 text-amber-500 focus:ring-amber-500">
                        <span class="text-sm text-zinc-300">Sobreescribir recetas existentes (por slug)</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="auto_publish" value="0">
                        <input type="checkbox" name="auto_publish" value="1"
                               class="rounded border-zinc-500 bg-zinc-700 text-amber-500 focus:ring-amber-500">
                        <span class="text-sm text-zinc-300">Publicar automáticamente</span>
                    </label>
                </div>
            </div>

            <button type="submit" id="import-submit"
                    class="w-full inline-flex items-center justify-center gap-3 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Iniciar Importación
            </button>
        </form>
    </div>

    {{-- Progress Container (shown after submit via JS) --}}
    @if(session('batch_id'))
    <div id="import-progress-container"
         data-batch-id="{{ session('batch_id') }}"
         class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-zinc-200">Progreso de Importación</h2>
            <span id="import-status-badge" class="text-xs px-2.5 py-1 rounded-full bg-blue-900/50 text-blue-300 border border-blue-700/50">
                Procesando...
            </span>
        </div>

        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-zinc-400" id="import-progress-text">Iniciando...</span>
                <span class="text-zinc-300 font-mono" id="import-progress-percent">0%</span>
            </div>
            <div class="w-full h-3 bg-zinc-700 rounded-full overflow-hidden">
                <div id="import-progress-bar"
                     class="h-full bg-amber-500 rounded-full transition-all duration-500"
                     style="width: 0%"></div>
            </div>
        </div>

        <div id="import-summary" class="hidden space-y-3">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-emerald-900/30 border border-emerald-700/40 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-emerald-400" id="import-created">0</p>
                    <p class="text-xs text-emerald-600 mt-0.5">Creadas</p>
                </div>
                <div class="bg-blue-900/30 border border-blue-700/40 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-blue-400" id="import-updated">0</p>
                    <p class="text-xs text-blue-600 mt-0.5">Actualizadas</p>
                </div>
                <div class="bg-red-900/30 border border-red-700/40 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-red-400" id="import-failed">0</p>
                    <p class="text-xs text-red-600 mt-0.5">Errores</p>
                </div>
            </div>
            <a href="{{ route('admin.recipes.index') }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm font-medium transition-colors">
                Ver Recetas Importadas
            </a>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // File label update
    var fileInput = document.getElementById('csv_file');
    var fileLabel = document.getElementById('file-label');
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function () {
            fileLabel.textContent = this.files[0] ? this.files[0].name : 'Arrastra tu CSV aquí o haz click para seleccionar';
        });
    }
});
</script>
@endsection
