#!/bin/bash

# SwaedUAE Platform Complete Deployment Script
# This script finalizes the deployment of all updates to the production website

echo "=== SwaedUAE Platform Complete Deployment ==="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "Error: This script must be run from the Laravel project root directory"
    exit 1
fi

echo "1. Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "2. Optimizing autoloader..."
composer dump-autoload --optimize

echo "3. Setting proper file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "4. Running final deployment verification..."
php final-deployment-check.php

echo "5. Deployment complete!"
echo ""
echo "=== Final Steps ==="
echo "✓ Please ensure nginx is configured with the provided configuration"
echo "✓ Restart nginx: sudo systemctl restart nginx"
echo "✓ Restart PHP-FPM: sudo systemctl restart php8.3-fpm"
echo "✓ Test the website at https://swaeduae.ae"
echo ""
echo "🎉 All updates have been deployed to the production website!"