<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // dd('Middleware loading');

        $middleware->prependToGroup('web', Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash::class); // enable automatic redirection on incorrect URL trailing slashes

        $middleware->prependToGroup('web', [
            \App\Http\Middleware\RemoveIndexPhp::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->register(new Illuminatech\UrlTrailingSlash\RoutingServiceProvider($app)); // register trailing slashes routing

return $app;
