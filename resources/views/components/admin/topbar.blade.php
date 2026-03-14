<header class="h-16 bg-white border-b border-zinc-200 flex items-center justify-between px-6 sticky top-0 z-30">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-zinc-800">@yield('page-title', 'Dashboard')</h1>
    </div>

    <div class="flex items-center gap-3">
        {{-- Quick search --}}
        <div class="relative hidden md:block">
            <input type="search"
                   placeholder="Buscar recetas..."
                   id="admin-search"
                   class="w-64 pl-9 pr-4 py-2 text-sm border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-zinc-50">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        {{-- New recipe button --}}
        <a href="{{ route('admin.recipes.create') }}"
           class="flex items-center gap-2 bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden md:inline">Nueva Receta</span>
        </a>

        {{-- View site --}}
        <a href="{{ route('home') }}" target="_blank"
           class="text-zinc-500 hover:text-zinc-700 transition-colors" title="Ver sitio web">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        </a>
    </div>
</header>
