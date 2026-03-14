<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/admin.css', 'resources/js/admin/app.js'])
    @stack('head')
</head>
<body class="h-full bg-zinc-50 font-sans antialiased">
    <div class="flex h-full">
        {{-- Sidebar --}}
        @include('components.admin.sidebar')

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-h-screen ml-64">
            {{-- Topbar --}}
            @include('components.admin.topbar')

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mx-6 mt-4">
                    <div class="flash-success flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="mx-6 mt-4">
                    <div class="flash-error flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- Main content area --}}
            <main class="flex-1 p-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="px-6 py-4 border-t border-zinc-200 bg-white">
                <p class="text-xs text-zinc-400 text-center">{{ config('app.name') }} Admin Panel &copy; {{ date('Y') }}</p>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
