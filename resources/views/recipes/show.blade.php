@extends('layouts.app')

@section('seo_head')
<title>{{ $recipe->seo_title ?? $recipe->title }} | Marvin Baptista</title>
<meta name="description" content="{{ $recipe->seo_description ?? Str::limit(strip_tags($recipe->description ?? ''), 160) }}">
<link rel="canonical" href="{{ route('recipe.show', $recipe->slug) }}">

{{-- Open Graph --}}
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $recipe->seo_title ?? $recipe->title }}">
<meta property="og:description" content="{{ $recipe->seo_description ?? Str::limit(strip_tags($recipe->description ?? ''), 160) }}">
<meta property="og:url" content="{{ route('recipe.show', $recipe->slug) }}">
@if($recipe->featured_image)
<meta property="og:image" content="{{ $recipe->featured_image }}">
<meta property="og:image:alt" content="{{ $recipe->image_alt ?? $recipe->title }}">
@endif
<meta property="og:site_name" content="Marvin Baptista">
<meta property="article:published_time" content="{{ $recipe->published_at?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $recipe->updated_at?->toIso8601String() }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $recipe->seo_title ?? $recipe->title }}">
<meta name="twitter:description" content="{{ $recipe->seo_description ?? Str::limit(strip_tags($recipe->description ?? ''), 160) }}">
@if($recipe->featured_image)
<meta name="twitter:image" content="{{ $recipe->featured_image }}">
@endif
@endsection

