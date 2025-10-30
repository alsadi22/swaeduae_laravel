<?php
/**
 * SwaedUAE Platform Deployment Verification
 * 
 * This script verifies that the deployment was successful and all new features are working.
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Create a kernel instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SwaedUAE Platform Deployment Verification ===\n\n";

// Verification checks
$checks = [
    'Database Connection' => function() {
        try {
            $db = app('db');
            $db->connection()->getPdo();
            return true;
        } catch (Exception $e) {
            return false;
        }
    },
    
    'User Model' => function() {
        try {
            \App\Models\User::first();
            return true;
        } catch (Exception $e) {
            return false;
        }
    },
    
    'Setting Model' => function() {
        try {
            \App\Models\Setting::first();
            return true;
        } catch (Exception $e) {
            return false;
        }
    },
    
    'Page Model' => function() {
        try {
            \App\Models\Page::first();
            return true;
        } catch (Exception $e) {
            return false;
        }
    },
    
    'Unique ID Field' => function() {
        try {
            $user = \App\Models\User::first();
            return $user && isset($user->unique_id);
        } catch (Exception $e) {
            return false;
        }
    },
    
    'Settings Table' => function() {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('settings');
        } catch (Exception $e) {
            return false;
        }
    },
    
    'Pages Table' => function() {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('pages');
        } catch (Exception $e) {
            return false;
        }
    },
    
    'Scheduled Reports Table' => function() {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('scheduled_reports');
        } catch (Exception $e) {
            return false;
        }
    }
];

echo "Running verification checks...\n\n";

$passed = 0;
$total = count($checks);

foreach ($checks as $checkName => $checkFunction) {
    try {
        $result = $checkFunction();
        if ($result) {
            echo "✓ {$checkName}: PASSED\n";
            $passed++;
        } else {
            echo "✗ {$checkName}: FAILED\n";
        }
    } catch (Exception $e) {
        echo "✗ {$checkName}: ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n=== Verification Summary ===\n";
echo "Passed: {$passed}/{$total}\n";

if ($passed == $total) {
    echo "🎉 All checks passed! Deployment was successful.\n";
} else {
    echo "⚠️  Some checks failed. Please review the output above.\n";
}

echo "\n=== Manual Verification Steps ===\n";
echo "1. Visit /admin/settings to verify settings panel functionality\n";
echo "2. Visit /admin/pages to verify page management system\n";
echo "3. Visit /register and /organization/register to verify separate registration flows\n";
echo "4. Create a new user to verify unique ID generation (should start with SV)\n";
echo "5. Check that default pages (About Us, Contact Us, etc.) are accessible\n";
echo "6. Verify that the analytics dashboard shows trend analysis charts\n";
echo "7. Test the custom report builder functionality\n";

?>