<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking database connection...\n";

try {
    $db = app('db');
    $pdo = $db->connection()->getPdo();
    echo "Database connection: SUCCESS\n";
    
    // Check if migrations table exists
    $migrationsExist = \Illuminate\Support\Facades\Schema::hasTable('migrations');
    echo "Migrations table exists: " . ($migrationsExist ? "YES" : "NO") . "\n";
    
    if ($migrationsExist) {
        $migrationCount = \Illuminate\Support\Facades\DB::table('migrations')->count();
        echo "Number of migrations: " . $migrationCount . "\n";
    }
    
    // Check if users table exists
    $usersExist = \Illuminate\Support\Facades\Schema::hasTable('users');
    echo "Users table exists: " . ($usersExist ? "YES" : "NO") . "\n";
    
    // Check if settings table exists
    $settingsExist = \Illuminate\Support\Facades\Schema::hasTable('settings');
    echo "Settings table exists: " . ($settingsExist ? "YES" : "NO") . "\n";
    
    // Check if pages table exists
    $pagesExist = \Illuminate\Support\Facades\Schema::hasTable('pages');
    echo "Pages table exists: " . ($pagesExist ? "YES" : "NO") . "\n";
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>