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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('schema_org')
</head>
<body class="bg-white text-zinc-900 font-sans antialiased" data-recipe-slug="{{ $recipe->slug ?? '' }}">

    @include('components.public.header')

    <main id="main-content">
        @yield('content')
    </main>

    @include('components.public.footer')

    @stack('scripts')

</body>
</html>
