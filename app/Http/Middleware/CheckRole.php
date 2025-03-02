<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Vérifie si l'utilisateur a le rôle spécifié.
     *
     * Ce middleware permet de restreindre l'accès aux routes en fonction du rôle de l'utilisateur.
     * Par exemple, certaines routes ne sont accessibles qu'aux super administrateurs, secrétaires ou étudiants.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect('login');
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        // Si l'utilisateur est superAdmin, il a accès à tout
        if ($user->hasRole('superAdmin')) {
            return $next($request);
        }
        
        // Vérifier pour les rôles multiples (ex: 'role:admin,editor')
        $roles = explode(',', $role);
        foreach ($roles as $singleRole) {
            if ($user->hasRole($singleRole)) {
                return $next($request);
            }
        }
        
        // Si aucun des rôles requis n'est présent
        abort(403, 'Accès non autorisé. Rôle de ' . ucfirst($roles[0]) . ' requis.');
    }
} 