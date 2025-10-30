<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Route Registration Test</h1>";

try {
    // Load the autoloader
    require_once __DIR__.'/../vendor/autoload.php';
    echo "<p>✓ Autoloader loaded</p>";
    
    // Create a minimal application instance
    $app = new \Illuminate\Foundation\Application(__DIR__.'/..');
    echo "<p>✓ Application instance created</p>";
    
    // Register essential services
    $app->singleton(
        \Illuminate\Contracts\Http\Kernel::class,
        \App\Http\Kernel::class
    );
    
    $app->singleton(
        \Illuminate\Contracts\Console\Kernel::class,
        \App\Console\Kernel::class
    );
    
    $app->singleton(
        \Illuminate\Contracts\Debug\ExceptionHandler::class,
        \App\Exceptions\Handler::class
    );
    
    echo "<p>✓ Essential services registered</p>";
    
    // Create router instance
    $router = new \Illuminate\Routing\Router(new \Illuminate\Events\Dispatcher($app), $app);
    echo "<p>✓ Router created</p>";
    
    // Set up Route facade
    \Illuminate\Support\Facades\Facade::setFacadeApplication($app);
    $app->instance('router', $router);
    
    echo "<p>✓ Route facade configured</p>";
    
    // Test manual route registration
    $router->get('/', function() {
        return 'Homepage works!';
    });
    
    $router->get('/test', function() {
        return 'Test route works!';
    });
    
    echo "<p>✓ Manual routes registered</p>";
    
    // Check routes
    $routes = $router->getRoutes();
    echo "<p>Routes count: " . count($routes) . "</p>";
    
    // Now try to include web.php
    echo "<h2>Including web.php routes:</h2>";
    
    if (file_exists(__DIR__.'/../routes/web.php')) {
        ob_start();
        include __DIR__.'/../routes/web.php';
        $output = ob_get_clean();
        echo "<p>✓ web.php included. Output: " . htmlspecialchars($output) . "</p>";
        
        $routes = $router->getRoutes();
        echo "<p>Routes after web.php: " . count($routes) . "</p>";
        
        // List all routes
        echo "<h3>All Routes:</h3>";
        echo "<ul>";
        foreach ($routes as $route) {
            echo "<li>" . implode('|', $route->methods()) . " " . $route->uri() . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ web.php not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}