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

    {{-- Format Tabs --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden" id="format-tabs">

        {{-- Tab headers --}}
        <div class="flex border-b border-zinc-700">
            <button data-tab="nuevo" onclick="switchFormatTab('nuevo')"
                    class="fmt-tab-btn flex-1 px-4 py-3 text-sm font-medium transition-colors text-center bg-zinc-700 text-amber-400 border-b-2 border-amber-400">
                Formato Nuevo (Recomendado)
            </button>
            <button data-tab="clasico" onclick="switchFormatTab('clasico')"
                    class="fmt-tab-btn flex-1 px-4 py-3 text-sm font-medium transition-colors text-center text-zinc-400 hover:text-zinc-200">
                Formato Clásico
            </button>
        </div>

        {{-- Nuevo format --}}
        <div id="fmt-nuevo" class="p-5 space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-900/40 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-sm font-semibold text-zinc-200 mb-3">Columnas del CSV Nuevo Formato</h2>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-zinc-700/50 rounded-lg p-2.5">
                            <code class="text-amber-300 font-mono font-bold">titulo</code>
                            <p class="text-zinc-400 mt-0.5">Nombre de la receta</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5">
                            <code class="text-amber-300 font-mono font-bold">pais</code>
                            <p class="text-zinc-400 mt-0.5">País de origen (ej: China)</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5">
                            <code class="text-amber-300 font-mono font-bold">dificultad</code>
                            <p class="text-zinc-400 mt-0.5">Fácil / Media / Difícil</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5">
                            <code class="text-amber-300 font-mono font-bold">tiempo_preparacion</code>
                            <p class="text-zinc-400 mt-0.5">Minutos (número)</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5">
                            <code class="text-amber-300 font-mono font-bold">tiempo_coccion</code>
                            <p class="text-zinc-400 mt-0.5">Minutos (número)</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5">
                            <code class="text-amber-300 font-mono font-bold">porciones</code>
                            <p class="text-zinc-400 mt-0.5">Número de porciones</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5 col-span-2">
                            <code class="text-amber-300 font-mono font-bold">ingredientes</code>
                            <p class="text-zinc-400 mt-0.5">Uno por línea: "2 tazas de arroz", "½ litro de agua"</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5 col-span-2">
                            <code class="text-amber-300 font-mono font-bold">preparacion</code>
                            <p class="text-zinc-400 mt-0.5">Pasos numerados: "1. Lavar el arroz\n2. Hervir el agua..."</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5 col-span-2">
                            <code class="text-zinc-300 font-mono">amazon_link</code>
                            <span class="text-zinc-500 text-xs ml-1">(opcional)</span>
                            <p class="text-zinc-400 mt-0.5">URL Amazon con ASIN — crea/vincula libro automáticamente con afiliado</p>
                        </div>
                        <div class="bg-zinc-700/50 rounded-lg p-2.5 col-span-2">
                            <code class="text-zinc-300 font-mono">imagen_url</code>
                            <span class="text-zinc-500 text-xs ml-1">(opcional)</span>
                            <p class="text-zinc-400 mt-0.5">URL de imagen (Google Drive, HTTP) — se descarga y optimiza automáticamente</p>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-blue-900/20 border border-blue-700/30 rounded-lg">
                        <p class="text-xs text-blue-300">
                            <strong>Nota:</strong> Las imágenes de Google Drive se descargan y optimizan a 1200×800 px JPEG. Los libros Amazon se crean a partir del ASIN y se vinculan a la receta con URLs de afiliado por país (MX, ES, AR, US).
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clásico format --}}
        <div id="fmt-clasico" class="p-5 space-y-3" style="display:none">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-sm font-semibold text-zinc-200 mb-2">Columnas del CSV Formato Clásico</h2>
                    <ul class="text-sm text-zinc-400 space-y-1">
                        <li>• Requerido: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-amber-300">title, description</code></li>
                        <li>• Opcionales: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">subtitle, prep_time, cook_time, servings, difficulty, origin_country, story, tips, seo_title, seo_description</code></li>
                        <li>• Ingredientes: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">ingredients</code> separados por <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">;</code> — cada uno: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">cantidad|unidad|nombre|grupo</code></li>
                        <li>• Pasos: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">steps</code> separados por <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">;</code> — cada uno: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">título|descripción</code></li>
                        <li>• Tags: <code class="bg-zinc-700 px-1.5 py-0.5 rounded text-xs font-mono text-zinc-300">tags</code> separados por coma</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="px-5 pb-4 border-t border-zinc-700 pt-4">
            <p class="text-xs text-zinc-500">El formato se detecta automáticamente según las columnas del archivo. Asegúrate que el CSV esté en codificación <strong class="text-zinc-400">UTF-8</strong>.</p>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-5" id="upload-section">
        <h2 class="text-sm font-semibold text-zinc-300">Subir Archivo CSV</h2>

        <form id="import-form" method="POST" action="{{ route('admin.recipes.import.store') }}" enctype="multipart/form-data" class="space-y-4">
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

            <button type="submit" id="import-submit"
                    class="w-full inline-flex items-center justify-center gap-3 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" id="submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <svg class="w-4 h-4 animate-spin hidden" id="submit-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="submit-label">Iniciar Importación</span>
            </button>
        </form>
    </div>

    {{-- Error display --}}
    <div id="import-error" class="hidden p-4 bg-red-900/50 border border-red-700 rounded-xl text-red-300 text-sm"></div>

    {{-- Progress Container (shown after submit via JS) --}}
    <div id="import-progress-container" class="hidden bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
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
                    <p class="text-2xl font-bold text-emerald-400" id="import-total">0</p>
                    <p class="text-xs text-emerald-600 mt-0.5">Total procesadas</p>
                </div>
                <div class="bg-blue-900/30 border border-blue-700/40 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-blue-400" id="import-jobs">0</p>
                    <p class="text-xs text-blue-600 mt-0.5">Lotes completados</p>
                </div>
                <div class="bg-red-900/30 border border-red-700/40 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-red-400" id="import-failed">0</p>
                    <p class="text-xs text-red-600 mt-0.5">Lotes con error</p>
                </div>
            </div>
            <a href="{{ route('admin.recipes.index') }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm font-medium transition-colors">
                Ver Recetas Importadas
            </a>
        </div>

        {{-- Pending info --}}
        <div id="import-pending-info" class="p-3 bg-amber-900/20 border border-amber-700/30 rounded-lg">
            <p class="text-xs text-amber-300">
                <strong>Importante:</strong> El worker de colas debe estar corriendo para procesar la importación. Si las recetas no aparecen, ejecuta <code class="bg-zinc-700 px-1 rounded">php artisan queue:work</code> en una terminal.
            </p>
        </div>
    </div>

