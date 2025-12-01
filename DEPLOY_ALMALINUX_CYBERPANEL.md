# SMATCAMPUS Deployment for AlmaLinux with CyberPanel

**Server IP:** 203.161.47.72  
**Server OS:** AlmaLinux with CyberPanel  
**User:** root  

---

## 🚀 Quick Deployment (One Command)

SSH into your server and run:

```bash
ssh root@203.161.47.72
# Enter password: F!sh9T@ble...

# Download and run deployment script
curl -sL https://raw.githubusercontent.com/frankhostltd3/skolariscloud3/main/deploy-almalinux-cyberpanel.sh -o deploy.sh
chmod +x deploy.sh
./deploy.sh
```

The script will:
- ✅ Install PHP 8.2 and all required extensions
- ✅ Install Composer, Node.js, Git, Supervisor
- ✅ Clone your repository
- ✅ Configure database
- ✅ Install dependencies
- ✅ Run migrations
- ✅ Set up queue workers
- ✅ Configure permissions

**Time required:** 10-15 minutes

---

## 📋 Manual Step-by-Step Guide

If you prefer to do it manually:

### Step 1: SSH into Server
```bash
ssh root@203.161.47.72
# Password: F!sh9T@ble...
```

### Step 2: Create Website in CyberPanel
1. Open browser: `https://203.161.47.72:8090`
2. Login to CyberPanel
3. Go to **Websites > Create Website**
4. Fill in:
   - **Domain:** your-domain.com (e.g., skolaris.com)
   - **Email:** admin@your-domain.com
   - **Package:** Default
   - **PHP:** 8.2
5. Click **Create Website**

### Step 3: Install Required Software
```bash
# Install EPEL and Remi repositories
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm

# Enable PHP 8.2
dnf module reset php -y
dnf module enable php:remi-8.2 -y

# Install PHP and extensions
dnf install -y php php-cli php-fpm php-mysqlnd php-zip php-devel \
    php-gd php-mbstring php-curl php-xml php-pear php-bcmath \
    php-json php-intl php-soap php-redis

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js 18
curl -fsSL https://rpm.nodesource.com/setup_18.x | bash -
dnf install -y nodejs

# Install Supervisor
dnf install -y supervisor
systemctl enable supervisord
systemctl start supervisord

# Install Git (if not present)
dnf install -y git
```

### Step 4: Create Database
```bash
# Create database
mysql -u root -e "CREATE DATABASE skolaris_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Create user and grant privileges
mysql -u root -e "CREATE USER 'skolaris_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';"
mysql -u root -e "GRANT ALL PRIVILEGES ON skolaris_db.* TO 'skolaris_user'@'localhost';"
mysql -u root -e "GRANT ALL PRIVILEGES ON \`tenant_%\`.* TO 'skolaris_user'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"
```

### Step 5: Clone Repository
```bash
# Navigate to website directory (replace with your domain)
cd /home/your-domain.com/public_html

# Clone repository
git clone https://github.com/frankhostltd3/skolariscloud3.git .
```

### Step 6: Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Edit .env file
nano .env
```

Update these values:
```env
APP_NAME="SMATCAMPUS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skolaris_db
DB_USERNAME=skolaris_user
DB_PASSWORD=YourStrongPassword123!

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

Save with `Ctrl+X`, then `Y`, then `Enter`

### Step 7: Install Dependencies
```bash
# Install PHP dependencies
COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Build frontend assets
npm run build
```

### Step 8: Set Up Laravel
```bash
# Generate application key
php artisan key:generate --force

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force

# Run tenant migrations
php artisan tenants:migrate

# (Optional) Seed database
php artisan db:seed --force
```

### Step 9: Optimize Application
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 10: Set Permissions
```bash
# Set ownership (CyberPanel uses 'nobody')
chown -R nobody:nobody /home/your-domain.com/public_html

# Set directory permissions
find /home/your-domain.com/public_html -type f -exec chmod 644 {} \;
find /home/your-domain.com/public_html -type d -exec chmod 755 {} \;

# Storage and cache writable
chmod -R 775 /home/your-domain.com/public_html/storage
chmod -R 775 /home/your-domain.com/public_html/bootstrap/cache

# Secure .env
chmod 600 /home/your-domain.com/public_html/.env
```

### Step 11: Configure Queue Workers
```bash
# Create supervisor config
cat > /etc/supervisord.d/skolariscloud.ini <<'EOF'
[program:skolariscloud-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/your-domain.com/public_html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=nobody
numprocs=2
redirect_stderr=true
stdout_logfile=/home/your-domain.com/public_html/storage/logs/worker.log
stopwaitsecs=3600
EOF

# Update supervisor
supervisorctl reread
supervisorctl update
supervisorctl start skolariscloud-worker:*
```

