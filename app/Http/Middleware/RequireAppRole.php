<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class RequireAppRole
{
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

        $allowed = ['directeur', 'secretaire', 'surveillante', ...User::BASIC_ROLES];
        if (! $user->hasAnyRole($allowed)) {
            abort(403, 'Accès réservé au personnel de l\'entreprise.');
        }

        if (is_null($user->ecole_setting_id)) {
            abort(403, 'Votre compte n\'est pas associé à une école. Contactez l\'administrateur.');
        }

        return $next($request);
    }
}
