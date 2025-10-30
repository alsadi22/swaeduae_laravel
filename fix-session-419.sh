#!/bin/bash

# Fix 419 Page Expired Error
# This script fixes session and CSRF token issues

echo "🔧 Fixing 419 Page Expired Error..."

cd /var/www/swaeduae/swaeduae_laravel

# Update .env file with correct session settings
echo "📝 Updating .env configuration..."

# Remove old SESSION settings if they exist
sed -i '/^SESSION_DOMAIN=/d' .env
sed -i '/^SESSION_SECURE_COOKIE=/d' .env
sed -i '/^SESSION_SAME_SITE=/d' .env

# Add correct SESSION settings
cat >> .env << 'EOF'

# Session Configuration for HTTPS
SESSION_DOMAIN=.swaeduae.ae
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
EOF

echo "✅ .env updated"

# Clear all caches
echo "🗑️  Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Recreate config cache
echo "💾 Creating config cache..."
php artisan config:cache

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data storage/framework/sessions
chmod -R 775 storage/framework/sessions

echo "✅ All done! Please refresh the registration page."
echo ""
echo "📋 Session Settings Applied:"
echo "   - SESSION_DOMAIN: .swaeduae.ae"
echo "   - SESSION_SECURE_COOKIE: true"
echo "   - SESSION_SAME_SITE: lax"
echo ""
echo "🔄 Users should now:"
echo "   1. Clear browser cookies for swaeduae.ae"
echo "   2. Refresh the registration page"
echo "   3. Try registering again"

