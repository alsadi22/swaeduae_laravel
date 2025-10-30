<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Facades\Route;

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Set the application instance for facades
    Illuminate\Support\Facades\Facade::setFacadeApplication($app);
    
    echo "Application and facades set up\n";
    
    // Manually include the web routes
    echo "Loading web routes...\n";
    include __DIR__.'/../routes/web.php';
    
    echo "Web routes loaded\n";
    
    // Get the router
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    echo "Total routes registered: " . $routes->count() . "\n";
    
    // List all routes
    foreach ($routes as $route) {
        echo "- " . implode('|', $route->methods()) . " " . $route->uri() . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}