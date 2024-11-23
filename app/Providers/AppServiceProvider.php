<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GoogleCustomSearchService;
use Illuminate\Http\Client\Factory as HttpFactory;
use OpenAI;
use OpenAI\Client;
use App\Services\OpenAiModerationService;

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

        // 注册 OpenAI 客户端
        $this->app->singleton(Client::class, function () {
            return OpenAI::client(config('services.openai.api_key'));
        });

        // 注册审核服务
        $this->app->singleton(OpenAiModerationService::class, function ($app) {
            return new OpenAiModerationService($app->make(Client::class));
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
