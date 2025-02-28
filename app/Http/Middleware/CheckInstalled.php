<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\InstallationHelper;

class CheckInstalled
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
        // Vérifier si l'application est installée et si les migrations correspondent
        $installationStatus = InstallationHelper::getInstallationStatus();
        $installed = $installationStatus['installed'];
        $matchPercentage = $installationStatus['match_percentage'] ?? 0;
        $hasAdminUser = InstallationHelper::hasAdminUser();
        
        // Journaliser l'état de l'installation pour le débogage
        \Log::info("Middleware CheckInstalled - Installation status: " . ($installed ? 'Installed' : 'Not installed') . 
                  ", Match: {$matchPercentage}%, Admin user: " . ($hasAdminUser ? 'Yes' : 'No') . 
                  ", Route: " . $request->path());
        
        // Toujours autoriser l'accès aux assets et aux routes d'authentification
        if ($request->is('assets/*') || $request->is('css/*') || $request->is('js/*') || 
            $request->is('logout') || $request->is('register')) {
            return $next($request);
        }
        
        // Si nous sommes sur les routes d'installation, toujours permettre l'accès
        if ($request->is('install') || $request->is('install/*')) {
            \Log::info("Allowing access to installation routes");
            return $next($request);
        }
        
        // Si l'application n'est pas installée du tout ou s'il n'y a pas d'utilisateur admin, 
        // rediriger vers l'installation
        if (!$installed || !$hasAdminUser) {
            \Log::info("Redirecting to installation page (Not installed or no admin user)");
            return redirect()->route('install.index');
        }
        
        // Si nous sommes sur la page de login, autoriser l'accès même si les migrations ne correspondent pas à 100%
        if ($request->is('login')) {
            \Log::info("Allowing access to login page even if migrations don't match 100%");
            return $next($request);
        }
        
        // Si l'application est installée mais que les migrations ne correspondent pas à 100%
        // et que nous ne sommes pas déjà sur la page d'accueil
        if ($matchPercentage < 100 && !$request->is('/')) {
            // Afficher un message d'avertissement mais permettre l'accès à l'application
            // au lieu de rediriger en boucle
            \Log::warning("Application installed but migrations don't match 100% ({$matchPercentage}%)");
            // On pourrait ajouter un flash message ici pour informer l'administrateur
            
            // Continuer la requête sans redirection
            return $next($request);
        }
        
        return $next($request);
    }
} 