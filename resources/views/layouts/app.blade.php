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

    {{-- og:locale global (español) --}}
    <meta property="og:locale" content="es_ES">

    {{-- Google Search Console verification (configurado en Ajustes → SEO) --}}
    @if(!empty($settings['google_search_console']))
    <meta name="google-site-verification" content="{{ $settings['google_search_console'] }}">
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

    {{-- Google Analytics 4 (solo si está configurado en Ajustes → SEO) --}}
    @php $gaId = $settings['google_analytics_id'] ?? null; @endphp
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $gaId }}', {
        send_page_view: true,
        cookie_flags: 'SameSite=None;Secure'
    });

    /* ── Helper global de analytics ─────────────────────────────────────────
       Uso: window._ga.push('nombre_evento', { param: 'valor' })
       → envía a GA4 Y empuja al dataLayer (compatible con GTM en el futuro)
       ──────────────────────────────────────────────────────────────────── */
    window._ga = {
        push: function(name, params) {
            var p = params || {};
            if (typeof gtag === 'function') gtag('event', name, p);
            window.dataLayer.push(Object.assign({ event: name }, p));
        }
    };

    /* ── Tracking declarativo por atributo data-ga-event ────────────────────
       Agrega  data-ga-event="nombre"  a cualquier elemento HTML para que
       cada clic dispare automáticamente el evento en GA4.
       Parámetros opcionales: data-ga-category  data-ga-label  data-ga-value
       ──────────────────────────────────────────────────────────────────── */
    document.addEventListener('click', function(e) {
        var el = e.target.closest('[data-ga-event]');
        if (!el) return;
        var p = { event_category: el.dataset.gaCategory || 'engagement' };
        if (el.dataset.gaLabel)  p.event_label = el.dataset.gaLabel;
        if (el.dataset.gaValue)  p.value        = Number(el.dataset.gaValue);
        if (el.dataset.gaItemId) p.item_id      = el.dataset.gaItemId;
        window._ga.push(el.dataset.gaEvent, p);
    }, { passive: true });

    /* ── Affiliate tracking automático (todos los links de Amazon) ──────────
       Captura clics en cualquier <a href*="amazon."> sin atributos extra.
       ──────────────────────────────────────────────────────────────────── */
    document.addEventListener('click', function(e) {
        var link = e.target.closest('a[href*="amazon."]');
        if (!link) return;
        window._ga.push('affiliate_click', {
            event_category: 'affiliate',
            event_label: (link.textContent || '').trim().substring(0, 100),
            link_url: link.href
        });
    }, { passive: true });
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
