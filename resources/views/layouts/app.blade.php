<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#f59e0b">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @yield('seo_head')

    @hasSection('seo_head')
    @else
    <title>{{ config('app.name', 'Marvin Baptista') }}</title>
    <meta name="description" content="Recetas con sabor a Latinoamérica y el Mediterráneo">
    @endif

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="shortcut icon" href="/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('schema_org')

    {{-- Google Analytics 4 (solo si está configurado) --}}
    @php $gaId = $settings['google_analytics_id'] ?? null; @endphp
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}');
    </script>
    @endif
</head>
<body class="bg-white text-zinc-900 font-sans antialiased overflow-x-hidden" data-recipe-slug="{{ $recipe->slug ?? '' }}">

    @include('components.public.header')

    <main id="main-content">
        @yield('content')
    </main>

    @include('components.public.footer')

    @stack('scripts')

</body>
</html>
