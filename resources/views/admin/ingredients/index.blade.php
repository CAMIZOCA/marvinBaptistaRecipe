@extends('admin.layouts.app')

@section('page-title', 'Ingredientes')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Ingredientes</h1>
            <p class="text-zinc-400 text-sm mt-1">{{ isset($ingredients) ? $ingredients->total() : 0 }} ingredientes</p>
        </div>
        <a href="/admin/ingredientes/create"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Ingrediente
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Search --}}
    <form method="GET" action="/admin/ingredientes" class="flex gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar ingrediente..."
                   class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 text-zinc-200 placeholder-zinc-500 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <button type="submit"
                class="px-4 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm transition-colors">
            Buscar
        </button>
    </form>

    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        @if(isset($ingredients) && $ingredients->count() > 0)
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Slug</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700/50">
                @foreach($ingredients as $ingredient)
                <tr class="hover:bg-zinc-700/30 transition-colors">
                    <td class="px-4 py-3 font-medium text-zinc-200">{{ $ingredient->name }}</td>
                    <td class="px-4 py-3 font-mono text-sm text-zinc-400">{{ $ingredient->slug }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/admin/ingredientes/{{ $ingredient->id }}/edit"
                               class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="/admin/ingredientes/{{ $ingredient->id }}"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($ingredient->name) }}»?')">
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
            </tbody>
        </table>
        @if($ingredients->hasPages())
        <div class="px-4 py-3 border-t border-zinc-700">
            {{ $ingredients->appends(request()->query())->links() }}
        </div>
        @endif
        @else
        <div class="flex flex-col items-center justify-center py-16">
            <h3 class="text-lg font-semibold text-zinc-300 mb-2">Sin ingredientes</h3>
            <a href="/admin/ingredientes/create"
               class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors mt-4">
                Nuevo Ingrediente
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
