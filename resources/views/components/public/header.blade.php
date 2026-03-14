<header class="sticky top-0 z-50 bg-white border-b border-zinc-100 shadow-sm" id="site-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 group" aria-label="Marvin Baptista - Inicio">
                <span class="font-serif text-xl font-bold text-zinc-900 group-hover:text-amber-600 transition-colors tracking-tight"
                      style="font-family: 'Playfair Display', serif;">
                    Marvin Baptista
                </span>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-1" role="navigation" aria-label="Navegación principal">
                <a href="{{ route('home') }}"
                   class="px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors {{ request()->routeIs('home') ? 'text-zinc-900 bg-zinc-50' : '' }}">
                    Inicio
                </a>

                {{-- Recetas Dropdown --}}
                <div class="relative" id="recipes-dropdown-wrapper">
                    <button type="button"
                            id="recipes-dropdown-btn"
                            aria-haspopup="true"
                            aria-expanded="false"
                            class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors {{ request()->routeIs('recipes.*','recipe.*','category.*') ? 'text-zinc-900 bg-zinc-50' : '' }}">
                        Recetas
                        <svg class="w-4 h-4 transition-transform" id="recipes-dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="recipes-dropdown"
                         class="hidden absolute top-full left-0 mt-1 w-64 bg-white border border-zinc-200 rounded-2xl shadow-xl py-2 z-50"
                         role="menu">
                        <div class="px-3 pt-1 pb-2 border-b border-zinc-100">
                            <a href="{{ route('recipes.index') }}"
                               class="flex items-center gap-2 px-2 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors"
                               role="menuitem">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                Todas las Recetas
                            </a>
                        </div>
                        @if(isset($headerCategories))
                        <div class="py-1">
                            @foreach($headerCategories as $cat)
                            <a href="{{ route('category.show', $cat->slug) }}"
                               class="flex items-center px-5 py-2 text-sm text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 transition-colors"
                               role="menuitem">
                                {{ $cat->name }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <a href="{{ route('blog.index') }}"
                   class="px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors {{ request()->routeIs('blog.*') ? 'text-zinc-900 bg-zinc-50' : '' }}">
                    Blog
                </a>

                <a href="{{ route('store.index') }}"
                   class="px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors {{ request()->routeIs('store.*') ? 'text-zinc-900 bg-zinc-50' : '' }}">
                    Tienda
                </a>

                <a href="{{ route('page.show', 'sobre-mi') }}"
                   class="px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors">
                    Sobre Mí
                </a>
            </nav>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                {{-- Search button --}}
                <a href="{{ route('recipes.index') }}"
                   class="hidden sm:flex p-2 text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors"
                   aria-label="Buscar recetas">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </a>

                {{-- Mobile hamburger --}}
                <button type="button"
                        id="mobile-menu-btn"
                        aria-label="Abrir menú"
                        aria-expanded="false"
                        aria-controls="mobile-menu"
                        class="md:hidden p-2 text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" id="hamburger-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="w-5 h-5 hidden" id="close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu"
         class="hidden md:hidden border-t border-zinc-100 bg-white"
         role="navigation"
         aria-label="Navegación móvil">
        <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
            <a href="{{ route('home') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">
                Inicio
            </a>
            <a href="{{ route('recipes.index') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">
                Todas las Recetas
            </a>
            @if(isset($headerCategories))
            @foreach($headerCategories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="flex items-center pl-6 pr-3 py-2 text-sm text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">
                {{ $cat->name }}
            </a>
            @endforeach
            @endif
            <a href="{{ route('blog.index') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors {{ request()->routeIs('blog.*') ? 'bg-zinc-50 text-zinc-900' : '' }}">
                Blog
            </a>
            <a href="{{ route('store.index') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">
                Tienda
            </a>
            <a href="{{ route('page.show', 'sobre-mi') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">
                Sobre Mí
            </a>
        </div>
    </div>
</header>
