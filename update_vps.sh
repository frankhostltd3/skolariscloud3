#!/bin/bash

# ============================================================================
# SMATCAMPUS VPS Update Script
# ============================================================================
# This script safely updates your existing VPS installation with new changes
# from GitHub while preserving your .env file and uploaded data.
#
# Usage: Run this script on your VPS server
#   chmod +x update_vps.sh
#   ./update_vps.sh
# ============================================================================

set -e  # Exit on error

echo "🚀 SMATCAMPUS Update Script"
echo "================================"
echo ""

# Variables (adjust these to match your setup)
APP_DIR="/home/frankhost.us/smatcampus"
WEBROOT_DIR="/home/frankhost.us/public_html"
REPO_URL="https://github.com/frankhostltd3/skolariscloud3.git"
BRANCH="main"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Helper function
print_step() {
    echo -e "${GREEN}➜ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if running on VPS
if [ ! -d "$APP_DIR" ]; then
    print_error "Application directory not found: $APP_DIR"
    echo "Please update the APP_DIR variable in this script to match your setup."
    exit 1
fi

cd "$APP_DIR"

# Step 1: Enable maintenance mode
print_step "Step 1: Enabling maintenance mode..."
php artisan down || print_warning "Could not enable maintenance mode (app might not be set up yet)"

# Step 2: Backup current .env file
print_step "Step 2: Backing up .env file..."
if [ -f ".env" ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    echo "✓ .env backed up"
else
    print_warning ".env file not found - will need to create one"
fi

# Step 3: Stash any local changes
print_step "Step 3: Stashing local changes (if any)..."
if [ -d ".git" ]; then
    git stash || echo "Nothing to stash"
else
    print_error "Not a git repository. Please clone the repository first."
    exit 1
fi

# Step 4: Pull latest changes from GitHub
print_step "Step 4: Pulling latest changes from GitHub..."
git fetch origin
git pull origin $BRANCH

# Step 5: Restore .env file
print_step "Step 5: Restoring .env file..."
if [ -f ".env.backup."* ]; then
    LATEST_BACKUP=$(ls -t .env.backup.* | head -1)
    cp "$LATEST_BACKUP" .env
    echo "✓ .env restored from backup"
fi

# Step 6: Install/Update Composer dependencies
print_step "Step 6: Installing/Updating Composer dependencies..."
if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader
    echo "✓ Composer dependencies updated"
else
    print_warning "Composer not found. Skipping dependency installation."
fi

# Step 7: Run database migrations
print_step "Step 7: Running database migrations..."
php artisan migrate --force

# Step 8: Run tenant migrations (if applicable)
print_step "Step 8: Running tenant migrations..."
php artisan tenants:migrate || print_warning "Tenant migrations not available or failed"

# Step 9: Clear and rebuild caches
print_step "Step 9: Clearing and rebuilding caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 10: Set proper permissions
print_step "Step 10: Setting file permissions..."
chmod -R 755 "$APP_DIR"
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

# If running as root or with sudo, set ownership
if [ "$EUID" -eq 0 ]; then
    WEBUSER=$(stat -c '%U' "$WEBROOT_DIR" 2>/dev/null || echo "frankhost.us")
    chown -R $WEBUSER:$WEBUSER "$APP_DIR/storage"
    chown -R $WEBUSER:$WEBUSER "$APP_DIR/bootstrap/cache"
    echo "✓ Permissions set for user: $WEBUSER"
else
    print_warning "Not running as root. You may need to set permissions manually."
fi

# Step 11: Disable maintenance mode
print_step "Step 11: Disabling maintenance mode..."
php artisan up

echo ""
echo "================================"
echo -e "${GREEN}✅ Update completed successfully!${NC}"
echo "================================"
echo ""
echo "📋 What was updated:"
echo "  ✓ Bank Payment Instructions system"
echo "  ✓ Currency edit bug fix"
echo "  ✓ All settings and configurations"
echo "  ✓ Database migrations run"
echo "  ✓ Caches rebuilt"
echo ""
echo "🔍 Post-update checks:"
echo "  1. Test login functionality"
echo "  2. Check Settings → Payment Settings"
echo "  3. Test Settings → Currencies (edit should work now)"
echo "  4. Verify bank payment instructions display"
echo ""
echo "📝 .env backup saved at: $(ls -t .env.backup.* | head -1)"
echo ""
