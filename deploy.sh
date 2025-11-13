#!/bin/bash

# SMATCAMPUS Deployment Script for frankhost.us
# Run this script on your VPS server

echo "🚀 Starting SMATCAMPUS deployment..."

# Variables
DOMAIN="frankhost.us"
PROJECT_DIR="/var/www/html/smatcampus"
REPO_URL="https://github.com/frankhostltd3/skolariscloud3.git"

# Create project directory
echo "📁 Creating project directory..."
sudo mkdir -p $PROJECT_DIR
cd $PROJECT_DIR

# Clone or pull repository
if [ -d ".git" ]; then
    echo "🔄 Updating existing repository..."
    git pull origin main
else
    echo "📥 Cloning repository..."
    git clone $REPO_URL .
fi

# Copy environment file
echo "⚙️ Setting up environment..."
cp .env.example .env

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Set up database
echo "🗄️ Setting up database..."
php artisan migrate --force

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "🔐 Setting file permissions..."
sudo chown -R www-data:www-data $PROJECT_DIR
sudo chmod -R 755 $PROJECT_DIR
sudo chmod -R 775 $PROJECT_DIR/storage
sudo chmod -R 775 $PROJECT_DIR/bootstrap/cache

echo "✅ Deployment completed successfully!"
echo "🌐 Your site should be accessible at: https://$DOMAIN"

# Display next steps
echo ""
echo "📋 Next Steps:"
echo "1. Configure your web server to point to: $PROJECT_DIR/public"
echo "2. Set up SSL certificate for HTTPS"
echo "3. Configure your .env file with actual database credentials"
echo "4. Test the application"