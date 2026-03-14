@extends('admin.layouts.app')
@section('page-title', 'Artículos del Blog')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-zinc-100">Artículos del Blog</h1>
            <p class="text-sm text-zinc-400 mt-0.5">{{ $posts->total() }} artículos en total</p>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Artículo
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-900/30 border border-emerald-700 text-emerald-300 rounded-xl px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-zinc-800 rounded-2xl border border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-700/50 text-zinc-400 uppercase text-xs tracking-wider">
                <tr>
                    <th class="text-left px-5 py-3">Título</th>
                    <th class="text-left px-5 py-3 hidden sm:table-cell">Categoría</th>
                    <th class="text-left px-5 py-3 hidden md:table-cell">Publicado</th>
                    <th class="text-left px-5 py-3 hidden lg:table-cell">Fecha</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @forelse($posts as $post)
                <tr class="hover:bg-zinc-700/30 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" alt=""
                                 class="w-10 h-10 rounded-lg object-cover shrink-0">
                            @else
                            <div class="w-10 h-10 rounded-lg bg-zinc-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-semibold text-zinc-100 line-clamp-1">{{ $post->title }}</p>
                                <p class="text-xs text-zinc-500 font-mono">/blog/{{ $post->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        <span class="text-zinc-400">{{ $post->category ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell">
                        <form method="POST" action="{{ route('admin.blog.toggle-published', $post) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $post->is_published ? 'bg-emerald-900/50 text-emerald-400 hover:bg-emerald-900' : 'bg-zinc-700 text-zinc-400 hover:bg-zinc-600' }}">
                                {{ $post->is_published ? 'Publicado' : 'Borrador' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-5 py-4 hidden lg:table-cell text-zinc-400 text-xs">
                        {{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            @if($post->is_published)
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                               class="p-1.5 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors" title="Ver">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            @endif
                            <a href="{{ route('admin.blog.edit', $post) }}"
                               class="p-1.5 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.blog.destroy', $post) }}"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($post->title) }}»?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 text-zinc-400 hover:text-red-400 hover:bg-red-950/30 rounded-lg transition-colors" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-zinc-500">
                        No hay artículos aún.
                        <a href="{{ route('admin.blog.create') }}" class="text-indigo-400 hover:underline ml-1">Crear el primero →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div class="flex justify-center">{{ $posts->links() }}</div>
    @endif

</div>
@endsection
