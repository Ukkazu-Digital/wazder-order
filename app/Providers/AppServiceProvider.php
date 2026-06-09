<?php

namespace App\Providers;

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
        // Bind current tenant instance by request host if available
        try {
            $host = request()->getHost();
            \App\Models\Tenant::where('domain', $host)->first();
        } catch (\Exception $e) {
            // ignore when running in console
        }
    }
}
