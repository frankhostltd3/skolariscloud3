#!/bin/bash

# Quick replacement script for CyberPanel
# This will replace everything in your public_html with the Laravel project

echo "🚀 Replacing content in /home/frankhost.us/public_html with SMATCAMPUS..."

# Navigate to the domain directory
cd /home/frankhost.us

# Create backup of existing content
echo "📦 Creating backup..."
mkdir -p backups/$(date +%Y%m%d_%H%M%S)
cp -r public_html/* backups/$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true

# Clear existing content
echo "🧹 Clearing existing content..."
rm -rf public_html/*
rm -rf public_html/.??*

# Clone the Laravel project
echo "📥 Cloning SMATCAMPUS project..."
cd public_html
git clone https://github.com/frankhostltd3/skolariscloud3.git .

# Install dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev

# Setup environment
echo "⚙️ Setting up environment..."
cp .env.example .env

# You'll need to edit this manually with your database credentials
echo "📝 Please edit .env file with your database credentials:"
echo "DB_DATABASE=fran_ugketravel36"
echo "DB_USERNAME=fran_larvcorex7" 
echo "DB_PASSWORD=g7TkP9zvL2@xQ1"

# Generate key and setup
php artisan key:generate --force

# Setup database
echo "🗄️ Running migrations..."
php artisan migrate --force

# Cache everything
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "🔐 Setting permissions..."
chown -R frankhost.us:frankhost.us /home/frankhost.us/public_html
chmod -R 755 /home/frankhost.us/public_html
chmod -R 775 /home/frankhost.us/public_html/storage
chmod -R 775 /home/frankhost.us/public_html/bootstrap/cache

echo "✅ Replacement completed!"
echo "🌐 Your site should now be live at: https://frankhost.us"
echo ""
echo "⚠️  Important: Configure document root to point to 'public' folder in CyberPanel"
echo "   OR use the rewrite rules provided in CYBERPANEL_DEPLOY.md"