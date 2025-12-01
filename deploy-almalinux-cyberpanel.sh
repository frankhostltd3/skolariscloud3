#!/bin/bash

# SMATCAMPUS Deployment Script for AlmaLinux with CyberPanel
# Server: 203.161.47.72
# User: root

set -e  # Exit on error

echo "🚀 Starting SMATCAMPUS deployment on AlmaLinux with CyberPanel..."

# ============================================
# CONFIGURATION
# ============================================
SERVER_IP="203.161.47.72"
PROJECT_DIR="/home/skolaris.com/public_html"  # Adjust domain
REPO_URL="https://github.com/frankhostltd3/skolariscloud3.git"
WEB_USER="nobody"  # CyberPanel uses 'nobody' for web files
PHP_VERSION="8.2"

# ============================================
# Pre-flight checks
# ============================================
echo "🔍 Checking prerequisites..."

if [[ $EUID -ne 0 ]]; then
   echo "❌ This script must be run as root"
   exit 1
fi

# Check if CyberPanel is installed
if ! command -v cyberpanel &> /dev/null; then
    echo "⚠️ CyberPanel not detected. Continuing anyway..."
fi

# ============================================
# Install missing PHP extensions
# ============================================
echo "📦 Installing/updating PHP extensions..."

# Install EPEL and Remi repository (for newer PHP)
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm || true

# Enable PHP 8.2 module
dnf module reset php -y || true
dnf module enable php:remi-8.2 -y || true

# Install PHP and required extensions
dnf install -y php php-cli php-fpm php-mysqlnd php-zip php-devel \
    php-gd php-mbstring php-curl php-xml php-pear php-bcmath \
    php-json php-intl php-soap php-redis

# Install Composer if not present
if ! command -v composer &> /dev/null; then
    echo "🎼 Installing Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# Install Git if not present
if ! command -v git &> /dev/null; then
    echo "📥 Installing Git..."
    dnf install -y git
fi

# Install Node.js and npm
if ! command -v node &> /dev/null; then
    echo "📦 Installing Node.js..."
    curl -fsSL https://rpm.nodesource.com/setup_18.x | bash -
    dnf install -y nodejs
fi

# Install Supervisor for queue workers
if ! command -v supervisord &> /dev/null; then
    echo "👮 Installing Supervisor..."
    dnf install -y supervisor
    systemctl enable supervisord
    systemctl start supervisord
fi

# ============================================
# Get domain information
# ============================================
echo ""
echo "📝 Domain Configuration:"
read -p "Enter your domain name (e.g., skolaris.com): " DOMAIN
read -p "Enter database name: " DB_NAME
read -p "Enter database username: " DB_USER
read -sp "Enter database password: " DB_PASS
echo ""

# Set project directory based on domain
PROJECT_DIR="/home/${DOMAIN}/public_html"

# ============================================
# Create/update website in CyberPanel
# ============================================
echo ""
echo "📋 IMPORTANT: Create website in CyberPanel first!"
echo "   1. Open CyberPanel at https://${SERVER_IP}:8090"
echo "   2. Go to Websites > Create Website"
echo "   3. Domain: ${DOMAIN}"
echo "   4. Email: admin@${DOMAIN}"
echo "   5. PHP: 8.2"
echo "   6. Create the website"
echo ""
read -p "Have you created the website in CyberPanel? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Please create the website in CyberPanel first, then run this script again."
    exit 1
fi

# ============================================
# Create database via CyberPanel CLI
# ============================================
echo "🗄️ Creating database..."

# Check if database exists
if mysql -u root -e "use ${DB_NAME}" 2>/dev/null; then
    echo "ℹ️ Database ${DB_NAME} already exists"
else
    mysql -u root -e "CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    echo "✅ Database created"
fi

# Create user if doesn't exist
mysql -u root -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -u root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -u root -e "GRANT ALL PRIVILEGES ON \`tenant_%\`.* TO '${DB_USER}'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

echo "✅ Database configured"

# ============================================
# Clone or update repository
# ============================================
echo "📥 Cloning/updating repository..."

if [ ! -d "$PROJECT_DIR" ]; then
    mkdir -p $PROJECT_DIR
fi

cd $PROJECT_DIR

if [ -d ".git" ]; then
    echo "🔄 Updating existing repository..."
    git stash || true
    git pull origin main
