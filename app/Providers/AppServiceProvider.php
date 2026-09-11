<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Remplace la notification Filament de reset password par notre version FR
        $this->app->bind(
            \Filament\Auth\Notifications\ResetPassword::class,
            \App\Notifications\ResetPasswordNotification::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || str_starts_with(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }
    }
}
