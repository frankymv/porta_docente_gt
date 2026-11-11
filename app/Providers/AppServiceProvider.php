<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL; // <-- Añade esta línea arriba


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
        //
            // Fuerza HTTPS solo si el entorno no es local
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

    }
}