else
    echo "📥 Cloning repository..."
    # Backup existing files
    if [ "$(ls -A $PROJECT_DIR)" ]; then
        echo "⚠️ Directory not empty, backing up..."
        mkdir -p ${PROJECT_DIR}_backup_$(date +%Y%m%d_%H%M%S)
        mv ${PROJECT_DIR}/* ${PROJECT_DIR}_backup_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
    fi
    git clone $REPO_URL .
fi

# ============================================
# Set up environment
# ============================================
echo "⚙️ Configuring environment..."

if [ ! -f ".env" ]; then
    cp .env.example .env
    
    # Update .env with provided values
    sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env
    sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
    
    echo "✅ .env file created and configured"
else
    echo "ℹ️ .env file already exists"
fi

# ============================================
# Install dependencies
# ============================================
echo "📦 Installing PHP dependencies..."
COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev --no-interaction

echo "📦 Installing Node dependencies..."
npm install

echo "🏗️ Building frontend assets..."
npm run build

# ============================================
# Set up Laravel
# ============================================
echo "🔑 Generating application key..."
php artisan key:generate --force

echo "🔗 Creating storage link..."
php artisan storage:link || true

# ============================================
# Run migrations
# ============================================
echo "🗄️ Running database migrations..."
read -p "Run migrations now? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    
    # Run tenant migrations
    if php artisan list | grep -q "tenants:migrate"; then
        echo "🏢 Running tenant migrations..."
        php artisan tenants:migrate
    fi
    
    # Seed database
    read -p "Seed database? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
    fi
fi

# ============================================
# Optimize application
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
# Set proper permissions for CyberPanel
# ============================================
echo "🔐 Setting file permissions..."

# CyberPanel uses 'nobody' user
chown -R nobody:nobody $PROJECT_DIR
find $PROJECT_DIR -type f -exec chmod 644 {} \;
find $PROJECT_DIR -type d -exec chmod 755 {} \;
chmod -R 775 $PROJECT_DIR/storage
chmod -R 775 $PROJECT_DIR/bootstrap/cache
chmod 600 $PROJECT_DIR/.env

# ============================================
# Configure Supervisor for queue workers
# ============================================
echo "👮 Configuring queue workers..."

cat > /etc/supervisord.d/skolariscloud.ini <<EOF
[program:skolariscloud-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${PROJECT_DIR}/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=nobody
numprocs=2
redirect_stderr=true
stdout_logfile=${PROJECT_DIR}/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start skolariscloud-worker:*

# ============================================
# Set up cron job
# ============================================
echo "⏰ Setting up cron job..."

# Add Laravel scheduler to crontab if not exists
CRON_CMD="* * * * * cd ${PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1"
(crontab -l 2>/dev/null | grep -v "artisan schedule:run"; echo "$CRON_CMD") | crontab -

# ============================================
# Configure SSL via CyberPanel
# ============================================
echo ""
echo "🔒 SSL Certificate Setup:"
echo "   1. Open CyberPanel at https://${SERVER_IP}:8090"
echo "   2. Go to SSL > Issue SSL"
echo "   3. Select domain: ${DOMAIN}"
echo "   4. Issue SSL certificate"
echo ""

# ============================================
# Final instructions
# ============================================
echo ""
echo "✅ Deployment completed successfully!"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Your application URLs:"
echo "   Main site: https://${DOMAIN}"
echo "   Landlord: https://${DOMAIN}/landlord/login"
echo ""
echo "🎛️ CyberPanel: https://${SERVER_IP}:8090"
echo "   Server: ${SERVER_IP}"
echo ""
echo "📋 Next Steps:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "1. ✅ Issue SSL certificate via CyberPanel"
echo ""
echo "2. 🌐 Configure DNS for wildcard subdomains:"
echo "   A    @      ${SERVER_IP}"
echo "   A    www    ${SERVER_IP}"
echo "   A    *      ${SERVER_IP}"
echo ""
echo "3. 📝 Update .env for email configuration:"
echo "   nano ${PROJECT_DIR}/.env"
echo ""
echo "4. 🔒 Create wildcard SSL in CyberPanel:"
echo "   SSL > Issue SSL > Select *.${DOMAIN}"
echo ""
echo "5. ✅ Test your application:"
echo "   Visit https://${DOMAIN}"
echo ""
echo "6. 🏢 Create first tenant/school:"
echo "   https://${DOMAIN}/landlord/register"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📚 Useful Commands:"
echo "   View logs: tail -f ${PROJECT_DIR}/storage/logs/laravel.log"
echo "   Restart workers: supervisorctl restart skolariscloud-worker:*"
echo "   Clear cache: cd ${PROJECT_DIR} && php artisan cache:clear"
echo ""
