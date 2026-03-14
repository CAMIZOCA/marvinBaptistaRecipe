<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — {{ config('app.name') }}</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="h-full bg-zinc-900 flex items-center justify-center">
    <div class="w-full max-w-sm px-4">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 bg-indigo-600 rounded-2xl items-center justify-center mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">{{ config('app.name') }}</h1>
            <p class="text-zinc-400 text-sm mt-1">Panel de Administración</p>
        </div>

        {{-- Login form --}}
        <div class="bg-zinc-800 rounded-2xl p-8 shadow-2xl border border-zinc-700">
            <h2 class="text-lg font-semibold text-white mb-6">Iniciar Sesión</h2>

            @if($errors->any())
                <div class="bg-red-900/30 border border-red-700 text-red-300 rounded-lg px-4 py-3 mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Correo Electrónico
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-600 rounded-lg text-white text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors
                                  @error('email') border-red-500 @enderror"
                           placeholder="admin@ejemplo.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Contraseña
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           class="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-600 rounded-lg text-white text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-indigo-600 rounded border-zinc-600 bg-zinc-900 focus:ring-indigo-500">
                    <label for="remember" class="ml-2 text-sm text-zinc-400">Recordarme</label>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-zinc-800">
                    Iniciar Sesión
                </button>
            </form>
        </div>

        <p class="text-center text-zinc-600 text-xs mt-6">
            <a href="{{ route('home') }}" class="hover:text-zinc-400 transition-colors">← Ver sitio web</a>
        </p>
    </div>
</body>
</html>
