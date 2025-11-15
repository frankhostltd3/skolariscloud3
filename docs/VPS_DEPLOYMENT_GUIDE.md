# VPS Deployment Guide - Updating From GitHub

**Date:** November 15, 2025  
**Purpose:** Update existing VPS installation with latest changes from GitHub  
**Changes:** Bank Payment Instructions + Currency Bug Fix + All System Settings

---

## 🎯 Quick Start (Recommended Method)

### Option 1: Using the Update Script (Easiest)

**On your VPS (via SSH):**

```bash
# 1. Navigate to your application directory
cd /home/frankhost.us/smatcampus

# 2. Download the update script
wget https://raw.githubusercontent.com/frankhostltd3/skolariscloud3/main/update_vps.sh

# 3. Make it executable
chmod +x update_vps.sh

# 4. Run the update
./update_vps.sh
```

**What the script does:**
- ✅ Enables maintenance mode
- ✅ Backs up your .env file
- ✅ Pulls latest code from GitHub
- ✅ Restores your .env file
- ✅ Updates dependencies
- ✅ Runs database migrations
- ✅ Clears and rebuilds caches
- ✅ Sets proper permissions
- ✅ Disables maintenance mode

---

## 📋 Option 2: Manual Step-by-Step Deployment

If you prefer to do it manually or the script doesn't work:

### Step 1: Connect to Your VPS

```bash
ssh frankhost.us@your-vps-ip-address
# Or
ssh frankhost.us@frankhost.us
```

### Step 2: Navigate to Application Directory

```bash
cd /home/frankhost.us/smatcampus
# Or wherever your application is installed
```

### Step 3: Enable Maintenance Mode

```bash
php artisan down
```

This will show a maintenance page to users while you update.

### Step 4: Backup Your .env File

```bash
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
```

### Step 5: Pull Latest Changes from GitHub

```bash
# Stash any local changes
git stash

# Pull latest code
git pull origin main
```

### Step 6: Restore .env File

```bash
# If git overwrote your .env, restore it
cp .env.backup.* .env
# Or copy the most recent backup
ls -t .env.backup.* | head -1 | xargs -I {} cp {} .env
```

### Step 7: Update Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Step 8: Run Database Migrations

```bash
# Central database migrations
php artisan migrate --force

# Tenant database migrations
php artisan tenants:migrate
```

### Step 9: Clear All Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 10: Rebuild Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 11: Set Permissions

```bash
chmod -R 755 /home/frankhost.us/smatcampus
chmod -R 775 /home/frankhost.us/smatcampus/storage
chmod -R 775 /home/frankhost.us/smatcampus/bootstrap/cache

# If you have sudo access
sudo chown -R frankhost.us:frankhost.us /home/frankhost.us/smatcampus/storage
sudo chown -R frankhost.us:frankhost.us /home/frankhost.us/smatcampus/bootstrap/cache
```

### Step 12: Disable Maintenance Mode

```bash
php artisan up
```

---

## 🔍 Option 3: Using GitHub Actions (Automated)

If you have GitHub Actions set up with secrets:

1. **Push to main branch** (already done ✅)
2. **GitHub Actions automatically deploys** to your VPS
3. **Monitor deployment** at: https://github.com/frankhostltd3/skolariscloud3/actions

**Required Secrets:**
- `VPS_HOST` - Your VPS IP or domain
- `VPS_USER` - SSH username (e.g., frankhost.us)
- `VPS_PASSWORD` - SSH password
- `VPS_APP_PATH` - Application path (e.g., /home/frankhost.us/smatcampus)
- `VPS_WEBROOT_PATH` - Web root path (e.g., /home/frankhost.us/public_html)

---

## 🆕 What's New in This Update

### 1. Bank Payment Instructions ✅
- Configure bank details in Payment Settings
- Display bank transfer details to students/parents/staff
- 10 configurable fields (bank name, account, SWIFT, IBAN, etc.)
- **How to use:**
  1. Go to Settings → Payment Settings
  2. Enable "Bank Transfer / Direct Deposit"
  3. Fill in your bank details
  4. Save settings

### 2. Currency Edit Bug Fix ✅
- Fixed "No database selected" error when editing currencies
- All currency operations now work correctly
- **What was fixed:**
  - Edit currency route
  - Update currency route
  - Delete currency
  - Set default currency
  - Toggle active status
  - Toggle auto-update

