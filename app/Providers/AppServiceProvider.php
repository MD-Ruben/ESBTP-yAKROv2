<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Fix for key length issue with MySQL < 5.7.7 or MariaDB < 10.2.2
        Schema::defaultStringLength(191);
        
        // Force URLs to use the correct base path
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        } else {
            // Pour le développement local
            $rootUrl = request()->getSchemeAndHttpHost();
            
            // Vérifier si nous sommes sur le serveur de développement Laravel (port 8000)
            $isArtisanServe = (request()->getPort() == 8000);
            
            if (!$isArtisanServe) {
                // Si nous sommes sur Apache/WAMP, forcer l'URL de base pour le sous-dossier
                URL::forceRootUrl($rootUrl . '/smart_school_new/public');
            }
            
            URL::forceScheme('http');
        }
    }
}
