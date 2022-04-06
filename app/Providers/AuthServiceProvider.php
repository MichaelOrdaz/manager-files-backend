<?php

namespace App\Providers;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\User;
use App\Models\Permisos;
use App\Policies\EstadoPolicy;
use App\Policies\MunicipioPolicy;
use App\Policies\UsuarioPolicy;
use App\Policies\PermisoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Estado::class => EstadoPolicy::class,
        Municipio::class => MunicipioPolicy::class,
        User::class => UsuarioPolicy::class,
        Permisos::class => PermisoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
    }
}
