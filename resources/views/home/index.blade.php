@extends('layouts.app')

@section('seo_head')
<title>Marvin Baptista | Recetas Auténticas de Latinoamérica y el Mediterráneo</title>
<meta name="description" content="Recetas auténticas latinoamericanas y mediterráneas paso a paso: seco de pollo, ceviche, tacos, pasta y más. Con ingredientes frescos, tiempos exactos y consejos de chef.">
<link rel="canonical" href="{{ route('home') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="Marvin Baptista | Recetas con Alma">
<meta property="og:description" content="Descubre recetas auténticas latinoamericanas y mediterráneas con ingredientes frescos, tiempos exactos y fotos paso a paso.">
<meta property="og:url" content="{{ route('home') }}">
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "Marvin Baptista",
  "url": "{{ config('app.url') }}",
  "description": "Recetas auténticas de cocina latinoamericana y mediterránea",
  "potentialAction": {
    "@@type": "SearchAction",
    "target": "{{ route('recipes.index') }}?search={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
@endsection

@section('content')

{{-- ══════════════════════════════════════════════════════════
     HERO — Impacto visual + buscador inmediato
══════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-zinc-900" aria-label="Hero">
    {{-- Background gradient --}}
    <div class="absolute inset-0 bg-gradient-to-br from-zinc-900 via-zinc-800 to-amber-950"></div>
    <div class="absolute inset-0 opacity-20" style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Copy --}}
            <div class="space-y-8">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 text-amber-400 text-sm font-semibold tracking-wide uppercase">
                        <span class="w-8 h-px bg-amber-400"></span>
                        Cocina con alma
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[1.1]">
                        Recetas que<br>
                        <span class="text-amber-400">cuentan una</span><br>
                        historia
                    </h1>
                    <p class="text-lg text-zinc-300 leading-relaxed max-w-md">
                        De las costas del Ecuador hasta la Toscana. Recetas auténticas con ingredientes reales, tiempos exactos y los secretos que marcan la diferencia.
                    </p>
                </div>

                {{-- Search bar --}}
                <form action="{{ route('recipes.index') }}" method="GET" class="relative max-w-md">
                    <input type="search"
                           name="search"
                           placeholder="¿Qué quieres cocinar hoy?"
                           class="w-full pl-5 pr-14 py-4 rounded-2xl text-zinc-800 text-base font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400 shadow-xl">
                    <button type="submit" class="absolute right-2 top-2 bottom-2 px-4 bg-amber-500 hover:bg-amber-400 text-white rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>

                {{-- Quick filter tags --}}
                @php
                    $quickFilters = [
                        ['Rápidas (≤30 min)', route('recipes.index') . '?tiempo=rapido'],
                        ['Ecuatorianas',       route('category.show', 'recetas-ecuatorianas')],
                        ['Sin Gluten',         route('recipes.index') . '?tag=sin-gluten'],
                        ['Postres',            route('category.show', 'postres')],
                        ['Vegetarianas',       route('recipes.index') . '?tag=vegetariano'],
                    ];
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($quickFilters as [$label, $url])
                    <a href="{{ $url }}"
                       class="px-3 py-1.5 bg-white/10 hover:bg-amber-500 text-white text-sm rounded-full border border-white/20 hover:border-amber-500 transition-all">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Right: Hero recipe cards --}}
            <div class="hidden lg:grid grid-cols-2 gap-4">
                @forelse($heroRecipes as $i => $heroRecipe)
                <a href="{{ route('recipe.show', $heroRecipe->slug) }}"
                   class="{{ $i === 0 ? 'col-span-2' : '' }} relative overflow-hidden rounded-2xl group
                          {{ $i === 0 ? 'aspect-[2/1]' : 'aspect-square' }} bg-zinc-700">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-200/20 to-orange-300/20 flex items-center justify-center">
                        <svg class="w-16 h-16 text-amber-400/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @if($heroRecipe->featured_image)
                    <img src="{{ $heroRecipe->featured_image }}" alt="{{ $heroRecipe->title }}"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <p class="text-xs font-semibold text-amber-400 uppercase tracking-wide mb-1">
                            {{ $heroRecipe->categories?->first()?->name ?? '' }}
                        </p>
                        <p class="text-white font-bold {{ $i === 0 ? 'text-lg' : 'text-sm' }} line-clamp-2">
                            {{ $heroRecipe->title }}
                        </p>
                        @if($i === 0)
                        <div class="flex items-center gap-3 mt-2 text-xs text-zinc-300">
                            @if($heroRecipe->prep_time_minutes + $heroRecipe->cook_time_minutes > 0)
                            <span>⏱ {{ $heroRecipe->prep_time_minutes + $heroRecipe->cook_time_minutes }} min</span>
                            @endif
                            @if($heroRecipe->difficulty)
                            <span>📊 {{ $heroRecipe->difficulty_label }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </a>
                @empty
                {{-- Placeholder when no recipes --}}
                <div class="col-span-2 aspect-[2/1] bg-zinc-800 rounded-2xl flex items-center justify-center">
                    <p class="text-zinc-500 text-sm">Las recetas aparecerán aquí</p>
                </div>
                @endforelse
            </div>

        </div>

        {{-- Stats bar --}}
        <div class="mt-12 grid grid-cols-3 gap-4 max-w-md lg:max-w-none lg:flex lg:gap-10">
            @foreach([['✦', '10+', 'Recetas auténticas'], ['✦', '5', 'Países representados'], ['✦', '100%', 'Hecho con amor']] as [$icon, $num, $label])
            <div class="text-center lg:text-left">
                <p class="text-2xl font-black text-white">{{ $num }}</p>
                <p class="text-xs text-zinc-400 mt-0.5">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     QUICK RECIPES — Para cuando tienes poco tiempo
══════════════════════════════════════════════════════════ --}}
@if(isset($quickRecipes) && $quickRecipes->count() > 0)
<section class="py-12 bg-amber-50 border-y border-amber-100" aria-label="Recetas rápidas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-zinc-900 text-lg">Listas en 30 minutos</h2>
                    <p class="text-xs text-zinc-500">Rápidas, fáciles y deliciosas</p>
                </div>
            </div>
            <div class="h-px bg-amber-200 flex-1"></div>
            <a href="{{ route('recipes.index') }}" class="text-sm font-semibold text-amber-600 hover:text-amber-700 shrink-0 transition-colors">
                Ver todas →
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($quickRecipes as $recipe)
            <a href="{{ route('recipe.show', $recipe->slug) }}"
               class="group flex items-center gap-3 bg-white rounded-2xl p-4 border border-amber-100 hover:border-amber-300 hover:shadow-md transition-all">
                <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-amber-100">
                    @if($recipe->featured_image)
                    <img src="{{ $recipe->featured_image }}" alt="{{ $recipe->title }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-zinc-800 group-hover:text-amber-600 transition-colors line-clamp-2 leading-snug">
                        {{ $recipe->title }}
                    </p>
                    <p class="text-xs text-amber-500 font-medium mt-1">
                        ⏱ {{ ($recipe->prep_time_minutes ?? 0) + ($recipe->cook_time_minutes ?? 0) }} min
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════
     CATEGORÍAS — Navega por tipo de cocina
