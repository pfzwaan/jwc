<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // In production the app runs behind Caddy (127.0.0.1:8002), which terminates
        // TLS and forwards X-Forwarded-*. Without this, asset()/url() emit http://
        // and the browser blocks Filament's CSS/fonts as mixed content.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'honeypot' => \App\Http\Middleware\Honeypot::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
