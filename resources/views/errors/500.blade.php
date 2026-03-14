@extends('layouts.app')

@section('seo_head')
<title>Error del servidor | Marvin Baptista</title>
<meta name="robots" content="noindex">
@endsection

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center max-w-md">
        <p class="text-8xl font-bold text-zinc-300 mb-4">500</p>
        <h1 class="text-2xl font-bold text-zinc-900 mb-3" style="font-family: 'Playfair Display', serif;">
            Algo salió mal
        </h1>
        <p class="text-zinc-500 mb-8 leading-relaxed">
            Ocurrió un error inesperado en el servidor. Nuestro equipo ya fue notificado. Intenta de nuevo en unos minutos.
        </p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-xl text-sm transition-colors">
            ← Volver al inicio
        </a>
    </div>
</div>
@endsection
