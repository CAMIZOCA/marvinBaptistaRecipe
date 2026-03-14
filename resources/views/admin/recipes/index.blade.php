@extends('admin.layouts.app')

@section('page-title', 'Recetas')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Recetas</h1>
            <p class="text-zinc-400 text-sm mt-1">{{ $recipes->total() ?? 0 }} recetas en total</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.recipes.import.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Importar CSV
            </a>
            <a href="{{ route('admin.recipes.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Receta
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.recipes.index') }}" class="flex flex-wrap items-center gap-3 p-4 bg-zinc-800 rounded-xl border border-zinc-700">
        <div class="flex-1 min-w-48">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar recetas..."
                       class="w-full pl-10 pr-4 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 placeholder-zinc-500 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
            </div>
        </div>
        <select name="status"
                class="px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="">Todas</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicadas</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Borradores</option>
            <option value="ai_pending" {{ request('status') === 'ai_pending' ? 'selected' : '' }}>Pendientes IA</option>
        </select>
        <select name="category_id"
                class="px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="">Todas las categorías</option>
            @foreach($categories ?? [] as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
        <select name="difficulty"
                class="px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="">Dificultad</option>
            <option value="facil" {{ request('difficulty') === 'facil' ? 'selected' : '' }}>Fácil</option>
            <option value="media" {{ request('difficulty') === 'media' ? 'selected' : '' }}>Media</option>
            <option value="dificil" {{ request('difficulty') === 'dificil' ? 'selected' : '' }}>Difícil</option>
        </select>
        <button type="submit"
                class="px-4 py-2 bg-zinc-600 hover:bg-zinc-500 text-zinc-200 rounded-lg text-sm font-medium transition-colors">
            Filtrar
        </button>
        @if(request()->hasAny(['search','status','category_id','difficulty']))
        <a href="{{ route('admin.recipes.index') }}"
           class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-400 rounded-lg text-sm transition-colors">
            Limpiar
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        @if(isset($recipes) && $recipes->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Título</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Categorías</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Dificultad</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Vistas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">IA</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700/50">
                    @foreach($recipes as $recipe)
                    <tr class="hover:bg-zinc-700/30 transition-colors group">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($recipe->featured_image)
                                <img src="{{ $recipe->featured_image }}" alt="{{ $recipe->title }}"
                                     class="w-10 h-10 rounded-lg object-cover shrink-0">
                                @else
                                <div class="w-10 h-10 rounded-lg bg-zinc-700 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.recipes.edit', $recipe) }}"
                                       class="font-medium text-zinc-200 hover:text-amber-400 transition-colors line-clamp-1">
                                        {{ $recipe->title }}
                                    </a>
                                    @if($recipe->subtitle)
                                    <p class="text-xs text-zinc-500 line-clamp-1 mt-0.5">{{ $recipe->subtitle }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($recipe->categories ?? [] as $cat)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300 border border-blue-700/50">
                                    {{ $cat->name }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $difficulty = $recipe->difficulty ?? null;
                                $difficultyClasses = match($difficulty) {
                                    'facil'   => 'bg-emerald-900/50 text-emerald-300 border-emerald-700/50',
                                    'media'   => 'bg-yellow-900/50 text-yellow-300 border-yellow-700/50',
                                    'dificil' => 'bg-red-900/50 text-red-300 border-red-700/50',
                                    default   => 'bg-zinc-700 text-zinc-400 border-zinc-600',
                                };
                                $difficultyLabel = match($difficulty) {
                                    'facil'   => 'Fácil',
                                    'media'   => 'Media',
                                    'dificil' => 'Difícil',
                                    default   => '—',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $difficultyClasses }}">
                                {{ $difficultyLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm text-zinc-300 font-mono">{{ number_format($recipe->views_count ?? 0) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($recipe->ai_enhanced_at)
                            <span title="Mejorado con IA el {{ $recipe->ai_enhanced_at->format('d/m/Y') }}"
                                  class="inline-flex items-center justify-center w-6 h-6 bg-violet-900/50 rounded-full">
                                <svg class="w-3.5 h-3.5 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            @else
                            <span class="text-zinc-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.recipes.toggle-published', $recipe) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-zinc-800 {{ $recipe->is_published ? 'bg-amber-500' : 'bg-zinc-600' }}"
                                        title="{{ $recipe->is_published ? 'Publicada - click para despublicar' : 'Borrador - click para publicar' }}">
                                    <span class="inline-block w-4 h-4 transform rounded-full bg-white transition-transform {{ $recipe->is_published ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('recipe.show', $recipe->slug) }}" target="_blank"
                                   class="p-1.5 text-zinc-500 hover:text-zinc-300 transition-colors rounded"
                                   title="Ver en el sitio">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.recipes.edit', $recipe) }}"
                                   class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded"
                                   title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.recipes.destroy', $recipe) }}"
                                      onsubmit="return confirm('¿Eliminar la receta «{{ addslashes($recipe->title) }}»? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors rounded"
                                            title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($recipes->hasPages())
        <div class="px-4 py-3 border-t border-zinc-700">
            {{ $recipes->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 px-4">
            <div class="w-20 h-20 bg-zinc-700/50 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-zinc-300 mb-2">No hay recetas aún</h3>
            <p class="text-zinc-500 text-sm text-center max-w-sm mb-6">
                @if(request()->hasAny(['search','status','category_id','difficulty']))
                    No se encontraron recetas con los filtros seleccionados.
                @else
                    Empieza añadiendo tu primera receta o importa desde un archivo CSV.
                @endif
            </p>
            <div class="flex gap-3">
                <a href="{{ route('admin.recipes.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Receta
                </a>
                @if(request()->hasAny(['search','status','category_id','difficulty']))
                <a href="{{ route('admin.recipes.index') }}"
                   class="px-5 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg text-sm transition-colors">
                    Ver todas
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
