# PowerShell Deployment Script for SMATCAMPUS
# Run this script from Windows to deploy to your VPS

param(
    [string]$VpsHost = "209.74.83.45",
    [string]$VpsPort = "22022", 
    [string]$VpsUser = "root",
    [string]$VpsPassword = "F!sh9T@ble",
    [string]$ProjectDir = "/home/frankhost.us/public_html"
)

Write-Host "🚀 Starting SMATCAMPUS deployment to VPS..." -ForegroundColor Green

# Test SSH connection first
Write-Host "📡 Testing SSH connection..." -ForegroundColor Yellow
try {
    $testResult = ssh -p $VpsPort -o StrictHostKeyChecking=no "$VpsUser@$VpsHost" "echo 'Connection successful'"
    Write-Host "✅ SSH connection successful" -ForegroundColor Green
} catch {
    Write-Host "❌ SSH connection failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Function to run SSH commands
function Invoke-SshCommand {
    param([string]$Command)
    Write-Host "Executing: $Command" -ForegroundColor Cyan
    ssh -p $VpsPort -o StrictHostKeyChecking=no "$VpsUser@$VpsHost" $Command
}

# Navigate to project directory and pull latest changes
Write-Host "🔄 Updating repository..." -ForegroundColor Yellow
Invoke-SshCommand 'cd /home/frankhost.us/public_html && pwd && git status'

# Pull latest changes or clone if needed  
Invoke-SshCommand 'cd /home/frankhost.us/public_html && if [ -d .git ]; then echo "Pulling latest changes..."; git pull origin main; else echo "Cloning repository..."; git clone https://github.com/frankhostltd3/skolariscloud3.git .; fi'

# Install PHP dependencies
Write-Host "📦 Installing dependencies..." -ForegroundColor Yellow
Invoke-SshCommand 'cd /home/frankhost.us/public_html && composer install --optimize-autoloader --no-dev'

# Run Laravel commands
Write-Host "⚙️ Running Laravel setup..." -ForegroundColor Yellow
Invoke-SshCommand 'cd /home/frankhost.us/public_html && php artisan migrate --force'
Invoke-SshCommand 'cd /home/frankhost.us/public_html && php artisan config:cache'
Invoke-SshCommand 'cd /home/frankhost.us/public_html && php artisan route:cache'
Invoke-SshCommand 'cd /home/frankhost.us/public_html && php artisan view:cache'

# Set permissions
Write-Host "🔐 Setting permissions..." -ForegroundColor Yellow
Invoke-SshCommand 'chown -R frank5934:frank5934 /home/frankhost.us/public_html'
Invoke-SshCommand 'chmod -R 755 /home/frankhost.us/public_html'
Invoke-SshCommand 'chmod -R 775 /home/frankhost.us/public_html/storage /home/frankhost.us/public_html/bootstrap/cache'

Write-Host ""
Write-Host "✅ Deployment completed successfully!" -ForegroundColor Green
Write-Host "🌐 Check your site: https://frankhost.us" -ForegroundColor Cyan