<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CheckInstallation
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
        // Vérifier si le fichier .env existe
        $envExists = File::exists(base_path('.env'));
        
        // Vérifier si la connexion à la base de données fonctionne
        $dbConnected = false;
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Exception $e) {
            $dbConnected = false;
        }
        
        // Vérifier si des utilisateurs existent dans la base de données
        $usersExist = false;
        if ($dbConnected && Schema::hasTable('users')) {
            try {
                $usersExist = DB::table('users')->count() > 0;
            } catch (\Exception $e) {
                $usersExist = false;
            }
        }
        
        // Si on est sur la page /setup
        if ($request->is('setup') || $request->is('setup/*')) {
            // Si tout est configuré, rediriger vers login
            if ($envExists && $dbConnected && $usersExist) {
                return redirect()->route('login');
            }
            
            // Sinon, permettre l'accès à la page setup
            return $next($request);
        }
        
        // Si on n'est pas sur la page /setup et que l'installation n'est pas complète
        if (!$envExists || !$dbConnected || !$usersExist) {
            // Rediriger vers la page setup
            return redirect()->route('setup.index');
        }
        
        return $next($request);
    }
} 