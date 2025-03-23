<?php

namespace App\Providers;

use App\Service\NytClientService;
use Illuminate\Support\ServiceProvider;

class NytClientServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NytClientService::class, function ($app) {
            return new NytClientService(
                config('nyt-service.base_url'),
                config('nyt-service.api_key'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
