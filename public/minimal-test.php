<?php

// Simple test without Laravel bootstrap
echo "<!DOCTYPE html>";
echo "<html><head><title>Minimal Test</title></head><body>";
echo "<h1>SwaedUAE Laravel - Minimal Test</h1>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";

// Test if we can access the Laravel directory
$laravelPath = __DIR__ . '/..';
echo "<p>Laravel Path: " . $laravelPath . "</p>";
echo "<p>Laravel Path Exists: " . (is_dir($laravelPath) ? 'Yes' : 'No') . "</p>";

// Test if we can load the autoloader
$autoloaderPath = $laravelPath . '/vendor/autoload.php';
echo "<p>Autoloader Path: " . $autoloaderPath . "</p>";
echo "<p>Autoloader Exists: " . (file_exists($autoloaderPath) ? 'Yes' : 'No') . "</p>";

// Test if we can access the .env file
$envPath = $laravelPath . '/.env';
echo "<p>Env File Exists: " . (file_exists($envPath) ? 'Yes' : 'No') . "</p>";

// Test if we can access route files
$webRoutesPath = $laravelPath . '/routes/web.php';
echo "<p>Web Routes Exists: " . (file_exists($webRoutesPath) ? 'Yes' : 'No') . "</p>";

echo "<h2>Basic Laravel Test</h2>";

try {
    require_once $autoloaderPath;
    echo "<p>✓ Autoloader loaded successfully</p>";
    
    // Test if we can create a basic Laravel app without booting
    $app = new \Illuminate\Foundation\Application($laravelPath);
    echo "<p>✓ Laravel Application instance created</p>";
    
    // Test if we can access the config
    $configPath = $laravelPath . '/config/app.php';
    if (file_exists($configPath)) {
        $config = require $configPath;
        echo "<p>✓ Config loaded. App name: " . ($config['name'] ?? 'Unknown') . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='/'>Try Homepage</a> | <a href='/events'>Try Events</a></p>";
echo "</body></html>";