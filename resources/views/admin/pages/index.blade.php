@extends('admin.layouts.app')

@section('page-title', 'Páginas')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Páginas</h1>
            <p class="text-zinc-400 text-sm mt-1">Páginas estáticas del sitio</p>
        </div>
        <a href="/admin/paginas/create"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Página
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        @if(isset($pages) && $pages->count() > 0)
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Título</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Slug</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Publicada</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700/50">
                @foreach($pages as $page)
                <tr class="hover:bg-zinc-700/30 transition-colors">
                    <td class="px-4 py-3 font-medium text-zinc-200">{{ $page->title }}</td>
                    <td class="px-4 py-3 font-mono text-sm text-zinc-400">/{{ $page->slug }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="/admin/paginas/{{ $page->id }}/toggle" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="relative inline-flex items-center h-5 w-9 rounded-full transition-colors {{ $page->is_published ? 'bg-amber-500' : 'bg-zinc-600' }}">
                                <span class="inline-block w-3.5 h-3.5 transform rounded-full bg-white transition-transform {{ $page->is_published ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('page.show', $page->slug) }}" target="_blank"
                               class="p-1.5 text-zinc-500 hover:text-zinc-300 transition-colors rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <a href="/admin/paginas/{{ $page->id }}/edit"
                               class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="/admin/paginas/{{ $page->id }}"
                                  onsubmit="return confirm('¿Eliminar la página «{{ addslashes($page->title) }}»?')">
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
        @else
        <div class="flex flex-col items-center justify-center py-16">
            <h3 class="text-lg font-semibold text-zinc-300 mb-2">Sin páginas</h3>
            <p class="text-zinc-500 text-sm mb-6">Crea páginas como Sobre Mí, Privacidad, etc.</p>
            <a href="/admin/paginas/create"
               class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
                Nueva Página
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
