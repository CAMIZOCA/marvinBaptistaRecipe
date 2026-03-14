@extends('layouts.app')

@section('seo_head')
<title>Página no encontrada | Marvin Baptista</title>
<meta name="robots" content="noindex">
@endsection

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center max-w-md">
        <p class="text-8xl font-bold text-amber-400 mb-4">404</p>
        <h1 class="text-2xl font-bold text-zinc-900 mb-3" style="font-family: 'Playfair Display', serif;">
            Esta página no existe
        </h1>
        <p class="text-zinc-500 mb-8 leading-relaxed">
            La receta o artículo que buscas no está disponible. Puede que haya sido movido o eliminado.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-xl text-sm transition-colors">
                ← Volver al inicio
            </a>
            <a href="{{ route('recipes.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-zinc-200 hover:bg-zinc-50 text-zinc-700 font-semibold rounded-xl text-sm transition-colors">
                Ver recetas
            </a>
        </div>
    </div>
</div>
@endsection
