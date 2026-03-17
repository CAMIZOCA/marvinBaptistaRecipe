@extends('admin.layouts.app')

@section('page-title', isset($recipe) ? 'Editar Receta' : 'Nueva Receta')

@section('content')
@php
    $isEdit     = isset($recipe) && $recipe->exists;
    $formAction = $isEdit ? route('admin.recipes.update', $recipe) : route('admin.recipes.store');
    $enhanceUrl = $isEdit ? route('admin.recipes.ai.enhance', $recipe) : null;
    $fieldUrl   = $isEdit ? route('admin.recipes.ai.field',   $recipe) : null;
    $saveAiUrl  = $isEdit ? route('admin.recipes.ai.save',    $recipe) : null;
    $promptUrl  = $isEdit ? route('admin.recipes.ai.prompt',  $recipe) : null;
@endphp

<div class="p-6 space-y-4"
     data-enhance-url="{{ $enhanceUrl }}"
     data-field-url="{{ $fieldUrl }}"
     data-save-url="{{ $saveAiUrl }}"
     data-prompt-url="{{ $promptUrl }}"
     data-analyzer-type="recipe">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.recipes.index') }}"
               class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-zinc-100">
                    {{ $isEdit ? 'Editar: '.$recipe->title : 'Nueva Receta' }}
                </h1>
                @if($isEdit)
                <p class="text-zinc-500 text-xs mt-0.5">Última actualización: {{ $recipe->updated_at->diffForHumans() }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($isEdit)
            <a href="{{ route('recipe.show', $recipe->slug) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Ver
            </a>
            @endif
            <button type="submit" form="recipe-form"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-900/50 border border-red-700 rounded-xl text-red-300 text-sm space-y-1">
        @foreach($errors->all() as $error)
        <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <div class="flex overflow-x-auto border-b border-zinc-700 scrollbar-none" role="tablist">
            @foreach([
                ['tab' => 'content',       'label' => 'Contenido'],
                ['tab' => 'ingredients',   'label' => 'Ingredientes'],
                ['tab' => 'steps',         'label' => 'Preparación'],
                ['tab' => 'seo',           'label' => 'SEO'],
                ['tab' => 'faq',           'label' => 'FAQ'],
                ['tab' => 'books',         'label' => 'Libros'],
                ['tab' => 'settings',      'label' => 'Configuración'],
            ] as $t)
            <button type="button"
                    class="recipe-tab flex-shrink-0 px-5 py-3.5 text-sm font-medium transition-colors border-b-2 focus:outline-none"
                    data-tab="{{ $t['tab'] }}"
                    role="tab">
                {{ $t['label'] }}
            </button>
            @endforeach
        </div>

        {{-- FORM wraps everything --}}
        <form id="recipe-form" method="POST" action="{{ $formAction }}" class="min-h-[60vh]">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- ==================== TAB 1: CONTENT ==================== --}}
            <div data-tab-panel="content" class="tab-panel">
                <div class="flex flex-col xl:flex-row gap-0">
                    {{-- Main Content --}}
                    <div class="flex-1 p-6 space-y-5 border-r border-zinc-700">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Título *</label>
                            <input type="text" name="title" value="{{ old('title', $recipe->title ?? '') }}"
                                   placeholder="Título de la receta..."
                                   class="w-full px-4 py-3 bg-zinc-700 border border-zinc-600 text-zinc-100 text-xl font-semibold rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Subtítulo</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle', $recipe->subtitle ?? '') }}"
                                   placeholder="Un subtítulo descriptivo..."
                                   class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Descripción</label>
                            <input id="description" type="hidden" name="description" value="{{ old('description', $recipe->description ?? '') }}">
                            <trix-editor input="description" class="trix-content bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl min-h-32 focus:outline-none focus:ring-2 focus:ring-amber-500"></trix-editor>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">Historia / Introducción</label>
                                <button type="button"
                                        class="html-preview-toggle text-xs px-2.5 py-1 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg border border-zinc-600 transition-colors"
                                        data-target="story">Vista previa</button>
                            </div>
                            <textarea id="story" name="story"
                                      class="html-raw-editor w-full px-4 py-3 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl min-h-40 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono text-xs leading-relaxed resize-y"
                                      placeholder="HTML de la historia / introducción..."
                                      spellcheck="false">{{ old('story', $recipe->story ?? '') }}</textarea>
                            <div id="story-preview"
                                 class="html-preview-panel hidden mt-2 px-5 py-4 bg-zinc-900 border border-zinc-600 rounded-xl overflow-auto max-h-96"></div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">Trucos y Secretos</label>
                                <button type="button"
                                        class="html-preview-toggle text-xs px-2.5 py-1 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg border border-zinc-600 transition-colors"
                                        data-target="tips_secrets">Vista previa</button>
                            </div>
                            <textarea id="tips_secrets" name="tips_secrets"
                                      class="html-raw-editor w-full px-4 py-3 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl min-h-32 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono text-xs leading-relaxed resize-y"
                                      placeholder="HTML de trucos y secretos..."
                                      spellcheck="false">{{ old('tips_secrets', $recipe->tips_secrets ?? '') }}</textarea>
                            <div id="tips_secrets-preview"
                                 class="html-preview-panel hidden mt-2 px-5 py-4 bg-zinc-900 border border-zinc-600 rounded-xl overflow-auto max-h-96"></div>
                        </div>

                        {{-- Times & Servings Row --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Prep (min)</label>
                                <input type="number" name="prep_time_minutes" value="{{ old('prep_time_minutes', $recipe->prep_time_minutes ?? '') }}" min="0"
                                       class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Cocción (min)</label>
                                <input type="number" name="cook_time_minutes" value="{{ old('cook_time_minutes', $recipe->cook_time_minutes ?? '') }}" min="0"
                                       class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Porciones</label>
                                <input type="number" name="servings" value="{{ old('servings', $recipe->servings ?? '') }}" min="1"
                                       class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Dificultad</label>
                                <select name="difficulty"
                                        class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">—</option>
                                    <option value="easy" {{ old('difficulty', $recipe->difficulty ?? '') === 'easy' ? 'selected' : '' }}>Fácil</option>
                                    <option value="medium" {{ old('difficulty', $recipe->difficulty ?? '') === 'medium' ? 'selected' : '' }}>Media</option>
                                    <option value="hard" {{ old('difficulty', $recipe->difficulty ?? '') === 'hard' ? 'selected' : '' }}>Difícil</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT SIDEBAR --}}
                    <div class="xl:w-80 p-6 space-y-5 bg-zinc-800/50">

                        {{-- SEO & Content Analyzer --}}
                        <div id="seo-analyzer-panel" data-type="recipe" class="rounded-xl border border-zinc-600 overflow-hidden">
                            <div id="seo-panel-header"
                                 class="px-4 py-3 flex items-center gap-2.5 bg-zinc-700/50 cursor-pointer select-none hover:bg-zinc-700/80 transition-colors">
                                <div id="seo-traffic-light" class="w-3.5 h-3.5 rounded-full shrink-0 bg-zinc-600 transition-colors duration-300"></div>
                                <span class="text-sm font-semibold text-zinc-200 flex-1">Análisis de contenido</span>
                                <span id="seo-score-badge" class="text-xs font-bold px-2 py-0.5 rounded-full bg-zinc-600 text-zinc-400">—</span>
                                <svg id="seo-chevron" class="w-4 h-4 text-zinc-500 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            <div id="seo-checks-list" class="p-3 space-y-0.5 bg-zinc-800/60">
                                <p class="text-xs text-zinc-500 py-2 text-center">Analizando…</p>
                            </div>
                        </div>

                        {{-- Publish Card --}}
                        <div class="bg-zinc-700/50 rounded-xl p-4 border border-zinc-600 space-y-3">
                            <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Publicación</h3>
                            <div class="flex items-center justify-between">
                                <label class="text-sm text-zinc-300">Publicada</label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="is_published" value="0">
                                    <input type="checkbox" name="is_published" value="1"
                                           {{ old('is_published', $recipe->is_published ?? false) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-zinc-600 peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </div>
                            <div>
                                <label class="block text-xs text-zinc-400 mb-1">Fecha de publicación</label>
                                <input type="datetime-local" name="published_at"
                                       value="{{ old('published_at', isset($recipe->published_at) ? $recipe->published_at?->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>

                        {{-- Featured Image --}}
                        <div class="bg-zinc-700/50 rounded-xl p-4 border border-zinc-600 space-y-3">
                            <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Imagen Destacada</h3>
                            @if(isset($recipe->featured_image) && $recipe->featured_image)
                            <img src="{{ $recipe->featured_image }}" alt="Preview"
                                 class="w-full h-32 object-cover rounded-lg">
                            @endif
                            <input type="text" name="featured_image"
                                   value="{{ old('featured_image', $recipe->featured_image ?? '') }}"
                                   placeholder="/storage/recipes/... o https://..."
                                   class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <div>
                                <label class="block text-xs text-zinc-400 mb-1">Alt text imagen</label>
                                <input type="text" name="image_alt"
                                       value="{{ old('image_alt', $recipe->image_alt ?? '') }}"
                                       placeholder="Descripción de la imagen..."
                                       class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>

                        {{-- Categories --}}
                        <div class="bg-zinc-700/50 rounded-xl p-4 border border-zinc-600 space-y-2">
                            <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Categorías</h3>
                            <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                                @foreach($categories ?? [] as $category)
                                <label class="flex items-center gap-2 py-1 cursor-pointer group">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                           {{ in_array($category->id, old('categories', $recipe->categories?->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                           class="rounded border-zinc-500 bg-zinc-700 text-amber-500 focus:ring-amber-500">
                                    <span class="text-sm text-zinc-300 group-hover:text-zinc-100">{{ $category->name }}</span>
                                </label>
                                @if($category->children ?? false)
                                    @foreach($category->children as $child)
                                    <label class="flex items-center gap-2 py-1 pl-5 cursor-pointer group">
                                        <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                               {{ in_array($child->id, old('categories', $recipe->categories?->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                               class="rounded border-zinc-500 bg-zinc-700 text-amber-500 focus:ring-amber-500">
                                        <span class="text-sm text-zinc-400 group-hover:text-zinc-200">{{ $child->name }}</span>
                                    </label>
                                    @endforeach
                                @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div class="bg-zinc-700/50 rounded-xl p-4 border border-zinc-600 space-y-2">
                            <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Etiquetas</h3>
                            <div class="flex flex-wrap gap-2 max-h-36 overflow-y-auto">
                                @foreach($tags ?? [] as $tag)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                           {{ in_array($tag->id, old('tags', $recipe->tags?->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-all
                                                 peer-checked:bg-amber-500/20 peer-checked:border-amber-500 peer-checked:text-amber-300
                                                 border-zinc-600 text-zinc-400 hover:border-zinc-500">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Origin --}}
                        <div class="bg-zinc-700/50 rounded-xl p-4 border border-zinc-600 space-y-3">
                            <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Origen</h3>
                            <div>
                                <label class="block text-xs text-zinc-400 mb-1">País</label>
                                <input type="text" name="origin_country"
                                       value="{{ old('origin_country', $recipe->origin_country ?? '') }}"
                                       placeholder="México, España, Italia..."
                                       class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs text-zinc-400 mb-1">Región</label>
                                <input type="text" name="origin_region"
                                       value="{{ old('origin_region', $recipe->origin_region ?? '') }}"
                                       placeholder="Oaxaca, Cataluña..."
                                       class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ==================== TAB 2: INGREDIENTS ==================== --}}
            <div data-tab-panel="ingredients" class="tab-panel hidden p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold text-zinc-200">Ingredientes</h2>
                    <button type="button" id="add-ingredient-group"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar Grupo
                    </button>
                </div>

                <div id="ingredients-container" data-sortable data-json-target="ingredients_json">
                    {{-- Ingredient rows rendered by JS from JSON --}}
                </div>

                <button type="button" id="add-ingredient-row"
                        class="mt-4 w-full py-3 border-2 border-dashed border-zinc-600 hover:border-amber-500 text-zinc-500 hover:text-amber-400 rounded-xl text-sm font-medium transition-colors">
                    + Añadir Ingrediente
                </button>

                <textarea id="ingredients_json" name="ingredients" class="hidden">{{ old('ingredients', json_encode(
                    ($recipe->ingredients ?? collect())->map(fn($i) => [
                        'amount' => $i->amount,
                        'unit'   => $i->unit,
                        'name'   => $i->ingredient_name,
                        'notes'  => $i->notes,
                        'group'  => $i->ingredient_group,
                    ])->values()
                )) }}</textarea>

                <div class="mt-6 p-4 bg-zinc-700/30 rounded-xl border border-zinc-700 text-xs text-zinc-500">
                    <p class="font-medium text-zinc-400 mb-1">Guía de grupos</p>
                    <p>Usa grupos para organizar ingredientes: "Para la salsa", "Para el relleno", etc. Si solo tienes un grupo, puedes dejarlo sin nombre.</p>
                </div>
            </div>

            {{-- ==================== TAB 3: STEPS ==================== --}}
            <div data-tab-panel="steps" class="tab-panel hidden p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold text-zinc-200">Pasos de Preparación</h2>
                    <button type="button" id="add-step"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Añadir Paso
                    </button>
                </div>

                <div id="steps-container" data-sortable data-json-target="steps_json" class="space-y-4">
                    {{-- Steps rendered by JS --}}
                </div>

                <textarea id="steps_json" name="steps" class="hidden">{{ old('steps', json_encode($recipe->steps ?? [])) }}</textarea>

                <div id="steps-empty" class="text-center py-12 text-zinc-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <p class="font-medium">Sin pasos aún</p>
                    <p class="text-xs mt-1">Añade el primer paso para comenzar</p>
                </div>
            </div>

            {{-- ==================== TAB 4: SEO ==================== --}}
            <div data-tab-panel="seo" class="tab-panel hidden">
                <div class="flex flex-col xl:flex-row gap-0">
                    <div class="flex-1 p-6 space-y-5 border-r border-zinc-700">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">SEO Title</label>
                                <span id="seo-title-count" class="text-xs font-mono text-zinc-500">0/60</span>
                            </div>
                            <input type="text" id="seo_title" name="seo_title"
                                   value="{{ old('seo_title', $recipe->seo_title ?? '') }}"
                                   maxlength="60"
                                   class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Meta Description</label>
                                <span id="seo-desc-count" class="text-xs font-mono text-zinc-500">0/160</span>
                            </div>
                            <textarea id="seo_description" name="seo_description" rows="3"
                                      maxlength="160"
                                      class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('seo_description', $recipe->seo_description ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Keywords (SEO)</label>
                            <input type="text" name="seo_keywords"
                                   value="{{ old('seo_keywords', $recipe->seo_keywords ?? '') }}"
                                   placeholder="palabra1, palabra2, palabra3..."
                                   class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Rating (Schema)</label>
                                <input type="number" name="schema_rating_value"
                                       value="{{ old('schema_rating_value', $recipe->schema_rating_value ?? '') }}"
                                       min="1" max="5" step="0.1"
                                       placeholder="4.5"
                                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Votos (Schema)</label>
                                <input type="number" name="schema_rating_count"
                                       value="{{ old('schema_rating_count', $recipe->schema_rating_count ?? '') }}"
                                       min="0"
                                       placeholder="128"
                                       class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                    </div>

                    {{-- SEO Preview Panel --}}
                    <div class="xl:w-96 p-6 space-y-5">
                        <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Vista Previa Google</h3>
                        <div class="bg-white rounded-xl p-5 shadow-lg">
                            <div class="text-xs text-green-700 font-mono mb-1 truncate">
                                {{ config('app.url', 'https://marvinbaptista.com') }}/recetas/{{ $recipe->slug ?? 'nombre-receta' }}
                            </div>
                            <div id="seo-preview-title" class="text-blue-700 text-lg font-medium leading-snug mb-1 line-clamp-1">
                                {{ $recipe->seo_title ?? $recipe->title ?? 'Título de la receta' }}
                            </div>
                            <div id="seo-preview-description" class="text-zinc-600 text-sm line-clamp-2 leading-relaxed">
                                {{ $recipe->seo_description ?? $recipe->description ?? 'Meta descripción de la receta...' }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-3">JSON-LD Preview</h3>
                            <pre class="bg-zinc-900 text-emerald-400 text-xs p-4 rounded-xl overflow-x-auto max-h-64 scrollbar-thin font-mono leading-relaxed">{{ json_encode(['@context' => 'https://schema.org', '@type' => 'Recipe', 'name' => $recipe->title ?? '', 'description' => $recipe->description ?? ''], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB 5: FAQ ==================== --}}
            <div data-tab-panel="faq" class="tab-panel hidden p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold text-zinc-200">Preguntas Frecuentes</h2>
                    <button type="button" id="add-faq"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Añadir Pregunta
                    </button>
                </div>

                <div id="faq-container" class="space-y-4">
                    {{-- FAQ rows rendered by JS --}}
                </div>

                <textarea id="faqs_json" name="faqs" class="hidden">{{ old('faqs', json_encode($recipe->faqs ?? [])) }}</textarea>

                <div id="faq-empty" class="text-center py-10 text-zinc-500">
                    <p class="text-sm">No hay preguntas frecuentes todavía.</p>
                    <p class="text-xs mt-1">Las FAQs mejoran el SEO y aparecen como rich snippets en Google.</p>
                </div>
            </div>

            {{-- ==================== TAB 6: BOOKS ==================== --}}
            <div data-tab-panel="books" class="tab-panel hidden p-6">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-zinc-200 mb-2">Libros Relacionados</h2>
                    <p class="text-sm text-zinc-400">Asocia libros de Amazon afiliado a esta receta.</p>
                </div>

                <div class="mb-4">
                    <input type="text" id="book-search" placeholder="Buscar libro..."
                           class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div id="books-list" class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($books ?? [] as $book)
                    <label class="book-item flex items-center gap-3 p-3 bg-zinc-700/50 rounded-xl border border-zinc-600 hover:border-zinc-500 cursor-pointer transition-all"
                           data-book-title="{{ strtolower($book->title) }}">
                        <input type="checkbox" name="books[]" value="{{ $book->id }}"
                               {{ in_array($book->id, old('books', $recipe->books?->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                               class="rounded border-zinc-500 bg-zinc-700 text-amber-500 focus:ring-amber-500 shrink-0">
                        @if($book->cover_image_url)
                        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}"
                             class="w-10 h-14 object-cover rounded shrink-0">
                        @else
                        <div class="w-10 h-14 bg-zinc-600 rounded flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-200 line-clamp-2 leading-snug">{{ $book->title }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $book->author }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                @if(empty($books) || count($books ?? []) === 0)
                <div class="text-center py-10 text-zinc-500">
                    <p class="text-sm">No hay libros configurados.</p>
                    <a href="{{ route('admin.libros.create') }}" class="text-amber-400 hover:text-amber-300 text-sm mt-2 inline-block">Añadir primer libro →</a>
                </div>
                @endif
            </div>

            {{-- ==================== TAB 7: SETTINGS ==================== --}}
            <div data-tab-panel="settings" class="tab-panel hidden p-6">
                <div class="grid xl:grid-cols-2 gap-6">

                    {{-- AI Enhancement --}}
                    <div class="bg-zinc-700/30 rounded-xl border border-zinc-600 p-5 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-violet-900/50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-200">Mejora con IA</h3>
                                <p class="text-xs text-zinc-400">Usa GPT para optimizar título, descripción y SEO</p>
                            </div>
                        </div>

                        @if($isEdit)

                        {{-- AI Connection Status --}}
                        <div id="ai-status-bar" class="flex items-center gap-2 px-3 py-2 bg-zinc-700/40 rounded-lg text-xs">
                            <span id="ai-status-dot" class="w-2 h-2 rounded-full bg-zinc-500 shrink-0"></span>
                            <span id="ai-status-text" class="text-zinc-400">Sin verificar</span>
                            <button type="button" id="btn-ai-check"
                                    data-test-url="{{ route('admin.settings.test-ai') }}"
                                    class="ml-auto text-violet-400 hover:text-violet-300 underline underline-offset-2 transition-colors">
                                Verificar conexión
                            </button>
                        </div>

                        {{-- Inline error / info box --}}
                        <div id="ai-error-box" class="hidden rounded-lg border px-3 py-2.5 text-xs space-y-1">
                            <p id="ai-error-title" class="font-semibold"></p>
                            <p id="ai-error-detail" class="leading-relaxed opacity-90"></p>
                            <a href="{{ route('admin.settings.index') }}#tab-ai"
                               id="ai-error-link" class="hidden underline underline-offset-2 font-medium">
                                Ir a Ajustes → IA
                            </a>
                        </div>

                        <button type="button" id="btn-ai-enhance"
                                data-enhance-url="{{ $enhanceUrl }}"
                                class="w-full inline-flex items-center justify-center gap-3 px-4 py-3 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-medium text-sm transition-colors">
                            <span id="ai-enhance-spinner" class="hidden">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </span>
                            <span id="ai-enhance-text">Mejorar con IA</span>
                        </button>

                        {{-- Copy-prompt button (manual workflow: copy → paste in ChatGPT/Claude/Gemini) --}}
                        @if($isEdit)
                        <button type="button" id="btn-copy-prompt"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5
                                       bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl
                                       font-medium text-sm transition-colors border border-zinc-600">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1
                                         M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Construir Prompt
                        </button>
                        @endif

                        {{-- AI Diff View --}}
                        <div id="ai-diff-container" class="hidden space-y-4">
                            <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider border-b border-zinc-600 pb-2">
                                Sugerencias IA — selecciona qué aceptar
                            </div>
                            <div id="ai-diff-fields" class="space-y-3">
                                {{-- Populated by JS --}}
                            </div>
                            <button type="button" id="btn-ai-save"
                                    data-save-url="{{ $saveAiUrl }}"
                                    class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-medium text-sm transition-colors">
                                Guardar Cambios Aceptados
                            </button>
                        </div>

                        @if($recipe->ai_enhanced_at)
                        <p class="text-xs text-zinc-500">
                            Última mejora IA: {{ $recipe->ai_enhanced_at->format('d/m/Y H:i') }}
                        </p>
                        @endif
                        @else
                        <p class="text-sm text-zinc-500">Guarda la receta primero para habilitar la mejora con IA.</p>
                        @endif
                    </div>

                    {{-- Recipe Stats --}}
                    @if($isEdit)
                    <div class="bg-zinc-700/30 rounded-xl border border-zinc-600 p-5 space-y-4">
                        <h3 class="font-semibold text-zinc-200">Estadísticas</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-zinc-600/50">
                                <dt class="text-sm text-zinc-400">Vistas totales</dt>
                                <dd class="text-sm font-semibold text-zinc-200 font-mono">{{ number_format($recipe->views_count ?? 0) }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-zinc-600/50">
                                <dt class="text-sm text-zinc-400">Creada</dt>
                                <dd class="text-sm text-zinc-200">{{ $recipe->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-zinc-600/50">
                                <dt class="text-sm text-zinc-400">Actualizada</dt>
                                <dd class="text-sm text-zinc-200">{{ $recipe->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-zinc-600/50">
                                <dt class="text-sm text-zinc-400">Slug</dt>
                                <dd class="text-xs font-mono text-zinc-400">{{ $recipe->slug ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <dt class="text-sm text-zinc-400">Mejorada con IA</dt>
                                <dd class="text-sm text-zinc-200">{{ $recipe->ai_enhanced_at ? 'Sí' : 'No' }}</dd>
                            </div>
                        </dl>

                        </form>{{-- end form --}}

                        {{-- Danger Zone --}}
                        <div class="mt-6 p-4 bg-red-950/30 border border-red-900/50 rounded-xl">
                            <h4 class="text-sm font-semibold text-red-400 mb-3">Zona de peligro</h4>
                            <form method="POST" action="{{ route('admin.recipes.destroy', $recipe) }}"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($recipe->title) }}» permanentemente? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-4 py-2 bg-red-800/50 hover:bg-red-700/50 text-red-300 border border-red-700/50 rounded-lg text-sm font-medium transition-colors">
                                    Eliminar esta receta
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

    </div>
</div>

{{-- ==================== COPY-PROMPT MODAL ==================== --}}
@if($isEdit)
<div id="prompt-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
     role="dialog" aria-modal="true" aria-labelledby="prompt-modal-title">

    <div class="relative w-full max-w-2xl bg-zinc-900 border border-zinc-700 rounded-2xl shadow-2xl
                flex flex-col max-h-[90vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-700 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-violet-900/60 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1
                                 M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h2 id="prompt-modal-title" class="text-sm font-bold text-zinc-100">
                    Prompt para ChatGPT / Claude / Gemini
                </h2>
            </div>
            <button type="button" id="prompt-modal-close"
                    class="p-1.5 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-5">

            {{-- Loading state --}}
            <div id="prompt-modal-loading" class="flex items-center justify-center gap-3 py-8">
                <svg class="w-5 h-5 animate-spin text-violet-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span class="text-sm text-zinc-400">Construyendo prompt…</span>
            </div>

            {{-- Content (hidden until loaded) --}}
            <div id="prompt-modal-content" class="hidden space-y-5">

                {{-- Step 1: Copy prompt --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                            1. Copia este prompt
                        </p>
                        <button type="button" id="btn-clipboard-copy"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-700 hover:bg-violet-600
                                       text-white rounded-lg text-xs font-medium transition-colors">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1
                                         M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span id="clipboard-copy-text">Copiar al portapapeles</span>
                        </button>
                    </div>
                    <textarea id="prompt-text-area" readonly rows="9"
                              class="w-full px-3 py-2.5 bg-zinc-800 border border-zinc-600 text-zinc-300 text-xs
                                     rounded-xl resize-none font-mono leading-relaxed focus:outline-none
                                     focus:ring-1 focus:ring-violet-500 cursor-text select-all"></textarea>
                </div>

                {{-- Step 2: Open AI tool --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                        2. Pégalo en tu IA favorita
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <a href="https://chat.openai.com/" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600
                                  text-zinc-200 rounded-lg text-xs font-medium transition-colors border border-zinc-600">
                            ChatGPT
                            <svg class="w-3 h-3 shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        <a href="https://claude.ai/new" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600
                                  text-zinc-200 rounded-lg text-xs font-medium transition-colors border border-zinc-600">
                            Claude
                            <svg class="w-3 h-3 shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        <a href="https://gemini.google.com/" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600
                                  text-zinc-200 rounded-lg text-xs font-medium transition-colors border border-zinc-600">
                            Gemini
                            <svg class="w-3 h-3 shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Step 3: Paste the JSON response --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                        3. Pega la respuesta JSON aquí
                    </p>
                    <textarea id="prompt-response-area" rows="6"
                              placeholder='{"seo_title":"...","seo_description":"...","story":"...","tips_secrets":"...","faq":[...],"amazon_keywords":[...],"internal_link_suggestions":[...]}'
                              class="w-full px-3 py-2.5 bg-zinc-800 border border-zinc-600 text-zinc-300 text-xs
                                     rounded-xl resize-y font-mono leading-relaxed focus:outline-none
                                     focus:ring-1 focus:ring-emerald-500 placeholder-zinc-600"></textarea>
                    <div id="prompt-parse-error"
                         class="hidden rounded-lg border border-red-800/60 bg-red-950/40 px-3 py-2 text-xs text-red-300"></div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div id="prompt-modal-footer"
             class="hidden shrink-0 px-5 py-4 border-t border-zinc-700 flex items-center justify-between gap-3">
            <button type="button" id="prompt-modal-close-footer"
                    class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg text-sm transition-colors">
                Cancelar
            </button>
            <button type="button" id="btn-apply-response"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-500
                           text-white rounded-lg text-sm font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Aplicar respuesta
            </button>
        </div>

    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Pass data to admin JS
window.recipeData = {
    ingredients: @json($recipe->ingredients ?? []),
    steps: @json($recipe->steps ?? []),
    faqs: @json($recipe->faqs ?? []),
    isEdit: {{ $isEdit ? 'true' : 'false' }},
};
</script>
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<script>
/**
 * Trix paste enhancement:
 * When the user pastes PLAIN TEXT (no HTML in clipboard — e.g. from ChatGPT,
 * a text editor, or a terminal), we intercept the native paste event and
 * convert \n\n to paragraph breaks and \n to <br> so line structure is preserved.
 *
 * Pastes that already carry HTML (from a website or a word processor) are left
 * to Trix's built-in handling (which keeps bold, lists, links, etc.).
 */
document.addEventListener('trix-initialize', function () {
    document.querySelectorAll('trix-editor').forEach(function (trixEl) {
        trixEl.addEventListener('paste', function (event) {
            var cd = event.clipboardData || window.clipboardData;
            if (!cd) return;

            var types   = cd.types ? Array.from(cd.types) : [];
            var hasHtml = types.includes('text/html');
            var hasText = types.includes('text/plain');

            // Only handle plain-text-only paste (no HTML version available)
            if (!hasText || hasHtml) return;

            event.preventDefault();

            var text   = cd.getData('text/plain');
            var editor = trixEl.editor;

            // Escape a single text line for safe HTML insertion
            function escLine(line) {
                return line
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            // Double newlines → separate <div> blocks (Trix paragraphs)
            // Single newlines → <br> within the same block
            var html = text
                .split(/\n{2,}/)
                .filter(function (p) { return p.trim().length > 0; })
                .map(function (p) {
                    var lines = p.trim().split('\n').map(escLine);
                    return '<div>' + lines.join('<br>') + '</div>';
                })
                .join('');

            if (html) editor.insertHTML(html);
        });
    });
});
</script>
<style>
.html-raw-editor { tab-size: 2; -moz-tab-size: 2; }
.html-preview-panel { color: #e4e4e7; line-height: 1.7; font-size: 0.9375rem; }
.html-preview-panel p { margin-bottom: 0.875rem; }
.html-preview-panel h1, .html-preview-panel h2, .html-preview-panel h3 { color: #f4f4f5; font-weight: 700; margin-top: 1.25rem; margin-bottom: 0.5rem; }
.html-preview-panel h1 { font-size: 1.25rem; }
.html-preview-panel h2 { font-size: 1.125rem; border-bottom: 1px solid #3f3f46; padding-bottom: 0.25rem; }
.html-preview-panel h3 { font-size: 1rem; }
.html-preview-panel a { color: #f59e0b; text-decoration: underline; }
.html-preview-panel ul { list-style: disc; padding-left: 1.25rem; margin-bottom: 0.75rem; }
.html-preview-panel li { margin-bottom: 0.25rem; }
.html-preview-panel nav.tabla-contenidos { background: rgba(39,39,42,0.8); border: 1px solid #3f3f46; border-left: 3px solid #f59e0b; padding: 0.875rem 1rem; border-radius: 0.5rem; margin: 1rem 0; }
.html-preview-panel nav.tabla-contenidos h2 { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a; margin: 0 0 0.5rem; border: none; padding: 0; }
.html-preview-panel nav.tabla-contenidos ul { list-style: none; padding: 0; margin: 0; }
.html-preview-panel nav.tabla-contenidos a { display: block; padding: 0.2rem 0; text-decoration: none; color: #f59e0b; }
</style>
<script>
(function () {
    'use strict';

    document.querySelectorAll('.html-preview-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.dataset.target;
            var textarea = document.getElementById(targetId);
            var preview  = document.getElementById(targetId + '-preview');
            if (!textarea || !preview) return;

            var isHidden = preview.classList.contains('hidden');
            if (isHidden) {
                preview.innerHTML = textarea.value;
                preview.classList.remove('hidden');
                btn.textContent = 'Ocultar previa';
                btn.classList.add('bg-amber-600', 'border-amber-500', 'text-white');
                btn.classList.remove('bg-zinc-700', 'text-zinc-300');
            } else {
                preview.classList.add('hidden');
                btn.textContent = 'Vista previa';
                btn.classList.remove('bg-amber-600', 'border-amber-500', 'text-white');
                btn.classList.add('bg-zinc-700', 'text-zinc-300');
            }
        });
    });

    document.querySelectorAll('.html-raw-editor').forEach(function (textarea) {
        textarea.addEventListener('input', function () {
            var preview = document.getElementById(textarea.id + '-preview');
            if (preview && !preview.classList.contains('hidden')) {
                preview.innerHTML = textarea.value;
            }
        });
    });
}());
</script>
@endpush
