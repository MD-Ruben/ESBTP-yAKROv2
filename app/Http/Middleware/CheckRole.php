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
     * Par exemple, certaines routes ne sont accessibles qu'aux administrateurs ou aux enseignants.
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

        // Vérifier si l'utilisateur a le rôle requis
        if ($role === 'admin' && $user->user_type !== 'admin' && $user->user_type !== 'superadmin') {
            abort(403, 'Accès non autorisé. Rôle d\'administrateur requis.');
        }

        if ($role === 'teacher' && !$user->isTeacher()) {
            abort(403, 'Accès non autorisé. Rôle d\'enseignant requis.');
        }

        if ($role === 'student' && !$user->isStudent()) {
            abort(403, 'Accès non autorisé. Rôle d\'étudiant requis.');
        }

        if ($role === 'parent' && !$user->isParent()) {
            abort(403, 'Accès non autorisé. Rôle de parent requis.');
        }

        return $next($request);
    }
} 