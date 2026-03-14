@props(['recipe'])

<article class="bg-white rounded-2xl overflow-hidden border border-zinc-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group">

    {{-- Image --}}
    <div class="aspect-video overflow-hidden bg-zinc-100 relative">
        @if($recipe->featured_image)
        <img src="{{ $recipe->featured_image }}"
             alt="{{ $recipe->image_alt ?? $recipe->title }}"
             loading="lazy"
             decoding="async"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-50 to-orange-100">
            <svg class="w-12 h-12 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif

        {{-- Difficulty Badge --}}
        @if($recipe->difficulty)
        @php
            $diffClass = match($recipe->difficulty) {
                'easy'   => 'bg-emerald-500',
                'medium' => 'bg-yellow-500',
                'hard'   => 'bg-red-500',
                default  => 'bg-zinc-500',
            };
            $diffLabel = match($recipe->difficulty) {
                'easy'   => 'Fácil',
                'medium' => 'Media',
                'hard'   => 'Difícil',
                default  => $recipe->difficulty,
            };
        @endphp
        <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white {{ $diffClass }}">
            {{ $diffLabel }}
        </span>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-5">
        {{-- Category --}}
        @if($recipe->categories?->first())
        <a href="{{ route('category.show', $recipe->categories->first()->slug) }}"
           class="text-xs font-semibold text-amber-600 hover:text-amber-700 uppercase tracking-wider transition-colors">
            {{ $recipe->categories->first()->name }}
        </a>
        @endif

        {{-- Title --}}
        <h3 class="mt-2 mb-3">
            <a href="{{ route('recipe.show', $recipe->slug) }}"
               class="text-base font-bold text-zinc-900 hover:text-amber-600 transition-colors line-clamp-2 leading-snug">
                {{ $recipe->title }}
            </a>
        </h3>

        {{-- Description excerpt --}}
        @if($recipe->subtitle ?? $recipe->description ?? null)
        <p class="text-xs text-zinc-500 line-clamp-2 leading-relaxed mb-3 -mt-1">
            {{ $recipe->subtitle ? $recipe->subtitle : Str::limit(strip_tags($recipe->description ?? ''), 90) }}
        </p>
        @endif

        {{-- Meta --}}
        <div class="flex items-center gap-4 text-xs text-zinc-500">
            @php
                $totalTime = ($recipe->prep_time_minutes ?? 0) + ($recipe->cook_time_minutes ?? 0);
            @endphp
            @if($totalTime > 0)
            <div class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $totalTime }} min</span>
            </div>
            @endif

            @if($recipe->servings)
            <div class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $recipe->servings }} porciones</span>
            </div>
            @endif

            @if($recipe->schema_rating_value)
            <div class="flex items-center gap-1 ml-auto">
                <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="font-semibold text-zinc-700">{{ number_format($recipe->schema_rating_value, 1) }}</span>
            </div>
            @endif
        </div>
    </div>
</article>
