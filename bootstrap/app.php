<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
            
            Route::middleware('web')
                ->group(base_path('routes/organization.php'));
            
            Route::middleware('web')
                ->group(base_path('routes/volunteer.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        // Temporarily commented out to debug routing issues
        // $middleware->alias([
        //     'role' => \App\Http\Middleware\RoleMiddleware::class,
        //     'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
