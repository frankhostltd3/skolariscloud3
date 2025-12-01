# Quick VPS Deployment Guide

## Option 1: Automated Deployment (Recommended)

### Step 1: Connect to Your VPS

```bash
ssh root@your-vps-ip
```

### Step 2: Run the Quick Deploy Script

```bash
# Download and run the script
curl -O https://raw.githubusercontent.com/frankhostltd3/skolariscloud3/main/quick-deploy.sh
chmod +x quick-deploy.sh
sudo ./quick-deploy.sh
```

The script will:
- ✅ Install all required software (PHP, MySQL, Nginx, Node.js, etc.)
- ✅ Create databases
- ✅ Clone your repository
- ✅ Configure environment
- ✅ Install dependencies
- ✅ Set up SSL certificates
- ✅ Configure Nginx and Supervisor
- ✅ Set proper permissions

**Total time: ~15-20 minutes**

---

## Option 2: Manual Step-by-Step

### 1. Connect to VPS
```bash
ssh root@your-vps-ip
```

### 2. Clone Repository
```bash
cd /var/www
git clone https://github.com/frankhostltd3/skolariscloud3.git
cd skolariscloud3
```

### 3. Run Deployment Script
```bash
chmod +x deploy.sh
sudo ./deploy.sh
```

### 4. Configure Nginx
```bash
# Copy nginx config
sudo cp nginx.conf /etc/nginx/sites-available/skolariscloud

# Edit domain name
sudo nano /etc/nginx/sites-available/skolariscloud
# Replace 'yourdomain.com' with your actual domain

# Enable site
sudo ln -s /etc/nginx/sites-available/skolariscloud /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Setup SSL
```bash
# Main domain
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Wildcard (for subdomains)
sudo certbot certonly --manual --preferred-challenges=dns \
  -d yourdomain.com -d *.yourdomain.com
```

### 6. Configure Supervisor
```bash
sudo cp supervisor.conf /etc/supervisor/conf.d/skolariscloud.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start skolariscloud-worker:*
```

---

## Post-Deployment Checklist

- [ ] Edit `.env` file with your settings
- [ ] Create first landlord account
- [ ] Test tenant creation
- [ ] Configure email settings
- [ ] Set up backups
- [ ] Configure DNS for wildcard domains
- [ ] Test SSL certificates
- [ ] Monitor logs

---

## Important Commands

### View Logs
```bash
# Application logs
tail -f /var/www/skolariscloud3/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# Queue workers
tail -f /var/www/skolariscloud3/storage/logs/worker.log
```

### Restart Services
```bash
# Nginx
sudo systemctl restart nginx

# PHP-FPM
sudo systemctl restart php8.2-fpm

# Supervisor workers
sudo supervisorctl restart skolariscloud-worker:*
```

### Clear Cache
```bash
cd /var/www/skolariscloud3
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Update Application
```bash
cd /var/www/skolariscloud3
php artisan down
git pull origin main
composer install --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
sudo supervisorctl restart skolariscloud-worker:*
php artisan up
```

---

## Troubleshooting

### Error 500
```bash
# Check permissions
sudo chown -R www-data:www-data /var/www/skolariscloud3
sudo chmod -R 775 /var/www/skolariscloud3/storage

# Check logs
tail -f /var/www/skolariscloud3/storage/logs/laravel.log
```

### Queue Not Working
```bash
# Check supervisor
sudo supervisorctl status
sudo supervisorctl restart skolariscloud-worker:*
```

### SSL Issues
```bash
# Renew certificates
sudo certbot renew
sudo systemctl reload nginx
```

---

## Default Credentials

After deployment, create your first landlord account by visiting:
`https://yourdomain.com/landlord/register`

---

## Support

- Full Documentation: `DEPLOYMENT.md`
- GitHub: https://github.com/frankhostltd3/skolariscloud3
- Issues: https://github.com/frankhostltd3/skolariscloud3/issues
