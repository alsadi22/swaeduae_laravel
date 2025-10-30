<?php

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "Laravel Application loaded successfully\n";
    
    // Get the router
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    echo "Total routes registered: " . $routes->count() . "\n";
    
    // List first 10 routes
    echo "\nFirst 10 routes:\n";
    $count = 0;
    foreach ($routes as $route) {
        if ($count >= 10) break;
        echo "- " . implode('|', $route->methods()) . " " . $route->uri() . "\n";
        $count++;
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}