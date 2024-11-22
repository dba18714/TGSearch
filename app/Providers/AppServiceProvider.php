<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GoogleCustomSearchService;
use Illuminate\Http\Client\Factory as HttpFactory;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GoogleCustomSearchService::class, function ($app) {
            return new GoogleCustomSearchService(
                $app->make(HttpFactory::class),
                config('services.google.search_api_key'),
                config('services.google.search_engine_id')
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