══════════════════════════════════════════════════════════ --}}
@if(isset($featuredCategories) && $featuredCategories->count() > 0)
<section class="py-16 bg-white" aria-label="Categorías de recetas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-zinc-900">
                Explora por Cocina
            </h2>
            <p class="mt-2 text-zinc-500">Cada categoría tiene sus propios sabores e historias</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($featuredCategories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-800 to-zinc-900 aspect-[4/3] flex flex-col justify-end p-5 hover:shadow-xl transition-all hover:-translate-y-1">
                {{-- Background subtle pattern --}}
                @php
                    $catColors = [
                        'Recetas Ecuatorianas'     => 'from-yellow-600/60 to-yellow-900/80',
                        'Recetas Latinoamericanas' => 'from-red-600/60 to-red-900/80',
                        'Recetas Mediterráneas'    => 'from-blue-600/60 to-blue-900/80',
                        'Desayunos'                => 'from-orange-500/60 to-orange-900/80',
                        'Postres'                  => 'from-pink-500/60 to-pink-900/80',
                        'Ensaladas'                => 'from-green-600/60 to-green-900/80',
                        'Bebidas'                  => 'from-cyan-600/60 to-cyan-900/80',
                    ];
                    $catEmoji = [
                        'Recetas Ecuatorianas'     => '🇪🇨',
                        'Recetas Latinoamericanas' => '🌮',
                        'Recetas Mediterráneas'    => '🫒',
                        'Desayunos'                => '☀️',
                        'Postres'                  => '🍰',
                        'Ensaladas'                => '🥗',
                        'Bebidas'                  => '🥤',
                    ];
                    $gradient = $catColors[$cat->name] ?? 'from-amber-600/60 to-amber-900/80';
                    $emoji = $catEmoji[$cat->name] ?? '🍽️';
                @endphp
                <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} group-hover:opacity-90 transition-opacity"></div>

                @if($cat->image)
                <img src="{{ $cat->image }}" alt="{{ $cat->name }}"
                     class="absolute inset-0 w-full h-full object-cover opacity-30 group-hover:opacity-40 group-hover:scale-105 transition-all duration-500 mix-blend-overlay">
                @endif

                <div class="relative">
                    <span class="text-3xl">{{ $emoji }}</span>
                    <p class="font-bold text-white text-base leading-snug mt-2">{{ $cat->name }}</p>
                    <p class="text-xs text-white/60 mt-0.5">{{ $cat->recipes_count }} {{ $cat->recipes_count === 1 ? 'receta' : 'recetas' }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════
     FEATURED RECIPES — Las más populares
══════════════════════════════════════════════════════════ --}}
@if(isset($featuredRecipes) && $featuredRecipes->count() > 0)
<section class="py-16 bg-zinc-50" aria-label="Recetas más populares">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-amber-500 text-sm font-semibold uppercase tracking-wider mb-2">Lo más visto</p>
                <h2 class="text-3xl font-bold text-zinc-900">Recetas Favoritas</h2>
                <p class="mt-1 text-zinc-500">Las más guardadas y cocinadas de la comunidad</p>
            </div>
            <a href="{{ route('recipes.index') }}"
               class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-zinc-700 hover:text-amber-600 transition-colors border border-zinc-200 hover:border-amber-300 px-4 py-2 rounded-xl bg-white hover:bg-amber-50">
                Ver todas
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredRecipes as $recipe)
            <x-public.recipe-card :recipe="$recipe"/>
            @endforeach
        </div>

        <div class="sm:hidden mt-6 text-center">
            <a href="{{ route('recipes.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600">
                Ver todas las recetas →
            </a>
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════
     TIPS DEL CHEF — Contenido de valor para retención
