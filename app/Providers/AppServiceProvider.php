<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator; // <-- 1. IMPORTAR LA CLASE

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. DEFINIR LA POLÍTICA DE ACCESO PARA EL DASHBOARD  
        Gate::define('view-dashboard', function ($user) {
            return $user->hasRole('Administrador') || $user->hasRole('Gerente Regional');
        });
    
        // 2. AÑADIR ESTA LÍNEA
        Paginator::useBootstrapFive();
    }
}