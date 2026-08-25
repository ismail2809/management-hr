<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAppRole
{
    private const ALLOWED_ROLES = ['admin', 'rh', 'manager', 'comptable', 'employee'];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Non authentifié.');
        }

        // Super-admin voit tout (pas de company_id requis)
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        if (! $user->hasAnyRole(self::ALLOWED_ROLES)) {
            abort(403, 'Accès réservé au personnel de l\'entreprise.');
        }

        if (is_null($user->company_id)) {
            abort(403, 'Votre compte n\'est pas associé à une entreprise. Contactez l\'administrateur.');
        }

        return $next($request);
    }
}
