#!/bin/bash

# Deployment Script for Skolaris Cloud 3 on Plesk
# Run this script from the root of your application directory (e.g., ~/httpdocs)

# Exit on error
set -e

echo "🚀 Starting deployment on Plesk..."

# 1. Pull the latest changes
# Uncomment the following lines if you are using Git on the server
# echo "📦 Pulling latest changes from Git..."
# git pull origin main

# 2. Install PHP dependencies
# Note: We assume 'composer' is available in the path. If not, use the full path (e.g., /opt/plesk/php/8.2/bin/php /usr/lib64/plesk-9.0/composer.phar)
echo "📦 Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Install Node dependencies and build assets
# Note: Ensure Node.js is installed and selected in Plesk
if command -v npm &> /dev/null; then
    echo "🎨 Building frontend assets..."
    npm install
    npm run build
else
    echo "⚠️ npm not found. Please ensure Node.js is enabled in Plesk."
fi

# 4. Run database migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# 5. Run tenant migrations
echo "🏢 Running tenant migrations..."
php artisan tenants:migrate

# 6. Clear and cache configuration
echo "🧹 Optimizing..."
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 7. Fix permissions (Optional, usually handled by Plesk, but good to ensure storage is writable)
# echo "🔒 Fixing permissions..."
# chmod -R 775 storage bootstrap/cache

echo "✅ Deployment complete!"
