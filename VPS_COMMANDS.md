# VPS Deployment Commands - Copy & Paste

## ⚡ FASTEST METHOD - One-Line Installation

SSH into your VPS and run this single command:

```bash
curl -sL https://raw.githubusercontent.com/frankhostltd3/skolariscloud3/main/quick-deploy.sh | sudo bash
```

**That's it!** The script will guide you through the entire setup.

---

## 📋 What You Need Before Starting

1. **VPS Server**
   - Ubuntu 22.04 LTS (recommended)
   - At least 2GB RAM, 2 CPU cores
   - 20GB+ storage
   - Root or sudo access

2. **Domain Name**
   - Your domain (e.g., skolaris.com)
   - DNS configured to point to your VPS IP

3. **Information Ready**
   - VPS IP address
   - Domain name
   - Email for SSL certificates
   - Desired database name and password

---

## 🚀 Step-by-Step Manual Commands

If you prefer manual control, copy these commands one by one:

### 1. Connect to VPS
```bash
ssh root@YOUR_VPS_IP
```

### 2. Update System
```bash
apt update && apt upgrade -y
```

### 3. Install Required Software
```bash
# Add PHP repository
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.2 and extensions
apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml \
  php8.2-curl php8.2-gd php8.2-mbstring php8.2-zip php8.2-bcmath \
  php8.2-intl php8.2-soap php8.2-redis

# Install MySQL
apt install -y mysql-server

# Install Nginx
apt install -y nginx

# Install Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Supervisor
apt install -y supervisor

# Install Certbot for SSL
apt install -y certbot python3-certbot-nginx

# Install Git
apt install -y git
```

### 4. Secure MySQL
```bash
mysql_secure_installation
```

### 5. Create Database
```bash
mysql -u root -p
```

Then in MySQL:
```sql
CREATE DATABASE skolariscloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'skolariscloud'@'localhost' IDENTIFIED BY 'YOUR_SECURE_PASSWORD';
GRANT ALL PRIVILEGES ON skolariscloud.* TO 'skolariscloud'@'localhost';
GRANT ALL PRIVILEGES ON `tenant_%`.* TO 'skolariscloud'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6. Clone Application
```bash
cd /var/www
git clone https://github.com/frankhostltd3/skolariscloud3.git
cd skolariscloud3
```

### 7. Set Up Environment
```bash
cp .env.example .env
nano .env
```

Update these values in .env:
```env
APP_URL=https://yourdomain.com
DB_DATABASE=skolariscloud
DB_USERNAME=skolariscloud
DB_PASSWORD=YOUR_SECURE_PASSWORD
APP_ENV=production
APP_DEBUG=false
```

### 8. Install Dependencies
```bash
COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 9. Set Up Application
```bash
php artisan key:generate --force
php artisan storage:link
php artisan migrate --force
```

### 10. Configure Nginx
```bash
cp nginx.conf /etc/nginx/sites-available/skolariscloud

# Edit the config file - replace yourdomain.com with your actual domain
nano /etc/nginx/sites-available/skolariscloud

# Enable the site
ln -s /etc/nginx/sites-available/skolariscloud /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default

# Test configuration
nginx -t

# Restart Nginx
systemctl restart nginx
```

### 11. Set Up SSL
```bash
# For main domain
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# For wildcard (subdomains) - requires DNS verification
certbot certonly --manual --preferred-challenges=dns \
  -d yourdomain.com -d *.yourdomain.com
```

### 12. Configure Queue Workers
```bash
cp supervisor.conf /etc/supervisor/conf.d/skolariscloud.conf
supervisorctl reread
supervisorctl update
supervisorctl start skolariscloud-worker:*
```

### 13. Set Up Cron Job
```bash
crontab -e
```

Add this line:
```cron
* * * * * cd /var/www/skolariscloud3 && php artisan schedule:run >> /dev/null 2>&1
```

### 14. Set Permissions
```bash
chown -R www-data:www-data /var/www/skolariscloud3
chmod -R 755 /var/www/skolariscloud3
chmod -R 775 /var/www/skolariscloud3/storage
chmod -R 775 /var/www/skolariscloud3/bootstrap/cache
```

### 15. Cache Configuration
```bash
cd /var/www/skolariscloud3
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ Verification

Visit your domain:
- Main site: `https://yourdomain.com`
- Landlord panel: `https://yourdomain.com/landlord/login`

---

## 🔧 Useful Commands

### Check Status
```bash
# Nginx
systemctl status nginx

# PHP-FPM
systemctl status php8.2-fpm

# MySQL
systemctl status mysql

# Supervisor
supervisorctl status

# Queue workers
supervisorctl status skolariscloud-worker:*
```

### View Logs
```bash
# Laravel logs
tail -f /var/www/skolariscloud3/storage/logs/laravel.log

# Nginx error logs
tail -f /var/log/nginx/error.log

# Worker logs
tail -f /var/www/skolariscloud3/storage/logs/worker.log
```

### Restart Services
```bash
systemctl restart nginx
systemctl restart php8.2-fpm
supervisorctl restart skolariscloud-worker:*
```

---

## 🆘 Emergency Commands

### Site Not Loading
```bash
# Check Nginx syntax
nginx -t

# Check PHP-FPM
systemctl status php8.2-fpm

# Check file permissions
ls -la /var/www/skolariscloud3/public
```

### Database Connection Error
```bash
# Test database connection
mysql -u skolariscloud -p skolariscloud

# Check .env database credentials
cat /var/www/skolariscloud3/.env | grep DB_
```

### Permission Issues
```bash
cd /var/www/skolariscloud3
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### Clear All Cache
```bash
cd /var/www/skolariscloud3
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

## 📞 Need Help?

1. Check full documentation: `DEPLOYMENT.md`
2. Review quick guide: `QUICK_DEPLOY.md`
3. GitHub Issues: https://github.com/frankhostltd3/skolariscloud3/issues

---

**Remember:** After deployment, immediately:
1. Change all default passwords
2. Configure email in .env
3. Set up regular backups
4. Test the application thoroughly
