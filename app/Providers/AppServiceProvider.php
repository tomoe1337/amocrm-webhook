<?php

namespace App\Providers;

use App\Services\AmoCrmService;
use App\Services\WebhookHandlerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(AmoCrmService::class, function () {
            return new AmoCrmService(
                config('services.amocrm.client_id'),
                config('services.amocrm.client_secret'),
                config('services.amocrm.subdomain'),
                config('services.amocrm.access_token')
            );
        });

        $this->app->bind(WebhookHandlerService::class, concrete: function ($app) {
            return new WebhookHandlerService($app->make(AmoCrmService::class));
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
