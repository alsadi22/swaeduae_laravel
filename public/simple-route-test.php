<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Facades\Route;

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "Laravel Application loaded\n";
    
    // Try to manually register a simple route
    Route::get('/manual-test', function () {
        return 'Manual route works!';
    });
    
    // Get the router
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    echo "Total routes: " . $routes->count() . "\n";
    
    // Try to find our manual route
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'manual-test')) {
            echo "Found manual route: " . $route->uri() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}