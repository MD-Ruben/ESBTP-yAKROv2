<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ESBTPEmploiTemps;
// Suppression de l'import de la politique qui n'existe plus
// use App\Policies\ESBTPEmploiTempsPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Suppression de la référence à la politique qui n'existe plus
        // ESBTPEmploiTemps::class => ESBTPEmploiTempsPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
