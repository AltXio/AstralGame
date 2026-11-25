<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Acceso a la sección de administración (se usa después de 'auth').
 *   ->middleware('admin')             administrador y superadministrador (métricas)
 *   ->middleware('admin:superadmin')  solo superadministrador (gestión de administradores)
 * Quien no tenga el rol vuelve al inicio con un aviso.
 */
class SoloAdmin
{
    public function handle(Request $request, Closure $next, string $nivel = 'admin'): Response
    {
        $user = $request->user();
        $permitido = $user && ($nivel === 'superadmin' ? $user->esSuperadmin() : $user->esAdmin());

        if (! $permitido) {
            return redirect()->route('home')->with('bloqueado', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
