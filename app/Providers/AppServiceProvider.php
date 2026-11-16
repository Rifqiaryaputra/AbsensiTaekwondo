<?php

namespace App\Providers;

use App\Models\IzinSakit;
use App\Observers\IzinSakitObserver;
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
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        IzinSakit::observe(IzinSakitObserver::class);
    }
}
