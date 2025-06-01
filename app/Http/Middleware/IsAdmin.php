<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->rol && $user->rol->nombre_rol === 'Administrador') {
            return $next($request);
        }

        abort(403, 'Acceso no autorizado');
    }
}
