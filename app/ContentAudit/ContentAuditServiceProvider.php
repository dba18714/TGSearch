<?php

namespace App\ContentAudit;

use Illuminate\Support\ServiceProvider;

class ContentAuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/content-audit.php', 'content-audit'
        );

        $this->app->singleton('content-audit', function ($app) {
            return new ContentAuditManager($app);
        });

        $this->app->singleton(Contracts\ContentAuditInterface::class, function ($app) {
            return $app->make('content-audit')->driver();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/content-audit.php' => config_path('content-audit.php'),
        ], 'content-audit-config');
    }
}