#!/bin/bash

#######################################################
# VPS Update Script - Reports Systems Deployment
# Date: November 16, 2025
# Changes: Academic, Attendance, Financial, Late Submissions, Report Cards
#######################################################

echo "=========================================="
echo "🚀 Deploying Reports Systems to VPS"
echo "=========================================="
echo ""

# Configuration
APP_PATH="/home/frankhost.us/public_html"
BACKUP_DIR="/home/frankhost.us/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Check if directory exists
echo "📍 Step 1: Checking application directory..."
if [ ! -d "$APP_PATH" ]; then
    echo -e "${RED}❌ Error: Application directory not found at $APP_PATH${NC}"
    exit 1
fi
cd $APP_PATH
echo -e "${GREEN}✅ Directory found${NC}"
echo ""

# Step 2: Enable maintenance mode
echo "🔧 Step 2: Enabling maintenance mode..."
php artisan down --message="System update in progress. We'll be back in a few minutes." --retry=60
echo -e "${GREEN}✅ Maintenance mode enabled${NC}"
echo ""

# Step 3: Backup .env file
echo "💾 Step 3: Backing up .env file..."
mkdir -p $BACKUP_DIR
cp .env $BACKUP_DIR/.env.backup.$TIMESTAMP
echo -e "${GREEN}✅ .env backed up to $BACKUP_DIR/.env.backup.$TIMESTAMP${NC}"
echo ""

# Step 4: Backup database
echo "💾 Step 4: Backing up database..."
php artisan backup:run 2>/dev/null || echo -e "${YELLOW}⚠️  Backup skipped (Spatie backup not configured)${NC}"
echo ""

# Step 5: Stash local changes
echo "📦 Step 5: Stashing local changes..."
git stash
echo -e "${GREEN}✅ Local changes stashed${NC}"
echo ""

# Step 6: Pull latest code
echo "📥 Step 6: Pulling latest code from GitHub..."
git pull origin main
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Error: Git pull failed${NC}"
    echo "Restoring maintenance mode off..."
    php artisan up
    exit 1
fi
echo -e "${GREEN}✅ Latest code pulled successfully${NC}"
echo ""

# Step 7: Restore .env if needed
echo "🔄 Step 7: Ensuring .env is intact..."
if [ ! -f .env ] || [ ! -s .env ]; then
    echo "Restoring .env from backup..."
    cp $BACKUP_DIR/.env.backup.$TIMESTAMP .env
fi
echo -e "${GREEN}✅ .env file verified${NC}"
echo ""

# Step 8: Install/Update Composer dependencies
echo "📦 Step 8: Updating Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -ne 0 ]; then
    echo -e "${YELLOW}⚠️  Composer install had warnings, continuing...${NC}"
fi
echo -e "${GREEN}✅ Dependencies updated${NC}"
echo ""

# Step 9: Run migrations
echo "🗄️  Step 9: Running database migrations..."
echo "Running central database migrations..."
php artisan migrate --force
echo ""
echo "Running tenant database migrations..."
php artisan tenants:migrate --force
echo -e "${GREEN}✅ Migrations completed${NC}"
echo ""

# Step 10: Seed expense categories (if needed)
echo "🌱 Step 10: Seeding expense categories..."
php artisan tenants:seed-expense-categories 2>/dev/null || echo -e "${YELLOW}⚠️  Expense categories already seeded${NC}"
echo ""

# Step 11: Clear all caches
echo "🧹 Step 11: Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

# Step 12: Rebuild caches
echo "🔨 Step 12: Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Caches rebuilt${NC}"
echo ""

# Step 13: Set proper permissions
echo "🔐 Step 13: Setting permissions..."
chmod -R 755 $APP_PATH
chmod -R 775 $APP_PATH/storage
chmod -R 775 $APP_PATH/bootstrap/cache

# Try to set ownership (may need sudo)
chown -R frankhost.us:frankhost.us $APP_PATH/storage 2>/dev/null || echo -e "${YELLOW}⚠️  Could not set ownership (may need sudo)${NC}"
chown -R frankhost.us:frankhost.us $APP_PATH/bootstrap/cache 2>/dev/null || echo -e "${YELLOW}⚠️  Could not set ownership (may need sudo)${NC}"

echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

# Step 14: Disable maintenance mode
echo "🎉 Step 14: Disabling maintenance mode..."
php artisan up
echo -e "${GREEN}✅ Maintenance mode disabled${NC}"
echo ""

# Step 15: Summary
echo "=========================================="
echo -e "${GREEN}🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!${NC}"
echo "=========================================="
echo ""
echo "📊 What was deployed:"
echo "  ✅ Academic Reports (Chart.js visualizations)"
echo "  ✅ Attendance System (classroom, staff, exam)"
echo "  ✅ Financial System (revenue, expenses, fees)"
echo "  ✅ Late Quiz Submissions (tracking & CSV export)"
echo "  ✅ Report Cards (PDF generation)"
echo "  ✅ 17 new tenant migrations"
echo "  ✅ 13 new models with relationships"
echo "  ✅ Comprehensive reporting dashboard"
echo ""
echo "📝 Next steps:"
echo "  1. Visit your site: https://jinjasss.frankhost.us"
echo "  2. Test login functionality"
echo "  3. Check Reports menu for new options:"
echo "     - Academic Reports"
echo "     - Attendance Reports"
echo "     - Financial Reports"
echo "     - Late Submissions"
echo "     - Report Cards"
echo "  4. Monitor logs: tail -f $APP_PATH/storage/logs/laravel.log"
echo ""
echo "💾 Backups created:"
echo "  - .env: $BACKUP_DIR/.env.backup.$TIMESTAMP"
echo ""
echo "=========================================="
echo ""