══════════════════════════════════════════════════════════ --}}
<section class="py-16 bg-white" aria-label="Consejos de cocina">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <p class="text-amber-500 text-sm font-semibold uppercase tracking-wider mb-2">Aprende más</p>
            <h2 class="text-3xl font-bold text-zinc-900">Consejos del Chef</h2>
            <p class="mt-2 text-zinc-500">Técnicas simples que transforman tus platos</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['🔪', 'Corte en Juliana', 'Verduras en tiras finas y uniformes. Clave para sofritos perfectos y cocciones rápidas. Mantén el cuchillo perpendicular y usa el nudillo como guía.'],
                ['🌡️', 'El Punto del Aceite', 'Pon un palillo en el aceite: si burbujea suavemente, está listo. Demasiado humo significa que se quemó y hay que empezar de nuevo.'],
                ['🧂', 'Salar la Pasta', 'El agua de pasta debe estar salada "como el mar" (1% de sal). Esto sazona la pasta desde adentro, algo que no se puede corregir después.'],
                ['🍋', 'Acidez para Equilibrar', 'Un chorrito de limón o vinagre al final de cualquier guiso potencia todos los sabores. Es el secreto que diferencia lo bueno de lo extraordinario.'],
                ['🔥', 'La Sartén Caliente', 'Para sellar carnes, la sartén debe estar muy caliente antes de agregar el ingrediente. El sonido al entrar debe ser un fuerte "ssss".'],
                ['🌿', 'Hierbas: cuándo agregarlas', 'Las hierbas duras (romero, tomillo) van al inicio de la cocción. Las delicadas (cilantro, albahaca, perejil) siempre al final para preservar su aroma.'],
            ] as [$emoji, $title, $text])
            <div class="p-6 bg-zinc-50 rounded-2xl border border-zinc-100 hover:border-amber-200 hover:bg-amber-50/30 transition-all group">
                <span class="text-3xl">{{ $emoji }}</span>
                <h3 class="font-bold text-zinc-900 mt-3 mb-2 group-hover:text-amber-700 transition-colors">{{ $title }}</h3>
                <p class="text-sm text-zinc-600 leading-relaxed">{{ $text }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     LATEST RECIPES — Recién publicadas
══════════════════════════════════════════════════════════ --}}
@if(isset($latestRecipes) && $latestRecipes->count() > 0)
<section class="py-16 bg-zinc-50 border-t border-zinc-100" aria-label="Últimas recetas publicadas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-amber-500 text-sm font-semibold uppercase tracking-wider mb-2">Recién publicadas</p>
                <h2 class="text-3xl font-bold text-zinc-900">Últimas Recetas</h2>
            </div>
            <a href="{{ route('recipes.index') }}" class="text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors">
                Ver todas →
            </a>
        </div>

        {{-- Lista horizontal para las últimas 3 --}}
        <div class="space-y-4">
            @foreach($latestRecipes as $recipe)
            <a href="{{ route('recipe.show', $recipe->slug) }}"
               class="group flex items-center gap-6 bg-white rounded-2xl p-4 border border-zinc-100 hover:border-amber-200 hover:shadow-md transition-all">

                {{-- Imagen --}}
                <div class="w-28 h-20 rounded-xl overflow-hidden shrink-0 bg-zinc-100">
                    @if($recipe->featured_image)
                    <img src="{{ $recipe->featured_image }}" alt="{{ $recipe->title }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-amber-50 to-orange-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    @if($recipe->categories?->first())
                    <span class="text-xs font-semibold text-amber-500 uppercase tracking-wide">
                        {{ $recipe->categories->first()->name }}
                    </span>
                    @endif
                    <h3 class="font-bold text-zinc-900 group-hover:text-amber-600 transition-colors mt-0.5 line-clamp-1">
                        {{ $recipe->title }}
                    </h3>
                    <p class="text-sm text-zinc-500 line-clamp-1 mt-1">{{ $recipe->subtitle }}</p>
                    <div class="flex items-center gap-4 mt-2 text-xs text-zinc-400">
                        @php $t = ($recipe->prep_time_minutes ?? 0) + ($recipe->cook_time_minutes ?? 0); @endphp
                        @if($t > 0)
                        <span>⏱ {{ $t }} min</span>
                        @endif
                        @if($recipe->servings)
                        <span>👥 {{ $recipe->servings }} porciones</span>
                        @endif
                        @if($recipe->schema_rating_value)
                        <span>⭐ {{ number_format($recipe->schema_rating_value, 1) }}</span>
                        @endif
                        <span class="ml-auto text-zinc-300">{{ $recipe->published_at?->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Arrow --}}
                <svg class="w-5 h-5 text-zinc-300 group-hover:text-amber-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════
     BOOK FEATURED — Tienda Amazon
