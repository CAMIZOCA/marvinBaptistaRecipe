@extends('admin.layouts.app')

@section('page-title', 'Libros')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Libros</h1>
            <p class="text-zinc-400 text-sm mt-1">{{ isset($books) ? $books->count() : 0 }} libros configurados</p>
        </div>
        <a href="{{ route('admin.libros.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Añadir Libro
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(isset($books) && $books->count() > 0)
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Libro</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Autor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Cuisina</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Recetas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Orden</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Activo</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700/50">
                    @foreach($books as $book)
                    <tr class="hover:bg-zinc-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($book->cover_image_url)
                                <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}"
                                     class="w-10 h-14 object-cover rounded shadow-md shrink-0">
                                @else
                                <div class="w-10 h-14 bg-zinc-700 rounded flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-zinc-200 line-clamp-1">{{ $book->title }}</p>
                                    @if($book->asin)
                                    <p class="text-xs text-zinc-500 font-mono">ASIN: {{ $book->asin }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-300">{{ $book->author ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($book->cuisine_type)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-900/50 text-amber-300 border border-amber-700/50">
                                {{ $book->cuisine_type }}
                            </span>
                            @else
                            <span class="text-zinc-600 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-mono text-zinc-300">{{ $book->recipes_count ?? 0 }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-mono text-zinc-400">{{ $book->sort_order ?? 0 }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.libros.index') }}/{{ $book->id }}/toggle" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="relative inline-flex items-center h-5 w-9 rounded-full transition-colors {{ $book->is_active ? 'bg-amber-500' : 'bg-zinc-600' }}">
                                    <span class="inline-block w-3.5 h-3.5 transform rounded-full bg-white transition-transform {{ $book->is_active ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @if($book->amazon_url_mx ?? $book->amazon_url_us)
                                <a href="{{ $book->amazon_url_mx ?? $book->amazon_url_us }}" target="_blank"
                                   class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded"
                                   title="Ver en Amazon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                @endif
                                <a href="{{ route('admin.libros.index') }}/{{ $book->id }}/edit"
                                   class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.libros.index') }}/{{ $book->id }}"
                                      onsubmit="return confirm('¿Eliminar «{{ addslashes($book->title) }}»?')">
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
        </div>
    </div>
    @else
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 flex flex-col items-center justify-center py-20">
        <div class="w-16 h-16 bg-zinc-700/50 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-zinc-300 mb-2">Sin libros aún</h3>
        <p class="text-zinc-500 text-sm mb-6">Añade libros de Amazon afiliado para enlazarlos con recetas.</p>
        <a href="{{ route('admin.libros.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
            Añadir Libro
        </a>
    </div>
    @endif

</div>
@endsection
