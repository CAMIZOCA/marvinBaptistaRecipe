<aside class="w-64 min-h-screen bg-zinc-900 text-zinc-100 flex flex-col fixed left-0 top-0 bottom-0 z-40 overflow-y-auto">
    {{-- Logo --}}
    <div class="h-16 flex items-center px-5 border-b border-zinc-800 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span class="font-semibold text-white text-sm truncate">{{ config('app.name') }}</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 py-4 space-y-0.5">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            Dashboard
        </a>

        {{-- CONTENIDO group --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Contenido</p>
        </div>

        {{-- Recetas --}}
        <a href="{{ route('admin.recipes.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.recipes.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Recetas
        </a>

        @if(request()->routeIs('admin.recipes.*'))
        <div class="space-y-0.5">
            <a href="{{ route('admin.recipes.create') }}" class="sidebar-nav-sub-item {{ request()->routeIs('admin.recipes.create') ? 'active' : '' }}">+ Nueva Receta</a>
            <a href="{{ route('admin.recipes.import.index') }}" class="sidebar-nav-sub-item {{ request()->routeIs('admin.recipes.import.*') ? 'active' : '' }}">Importar CSV</a>
        </div>
        @endif

        <a href="{{ route('admin.categorias.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Categorías
        </a>

        <a href="{{ route('admin.etiquetas.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.etiquetas.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Etiquetas
        </a>

        {{-- TIENDA group --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tienda</p>
        </div>

        <a href="{{ route('admin.libros.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.libros.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Libros Amazon
        </a>

        {{-- SEO group --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">SEO</p>
        </div>

        <a href="{{ route('admin.ingredientes.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.ingredientes.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            Ingredientes
        </a>

        <a href="{{ route('admin.paginas.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.paginas.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Páginas
        </a>

        {{-- AJUSTES group --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Ajustes</p>
        </div>

        @if(auth()->user()->canManageUsers())
        <a href="{{ route('admin.usuarios.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Usuarios
        </a>
        @endif

        <a href="{{ route('admin.settings.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Configuración
        </a>

        <a href="{{ route('admin.recipes.import.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('admin.recipes.import.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Importar CSV
        </a>

        {{-- Ver sitio --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Sitio Web</p>
        </div>
        <a href="{{ route('home') }}" target="_blank"
           class="sidebar-nav-item">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Ver Sitio Web
        </a>
    </nav>

    {{-- User info --}}
    <div class="p-4 border-t border-zinc-800 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-medium shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-zinc-200 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-zinc-500 truncate">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-zinc-500 hover:text-zinc-200 transition-colors" title="Cerrar sesión">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
