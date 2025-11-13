# CyberPanel Deployment Guide for SMATCAMPUS

## 🚀 Deploying to CyberPanel on AlmaLinux VPS

### Server Details
- **Domain:** frankhost.us
- **Server IP:** 209.74.83.45
- **Panel:** CyberPanel
- **OS:** AlmaLinux
- **Path:** /home/frankhost.us/public_html/

### Prerequisites
Make sure you have:
- CyberPanel installed and running
- Domain `frankhost.us` added to CyberPanel
- Database created in CyberPanel
- SSH access to your VPS

## Step-by-Step Deployment

### 1. SSH into Your VPS
```bash
ssh root@209.74.83.45
```

### 2. Navigate to Your Domain Directory
```bash
cd /home/frankhost.us/public_html
```

### 3. Backup Existing Files (if any)
```bash
# Create backup directory
mkdir -p /home/frankhost.us/backups/$(date +%Y%m%d_%H%M%S)
# Move existing files to backup
mv * /home/frankhost.us/backups/$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
```

### 4. Clone Your Repository
```bash
# Clone the repository
git clone https://github.com/frankhostltd3/skolariscloud3.git .

# Or if you want to replace everything:
rm -rf * .*
git clone https://github.com/frankhostltd3/skolariscloud3.git .
```

### 5. Install Dependencies
```bash
# Install Composer if not available
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install PHP dependencies
composer install --optimize-autoloader --no-dev
```

### 6. Configure Environment
```bash
# Copy and edit environment file
cp .env.example .env
nano .env
```

**Edit .env with your settings:**
```env
APP_NAME=SMATCAMPUS
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://frankhost.us

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=fran_ugketravel36
DB_USERNAME=fran_larvcorex7
DB_PASSWORD=g7TkP9zvL2@xQ1

# Add other configurations as needed
```

### 7. Generate Application Key & Setup Database
```bash
# Generate application key
php artisan key:generate --force

# Run database migrations
php artisan migrate --force

# Cache configuration for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Set Proper Permissions
```bash
# Set ownership to the domain user
chown -R frankhost.us:frankhost.us /home/frankhost.us/public_html

# Set proper permissions
chmod -R 755 /home/frankhost.us/public_html
chmod -R 775 /home/frankhost.us/public_html/storage
chmod -R 775 /home/frankhost.us/public_html/bootstrap/cache
```

### 9. Configure Document Root in CyberPanel

#### Option A: Using CyberPanel Web Interface
1. Login to CyberPanel: `https://209.74.83.45:8090`
2. Go to **Websites → List Websites**
3. Find `frankhost.us` and click **Manage**
4. Go to **Configurations → Rewrite Rules**
5. Add Laravel rewrite rules:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ /public/$1 [L]
```

#### Option B: Move Files to Match CyberPanel Structure
```bash
# Move all Laravel files to a subdirectory
mkdir -p /home/frankhost.us/laravel
mv /home/frankhost.us/public_html/* /home/frankhost.us/laravel/
mv /home/frankhost.us/public_html/.* /home/frankhost.us/laravel/ 2>/dev/null || true

# Move public folder contents to public_html
mv /home/frankhost.us/laravel/public/* /home/frankhost.us/public_html/
mv /home/frankhost.us/laravel/public/.* /home/frankhost.us/public_html/ 2>/dev/null || true

# Update index.php paths
sed -i "s|__DIR__.'/../|'/home/frankhost.us/laravel/'|g" /home/frankhost.us/public_html/index.php
```

### 10. SSL Certificate (Optional but Recommended)
```bash
# Install SSL via CyberPanel or use Let's Encrypt
# In CyberPanel: Websites → List Websites → frankhost.us → SSL → Issue SSL
```

## Quick Deployment Script

Save this as `cyberpanel_deploy.sh`:

```bash
#!/bin/bash
cd /home/frankhost.us/public_html
git pull origin main || git clone https://github.com/frankhostltd3/skolariscloud3.git .
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R frankhost.us:frankhost.us /home/frankhost.us/public_html
chmod -R 755 /home/frankhost.us/public_html
chmod -R 775 /home/frankhost.us/public_html/storage
chmod -R 775 /home/frankhost.us/public_html/bootstrap/cache
echo "Deployment completed!"
```

## Testing Your Deployment

1. Visit: `https://frankhost.us`
2. Check CyberPanel logs: **Websites → Access Logs**
3. Check error logs if issues occur
4. Test all functionality

## Troubleshooting

### Common Issues:
- **500 Error**: Check file permissions and .env configuration
- **Database Connection**: Verify database credentials in .env
- **Missing Dependencies**: Run `composer install`
- **Cache Issues**: Clear cache with `php artisan cache:clear`

### CyberPanel Specific:
- **Rewrite Rules**: Ensure Laravel rewrite rules are configured
- **PHP Version**: Make sure PHP 8.2+ is selected for the domain
- **File Permissions**: Use domain user (frankhost.us) not root

## Support
For CyberPanel specific issues, check their documentation or community forums.