══════════════════════════════════════════════════════════ --}}
@if(isset($featuredBook) && $featuredBook)
<section class="py-16 bg-gradient-to-br from-zinc-900 to-zinc-800" aria-label="Libro recomendado">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-10">
            @if($featuredBook->cover_image_url)
            <div class="shrink-0">
                <img src="{{ $featuredBook->cover_image_url }}" alt="{{ $featuredBook->title }}"
                     class="w-40 lg:w-52 rounded-2xl shadow-2xl shadow-black/50">
            </div>
            @endif
            <div class="flex-1 text-center lg:text-left">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/20 text-amber-400 rounded-full text-xs font-semibold uppercase tracking-wider mb-4">
                    📚 Libro Recomendado
                </span>
                <h2 class="text-2xl lg:text-3xl font-bold text-white mb-2">{{ $featuredBook->title }}</h2>
                @if($featuredBook->author)
                <p class="text-zinc-400 text-sm mb-4">Por <strong class="text-zinc-200">{{ $featuredBook->author }}</strong></p>
                @endif
                @if($featuredBook->description)
                <p class="text-zinc-300 leading-relaxed mb-6">{{ Str::limit($featuredBook->description, 200) }}</p>
                @endif
                <a href="{{ route('store.show', $featuredBook->id) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-white font-bold rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5">
                    Ver en Amazon
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════
     CTA FINAL — Newsletter / Invitación a explorar
══════════════════════════════════════════════════════════ --}}
<section class="py-16 bg-amber-500" aria-label="Explorar todas las recetas">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-3xl font-black text-white mb-3">
            ¿Listo para cocinar algo increíble?
        </h2>
        <p class="text-amber-100 text-lg mb-8">
            Más de 10 recetas auténticas con ingredientes reales, tiempos exactos y todos los secretos que necesitas.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('recipes.index') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-amber-600 font-bold rounded-xl hover:bg-amber-50 transition-all hover:shadow-lg hover:-translate-y-0.5">
                Ver todas las recetas
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="{{ route('category.show', 'recetas-ecuatorianas') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition-all">
                🇪🇨 Cocina Ecuatoriana
            </a>
        </div>
    </div>
</section>

@endsection
