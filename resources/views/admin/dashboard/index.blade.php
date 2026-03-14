@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
        $statCards = [
            ['label' => 'Total Recetas', 'value' => number_format($stats['total_recipes']), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Publicadas', 'value' => number_format($stats['published_recipes']), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0', 'color' => 'bg-green-50 text-green-600'],
            ['label' => 'Vistas Totales', 'value' => number_format($stats['total_views']), 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => 'bg-purple-50 text-purple-600'],
            ['label' => 'Pendientes IA', 'value' => number_format($stats['ai_pending']), 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'bg-amber-50 text-amber-600'],
        ];
        @endphp

        @foreach($statCards as $card)
        <div class="bg-white rounded-xl border border-zinc-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 {{ $card['color'] }} rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-800">{{ $card['value'] }}</p>
            <p class="text-sm text-zinc-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Latest recipes table --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
                <h3 class="font-semibold text-zinc-800">Últimas Recetas</h3>
                <a href="{{ route('admin.recipes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Ver todas →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-100">
                            <th class="text-left px-6 py-3 font-medium text-zinc-500">Receta</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-500 hidden md:table-cell">Categoría</th>
                            <th class="text-right px-4 py-3 font-medium text-zinc-500">Vistas</th>
                            <th class="text-right px-6 py-3 font-medium text-zinc-500">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50">
                        @forelse($latestRecipes as $recipe)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.recipes.edit', $recipe) }}" class="font-medium text-zinc-800 hover:text-indigo-600 transition-colors line-clamp-1">
                                    {{ $recipe->title }}
                                </a>
                                <p class="text-xs text-zinc-400 mt-0.5">{{ $recipe->created_at->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="text-zinc-500 text-xs">{{ $recipe->categories->first()?->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-zinc-600">{{ number_format($recipe->view_count) }}</td>
                            <td class="px-6 py-3 text-right">
                                @if($recipe->is_published)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Publicada</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600">Borrador</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-400">
                                No hay recetas aún.
                                <a href="{{ route('admin.recipes.create') }}" class="text-indigo-600 hover:underline ml-1">Crear la primera</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top recipes sidebar --}}
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="px-6 py-4 border-b border-zinc-100">
                <h3 class="font-semibold text-zinc-800">Más Visitadas</h3>
            </div>
            <div class="p-4 space-y-3">
                @forelse($topRecipes as $i => $recipe)
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-bold text-zinc-200 w-6 shrink-0">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.recipes.edit', $recipe) }}" class="text-sm font-medium text-zinc-700 hover:text-indigo-600 line-clamp-1">
                            {{ $recipe->title }}
                        </a>
                        <div class="flex items-center gap-1 mt-1">
                            <div class="h-1.5 bg-indigo-500 rounded-full" style="width: {{ $topRecipes->max('view_count') > 0 ? round(($recipe->view_count / $topRecipes->max('view_count')) * 100) : 0 }}%"></div>
                            <span class="text-xs text-zinc-400 shrink-0">{{ number_format($recipe->view_count) }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-zinc-400 text-center py-4">Sin datos de visitas.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
        <h3 class="font-semibold text-zinc-800 mb-4">Acciones Rápidas</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.recipes.create') }}"
               class="flex items-center gap-2 bg-indigo-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva Receta
            </a>
            <a href="{{ route('admin.recipes.import.index') }}"
               class="flex items-center gap-2 bg-white text-zinc-700 text-sm font-medium px-4 py-2.5 rounded-lg border border-zinc-300 hover:bg-zinc-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Importar CSV
            </a>
            <a href="{{ route('admin.libros.create') }}"
               class="flex items-center gap-2 bg-white text-zinc-700 text-sm font-medium px-4 py-2.5 rounded-lg border border-zinc-300 hover:bg-zinc-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Añadir Libro
            </a>
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-2 bg-white text-zinc-700 text-sm font-medium px-4 py-2.5 rounded-lg border border-zinc-300 hover:bg-zinc-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                Configuración
            </a>
        </div>
    </div>
@endsection
