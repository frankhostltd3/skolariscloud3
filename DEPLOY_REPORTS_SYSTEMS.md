# Deploy Reports Systems to VPS

**Date:** November 16, 2025  
**Deployment:** Academic, Attendance, Financial, Late Submissions, Report Cards Systems

---

## 📦 What's Being Deployed

### New Features (70 files, 11,607+ lines of code):

1. **Academic Reports Dashboard**
   - Chart.js visualizations (grade distribution, class performance, trends)
   - KPIs: Overall GPA, Pass Rate, Honor Roll, At Risk Students
   - Filters by academic year, semester, and class
   - Top 10 performers list
   - Subject performance analysis

2. **Attendance System**
   - 3 tracking modes: Classroom, Staff, Exam
   - Real-time KPIs and daily trend charts
   - Status tracking (present, absent, late, excused, sick, half-day)
   - Staff check-in/out with hours worked
   - Kiosk mode for self-service

3. **Financial System**
   - Revenue vs Expenses tracking
   - Fee collection monitoring
   - Payment methods distribution
   - Expense categories management
   - Class-wise fee collection analytics
   - 12 expense categories pre-seeded

4. **Late Quiz Submissions Report**
   - Full quiz tracking system
   - Filter by date range, class, quiz, student
   - CSV export functionality
   - Minutes late tracking with penalties
   - 3 new database tables

5. **Report Cards System**
   - Professional PDF generation
   - Single student reports
   - Bulk class downloads
   - 8 subjects with grades, GPA, attendance
   - Automated performance comments

### Database Changes:
- **17 new tenant migrations** (attendance, financial, quiz, academic tables)
- **13 new models** with relationships and scopes
- **5 new Artisan commands** (seeders and utilities)

---

## 🚀 Deployment Options

### Option 1: Automated Script (Recommended)

**SSH into your VPS:**
```bash
ssh frankhost.us@your-vps-ip
```

**Navigate to application directory:**
```bash
cd /home/frankhost.us/public_html
```

**Download and run the deployment script:**
```bash
# Download the script
wget https://raw.githubusercontent.com/frankhostltd3/skolariscloud3/main/update_vps_reports.sh

# Make it executable
chmod +x update_vps_reports.sh

# Run the deployment
./update_vps_reports.sh
```

**The script will automatically:**
- ✅ Enable maintenance mode
- ✅ Backup .env and database
- ✅ Pull latest code from GitHub
- ✅ Update Composer dependencies
- ✅ Run all migrations (central + tenant)
- ✅ Seed expense categories
- ✅ Clear and rebuild all caches
- ✅ Set proper permissions
- ✅ Disable maintenance mode

**Estimated time:** 5-10 minutes  
**Downtime:** 2-3 minutes

---

### Option 2: Manual Deployment

If you prefer manual control, follow these steps:

#### Step 1: Connect and Navigate
```bash
ssh frankhost.us@your-vps-ip
cd /home/frankhost.us/public_html
```

#### Step 2: Enable Maintenance Mode
```bash
php artisan down --message="Deploying new features. Back in 5 minutes." --retry=60
```

#### Step 3: Backup
```bash
# Backup .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Backup database (if Spatie backup configured)
php artisan backup:run
```

#### Step 4: Pull Latest Code
```bash
# Stash local changes
git stash

# Pull from GitHub
git pull origin main
```

#### Step 5: Update Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

#### Step 6: Run Migrations
```bash
# Central database
php artisan migrate --force

# Tenant databases (all 4 schools)
php artisan tenants:migrate --force
```

#### Step 7: Seed Expense Categories
```bash
php artisan tenants:seed-expense-categories
```

#### Step 8: Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### Step 9: Rebuild Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 10: Set Permissions
```bash
chmod -R 755 /home/frankhost.us/public_html
chmod -R 775 /home/frankhost.us/public_html/storage
chmod -R 775 /home/frankhost.us/public_html/bootstrap/cache

# Set ownership (may need sudo)
sudo chown -R frankhost.us:frankhost.us /home/frankhost.us/public_html/storage
sudo chown -R frankhost.us:frankhost.us /home/frankhost.us/public_html/bootstrap/cache
```

#### Step 11: Disable Maintenance Mode
```bash
php artisan up
```

---

## ✅ Post-Deployment Testing

### 1. Login Test
Visit: `https://jinjasss.frankhost.us/login`

### 2. Check Reports Menu
The admin sidebar should now have these new report options:
- **Academic Reports** - `/admin/reports/academic`
- **Attendance Reports** - `/admin/reports/attendance`
- **Financial Reports** - `/admin/reports/financial`
- **Late Submissions** - `/admin/reports/late-submissions`
- **Report Cards** - `/admin/reports/report-cards`
- **Enrollment Reports** - `/admin/reports/enrollment`

### 3. Test Academic Reports
1. Go to Reports → Academic Reports
2. Verify you see:
   - 4 KPI cards (GPA, Pass Rate, Honor Roll, At Risk)
   - 3 Chart.js visualizations
   - Top performers list
   - Subject performance table
3. Test filters (Academic Year, Semester, Class)

### 4. Test Attendance Reports
1. Go to Reports → Attendance Reports
2. Verify you see:
   - Daily attendance KPIs
   - Trend chart
   - Class comparison
   - Students requiring attention list

