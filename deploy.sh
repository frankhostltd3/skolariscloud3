#!/bin/bash

# SMATCAMPUS Deployment Script for VPS
# Run this script on your VPS server after customizing the variables

set -e  # Exit on error

echo "🚀 Starting SMATCAMPUS deployment..."

# ============================================
# CONFIGURATION - Customize these variables
# ============================================
DOMAIN="frankhost.us"
PROJECT_DIR="/var/www/skolariscloud3"  # Changed to standard location
REPO_URL="https://github.com/frankhostltd3/skolariscloud3.git"
WEB_USER="www-data"  # Apache/Nginx user (www-data for Ubuntu, nginx for CentOS)
PHP_VERSION="8.2"  # Adjust based on your PHP version

# ============================================
# Pre-flight checks
# ============================================
echo "🔍 Checking prerequisites..."

# Check if running as root or with sudo
if [[ $EUID -ne 0 ]]; then
   echo "❌ This script must be run with sudo privileges"
   exit 1
fi

# Check if composer exists
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install composer first."
    exit 1
fi

# Check if git exists
if ! command -v git &> /dev/null; then
    echo "❌ Git is not installed. Please install git first."
    exit 1
fi

# ============================================
# Create project directory
# ============================================
echo "📁 Creating project directory..."
mkdir -p $PROJECT_DIR
cd $PROJECT_DIR

# ============================================
# Clone or update repository
# ============================================
if [ -d ".git" ]; then
    echo "🔄 Updating existing repository..."
    git pull origin main
else
    echo "📥 Cloning repository..."
    git clone $REPO_URL .
fi

# ============================================
# Set up environment file
# ============================================
echo "⚙️ Setting up environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✅ Created .env file - YOU MUST EDIT THIS FILE with your database credentials"
else
    echo "ℹ️ .env file already exists, skipping..."
fi

# ============================================
# Install PHP dependencies
# ============================================
echo "📦 Installing PHP dependencies..."
COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev --no-interaction

# ============================================
# Install Node dependencies and build assets
# ============================================
if command -v npm &> /dev/null; then
    echo "📦 Installing Node dependencies..."
    npm install
    echo "🏗️ Building frontend assets..."
    npm run build
else
    echo "⚠️ npm not found, skipping frontend build"
fi

# ============================================
# Generate application key
# ============================================
echo "🔑 Generating application key..."
php artisan key:generate --force

# ============================================
# Create storage symbolic link
# ============================================
echo "🔗 Creating storage link..."
php artisan storage:link

# ============================================
# Set up database
# ============================================
echo "🗄️ Running migrations..."
echo "⚠️ Make sure your .env database credentials are correct!"
read -p "Do you want to run migrations now? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force

    # Run tenant migrations if they exist
    if php artisan list | grep -q "tenants:migrate"; then
        echo "🏢 Running tenant migrations..."
        php artisan tenants:migrate
    fi

    # Seed database
    read -p "Do you want to seed the database? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
    fi
fi

# ============================================
# Clear and cache configuration
# ============================================
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# ============================================
# Set proper permissions
# ============================================
echo "🔐 Setting file permissions..."
chown -R $WEB_USER:$WEB_USER $PROJECT_DIR
chmod -R 755 $PROJECT_DIR
chmod -R 775 $PROJECT_DIR/storage
chmod -R 775 $PROJECT_DIR/bootstrap/cache

# Make sure .env is not world-readable
chmod 644 $PROJECT_DIR/.env

# ============================================
# Create log directory if it doesn't exist
# ============================================
mkdir -p $PROJECT_DIR/storage/logs
touch $PROJECT_DIR/storage/logs/laravel.log
chown -R $WEB_USER:$WEB_USER $PROJECT_DIR/storage/logs

echo ""
echo "✅ Deployment completed successfully!"
echo "🌐 Your site should be accessible at: https://$DOMAIN"
echo ""
echo "📋 Important Next Steps:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "1. 📝 Edit .env file with your actual configuration:"
echo "   nano $PROJECT_DIR/.env"
echo ""
echo "2. 🌐 Configure your web server (Apache/Nginx) to point to:"
echo "   $PROJECT_DIR/public"
echo ""
echo "3. 🔒 Set up SSL certificate using Let's Encrypt:"
echo "   sudo certbot --nginx -d $DOMAIN"
echo ""
echo "4. 🗄️ Configure your database credentials in .env"
echo ""
echo "5. ✅ Test the application by visiting: https://$DOMAIN"
echo ""
echo "6. 📱 For multi-tenant setup, configure your wildcard domain:"
echo "   *.yourdomain.com should point to this server"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
