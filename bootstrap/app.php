<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\VerifyMpesaWebhookIp;
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
        $middleware->trustProxies(at: array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TRUSTED_PROXIES', '')),
        ))));

        $middleware->append(SecurityHeaders::class);
        $middleware->alias([
            'mpesa.webhook' => VerifyMpesaWebhookIp::class,
        ]);
        $middleware->web(append: [
            'throttle:global',
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/mpesa/callback',
            'api/c2b/validate',
            'api/c2b/confirm',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