### 3. All System Settings ✅
- General Settings (school info, logo, timezone)
- Academic Settings (terms, grading, attendance)
- System Settings (2FA, performance, security, backup)
- Messaging Settings (SMS, WhatsApp, Telegram)
- Currency Management with auto-update

---

## ✅ Post-Deployment Verification

After deployment, test these features:

### 1. Login Test
```bash
# Visit your site
https://jinjasss.frankhost.us/login
```

### 2. Currency Edit Test
```bash
# Go to Settings → Currencies
# Click Edit on any currency
# Should load without "No database selected" error
```

### 3. Bank Payment Instructions Test
```bash
# Go to Settings → Payment Settings
# Enable "Bank Transfer / Direct Deposit"
# Fill in bank details and save
# Check if details display on payment pages
```

### 4. Check Logs for Errors
```bash
tail -f /home/frankhost.us/smatcampus/storage/logs/laravel.log
```

---

## 🔧 Troubleshooting

### Issue: "Permission denied" errors

**Solution:**
```bash
sudo chown -R frankhost.us:frankhost.us /home/frankhost.us/smatcampus
sudo chmod -R 775 /home/frankhost.us/smatcampus/storage
sudo chmod -R 775 /home/frankhost.us/smatcampus/bootstrap/cache
```

### Issue: "Class not found" errors

**Solution:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue: Database migration errors

**Solution:**
```bash
# Check database connection
php artisan tinker
# Then type: DB::connection()->getPdo();

# If connection works, try migrations again
php artisan migrate --force
php artisan tenants:migrate
```

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check logs
tail -50 /home/frankhost.us/smatcampus/storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize
```

### Issue: Changes not appearing

**Solution:**
```bash
# Hard clear everything
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart PHP-FPM (if using)
sudo systemctl restart php-fpm
# Or for CyberPanel
sudo systemctl restart lscpd
```

---

## 📊 Deployment Checklist

- [ ] SSH into VPS
- [ ] Navigate to application directory
- [ ] Enable maintenance mode
- [ ] Backup .env file
- [ ] Pull latest changes from GitHub
- [ ] Restore .env file
- [ ] Update Composer dependencies
- [ ] Run database migrations (central + tenant)
- [ ] Clear all caches
- [ ] Rebuild caches
- [ ] Set proper permissions
- [ ] Disable maintenance mode
- [ ] Test login functionality
- [ ] Test currency edit functionality
- [ ] Test bank payment settings
- [ ] Check error logs
- [ ] Monitor application for 10 minutes

---

## 🔄 Rollback Procedure (If Something Goes Wrong)

If the update causes issues:

### Quick Rollback

```bash
# 1. Go to application directory
cd /home/frankhost.us/smatcampus

# 2. Revert to previous commit
git log --oneline  # Find the previous commit hash
git reset --hard <previous-commit-hash>

# 3. Restore .env backup
cp .env.backup.YYYYMMDD_HHMMSS .env

# 4. Clear caches
php artisan config:clear
php artisan cache:clear

# 5. Rebuild caches
php artisan config:cache

# 6. Disable maintenance mode
php artisan up
```

---

## 📞 Support

If you encounter issues:

1. **Check logs:** `storage/logs/laravel.log`
2. **Check GitHub Actions:** https://github.com/frankhostltd3/skolariscloud3/actions
3. **Review this guide:** Ensure all steps were followed
4. **Backup before retrying:** Always backup before attempting fixes

---

## 🎉 Success Indicators

You'll know the deployment was successful when:

✅ Website loads without errors  
✅ You can log in successfully  
✅ Settings → Currencies → Edit works without database errors  
✅ Payment Settings shows "Bank Transfer" option  
✅ Bank details display when configured  
✅ No errors in `storage/logs/laravel.log`  

---

## 📝 Notes

- **Deployment Time:** ~5-10 minutes
- **Downtime:** ~2-3 minutes (while in maintenance mode)
- **Database Changes:** Yes (new migrations will run)
- **Breaking Changes:** None (backward compatible)
- **Rollback Available:** Yes (see rollback procedure)

---

**Ready to deploy? Start with Option 1 (Update Script) for the easiest experience!**
