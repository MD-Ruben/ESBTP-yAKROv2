<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UpdateLastLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (Auth::check()) {
            $user = Auth::user();
            
            // Mettre à jour la date de dernière connexion uniquement si elle n'a pas été mise à jour aujourd'hui
            if (!$user->last_login_at || Carbon::parse($user->last_login_at)->toDateString() !== Carbon::now()->toDateString()) {
                $user->last_login_at = Carbon::now();
                $user->save();
            }
        }

        return $next($request);
    }
} 