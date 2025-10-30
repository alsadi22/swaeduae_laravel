<?php

/**
 * Authentication System Test Script
 * Tests registration, login, and email verification flow
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Models\User;

echo "\n========================================\n";
echo "Authentication System Test\n";
echo "========================================\n\n";

// Test 1: Check routes
echo "1. Checking Authentication Routes...\n";
$authRoutes = [
    'register',
    'organization.register',
    'login',
    'logout',
    'verification.notice',
    'verification.verify',
    'verification.send',
];

foreach ($authRoutes as $routeName) {
    if (Route::has($routeName)) {
        echo "   ✓ Route '{$routeName}' exists\n";
    } else {
        echo "   ✗ Route '{$routeName}' NOT FOUND\n";
    }
}

// Test 2: Check dashboard routes
echo "\n2. Checking Dashboard Routes...\n";
$dashboardRoutes = [
    'admin.dashboard',
    'organization.dashboard',
    'volunteer.dashboard',
];

foreach ($dashboardRoutes as $routeName) {
    if (Route::has($routeName)) {
        echo "   ✓ Route '{$routeName}' exists\n";
    } else {
        echo "   ✗ Route '{$routeName}' NOT FOUND\n";
    }
}

// Test 3: Check mail configuration
echo "\n3. Checking Mail Configuration...\n";
$mailDriver = Config::get('mail.default');
$mailFrom = Config::get('mail.from.address');
$mailFromName = Config::get('mail.from.name');
$appUrl = Config::get('app.url');

echo "   Mail Driver: {$mailDriver}\n";
echo "   Mail From: {$mailFrom}\n";
echo "   Mail From Name: {$mailFromName}\n";
echo "   App URL: {$appUrl}\n";

if ($mailDriver === 'log') {
    echo "   ⚠ WARNING: Mail driver is set to 'log'. Emails will be written to log files instead of being sent.\n";
    echo "   To fix: Update MAIL_MAILER in .env to 'smtp' and configure SMTP settings.\n";
}

// Test 4: Check User model email verification
echo "\n4. Checking User Model Email Verification...\n";
$userModel = new User();
$implements = class_implements($userModel);

if (isset($implements['Illuminate\Contracts\Auth\MustVerifyEmail'])) {
    echo "   ✓ User model implements MustVerifyEmail interface\n";
} else {
    echo "   ✗ User model does NOT implement MustVerifyEmail interface\n";
}

// Test 5: Check middleware
echo "\n5. Checking Middleware Registration...\n";
$middlewareAliases = app('router')->getMiddleware();

$requiredMiddleware = ['guest', 'auth', 'verified', 'role', 'permission'];
foreach ($requiredMiddleware as $middleware) {
    if (isset($middlewareAliases[$middleware])) {
        echo "   ✓ Middleware '{$middleware}' is registered\n";
    } else {
        echo "   ⚠ Middleware '{$middleware}' is NOT registered\n";
    }
}

// Test 6: Check recent user registrations
echo "\n6. Checking Recent User Registrations...\n";
$recentUsers = User::orderBy('created_at', 'desc')->take(5)->get(['id', 'name', 'email', 'email_verified_at', 'created_at']);

if ($recentUsers->count() > 0) {
    echo "   Recent users:\n";
    foreach ($recentUsers as $user) {
        $verified = $user->email_verified_at ? '✓ Verified' : '✗ Not Verified';
        echo "   - {$user->name} ({$user->email}) - {$verified} - Created: {$user->created_at}\n";
    }
} else {
    echo "   No users found in database\n";
}

// Test 7: Check for unverified users
echo "\n7. Checking Unverified Users...\n";
$unverifiedUsers = User::whereNull('email_verified_at')->count();
echo "   Total unverified users: {$unverifiedUsers}\n";

if ($unverifiedUsers > 0) {
    echo "   ⚠ There are {$unverifiedUsers} users who haven't verified their email\n";
}

// Test 8: Check roles
echo "\n8. Checking Roles...\n";
try {
    $roles = \Spatie\Permission\Models\Role::all(['name']);
    if ($roles->count() > 0) {
        echo "   Available roles:\n";
        foreach ($roles as $role) {
            echo "   - {$role->name}\n";
        }
    } else {
        echo "   ⚠ No roles found in database\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error checking roles: " . $e->getMessage() . "\n";
}

// Test 9: Check log files for verification emails
echo "\n9. Checking Log Files for Email Verification...\n";
$logFile = storage_path('logs/laravel.log');

if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $hasVerificationEmail = strpos($logContent, 'verification') !== false || strpos($logContent, 'Verify Email') !== false;
    
    if ($hasVerificationEmail) {
        echo "   ✓ Found verification email entries in log file\n";
        echo "   Note: Emails are being logged, not sent. Check {$logFile}\n";
    } else {
        echo "   ⚠ No verification email entries found in log file\n";
    }
} else {
    echo "   ✗ Log file not found\n";
}

// Test 10: Check .env configuration
echo "\n10. Checking Environment Configuration...\n";
$envFile = base_path('.env');

if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Check critical settings
    $criticalSettings = [
        'APP_URL' => env('APP_URL'),
        'MAIL_MAILER' => env('MAIL_MAILER'),
        'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    ];
    
    foreach ($criticalSettings as $key => $value) {
        echo "   {$key}={$value}\n";
    }
} else {
    echo "   ✗ .env file not found\n";
}

echo "\n========================================\n";
echo "Recommendations:\n";
echo "========================================\n";

$recommendations = [];

if ($mailDriver === 'log') {
    $recommendations[] = "1. Configure SMTP mail settings in .env to send actual emails";
    $recommendations[] = "   Example SMTP configuration:";
    $recommendations[] = "   MAIL_MAILER=smtp";
    $recommendations[] = "   MAIL_HOST=smtp.mailtrap.io (or your SMTP server)";
    $recommendations[] = "   MAIL_PORT=587";
    $recommendations[] = "   MAIL_USERNAME=your_username";
    $recommendations[] = "   MAIL_PASSWORD=your_password";
    $recommendations[] = "   MAIL_ENCRYPTION=tls";
    $recommendations[] = "   MAIL_FROM_ADDRESS=noreply@swaeduae.ae";
}

if ($unverifiedUsers > 0) {
    $recommendations[] = "\n2. Manually verify test users if needed using:";
    $recommendations[] = "   php artisan tinker";
    $recommendations[] = "   User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);";
}

$recommendations[] = "\n3. Test email sending with:";
$recommendations[] = "   php artisan tinker";
$recommendations[] = "   Mail::raw('Test email', function(\$message) { \$message->to('test@example.com')->subject('Test'); });";

if (empty($recommendations)) {
    echo "   ✓ No critical issues found!\n";
} else {
    foreach ($recommendations as $rec) {
        echo $rec . "\n";
    }
}

echo "\n========================================\n";
echo "Test Complete\n";
echo "========================================\n\n";