@section('schema_org')
@php
    $prepMin  = $recipe->prep_time_minutes ?? 0;
    $cookMin  = $recipe->cook_time_minutes ?? 0;
    $totalMin = $prepMin + $cookMin;

    $recipeSchema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Recipe',
        'name'        => $recipe->title,
        'description' => Str::limit(strip_tags($recipe->description ?? ''), 300),
        'url'         => route('recipe.show', $recipe->slug),
        'author'      => ['@type' => 'Person', 'name' => 'Marvin Baptista', 'url' => route('home')],
        'datePublished' => $recipe->published_at?->toIso8601String() ?? $recipe->created_at?->toIso8601String(),
        'dateModified'  => $recipe->updated_at?->toIso8601String(),
        'recipeCategory'=> $recipe->categories?->first()?->name ?? '',
        'recipeCuisine' => $recipe->origin_country ?? 'Latinoamericana',
    ];
    if ($recipe->featured_image)  $recipeSchema['image']       = $recipe->featured_image;
    if ($prepMin > 0)             $recipeSchema['prepTime']    = "PT{$prepMin}M";
    if ($cookMin > 0)             $recipeSchema['cookTime']    = "PT{$cookMin}M";
    if ($totalMin > 0)            $recipeSchema['totalTime']   = "PT{$totalMin}M";
    if ($recipe->servings)        $recipeSchema['recipeYield'] = $recipe->servings . ' porciones';
    if ($recipe->schema_rating_value) {
        $recipeSchema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $recipe->schema_rating_value,
            'ratingCount' => (string) ($recipe->schema_rating_count ?? 1),
            'bestRating'  => '5',
            'worstRating' => '1',
        ];
    }
    if ($recipe->ingredients->count()) {
        $recipeSchema['recipeIngredient'] = $recipe->ingredients->take(30)->map(
            fn($i) => trim("{$i->amount} {$i->unit} {$i->ingredient_name}")
        )->values()->toArray();
    }
    if ($recipe->steps->count()) {
        $recipeSchema['recipeInstructions'] = $recipe->steps->map(fn($s) => [
            '@type' => 'HowToStep',
            'name'  => $s->title ?? 'Paso ' . $s->step_number,
            'text'  => strip_tags($s->description ?? ''),
        ])->values()->toArray();
    }

    $breadcrumbItems = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio',   'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Recetas',  'item' => route('recipes.index')],
    ];
    $pos = 3;
    if ($recipe->categories?->first()) {
        $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $recipe->categories->first()->name, 'item' => route('category.show', $recipe->categories->first()->slug)];
    }
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $pos, 'name' => $recipe->title, 'item' => route('recipe.show', $recipe->slug)];

    $breadcrumbSchema = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumbItems];

    $faqSchema = null;
    if ($recipe->faqs->count()) {
        $faqSchema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $recipe->faqs->map(fn($f) => [
                '@type'          => 'Question',
                'name'           => $f->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f->answer ?? '')],
            ])->values()->toArray(),
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($recipeSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@if($faqSchema)
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif
@endsection

@section('content')
<article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-6 flex-wrap" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">Inicio</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('recipes.index') }}" class="hover:text-zinc-700 transition-colors">Recetas</a>
        @if($recipe->categories?->first())
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('category.show', $recipe->categories->first()->slug) }}" class="hover:text-zinc-700 transition-colors">{{ $recipe->categories->first()->name }}</a>
        @endif
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-medium" aria-current="page">{{ $recipe->title }}</span>
    </nav>

    <div class="grid lg:grid-cols-3 gap-10">

        {{-- ===== MAIN COLUMN ===== --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Title --}}
            <header>
                <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900 leading-tight"
                    style="font-family: 'Playfair Display', serif;">
                    {{ $recipe->title }}
                </h1>
                @if($recipe->subtitle)
                <p class="mt-2 text-lg text-zinc-500">{{ $recipe->subtitle }}</p>
                @endif
            </header>

            {{-- Featured Image --}}
            @if($recipe->featured_image)
            <div class="rounded-3xl overflow-hidden aspect-video bg-zinc-100">
                <img src="{{ $recipe->featured_image }}"
                     alt="{{ $recipe->image_alt ?? $recipe->title }}"
                     loading="lazy"
                     decoding="async"
                     class="w-full h-full object-cover"
                     width="800" height="450">
            </div>
            @endif

            {{-- Meta Bar --}}
            <div class="flex flex-wrap items-center gap-5 py-5 border-t border-b border-zinc-100">
                @php
                    $totalTime = ($recipe->prep_time_minutes ?? 0) + ($recipe->cook_time_minutes ?? 0);
                @endphp
                @if($totalTime > 0)
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-zinc-800">{{ $totalTime }} min</p>
                        <p class="text-xs text-zinc-400">Tiempo total</p>
                    </div>
                </div>
                @endif
                @if($recipe->prep_time_minutes)
                <div class="text-sm">
                    <p class="font-semibold text-zinc-800">{{ $recipe->prep_time_minutes }} min</p>
                    <p class="text-xs text-zinc-400">Preparación</p>
                </div>
                @endif
                @if($recipe->cook_time_minutes)
                <div class="text-sm">
                    <p class="font-semibold text-zinc-800">{{ $recipe->cook_time_minutes }} min</p>
                    <p class="text-xs text-zinc-400">Cocción</p>
                </div>
                @endif
                @if($recipe->servings)
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-zinc-800">{{ $recipe->servings }}</p>
                        <p class="text-xs text-zinc-400">Porciones</p>
                    </div>
                </div>
                @endif
                @if($recipe->difficulty)
                @php
                    $diffClass = match($recipe->difficulty) {
                        'facil'   => 'bg-emerald-100 text-emerald-700',
                        'media'   => 'bg-yellow-100 text-yellow-700',
                        'dificil' => 'bg-red-100 text-red-700',
                        default   => 'bg-zinc-100 text-zinc-600',
                    };
                    $diffLabel = match($recipe->difficulty) {
                        'facil'   => 'Fácil', 'media' => 'Media', 'dificil' => 'Difícil', default => $recipe->difficulty,
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $diffClass }}">
                    {{ $diffLabel }}
                </span>
                @endif
                @if($recipe->schema_rating_value)
                <div class="flex items-center gap-1.5 ml-auto">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= round($recipe->schema_rating_value) ? 'text-amber-400 fill-current' : 'text-zinc-200 fill-current' }}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                    <span class="text-sm font-semibold text-zinc-700">{{ number_format($recipe->schema_rating_value, 1) }}</span>
                    @if($recipe->schema_rating_count)
                    <span class="text-sm text-zinc-400">({{ $recipe->schema_rating_count }})</span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Share / Print --}}
            <div class="flex items-center gap-3">
                <span class="text-sm text-zinc-400">Compartir:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('recipe.show', $recipe->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" aria-label="Facebook">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($recipe->title) }}&url={{ urlencode(route('recipe.show', $recipe->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="p-2 bg-zinc-900 hover:bg-zinc-700 text-white rounded-lg transition-colors" aria-label="X / Twitter">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://wa.me/?text={{ urlencode($recipe->title.' - '.route('recipe.show', $recipe->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="p-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors" aria-label="WhatsApp">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                <button onclick="window.print()"
                        class="ml-2 p-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-600 rounded-lg transition-colors" aria-label="Imprimir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>
            </div>

            {{-- Jump Links --}}
            <div class="flex items-center gap-4 flex-wrap">
                <a href="#ingredientes"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Ir a Ingredientes
                </a>
                <a href="#preparacion"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Ir a Preparación
                </a>
            </div>

            {{-- Story / Intro --}}
            @if($recipe->story)
            <section class="prose prose-zinc max-w-none leading-relaxed">
                {!! $recipe->story !!}
            </section>
            @elseif($recipe->description)
            <section class="prose prose-zinc max-w-none leading-relaxed">
                {!! $recipe->description !!}
            </section>
            @endif

            {{-- ===== INGREDIENTS ===== --}}
            <section id="ingredientes" class="scroll-mt-20">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-zinc-900" style="font-family: 'Playfair Display', serif;">
                        Ingredientes
                    </h2>
                    {{-- Serving Adjuster --}}
                    @if($recipe->servings)
                    <div class="flex items-center gap-2 bg-zinc-100 rounded-xl p-1"
                         id="servings-adjuster"
                         data-base-servings="{{ $recipe->servings }}">
                        <button type="button" id="servings-minus"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-zinc-600 hover:text-zinc-900 font-bold transition-colors"
                                aria-label="Reducir porciones">−</button>
                        <span class="text-sm font-semibold text-zinc-700 min-w-[80px] text-center">
                            <span id="servings-display">{{ $recipe->servings }}</span> porciones
                        </span>
                        <button type="button" id="servings-plus"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-zinc-600 hover:text-zinc-900 font-bold transition-colors"
                                aria-label="Aumentar porciones">+</button>
                    </div>
                    @endif
                </div>

                <div class="space-y-6">
                    @if(is_array($recipe->ingredients) && count($recipe->ingredients) > 0)
                    @php
                        $groups = collect($recipe->ingredients)->groupBy('group');
                    @endphp
                    @foreach($groups as $groupName => $ingredients)
                    @if($groupName && $groups->count() > 1)
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-3">{{ $groupName }}</h3>
                    @else
                    <div>
                    @endif
                        <ul class="space-y-2">
                            @foreach($ingredients as $ingredient)
                            <li class="ingredient-row flex items-start gap-3 py-2 px-3 rounded-xl hover:bg-zinc-50 transition-colors group">
                                <label class="flex items-center gap-3 cursor-pointer w-full">
                                    <input type="checkbox"
                                           class="ingredient-checkbox w-5 h-5 rounded border-zinc-300 text-amber-500 focus:ring-amber-400 shrink-0"
                                           aria-label="Marcar {{ $ingredient['name'] ?? '' }}">
                                    <div class="flex items-baseline gap-2 flex-1">
                                        @if(isset($ingredient['amount']) && $ingredient['amount'])
                                        <span class="ingredient-amount font-semibold text-zinc-900 shrink-0"
                                              data-base-amount="{{ $ingredient['amount'] }}">
                                            {{ $ingredient['amount'] }}
                                        </span>
                                        @endif
                                        @if(isset($ingredient['unit']) && $ingredient['unit'])
                                        <span class="text-zinc-600 shrink-0">{{ $ingredient['unit'] }}</span>
                                        @endif
                                        <span class="text-zinc-800">{{ $ingredient['name'] ?? '' }}</span>
                                        @if(isset($ingredient['notes']) && $ingredient['notes'])
                                        <span class="text-sm text-zinc-400 italic">({{ $ingredient['notes'] }})</span>
                                        @endif
                                    </div>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                    @else
                    <p class="text-zinc-500 italic">No se han añadido ingredientes.</p>
                    @endif
                </div>
            </section>

            {{-- ===== STEPS ===== --}}
            <section id="preparacion" class="scroll-mt-20">
                <h2 class="text-2xl font-bold text-zinc-900 mb-6" style="font-family: 'Playfair Display', serif;">
                    Preparación
                </h2>
                <ol class="space-y-6">
                    @if(is_array($recipe->steps) && count($recipe->steps) > 0)
                    @foreach($recipe->steps as $index => $step)
                    <li class="flex gap-5">
                        <div class="shrink-0 w-9 h-9 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm mt-1">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 space-y-3">
                            @if(isset($step['title']) && $step['title'])
                            <h3 class="font-bold text-zinc-900 text-lg">{{ $step['title'] }}</h3>
                            @endif
                            <div class="prose prose-zinc max-w-none leading-relaxed">
                                {!! $step['description'] ?? '' !!}
                            </div>
                            @if(isset($step['duration_minutes']) && $step['duration_minutes'])
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        class="step-timer-btn inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-sm font-medium transition-colors"
                                        data-duration="{{ $step['duration_minutes'] * 60 }}"
                                        aria-label="Iniciar temporizador de {{ $step['duration_minutes'] }} minutos">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $step['duration_minutes'] }} min — Iniciar temporizador
                                </button>
                            </div>
                            @endif
                        </div>
                    </li>
                    @endforeach
                    @else
                    <p class="text-zinc-500 italic">No se han añadido pasos.</p>
                    @endif
                </ol>
            </section>

            {{-- ===== TIPS & SECRETS ===== --}}
            @if($recipe->tips_secrets)
            <section class="scroll-mt-20">
                <details class="bg-amber-50 border border-amber-100 rounded-2xl overflow-hidden group">
                    <summary class="flex items-center justify-between p-5 cursor-pointer list-none">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-200 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <h2 class="font-bold text-amber-900">Trucos y Secretos del Chef</h2>
                        </div>
                        <svg class="w-5 h-5 text-amber-600 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-5 pb-5 prose prose-amber max-w-none">
                        {!! $recipe->tips_secrets !!}
                    </div>
                </details>
            </section>
            @endif

            {{-- ===== FAQ ===== --}}
            @if($faqList->count() > 0)
            <section id="faq" class="scroll-mt-20" aria-label="Preguntas frecuentes">
                <h2 class="text-2xl font-bold text-zinc-900 mb-5" style="font-family: 'Playfair Display', serif;">
                    Preguntas Frecuentes
                </h2>
                <div class="space-y-3">
                    @foreach($faqList as $faq)
                    <details class="bg-white border border-zinc-200 rounded-2xl overflow-hidden group"
                             itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <summary class="flex items-center justify-between px-5 py-4 cursor-pointer list-none">
                            <span class="font-semibold text-zinc-900 pr-4" itemprop="name">
                                {{ $faq['question'] ?? '' }}
                            </span>
                            <svg class="w-5 h-5 text-zinc-400 shrink-0 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="px-5 pb-5 text-zinc-600 leading-relaxed prose prose-zinc max-w-none"
                             itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                            <div itemprop="text">{!! $faq['answer'] ?? '' !!}</div>
                        </div>
                    </details>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- ===== AMAZON BOOKS ===== --}}
            @if($recipe->books?->count() > 0)
            <section id="libros" class="scroll-mt-20" aria-label="Libros relacionados">
                <h2 class="text-2xl font-bold text-zinc-900 mb-5" style="font-family: 'Playfair Display', serif;">
                    Libros Relacionados
                </h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach($recipe->books as $book)
                    <div class="flex gap-4 p-4 bg-zinc-50 border border-zinc-100 rounded-2xl">
                        @if($book->cover_image_url)
                        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}"
                             class="w-16 h-22 object-cover rounded-lg shadow-sm shrink-0" style="height:88px;">
                        @endif
                        <div class="flex-1 min-w-0 space-y-2">
                            <p class="font-semibold text-zinc-900 leading-snug line-clamp-2">{{ $book->title }}</p>
                            @if($book->author)
                            <p class="text-sm text-zinc-500">{{ $book->author }}</p>
                            @endif
                            <a href="{{ $book->amazon_url_mx ?? $book->amazon_url_us ?? '#' }}"
                               target="_blank" rel="noopener noreferrer sponsored"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-white rounded-lg text-xs font-semibold transition-colors">
                                Ver en Amazon
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-zinc-400 mt-3 italic">
                    * Como afiliado de Amazon, recibo una pequeña comisión si compras a través de estos enlaces, sin costo adicional para ti.
                </p>
            </section>
            @endif

        </div>

        {{-- ===== SIDEBAR ===== --}}
        <aside class="hidden lg:block space-y-6 sticky top-24 self-start">

            {{-- Quick Summary Card --}}
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 space-y-4">
                <h3 class="font-bold text-amber-900">Resumen Rápido</h3>
                <dl class="space-y-3">
                    @if($recipe->prep_time_minutes)
                    <div class="flex justify-between text-sm">
                        <dt class="text-zinc-500">Preparación</dt>
                        <dd class="font-semibold text-zinc-800">{{ $recipe->prep_time_minutes }} min</dd>
                    </div>
                    @endif
                    @if($recipe->cook_time_minutes)
                    <div class="flex justify-between text-sm">
                        <dt class="text-zinc-500">Cocción</dt>
                        <dd class="font-semibold text-zinc-800">{{ $recipe->cook_time_minutes }} min</dd>
                    </div>
                    @endif
                    @php $totalTime = ($recipe->prep_time_minutes ?? 0) + ($recipe->cook_time_minutes ?? 0); @endphp
                    @if($totalTime > 0)
                    <div class="flex justify-between text-sm border-t border-amber-200 pt-3">
                        <dt class="font-semibold text-zinc-700">Total</dt>
                        <dd class="font-bold text-zinc-900">{{ $totalTime }} min</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Related Recipes --}}
            @if(isset($relatedRecipes) && $relatedRecipes->count() > 0)
            <div class="bg-white border border-zinc-100 rounded-2xl p-5 space-y-4">
                <h3 class="font-bold text-zinc-900">Recetas Relacionadas</h3>
                <div class="space-y-3">
                    @foreach($relatedRecipes as $related)
                    <a href="{{ route('recipe.show', $related->slug) }}"
                       class="flex items-center gap-3 group">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-zinc-100 shrink-0">
                            @if($related->featured_image)
                            <img src="{{ $related->featured_image }}" alt="{{ $related->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-zinc-900 group-hover:text-amber-600 transition-colors line-clamp-2 leading-snug">
                                {{ $related->title }}
                            </p>
                            @php $rTime = ($related->prep_time_minutes ?? 0) + ($related->cook_time_minutes ?? 0); @endphp
                            @if($rTime > 0)
                            <p class="text-xs text-zinc-400 mt-0.5">{{ $rTime }} min</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </aside>
    </div>

    {{-- Related Recipes (Mobile) --}}
    @if(isset($relatedRecipes) && $relatedRecipes->count() > 0)
    <section class="lg:hidden mt-12" aria-label="Recetas relacionadas">
        <h2 class="text-2xl font-bold text-zinc-900 mb-5" style="font-family: 'Playfair Display', serif;">
            Recetas Relacionadas
        </h2>
        <div class="grid sm:grid-cols-3 gap-5">
            @foreach($relatedRecipes->take(3) as $recipe)
            <x-public.recipe-card :recipe="$recipe"/>
            @endforeach
        </div>
    </section>
    @endif

</article>
@endsection
