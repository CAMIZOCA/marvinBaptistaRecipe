<footer class="bg-zinc-900 text-zinc-300 mt-16">

    {{-- Main footer --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Column 1: Logo + Tagline --}}
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="inline-block">
                    <span class="font-serif text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        Marvin Baptista
                    </span>
                </a>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Recetas auténticas con sabor a Latinoamérica y el Mediterráneo. Cocina con pasión, comparte con amor.
                </p>
                {{-- Social links --}}
                <div class="flex items-center gap-3 pt-2">
                    @if(isset($settings['social_instagram']) && $settings['social_instagram'])
                    <a href="{{ $settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-xl bg-zinc-800 hover:bg-amber-500 text-zinc-400 hover:text-zinc-900 transition-all"
                       aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                    @endif
                    @if(isset($settings['social_youtube']) && $settings['social_youtube'])
                    <a href="{{ $settings['social_youtube'] }}" target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-xl bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white transition-all"
                       aria-label="YouTube">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    @endif
                    @if(isset($settings['social_pinterest']) && $settings['social_pinterest'])
                    <a href="{{ $settings['social_pinterest'] }}" target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-xl bg-zinc-800 hover:bg-red-700 text-zinc-400 hover:text-white transition-all"
                       aria-label="Pinterest">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Column 2: Categories --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Categorías</h3>
                <ul class="space-y-2">
                    @if(isset($footerCategories) && $footerCategories->count() > 0)
                    @foreach($footerCategories->take(8) as $cat)
                    <li>
                        <a href="{{ route('category.show', $cat->slug) }}"
                           class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">
                            {{ $cat->name }}
                        </a>
                    </li>
                    @endforeach
                    @else
                    <li><a href="{{ route('recipes.index') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Todas las Recetas</a></li>
                    @endif
                </ul>
            </div>

            {{-- Column 3: Popular Recipes --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Recetas Populares</h3>
                <ul class="space-y-2">
                    @if(isset($footerRecipes) && $footerRecipes->count() > 0)
                    @foreach($footerRecipes->take(6) as $recipe)
                    <li>
                        <a href="{{ route('recipe.show', $recipe->slug) }}"
                           class="text-sm text-zinc-400 hover:text-amber-400 transition-colors line-clamp-1">
                            {{ $recipe->title }}
                        </a>
                    </li>
                    @endforeach
                    @else
                    <li><a href="{{ route('recipes.index') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Ver recetas →</a></li>
                    @endif
                </ul>
            </div>

            {{-- Column 4: Links --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Información</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('page.show', 'sobre-mi') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Sobre Mí</a></li>
                    <li><a href="{{ route('store.index') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Tienda de Libros</a></li>
                    <li><a href="{{ route('page.show', 'contacto') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Contacto</a></li>
                    <li><a href="{{ route('page.show', 'privacidad') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Privacidad</a></li>
                    <li><a href="{{ route('page.show', 'cookies') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Política de Cookies</a></li>
                    <li><a href="{{ route('page.show', 'aviso-legal') }}" class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">Aviso Legal</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Affiliate Disclaimer --}}
    <div class="border-t border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-xs text-zinc-500 text-center leading-relaxed">
                <strong class="text-zinc-400">Aviso de afiliado:</strong>
                {{ $settings['affiliate_disclaimer'] ?? 'Como afiliado de Amazon, recibo una pequeña comisión por las compras realizadas a través de mis enlaces, sin costo adicional para ti. Esto me ayuda a mantener el sitio.' }}
            </p>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-zinc-500">
                &copy; {{ date('Y') }} Marvin Baptista. Todos los derechos reservados.
            </p>
            <div class="flex items-center gap-4">
                <a href="{{ route('page.show', 'privacidad') }}" class="text-xs text-zinc-500 hover:text-zinc-300 transition-colors">Privacidad</a>
                <a href="{{ route('page.show', 'cookies') }}" class="text-xs text-zinc-500 hover:text-zinc-300 transition-colors">Cookies</a>
                <a href="{{ route('page.show', 'aviso-legal') }}" class="text-xs text-zinc-500 hover:text-zinc-300 transition-colors">Términos</a>
            </div>
        </div>
    </div>

</footer>
