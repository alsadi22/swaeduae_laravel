<?php

require_once 'vendor/autoload.php';

// Create a new Laravel application instance
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the mail configuration
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

try {
    Mail::to('admin@swaeduae.ae')->send(new TestEmail('This is a test email from SWAED platform.'));
    echo "Test email sent successfully!\n";
} catch (\Exception $e) {
    echo "Failed to send email: " . $e->getMessage() . "\n";
}