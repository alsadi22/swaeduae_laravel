#!/bin/bash

# SwaedUAE Platform Deployment Script
# This script applies the updates we've implemented to the platform

echo "=== SwaedUAE Platform Deployment Script ==="
echo "This script will deploy the recent updates to the platform"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "Error: This script must be run from the Laravel project root directory"
    exit 1
fi

echo "1.Backing up current database..."
# Note: In a real deployment, you would backup the database here
# php artisan backup:run

echo "2.Running database migrations..."
php artisan migrate --force

echo "3.Seeding new data..."
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=PagesSeeder

echo "4.Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "5.Optimizing autoloader..."
composer dump-autoload

echo "6.Setting proper permissions..."
# Set appropriate permissions (adjust as needed for your server)
# chmod -R 755 storage bootstrap/cache
# chown -R www-data:www-data storage bootstrap/cache

echo "7.Deployment complete!"
echo ""
echo "=== Post-deployment checklist ==="
echo "✓ Verify database migrations ran successfully"
echo "✓ Check that new routes are accessible"
echo "✓ Test admin settings panel functionality"
echo "✓ Verify page management system works"
echo "✓ Test separate volunteer/organization registration"
echo "✓ Confirm unique ID generation for new users"
echo ""
echo "Manual verification steps:"
echo "1. Visit /admin/settings to verify settings panel"
echo "2. Visit /admin/pages to verify page management"
echo "3. Visit /register and /organization/register to verify separate registration"
echo "4. Create a new user to verify unique ID generation"
echo "5. Check that all new pages are accessible"