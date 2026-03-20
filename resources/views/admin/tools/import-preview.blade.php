@extends('admin.layouts.app')

@section('page-title', 'Revisar importación')

@php
$paisesOpciones = [
    '— América Latina —' => null,
    'Argentina'           => 'Argentina',
    'Bolivia'             => 'Bolivia',
    'Brasil'              => 'Brasil',
    'Chile'               => 'Chile',
    'Colombia'            => 'Colombia',
    'Costa Rica'          => 'Costa Rica',
    'Cuba'                => 'Cuba',
    'Ecuador'             => 'Ecuador',
    'El Salvador'         => 'El Salvador',
    'Guatemala'           => 'Guatemala',
    'Haití'               => 'Haití',
    'Honduras'            => 'Honduras',
    'México'              => 'México',
    'Nicaragua'           => 'Nicaragua',
    'Panamá'              => 'Panamá',
    'Paraguay'            => 'Paraguay',
    'Perú'                => 'Perú',
    'Puerto Rico'         => 'Puerto Rico',
    'República Dominicana'=> 'República Dominicana',
    'Uruguay'             => 'Uruguay',
    'Venezuela'           => 'Venezuela',
    '— América del Norte —' => null,
    'Canadá'              => 'Canadá',
    'Estados Unidos'      => 'Estados Unidos',
    '— Europa —'          => null,
    'Alemania'            => 'Alemania',
    'Austria'             => 'Austria',
    'Bélgica'             => 'Bélgica',
    'Bulgaria'            => 'Bulgaria',
    'Croacia'             => 'Croacia',
    'Dinamarca'           => 'Dinamarca',
    'Eslovaquia'          => 'Eslovaquia',
    'Eslovenia'           => 'Eslovenia',
    'España'              => 'España',
    'Estonia'             => 'Estonia',
    'Finlandia'           => 'Finlandia',
    'Francia'             => 'Francia',
    'Grecia'              => 'Grecia',
    'Hungría'             => 'Hungría',
    'Irlanda'             => 'Irlanda',
    'Islandia'            => 'Islandia',
    'Italia'              => 'Italia',
    'Letonia'             => 'Letonia',
    'Lituania'            => 'Lituania',
    'Luxemburgo'          => 'Luxemburgo',
    'Malta'               => 'Malta',
    'Noruega'             => 'Noruega',
    'Países Bajos'        => 'Países Bajos',
    'Polonia'             => 'Polonia',
    'Portugal'            => 'Portugal',
    'Reino Unido'         => 'Reino Unido',
    'República Checa'     => 'República Checa',
    'Rumanía'             => 'Rumanía',
    'Suecia'              => 'Suecia',
    'Suiza'               => 'Suiza',
    'Ucrania'             => 'Ucrania',
    '— Asia —'            => null,
    'Arabia Saudita'      => 'Arabia Saudita',
    'Bangladesh'          => 'Bangladesh',
    'China'               => 'China',
    'Corea del Norte'     => 'Corea del Norte',
    'Corea del Sur'       => 'Corea del Sur',
    'Emiratos Árabes Unidos' => 'Emiratos Árabes Unidos',
    'Filipinas'           => 'Filipinas',
    'India'               => 'India',
    'Indonesia'           => 'Indonesia',
    'Irak'                => 'Irak',
    'Irán'                => 'Irán',
    'Israel'              => 'Israel',
    'Japón'               => 'Japón',
    'Jordania'            => 'Jordania',
    'Kazajistán'          => 'Kazajistán',
    'Líbano'              => 'Líbano',
    'Malasia'             => 'Malasia',
    'Mongolia'            => 'Mongolia',
    'Nepal'               => 'Nepal',
    'Pakistán'            => 'Pakistán',
    'Siria'               => 'Siria',
    'Sri Lanka'           => 'Sri Lanka',
    'Tailandia'           => 'Tailandia',
    'Taiwán'              => 'Taiwán',
    'Turquía'             => 'Turquía',
    'Vietnam'             => 'Vietnam',
    '— África —'          => null,
    'Angola'              => 'Angola',
    'Argelia'             => 'Argelia',
    'Camerún'             => 'Camerún',
    'Egipto'              => 'Egipto',
    'Etiopía'             => 'Etiopía',
    'Ghana'               => 'Ghana',
    'Kenia'               => 'Kenia',
    'Marruecos'           => 'Marruecos',
    'Mozambique'          => 'Mozambique',
    'Nigeria'             => 'Nigeria',
    'Senegal'             => 'Senegal',
    'Sudáfrica'           => 'Sudáfrica',
    'Tanzania'            => 'Tanzania',
    'Túnez'               => 'Túnez',
    '— Oceanía —'         => null,
    'Australia'           => 'Australia',
    'Nueva Zelanda'       => 'Nueva Zelanda',
    '— Otras —'           => null,
    'Internacional'       => 'Internacional',
    'Mediterránea'        => 'Mediterránea',
    'Medio Oriente'       => 'Medio Oriente',
];
@endphp

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.recipes.import.index') }}"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors"
           title="Cancelar y volver">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Revisar antes de importar</h1>
            <p class="text-zinc-400 text-sm mt-0.5">
                <span id="selected-count">{{ count($rows) }}</span> de {{ count($rows) }} recetas seleccionadas
            </p>
        </div>
        <div class="ml-auto">
            <button onclick="submitImport()"
                    class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 font-bold rounded-xl transition-all hover:shadow-lg text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Importar <span id="btn-count">{{ count($rows) }}</span> recetas
            </button>
        </div>
    </div>

    {{-- Warnings --}}
    @if(count($warnings) > 0)
    <div class="space-y-2">
        @foreach($warnings as $warning)
        <div class="flex items-start gap-3 p-4 bg-amber-900/30 border border-amber-700/50 rounded-xl">
            <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm text-amber-300">{!! $warning !!}</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- BULK EDIT PANEL --}}
    <div class="bg-zinc-800 border border-zinc-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-zinc-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <h2 class="text-sm font-semibold text-zinc-200">Ajuste en lote — aplica a todas las filas</h2>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Bulk: País --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wide">País de origen</label>
                <div class="flex gap-2">
                    <select id="bulk-pais" class="flex-1 bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">— Sin cambio —</option>
                        @foreach($paisesOpciones as $label => $value)
                            @if(is_null($value))
                                <option disabled>{{ $label }}</option>
                            @else
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button onclick="applyBulk('pais')"
                            class="px-3 py-2 bg-zinc-600 hover:bg-zinc-500 text-zinc-200 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                        Aplicar
                    </button>
                </div>
            </div>

            {{-- Bulk: Dificultad --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wide">Dificultad</label>
                <div class="flex gap-2">
                    <select id="bulk-dificultad" class="flex-1 bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">— Sin cambio —</option>
                        <option value="Fácil">Fácil</option>
                        <option value="Media">Media</option>
                        <option value="Difícil">Difícil</option>
                    </select>
                    <button onclick="applyBulk('dificultad')"
                            class="px-3 py-2 bg-zinc-600 hover:bg-zinc-500 text-zinc-200 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                        Aplicar
                    </button>
                </div>
            </div>

            {{-- Bulk: Amazon link --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wide">
                    Enlace Amazon
                    <span class="ml-1 text-zinc-500 normal-case font-normal">(vacío = sin libro)</span>
                </label>
                <div class="flex gap-2">
                    <input id="bulk-amazon_link" type="text"
                           placeholder="https://amazon.com/dp/... o dejar vacío"
                           class="flex-1 bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 placeholder-zinc-500">
                    <button onclick="applyBulk('amazon_link')"
                            class="px-3 py-2 bg-zinc-600 hover:bg-zinc-500 text-zinc-200 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                        Aplicar
                    </button>
                </div>
            </div>
        </div>
        <div class="px-5 pb-4">
            <button onclick="applyAllBulk()"
                    class="w-full py-2.5 bg-zinc-700 hover:bg-zinc-600 border border-zinc-600 text-zinc-200 rounded-lg text-sm font-medium transition-colors">
                Aplicar todos los campos de arriba a todas las filas
            </button>
        </div>
    </div>

    {{-- ROWS TABLE --}}
    <div class="bg-zinc-800 border border-zinc-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-zinc-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-zinc-200">Recetas a importar</h2>
            <div class="flex items-center gap-3">
                <button onclick="selectAll(true)" class="text-xs text-amber-400 hover:text-amber-300">Seleccionar todas</button>
                <span class="text-zinc-600">·</span>
                <button onclick="selectAll(false)" class="text-xs text-zinc-400 hover:text-zinc-300">Deseleccionar todas</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-700 text-xs text-zinc-400 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left w-8">#</th>
                        <th class="px-4 py-3 text-left min-w-[220px]">Título</th>
                        <th class="px-4 py-3 text-left w-40">País de origen</th>
                        <th class="px-4 py-3 text-left w-32">Dificultad</th>
                        <th class="px-4 py-3 text-left w-24">Porciones</th>
                        <th class="px-4 py-3 text-left w-20">Imagen</th>
                        <th class="px-4 py-3 text-left min-w-[200px]">Amazon link</th>
                        <th class="px-4 py-3 text-center w-16">Incluir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700/50" id="rows-tbody">
                    @foreach($rows as $idx => $row)
                    <tr class="hover:bg-zinc-700/30 transition-colors" id="row-{{ $idx }}">
                        {{-- # --}}
                        <td class="px-4 py-2.5 text-zinc-500 text-xs">{{ $idx + 1 }}</td>

                        {{-- Título --}}
                        <td class="px-4 py-2.5">
                            <span class="text-zinc-200 font-medium line-clamp-2" title="{{ $row['titulo'] ?? '' }}">
                                {{ Str::limit($row['titulo'] ?? 'Sin título', 60) }}
                            </span>
                        </td>

                        {{-- País (editable) --}}
                        <td class="px-4 py-2.5">
                            <select name="overrides[{{ $idx }}][pais]"
                                    data-field="pais"
                                    data-row="{{ $idx }}"
                                    class="row-select w-full bg-zinc-700 border border-zinc-600 rounded-lg px-2 py-1.5 text-zinc-200 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @foreach($paisesOpciones as $label => $value)
                                    @if(is_null($value))
                                        <option disabled>{{ $label }}</option>
                                    @else
                                        <option value="{{ $value }}" {{ ($row['pais'] ?? 'Internacional') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>

                        {{-- Dificultad (editable) --}}
                        <td class="px-4 py-2.5">
                            <select name="overrides[{{ $idx }}][dificultad]"
                                    data-field="dificultad"
                                    data-row="{{ $idx }}"
                                    class="row-select w-full bg-zinc-700 border border-zinc-600 rounded-lg px-2 py-1.5 text-zinc-200 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                                <option value="Fácil"   {{ ($row['dificultad'] ?? '') === 'Fácil'   ? 'selected' : '' }}>Fácil</option>
                                <option value="Media"   {{ ($row['dificultad'] ?? 'Media') === 'Media'   ? 'selected' : '' }}>Media</option>
                                <option value="Difícil" {{ ($row['dificultad'] ?? '') === 'Difícil' ? 'selected' : '' }}>Difícil</option>
                            </select>
                        </td>

                        {{-- Porciones (read-only) --}}
                        <td class="px-4 py-2.5 text-zinc-400 text-xs text-center">
                            {{ $row['porciones'] ?? '—' }}
                        </td>

                        {{-- Imagen (indicator) --}}
                        <td class="px-4 py-2.5 text-center">
                            @if(!empty($row['imagen_url']))
                                <span class="inline-flex items-center gap-1 text-xs text-emerald-400" title="{{ $row['imagen_url'] }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Sí
                                </span>
                            @else
                                <span class="text-xs text-zinc-600">No</span>
                            @endif
                        </td>

                        {{-- Amazon link (editable, clearable) --}}
                        <td class="px-4 py-2.5">
                            <input type="text"
                                   name="overrides[{{ $idx }}][amazon_link]"
                                   data-field="amazon_link"
                                   data-row="{{ $idx }}"
                                   value="{{ $row['amazon_link'] ?? '' }}"
                                   placeholder="Vacío = sin libro"
                                   class="row-input w-full bg-zinc-700 border border-zinc-600 rounded-lg px-2 py-1.5 text-zinc-300 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500 placeholder-zinc-600">
                        </td>

                        {{-- Checkbox incluir --}}
                        <td class="px-4 py-2.5 text-center">
                            <input type="checkbox"
                                   class="row-checkbox w-4 h-4 rounded border-zinc-600 bg-zinc-700 text-amber-500 focus:ring-amber-500 cursor-pointer"
                                   data-row="{{ $idx }}"
                                   checked
                                   onchange="updateSelectedCount()">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Queue worker reminder (always visible before confirming) --}}
    <div id="queue-reminder" class="flex items-start gap-3 p-4 bg-blue-950/40 border border-blue-800/50 rounded-xl">
        <svg class="w-5 h-5 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="space-y-1.5">
            <p class="text-sm font-semibold text-blue-300">Recuerda tener el worker de colas activo</p>
            <p class="text-xs text-blue-400 leading-relaxed">
                La importación se procesa en segundo plano. Si las recetas no aparecen tras confirmar,
                abre una terminal en la raíz del proyecto y ejecuta:
            </p>
            <code class="inline-block text-xs bg-zinc-900 border border-zinc-700 text-amber-300 px-3 py-1.5 rounded-lg font-mono select-all">
                php artisan queue:work
            </code>
            <p class="text-xs text-blue-500">
                Déjalo corriendo hasta que el progreso llegue al 100 %. Puedes cerrarlo después.
            </p>
        </div>
    </div>

    {{-- Progress section (hidden until import starts) --}}
    <div id="progress-section" class="hidden bg-zinc-800 border border-zinc-700 rounded-xl p-6 space-y-4">
        <h2 class="text-sm font-semibold text-zinc-200">Importando recetas…</h2>
        <div class="w-full bg-zinc-700 rounded-full h-3">
            <div id="progress-bar" class="bg-amber-500 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
        <p id="progress-text" class="text-sm text-zinc-400">Iniciando…</p>

        {{-- Queue reminder repeated inside progress panel --}}
        <div id="progress-queue-reminder" class="flex items-start gap-3 p-3 bg-amber-900/20 border border-amber-700/30 rounded-lg">
            <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-xs text-amber-300 leading-relaxed">
                <strong>Si la barra no avanza</strong>, el worker no está corriendo. Abre una terminal y ejecuta:&nbsp;
                <code class="bg-zinc-900 text-amber-300 px-1.5 py-0.5 rounded font-mono select-all">php artisan queue:work</code>
            </p>
        </div>

        <div id="progress-done" class="hidden flex items-center gap-2 text-emerald-400 font-medium text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Importación completada</span>
            <a href="{{ route('admin.recipes.index') }}" class="ml-4 underline text-amber-400 hover:text-amber-300">Ver recetas</a>
        </div>
    </div>

    {{-- Footer actions --}}
    <div class="flex items-center justify-between pt-2 pb-6">
        <a href="{{ route('admin.recipes.import.index') }}"
           class="px-4 py-2.5 border border-zinc-600 text-zinc-400 hover:text-zinc-200 hover:border-zinc-500 rounded-xl text-sm transition-colors">
            Cancelar
        </a>
        <button onclick="submitImport()"
                id="import-btn-bottom"
                class="flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 font-bold rounded-xl transition-all hover:shadow-lg text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Importar <span class="btn-count-ref">{{ count($rows) }}</span> recetas
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
const TOTAL_ROWS  = {{ count($rows) }};
const CONFIRM_URL = '{{ route('admin.recipes.import.confirm') }}';
const PROGRESS_URL_TPL = '{{ route('admin.recipes.import.progress', ':batch') }}';
const CSRF_TOKEN  = '{{ csrf_token() }}';
const IMPORT_KEY  = '{{ $key }}';

// ── Bulk apply ────────────────────────────────────────────────────────────
function applyBulk(field) {
    const bulkEl = document.getElementById(`bulk-${field}`);
    if (!bulkEl) return;
    const value = bulkEl.value;
    // Don't apply empty value for selects (means "no change")
    if (field !== 'amazon_link' && value === '') return;

    document.querySelectorAll(`[data-field="${field}"]`).forEach(el => {
        el.value = value;
    });
}

function applyAllBulk() {
    ['pais', 'dificultad', 'amazon_link'].forEach(f => applyBulk(f));
}

// ── Select all / none ─────────────────────────────────────────────────────
function selectAll(checked) {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = checked;
        highlightRow(cb);
    });
    updateSelectedCount();
}

function highlightRow(checkbox) {
    const row = document.getElementById(`row-${checkbox.dataset.row}`);
    if (row) {
        row.classList.toggle('opacity-40', !checkbox.checked);
    }
}

function updateSelectedCount() {
    const selected = document.querySelectorAll('.row-checkbox:checked').length;
    document.getElementById('selected-count').textContent = selected;
    document.getElementById('btn-count').textContent = selected;
    document.querySelectorAll('.btn-count-ref').forEach(el => el.textContent = selected);
    document.querySelectorAll('.row-checkbox').forEach(cb => highlightRow(cb));
}

// ── Submit import ─────────────────────────────────────────────────────────
async function submitImport() {
    const selected = document.querySelectorAll('.row-checkbox:checked').length;
    if (selected === 0) {
        alert('Selecciona al menos una receta para importar.');
        return;
    }

    if (!confirm(`¿Importar ${selected} receta(s)?`)) return;

    // Collect data
    const body = new URLSearchParams();
    body.append('_token', CSRF_TOKEN);
    body.append('key', IMPORT_KEY);

    // Skip rows
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        if (!cb.checked) {
            body.append('skip_rows[]', cb.dataset.row);
        }
    });

    // Overrides: pais, dificultad, amazon_link
    document.querySelectorAll('.row-select, .row-input').forEach(el => {
        body.append(el.name, el.value);
    });

    // Show progress section, hide the static queue reminder (it's repeated inside progress), disable buttons
    document.getElementById('progress-section').classList.remove('hidden');
    document.getElementById('queue-reminder').classList.add('hidden');
    document.getElementById('import-btn-bottom').disabled = true;
    document.querySelectorAll('button[onclick="submitImport()"]').forEach(b => b.disabled = true);

    try {
        const res = await fetch(CONFIRM_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
        });

        const data = await res.json();

        if (!res.ok || data.error) {
            document.getElementById('progress-text').textContent = data.error ?? 'Error al iniciar la importación.';
            document.getElementById('progress-text').classList.add('text-red-400');
            return;
        }

        // Poll progress
        pollProgress(data.batch_id, data.total_recipes);

    } catch (err) {
        document.getElementById('progress-text').textContent = 'Error de red: ' + err.message;
        document.getElementById('progress-text').classList.add('text-red-400');
    }
}

function pollProgress(batchId, total) {
    const progressBar  = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const progressDone = document.getElementById('progress-done');
    const url = PROGRESS_URL_TPL.replace(':batch', batchId);

    const interval = setInterval(async () => {
        try {
            const res  = await fetch(url);
            const data = await res.json();

            const pct = data.progress ?? 0;
            progressBar.style.width = pct + '%';
            progressText.textContent = `${data.processed_jobs} de ${data.total_jobs} lotes procesados (${pct}%)`;

            if (data.failed_jobs > 0) {
                progressText.textContent += ` · ${data.failed_jobs} con errores`;
            }

            if (data.finished || data.cancelled) {
                clearInterval(interval);
                progressBar.style.width = '100%';
                progressDone.classList.remove('hidden');
                // Hide the queue worker reminder once we know it finished
                document.getElementById('progress-queue-reminder').classList.add('hidden');
                progressText.textContent = data.cancelled
                    ? 'Importación cancelada.'
                    : `Completado: ${data.processed_jobs} lotes procesados.`;
            }
        } catch {
            // silently retry
        }
    }, 2000);
}
</script>
@endpush