### Step 12: Set Up Cron Job
```bash
# Add to crontab
crontab -e

# Add this line:
* * * * * cd /home/your-domain.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### Step 13: Issue SSL Certificate
1. Open CyberPanel: `https://203.161.47.72:8090`
2. Go to **SSL > Issue SSL**
3. Select your domain
4. Click **Issue SSL**

For wildcard subdomain support:
1. Go to **SSL > Issue SSL**
2. Select domain: `*.your-domain.com`
3. Click **Issue SSL**

### Step 14: Configure DNS
Add these DNS records at your domain registrar:

```
Type    Name    Value              TTL
A       @       203.161.47.72      3600
A       www     203.161.47.72      3600
A       *       203.161.47.72      3600
```

---

## 🎯 Post-Deployment Testing

### 1. Test Main Website
```bash
curl -I https://your-domain.com
# Should return: HTTP/2 200
```

### 2. Check Queue Workers
```bash
supervisorctl status
# Should show: skolariscloud-worker:skolariscloud-worker_00 RUNNING
```

### 3. View Application Logs
```bash
tail -f /home/your-domain.com/public_html/storage/logs/laravel.log
```

### 4. Test Database Connection
```bash
cd /home/your-domain.com/public_html
php artisan tinker
# Run: DB::connection()->getPdo();
# Should not throw errors
```

---

## 🔧 Useful Commands

### View Logs
```bash
# Application logs
tail -f /home/your-domain.com/public_html/storage/logs/laravel.log

# Worker logs
tail -f /home/your-domain.com/public_html/storage/logs/worker.log

# Nginx error logs (via CyberPanel)
# Websites > List Websites > Manage > Error Log
```

### Restart Services
```bash
# Restart queue workers
supervisorctl restart skolariscloud-worker:*

# Restart PHP-FPM
systemctl restart php-fpm

# Restart Nginx (via CyberPanel recommended)
# Or: systemctl restart lscpd
```

### Clear Cache
```bash
cd /home/your-domain.com/public_html
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Update Application
```bash
cd /home/your-domain.com/public_html
git pull origin main
composer install --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart skolariscloud-worker:*
```

---

## 🐛 Troubleshooting

### Issue: 500 Internal Server Error
```bash
# Check error logs
tail -100 /home/your-domain.com/public_html/storage/logs/laravel.log

# Common fixes:
chmod -R 775 storage bootstrap/cache
chown -R nobody:nobody /home/your-domain.com/public_html
php artisan config:clear
```

### Issue: Database Connection Failed
```bash
# Test connection
mysql -u skolaris_user -p skolaris_db

# Check .env file
cat /home/your-domain.com/public_html/.env | grep DB_

# Verify credentials match
```

### Issue: Queue Not Processing
```bash
# Check supervisor status
supervisorctl status

# Restart workers
supervisorctl restart skolariscloud-worker:*

# Check worker logs
tail -50 /home/your-domain.com/public_html/storage/logs/worker.log
```

### Issue: Subdomain Not Working
1. Verify wildcard DNS record exists (`* A 203.161.47.72`)
2. Issue wildcard SSL certificate in CyberPanel
3. Check tenant database exists
4. Clear application cache

### Issue: SSL Certificate Fails
- Ensure port 80 and 443 are open in firewall
- Check domain DNS is propagated: `nslookup your-domain.com`
- Try manual SSL via CyberPanel interface

---

## 📞 Support

- **GitHub:** https://github.com/frankhostltd3/skolariscloud3
- **Server IP:** 203.161.47.72
- **CyberPanel:** https://203.161.47.72:8090

---

## ✅ Deployment Checklist

- [ ] Website created in CyberPanel
- [ ] PHP 8.2 and extensions installed
- [ ] Composer installed
- [ ] Node.js 18+ installed
- [ ] Supervisor installed
- [ ] Database created and configured
- [ ] Repository cloned
- [ ] .env file configured
- [ ] Dependencies installed (PHP + Node)
- [ ] Application key generated
- [ ] Migrations run (central + tenant)
- [ ] Storage link created
- [ ] Permissions set correctly
- [ ] Queue workers configured
- [ ] Cron job set up
- [ ] SSL certificate issued
- [ ] DNS configured (A + wildcard)
- [ ] Application accessible via HTTPS
- [ ] Subdomains working (test.your-domain.com)

---

**Total Deployment Time:** 15-20 minutes with automated script, 30-45 minutes manually.
