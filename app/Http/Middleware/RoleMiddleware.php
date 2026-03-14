<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        $allowed = match($role) {
            'super_admin' => $user?->isSuperAdmin(),
            'admin' => $user?->isAdmin(),
            'editor' => $user?->isEditor(),
            default => false,
        };

        if (!$allowed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
            abort(403, 'No tienes permisos suficientes para realizar esta acción.');
        }

        return $next($request);
    }
}
