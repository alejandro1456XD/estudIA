<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <-- Añadido
use Illuminate\Support\ServiceProvider;

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
        // ---------------------------------------------------------------------
        // CORRECCIÓN: Fuerza HTTPS para enlaces en entornos de producción (Railway/Render)
        // Esto resuelve el error de "La solicitud no es segura" en los formularios.
        // ---------------------------------------------------------------------
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Si tienes más código en boot(), colócalo aquí abajo
    }
}