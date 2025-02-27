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
            // Pour le développement local, si l'application est dans un sous-dossier
            $rootUrl = request()->getSchemeAndHttpHost();
            
            // Forcer l'URL de base pour le sous-dossier smart_school_new/public
            URL::forceRootUrl($rootUrl . '/smart_school_new/public');
            URL::forceScheme('http');
        }
    }
}
