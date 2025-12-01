#!/bin/bash

# Quick VPS Deployment Script
# This script provides interactive prompts to help you deploy

echo "🚀 SMATCAMPUS VPS Deployment Helper"
echo "===================================="
echo ""

# Check if running on Linux
if [[ "$OSTYPE" != "linux-gnu"* ]]; then
    echo "❌ This script must be run on a Linux server"
    exit 1
fi

echo "📋 Pre-deployment Checklist:"
echo "   - VPS with Ubuntu 22.04 or similar"
echo "   - Root or sudo access"
echo "   - Domain name configured"
echo "   - DNS A records pointing to server IP"
echo ""

read -p "Have you completed the above? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Please complete the checklist first"
    exit 1
fi

# Get user inputs
echo ""
echo "📝 Configuration:"
echo ""
read -p "Enter your domain name (e.g., skolaris.com): " DOMAIN
read -p "Enter database name: " DB_NAME
read -p "Enter database username: " DB_USER
read -sp "Enter database password: " DB_PASS
echo ""
read -p "Enter your email for SSL certificate: " EMAIL
echo ""

# Confirmation
echo ""
echo "📋 Summary:"
echo "   Domain: $DOMAIN"
echo "   Database: $DB_NAME"
echo "   DB User: $DB_USER"
echo "   Email: $EMAIL"
echo ""
read -p "Proceed with deployment? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Deployment cancelled"
    exit 1
fi

echo ""
echo "🚀 Starting deployment..."
echo ""

# Update system
echo "📦 Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
echo "🐘 Installing PHP 8.2..."
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-xml php8.2-curl php8.2-gd \
    php8.2-mbstring php8.2-zip php8.2-bcmath \
    php8.2-intl php8.2-soap php8.2-redis

# Install MySQL
echo "🗄️ Installing MySQL..."
sudo apt install -y mysql-server

# Install Nginx
echo "🌐 Installing Nginx..."
sudo apt install -y nginx

# Install Node.js
echo "📦 Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
echo "🎼 Installing Composer..."
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install Supervisor
echo "👮 Installing Supervisor..."
sudo apt install -y supervisor

# Install Certbot
echo "🔒 Installing Certbot..."
sudo apt install -y certbot python3-certbot-nginx

# Configure MySQL
echo "🗄️ Configuring MySQL..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
sudo mysql -e "GRANT ALL PRIVILEGES ON \`tenant_%\`.* TO '$DB_USER'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Clone repository
echo "📥 Cloning repository..."
sudo mkdir -p /var/www
cd /var/www
if [ -d "skolariscloud3" ]; then
    cd skolariscloud3
    sudo git pull origin main
else
    sudo git clone https://github.com/frankhostltd3/skolariscloud3.git
    cd skolariscloud3
fi

# Set up environment
echo "⚙️ Configuring environment..."
if [ ! -f ".env" ]; then
    sudo cp .env.example .env
    # Update .env with provided values
    sudo sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|" .env
    sudo sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
    sudo sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
    sudo sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env
    sudo sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
    sudo sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
fi

# Install dependencies
echo "📦 Installing dependencies..."
sudo COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev --no-interaction
sudo npm install
sudo npm run build

# Generate key
echo "🔑 Generating application key..."
sudo php artisan key:generate --force

# Storage link
echo "🔗 Creating storage link..."
sudo php artisan storage:link

# Run migrations
echo "🗄️ Running migrations..."
sudo php artisan migrate --force

# Cache configuration
echo "⚡ Caching configuration..."
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

# Set permissions
echo "🔐 Setting permissions..."
sudo chown -R www-data:www-data /var/www/skolariscloud3
sudo chmod -R 755 /var/www/skolariscloud3
sudo chmod -R 775 /var/www/skolariscloud3/storage
sudo chmod -R 775 /var/www/skolariscloud3/bootstrap/cache
sudo chmod 644 /var/www/skolariscloud3/.env

# Configure Nginx
echo "🌐 Configuring Nginx..."
sudo cp nginx.conf /etc/nginx/sites-available/skolariscloud
sudo sed -i "s|yourdomain.com|$DOMAIN|g" /etc/nginx/sites-available/skolariscloud
sudo sed -i "s|/var/www/skolariscloud3|$(pwd)|g" /etc/nginx/sites-available/skolariscloud

# Enable site
sudo ln -sf /etc/nginx/sites-available/skolariscloud /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# Configure Supervisor
echo "👮 Configuring Supervisor..."
sudo cp supervisor.conf /etc/supervisor/conf.d/skolariscloud.conf
sudo sed -i "s|/var/www/skolariscloud3|$(pwd)|g" /etc/supervisor/conf.d/skolariscloud.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start skolariscloud-worker:*

# Setup cron
echo "⏰ Setting up cron jobs..."
(sudo crontab -l 2>/dev/null; echo "* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1") | sudo crontab -

# Setup SSL
echo "🔒 Setting up SSL certificate..."
sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email $EMAIL --redirect

# Setup wildcard SSL
echo "🔒 Setting up wildcard SSL..."
echo ""
echo "⚠️ IMPORTANT: You need to manually add a DNS TXT record"
echo "   to verify domain ownership for wildcard certificate."
echo ""
read -p "Press Enter to continue with wildcard SSL setup..."

sudo certbot certonly --manual --preferred-challenges=dns \
  -d $DOMAIN -d *.$DOMAIN --email $EMAIL --agree-tos

echo ""
echo "✅ Deployment completed successfully!"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Your application is now accessible at:"
echo "   Main site: https://$DOMAIN"
echo "   Landlord: https://$DOMAIN/landlord/login"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📝 Next Steps:"
echo "   1. Visit your site and verify it's working"
echo "   2. Create your first landlord account"
echo "   3. Configure email settings in .env"
echo "   4. Set up regular backups"
echo ""
echo "📚 Full documentation: $(pwd)/DEPLOYMENT.md"
echo ""