### 5. Test Financial Reports
1. Go to Reports → Financial Reports
2. Verify you see:
   - Revenue, Expenses, Net Profit, Pending Fees cards
   - 3 charts (Revenue vs Expenses, Payment Methods, Expense Breakdown)
   - Recent transactions table
   - Outstanding payments list

### 6. Test Late Submissions
1. Go to Reports → Late Submissions
2. Verify filtering works
3. Test CSV export

### 7. Test Report Cards
1. Go to Reports → Report Cards
2. Try generating a single student report
3. Try generating bulk class reports

### 8. Check Database Tables
```bash
# Connect to tenant database
mysql -u your_user -p

# Switch to a tenant database
USE tenant_000001;

# Check new tables exist
SHOW TABLES LIKE '%attendance%';
SHOW TABLES LIKE '%quiz%';
SHOW TABLES LIKE '%invoice%';
SHOW TABLES LIKE '%expense%';
SHOW TABLES LIKE '%transaction%';
```

### 9. Monitor Error Logs
```bash
# Real-time log monitoring
tail -f /home/frankhost.us/public_html/storage/logs/laravel.log

# Check last 50 lines
tail -50 /home/frankhost.us/public_html/storage/logs/laravel.log
```

---

## 🔧 Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
```bash
cd /home/frankhost.us/public_html

# Check logs
tail -50 storage/logs/laravel.log

# Clear everything
php artisan optimize:clear

# Rebuild
php artisan optimize

# Check permissions
chmod -R 775 storage bootstrap/cache
```

### Issue: Chart.js Not Loading

**Solution:**
```bash
# Clear view cache
php artisan view:clear

# Hard refresh browser (Ctrl+F5)
# Or clear browser cache
```

### Issue: Migration Errors

**Solution:**
```bash
# Check which migrations ran
php artisan migrate:status

# Check tenant migrations
php artisan tenants:migrate --pretend

# If stuck, try:
php artisan migrate:refresh --force
php artisan tenants:migrate --force
```

### Issue: Expense Categories Not Showing

**Solution:**
```bash
# Reseed expense categories
php artisan tenants:seed-expense-categories

# Verify in database
mysql -u user -p tenant_000001 -e "SELECT * FROM expense_categories;"
```

### Issue: Report Cards Not Generating

**Solution:**
```bash
# Clear view cache
php artisan view:clear

# Check if students exist
mysql -u user -p tenant_000001 -e "SELECT COUNT(*) FROM users WHERE is_active=1;"

# Check logs
tail -50 storage/logs/laravel.log | grep -i "report"
```

---

## 🔄 Rollback Procedure

If something goes wrong:

```bash
cd /home/frankhost.us/public_html

# Find previous commit
git log --oneline | head -5

# Rollback to previous commit (before d5b921f)
git reset --hard <previous-commit-hash>

# Restore .env
cp .env.backup.YYYYMMDD_HHMMSS .env

# Clear caches
php artisan config:clear
php artisan cache:clear

# Rebuild
php artisan config:cache

# Bring site back up
php artisan up
```

---

## 📊 Expected Results

After successful deployment:

✅ **6 report systems** fully functional  
✅ **17 database tables** created in all tenant databases  
✅ **Chart.js visualizations** rendering correctly  
✅ **CSV exports** working for late submissions  
✅ **PDF generation** working for report cards  
✅ **12 expense categories** seeded in all schools  
✅ **No errors** in Laravel logs  
✅ **Site loading** in under 2 seconds  

---

## 📈 What Users Will See

### For Admins:
- Complete reporting dashboard with 6 comprehensive reports
- Real-time analytics with Chart.js visualizations
- Export functionality (CSV, PDF)
- Filter options for all reports
- Professional report cards for students

### For Teachers:
- Attendance tracking interface
- Quiz management system
- Late submission monitoring
- Report card generation

### For Students:
- View their report cards
- Check their quiz submissions
- View attendance records

### For Parents:
- Access student report cards
- Monitor academic performance
- View attendance history

---

## 🎯 Key Metrics

**Code Statistics:**
- Files changed: 70
- Lines added: 11,607+
- New controllers: 4
- New models: 14
- New migrations: 17
- New views: 20+
- New commands: 5

**Database Impact:**
- New tables: 17 (per tenant)
- Affected databases: 4 tenant databases
- Sample data: 27 quizzes, 12 expense categories

---

## 📞 Support Checklist

Before asking for help, verify:

- [ ] Latest code pulled from GitHub
- [ ] All migrations ran successfully
- [ ] Caches cleared and rebuilt
- [ ] Permissions set correctly
- [ ] Error logs checked
- [ ] Browser cache cleared
- [ ] Site accessible without maintenance mode
- [ ] Database connection working

---

## 🎉 Success Indicators

Your deployment is successful when:

✅ All 6 reports accessible from admin menu  
✅ Chart.js charts rendering properly  
✅ No errors in `/storage/logs/laravel.log`  
✅ CSV export downloads working  
✅ Report cards generate successfully  
✅ Database tables created in all tenants  
✅ Expense categories visible in financial reports  
✅ Site loading without 500 errors  

---

## 📝 Notes

- **Deployment tested:** Locally on Windows + WAMP
- **Production environment:** AlmaLinux + CyberPanel + LiteSpeed
- **PHP version:** 8.1+ recommended
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **Node.js:** Required for Chart.js (should already be installed)

---

**Ready to deploy? Use Option 1 (Automated Script) for the smoothest experience!**

**Questions? Check logs first, then review troubleshooting section.**
