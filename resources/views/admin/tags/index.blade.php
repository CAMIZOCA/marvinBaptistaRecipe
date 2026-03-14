@extends('admin.layouts.app')

@section('page-title', 'Etiquetas')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Etiquetas</h1>
            <p class="text-zinc-400 text-sm mt-1">{{ isset($tags) ? $tags->count() : 0 }} etiquetas</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Inline Create Form --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5">
        <h2 class="text-sm font-semibold text-zinc-300 mb-4">Nueva Etiqueta</h2>
        <form method="POST" action="{{ route('admin.etiquetas.index') }}" class="flex flex-wrap items-end gap-4">
            @csrf
            <div class="flex-1 min-w-40">
                <label class="block text-xs text-zinc-400 mb-1.5">Nombre *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Vegetariano, Rápido, Sin gluten..."
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                       required>
            </div>
            <div class="w-40">
                <label class="block text-xs text-zinc-400 mb-1.5">Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                           class="w-10 h-10 rounded-lg bg-zinc-700 border border-zinc-600 cursor-pointer p-1">
                    <input type="text" id="color-hex-input" value="{{ old('color', '#6366f1') }}"
                           placeholder="#6366f1"
                           class="flex-1 px-3 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div>
                <label class="block text-xs text-zinc-400 mb-1.5">Slug (opcional)</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       placeholder="se genera automáticamente"
                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-xl placeholder-zinc-500 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Añadir
            </button>
        </form>
    </div>

    {{-- Tags Grid --}}
    @if(isset($tags) && $tags->count() > 0)
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($tags as $tag)
        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-4 flex items-center justify-between gap-3 hover:border-zinc-600 transition-colors">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-4 h-4 rounded-full shrink-0" style="background-color: {{ $tag->color ?? '#6366f1' }}"></div>
                <div class="min-w-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          style="background-color: {{ $tag->color ?? '#6366f1' }}20; color: {{ $tag->color ?? '#6366f1' }}; border: 1px solid {{ $tag->color ?? '#6366f1' }}40;">
                        {{ $tag->name }}
                    </span>
                    <p class="text-xs text-zinc-500 mt-1">{{ $tag->recipes_count ?? 0 }} recetas · /{{ $tag->slug }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                <a href="{{ route('admin.etiquetas.index') }}/{{ $tag->id }}/edit"
                   class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
                <form method="POST" action="{{ route('admin.etiquetas.index') }}/{{ $tag->id }}"
                      onsubmit="return confirm('¿Eliminar la etiqueta «{{ addslashes($tag->name) }}»?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors rounded">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 flex flex-col items-center justify-center py-12">
        <p class="text-zinc-400 text-sm">No hay etiquetas aún. Usa el formulario de arriba para crear la primera.</p>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var colorInput = document.querySelector('input[type="color"]');
    var hexInput = document.getElementById('color-hex-input');
    if (colorInput && hexInput) {
        colorInput.addEventListener('input', function () {
            hexInput.value = this.value;
        });
        hexInput.addEventListener('input', function () {
            if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
                colorInput.value = this.value;
            }
        });
    }
});
</script>
@endsection
