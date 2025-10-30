<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Laravel Bootstrap Test</h1>";

try {
    // Load the autoloader
    require_once __DIR__.'/../vendor/autoload.php';
    echo "<p>✓ Autoloader loaded</p>";
    
    // Create the application
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "<p>✓ Application created</p>";
    
    // Boot the application properly
    $app->boot();
    echo "<p>✓ Application booted</p>";
    
    // Get the router
    $router = $app->make('router');
    $routes = $router->getRoutes();
    echo "<p>✓ Router loaded with " . count($routes) . " routes</p>";
    
    // List all routes
    echo "<h2>All Routes:</h2>";
    echo "<ul>";
    foreach ($routes as $route) {
        echo "<li>" . implode('|', $route->methods()) . " " . $route->uri() . " -> " . $route->getActionName() . "</li>";
    }
    echo "</ul>";
    
    // Test a simple request
    echo "<h2>Request Test:</h2>";
    $request = \Illuminate\Http\Request::create('/', 'GET');
    echo "<p>Created request for: " . $request->getUri() . "</p>";
    
    try {
        $response = $app->handle($request);
        echo "<p>✓ Request handled successfully</p>";
        echo "<p>Response status: " . $response->getStatusCode() . "</p>";
        echo "<p>Response content preview:</p>";
        echo "<pre>" . htmlspecialchars(substr($response->getContent(), 0, 500)) . "</pre>";
    } catch (Exception $e) {
        echo "<p>❌ Request handling failed: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Bootstrap error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}