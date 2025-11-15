#!/bin/bash

# Manual Deployment Script for SMATCAMPUS on CyberPanel
# Run this ON YOUR VPS SERVER

echo "🚀 Starting SMATCAMPUS manual deployment..."

# Variables
DOMAIN="frankhost.us"
PROJECT_DIR="/home/frankhost.us/public_html"
REPO_URL="https://github.com/frankhostltd3/skolariscloud3.git"
WEBSITE_USER="frank5934"

# Navigate to project directory
echo "📁 Navigating to project directory: $PROJECT_DIR"
cd $PROJECT_DIR || { echo "❌ Failed to navigate to $PROJECT_DIR"; exit 1; }

# Update repository
if [ -d ".git" ]; then
    echo "🔄 Pulling latest changes from GitHub..."
    git pull origin main
else
    echo "📥 Cloning repository for first time..."
    git clone $REPO_URL .
fi

# Install/update PHP dependencies
echo "📦 Installing PHP dependencies..."
if command -v composer >/dev/null 2>&1; then
    composer install --optimize-autoloader --no-dev
else
    echo "⚠️ Composer not found. Please install Composer first."
    echo "Run: curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer"
fi

# Laravel setup commands
echo "⚙️ Running Laravel setup..."
php artisan migrate --force || echo "⚠️ Migration failed - check database connection"
php artisan config:cache || echo "⚠️ Config cache failed"
php artisan route:cache || echo "⚠️ Route cache failed"
php artisan view:cache || echo "⚠️ View cache failed"

# Set proper permissions for CyberPanel
echo "🔐 Setting proper file permissions..."
chown -R $WEBSITE_USER:$WEBSITE_USER $PROJECT_DIR || echo "⚠️ chown failed - you may need to run as root"
chmod -R 755 $PROJECT_DIR
chmod -R 775 $PROJECT_DIR/storage $PROJECT_DIR/bootstrap/cache

echo ""
echo "✅ Manual deployment completed!"
echo "🌐 Check your site: https://$DOMAIN"
echo ""
echo "📋 If you see issues:"
echo "1. Check .env file has correct database credentials"
echo "2. Verify CyberPanel rewrite rules are set for Laravel"
echo "3. Check error logs in CyberPanel"