</div>

<script>
function switchFormatTab(tab) {
    document.querySelectorAll('.fmt-tab-btn').forEach(function (btn) {
        var isActive = btn.dataset.tab === tab;
        btn.className = 'fmt-tab-btn flex-1 px-4 py-3 text-sm font-medium transition-colors text-center ' +
            (isActive ? 'bg-zinc-700 text-amber-400 border-b-2 border-amber-400' : 'text-zinc-400 hover:text-zinc-200');
    });
    ['nuevo', 'clasico'].forEach(function (t) {
        var el = document.getElementById('fmt-' + t);
        if (el) el.style.display = t === tab ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function () {
    var fileInput  = document.getElementById('csv_file');
    var fileLabel  = document.getElementById('file-label');
    var form       = document.getElementById('import-form');
    var submitBtn  = document.getElementById('import-submit');
    var submitIcon = document.getElementById('submit-icon');
    var submitSpinner = document.getElementById('submit-spinner');
    var submitLabel   = document.getElementById('submit-label');
    var errorDiv   = document.getElementById('import-error');
    var progressContainer = document.getElementById('import-progress-container');
    var progressBar  = document.getElementById('import-progress-bar');
    var progressText = document.getElementById('import-progress-text');
    var progressPct  = document.getElementById('import-progress-percent');
    var statusBadge  = document.getElementById('import-status-badge');
    var summaryDiv   = document.getElementById('import-summary');
    var pendingInfo  = document.getElementById('import-pending-info');

    var batchId    = null;
    var pollTimer  = null;

    // File label update
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function () {
            fileLabel.textContent = this.files[0]
                ? this.files[0].name
                : 'Arrastra tu CSV aquí o haz click para seleccionar';
        });
    }

    // Form submit — native POST/redirect to preview page
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!fileInput.files[0]) {
                e.preventDefault();
                errorDiv.textContent = 'Debes seleccionar un archivo CSV.';
                errorDiv.classList.remove('hidden');
                return;
            }
            // Show spinner while uploading (browser will navigate away)
            submitBtn.disabled = true;
            submitIcon.classList.add('hidden');
            submitSpinner.classList.remove('hidden');
            submitLabel.textContent = 'Subiendo archivo...';
            // Let the browser submit normally (form has action + method set)
        });
    }

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(pollProgress, 2500);
        pollProgress();
    }

    function pollProgress() {
        if (!batchId) return;

        fetch('{{ url('admin/recetas/importar/progreso') }}/' + batchId)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var pct = data.progress || 0;

            progressBar.style.width = pct + '%';
            progressPct.textContent = pct + '%';

            document.getElementById('import-jobs').textContent = data.processed_jobs || 0;
            document.getElementById('import-failed').textContent = data.failed_jobs || 0;

            if (data.finished) {
                clearInterval(pollTimer);
                progressText.textContent = 'Importación completada';
                statusBadge.textContent = data.failed_jobs > 0 ? 'Completado con errores' : 'Completado';
                statusBadge.className = data.failed_jobs > 0
                    ? 'text-xs px-2.5 py-1 rounded-full bg-amber-900/50 text-amber-300 border border-amber-700/50'
                    : 'text-xs px-2.5 py-1 rounded-full bg-emerald-900/50 text-emerald-300 border border-emerald-700/50';
                summaryDiv.classList.remove('hidden');
                pendingInfo.classList.add('hidden');
                progressBar.style.width = '100%';
                progressPct.textContent = '100%';
            } else if (data.cancelled) {
                clearInterval(pollTimer);
                progressText.textContent = 'Importación cancelada';
                statusBadge.textContent = 'Cancelado';
                statusBadge.className = 'text-xs px-2.5 py-1 rounded-full bg-red-900/50 text-red-300 border border-red-700/50';
            } else {
                progressText.textContent = 'Procesando lotes... (' + (data.processed_jobs || 0) + ' / ' + (data.total_jobs || '?') + ')';
            }
        })
        .catch(function () {
            // silent — keep polling
        });
    }
});
</script>
@endsection
