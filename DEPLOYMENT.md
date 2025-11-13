# SMATCAMPUS - School Management System

## Deployment Instructions

### 1. Server Requirements
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- Node.js & NPM (for asset compilation)

### 2. Domain & Server Details
- **Domain:** frankhost.us
- **Server IP:** 209.74.83.45
- **Database:** fran_ugketravel36
- **DB User:** fran_larvcorex7

### 3. Deployment Steps

#### Step 1: Upload Files
Upload all project files to your server's web directory (usually `public_html` or `www`)

#### Step 2: Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 3: Database Setup
```bash
# Run migrations
php artisan migrate

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 4: Web Server Configuration
Make sure your web server points to the `public` directory as the document root.

### 4. Security Checklist
- [ ] Set `APP_DEBUG=false` in production
- [ ] Set `APP_ENV=production`
- [ ] Configure proper file permissions
- [ ] Set up SSL certificate for HTTPS
- [ ] Configure firewall rules

### 5. Post-Deployment
- Test all functionality
- Check error logs
- Verify database connections
- Test email functionality

## Local Development
```bash
# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

## Features
- Student Management
- Teacher Management
- Grade Management
- Attendance Tracking
- Parent Communication
- Financial Management
- Reporting Dashboard

## Support
For technical support, contact your development team.
