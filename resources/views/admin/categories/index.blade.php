@extends('admin.layouts.app')

@section('page-title', 'Categorías')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Categorías</h1>
            <p class="text-zinc-400 text-sm mt-1">Organiza las recetas en categorías jerárquicas</p>
        </div>
        <a href="{{ route('admin.categorias.index') }}/create"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Categoría
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        @if(isset($categories) && $categories->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Recetas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Orden</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700/50">
                    @foreach($categories as $category)
                    {{-- Parent row --}}
                    <tr class="hover:bg-zinc-700/20 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                     class="w-9 h-9 rounded-lg object-cover shrink-0">
                                @else
                                <div class="w-9 h-9 rounded-lg bg-zinc-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <span class="font-semibold text-zinc-200">{{ $category->name }}</span>
                                    @if($category->parent_id === null)
                                    <span class="ml-2 text-xs bg-blue-900/50 text-blue-400 border border-blue-700/50 px-1.5 py-0.5 rounded-full">Principal</span>
                                    @endif
                                    <p class="text-xs text-zinc-500 font-mono">/{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-semibold bg-zinc-700 text-zinc-300">
                                {{ $category->recipes_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-mono text-zinc-400">{{ $category->sort_order ?? 0 }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categorias.index') }}/{{ $category->id }}/edit"
                                   class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.categorias.index') }}/{{ $category->id }}"
                                      onsubmit="return confirm('¿Eliminar la categoría «{{ addslashes($category->name) }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Children rows --}}
                    @foreach($category->children ?? [] as $child)
                    <tr class="hover:bg-zinc-700/20 transition-colors bg-zinc-800/30">
                        <td class="px-4 py-2.5">
                            <div class="flex items-center gap-3 pl-8">
                                <div class="text-zinc-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm text-zinc-300">{{ $child->name }}</span>
                                    <p class="text-xs text-zinc-500 font-mono">/{{ $child->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-semibold bg-zinc-700 text-zinc-400">
                                {{ $child->recipes_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="text-sm font-mono text-zinc-500">{{ $child->sort_order ?? 0 }}</span>
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categorias.index') }}/{{ $child->id }}/edit"
                                   class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.categorias.index') }}/{{ $child->id }}"
                                      onsubmit="return confirm('¿Eliminar «{{ addslashes($child->name) }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16">
            <h3 class="text-lg font-semibold text-zinc-300 mb-2">Sin categorías</h3>
            <p class="text-zinc-500 text-sm mb-6">Crea categorías para organizar las recetas.</p>
            <a href="{{ route('admin.categorias.index') }}/create"
               class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
                Nueva Categoría
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
