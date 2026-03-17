@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- ══════════════════════════════════════════════════════════════
         ANALYTICS ROW  (GA4 + Search Console)
    ══════════════════════════════════════════════════════════════ --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">Últimos 7 días</h2>
            @if($googleConfigured)
                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    API conectada · caché 4h
                </span>
            @else
                <a href="{{ route('admin.settings.index') }}#seo"
                   class="inline-flex items-center gap-1.5 text-xs text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full hover:bg-amber-100 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/></svg>
                    Configurar Google API
                </a>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            {{-- GA4: Visitantes únicos --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium">Visitantes</span>
                </div>
                @if($ga4Stats)
                    <p class="text-xl font-bold text-zinc-800">{{ number_format($ga4Stats['active_users']) }}</p>
                @else
                    <p class="text-xl font-bold text-zinc-300">—</p>
                @endif
                <p class="text-xs text-zinc-400 mt-0.5">GA4 · únicos</p>
            </div>

            {{-- GA4: Sesiones --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium">Sesiones</span>
                </div>
                @if($ga4Stats)
                    <p class="text-xl font-bold text-zinc-800">{{ number_format($ga4Stats['sessions']) }}</p>
                @else
                    <p class="text-xl font-bold text-zinc-300">—</p>
                @endif
                <p class="text-xs text-zinc-400 mt-0.5">GA4</p>
            </div>

            {{-- GA4: Páginas vistas --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 bg-violet-50 text-violet-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium">Páginas vistas</span>
                </div>
                @if($ga4Stats)
                    <p class="text-xl font-bold text-zinc-800">{{ number_format($ga4Stats['page_views']) }}</p>
                @else
                    <p class="text-xl font-bold text-zinc-300">—</p>
                @endif
                <p class="text-xs text-zinc-400 mt-0.5">GA4</p>
            </div>

            {{-- Search Console: Clics --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 bg-green-50 text-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium">Clics</span>
                </div>
                @if($scStats)
                    <p class="text-xl font-bold text-zinc-800">{{ number_format($scStats['clicks']) }}</p>
                @else
                    <p class="text-xl font-bold text-zinc-300">—</p>
                @endif
                <p class="text-xs text-zinc-400 mt-0.5">Search Console</p>
            </div>

            {{-- Search Console: Impresiones --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 bg-teal-50 text-teal-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium">Impresiones</span>
                </div>
                @if($scStats)
                    <p class="text-xl font-bold text-zinc-800">{{ number_format($scStats['impressions']) }}</p>
                @else
                    <p class="text-xl font-bold text-zinc-300">—</p>
                @endif
                <p class="text-xs text-zinc-400 mt-0.5">Search Console</p>
            </div>

            {{-- Páginas en sitemap --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium">En sitemap</span>
                </div>
                <p class="text-xl font-bold text-zinc-800">{{ number_format($stats['sitemap_pages']) }}</p>
                <p class="text-xs text-zinc-400 mt-0.5">
                    <a href="/sitemap.xml" target="_blank" class="hover:text-amber-500 transition-colors">páginas publicadas ↗</a>
                </p>
            </div>

        </div>

        {{-- SC Posición promedio + CTR row --}}
        @if($scStats)
        <div class="grid grid-cols-2 gap-3 mt-3">
            <div class="bg-white rounded-xl border border-zinc-200 px-4 py-3 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-zinc-400 font-medium">Posición promedio Google</p>
                    <p class="text-lg font-bold text-zinc-800 mt-0.5">#{{ $scStats['position'] }}</p>
                </div>
                <div class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-zinc-200 px-4 py-3 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-zinc-400 font-medium">CTR orgánico</p>
                    <p class="text-lg font-bold text-zinc-800 mt-0.5">{{ $scStats['ctr'] }}%</p>
                </div>
                <div class="w-8 h-8 bg-cyan-50 text-cyan-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Setup notice --}}
        @if(!$googleConfigured)
        <div class="mt-3 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3 text-sm">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/></svg>
            <div>
                <p class="font-medium text-amber-800">Para ver datos reales de GA4 y Search Console</p>
                <p class="text-amber-700 mt-0.5">Necesitas subir el archivo <code class="bg-amber-100 px-1 rounded font-mono text-xs">google-credentials.json</code> al servidor y configurar el <strong>GA4 Property ID</strong> y <strong>Search Console URL</strong> en <a href="{{ route('admin.settings.index') }}" class="underline font-medium">Ajustes → SEO</a>.</p>
            </div>
        </div>
        @endif
    </div>

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
