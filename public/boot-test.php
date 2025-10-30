<?php

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "Application created\n";
    
    // Boot the application
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    echo "Kernel created\n";
    
    // Create a request
    $request = Illuminate\Http\Request::capture();
    
    echo "Request captured\n";
    
    // Get the router after booting
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    echo "Total routes registered: " . $routes->count() . "\n";
    
    // List first few routes
    $count = 0;
    foreach ($routes as $route) {
        if ($count >= 5) break;
        echo "- " . implode('|', $route->methods()) . " " . $route->uri() . "\n";
        $count++;
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}