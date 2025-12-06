# Deployment Guide for Skolaris Cloud 3 on AlmaLinux/Plesk VPS

This guide outlines the steps to deploy your Laravel application to a VPS running AlmaLinux and Plesk.

## Prerequisites

Ensure your Plesk server has the following installed/configured:
- **PHP 8.2** or higher.
- **Composer** (Dependency Manager for PHP).
- **Node.js & NPM** (For building frontend assets).
- **Git** (For version control).
- **MySQL/MariaDB** database server.

## Step 1: Prepare Your Application

1.  **Local Packages**: Your `composer.json` references local packages in `./packages/skolaris/`. Ensure the `packages` directory is committed to your Git repository.
2.  **Version Control**: Push your code to a remote repository (GitHub, GitLab, Bitbucket).
    ```bash
    git add .
    git commit -m "Ready for deployment"
    git push origin main
    ```

## Step 2: Plesk Domain Setup

1.  Log in to your Plesk Panel.
2.  Go to **Websites & Domains**.
3.  Add a Domain or Subdomain (e.g., `app.yourdomain.com`).
4.  **Document Root**: Change the document root to point to the `public` folder of your application (e.g., `httpdocs/public` or `skolaris/public`).
    *   *Note*: If you clone into a specific folder, adjust the path accordingly.
5.  **PHP Settings**:
    *   Set PHP version to **8.2**.
    *   Increase `memory_limit` (e.g., 512M or 1024M).
    *   Increase `upload_max_filesize` and `post_max_size` (e.g., 64M).
    *   Ensure `disable_functions` does not block `proc_open`, `exec`, etc., if needed for backups or PDF generation.

## Step 3: Database Setup

1.  In Plesk, go to **Databases**.
2.  Click **Add Database**.
3.  Create a database name (e.g., `skolaris_db`).
4.  Create a database user and password.
5.  **Important**: Since this is a multi-tenant app, ensure the database user has permissions to **create new databases** if your tenant architecture requires separate databases per tenant. If you are using a single database with tenant IDs, standard permissions are fine.

## Step 4: Deploying the Code

### Option A: Using Git (Recommended)
1.  SSH into your server.
2.  Navigate to your domain's directory (e.g., `/var/www/vhosts/yourdomain.com/httpdocs`).
3.  Clone your repository:
    ```bash
    git clone https://github.com/yourusername/skolariscloud3.git .
    ```
    *(Note: If the directory is not empty, you might need to move files or clone into a subdirectory and move them).*

### Option B: Using Plesk Git Extension
1.  In Plesk, go to **Git**.
2.  Add your repository URL.
3.  Select the deployment path (your document root parent).

## Step 5: Installation & Configuration

1.  **Install PHP Dependencies**:
    ```bash
    composer install --optimize-autoloader --no-dev
    ```
    *Note: If this fails due to local packages, ensure the `packages` folder exists on the server.*

2.  **Environment Configuration**:
    *   Copy the example env file:
        ```bash
        cp .env.example .env
        ```
    *   Edit `.env` with your production settings:
        ```bash
        nano .env
        ```
        *   Set `APP_ENV=production`
        *   Set `APP_DEBUG=false`
        *   Set `APP_URL=https://app.yourdomain.com`
        *   Configure `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
        *   Configure Mail settings, Redis (if used), etc.

3.  **Generate Key**:
    ```bash
    php artisan key:generate
    ```

4.  **Run Migrations**:
    ```bash
    php artisan migrate --force
    ```
    *   If you have tenant migrations:
    ```bash
    php artisan tenants:migrate
    ```

5.  **Frontend Build**:
    ```bash
    npm install
    npm run build
    ```

6.  **Storage Linking**:
    ```bash
    php artisan storage:link
    ```

7.  **Permissions**:
    Ensure the web server (usually `apache` or `nginx` user) has write access to `storage` and `bootstrap/cache`.
    ```bash
    chmod -R 775 storage bootstrap/cache
    chown -R <your_user>:psacln storage bootstrap/cache
    ```
    *(Note: `psacln` is the common group in Plesk).*

## Step 6: Post-Deployment

1.  **Queue Worker**:
    *   You need a worker to process background jobs (emails, backups).
    *   In Plesk, you can use **Scheduled Tasks** (Cron) or install **Supervisor** (if you have root access).
    *   Simple Cron approach (run every minute):
        ```bash
        cd /var/www/vhosts/yourdomain.com/httpdocs && php artisan schedule:run >> /dev/null 2>&1
        ```

2.  **SSL Certificate**:
    *   Go to **SSL/TLS Certificates** in Plesk.
    *   Install a free Let's Encrypt certificate.
    *   Enable "Redirect from HTTP to HTTPS".

## Troubleshooting

*   **500 Error**: Check `storage/logs/laravel.log`.
*   **403 Forbidden**: Check permissions or ensure Document Root points to `public`.
*   **Vite Assets 404**: Ensure `npm run build` ran successfully and `public/build` exists.

