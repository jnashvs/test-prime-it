<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

// For Sanctum SPA authentication with API routes
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies; // If you use encrypted cookies
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: [
            'appearance',
            'sidebar_state',
            // Add Laravel's default XSRF-TOKEN cookie if not already excluded by default
            // in your Laravel version, though usually it's handled.
            // 'XSRF-TOKEN',
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Configure API middleware for Sanctum SPA
        $middleware->api(prepend: [ // Prepend to ensure it runs early
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->api(append: [
            // If your cookies are encrypted and you need to access session from API
            // \Illuminate\Cookie\Middleware\EncryptCookies::class, // Ensure this is appropriate for your setup
            StartSession::class,
            // Add other API specific middleware here if needed
            // e.g., \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
