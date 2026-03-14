<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Tu cuenta ha sido desactivada.');
        }

        if (!$user->isEditor()) {
            abort(403, 'No tienes permisos para acceder a esta área.');
        }

        return $next($request);
    }
}
