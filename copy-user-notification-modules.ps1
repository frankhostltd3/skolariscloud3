# Copy Tenant User Management and Notification System
$sourceBase = "C:\wamp5\www\skolariscloud2"
$targetBase = "C:\wamp5\www\skolariscloud3"

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "User Management & Notifications Copy" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan

$filescopied = 0

function Copy-FileWithStructure {
    param($source, $destination)
    $destDir = Split-Path $destination -Parent
    if (-not (Test-Path $destDir)) {
        New-Item -ItemType Directory -Path $destDir -Force | Out-Null
    }
    Copy-Item $source $destination -Force
    $global:filescopied++
    Write-Host "  Copied: $(Split-Path $destination -Leaf)" -ForegroundColor Green
}

# ========== USER MANAGEMENT MODULE ==========
Write-Host "`n========== USER MANAGEMENT MODULE ==========" -ForegroundColor Yellow

# 1. User Controllers
Write-Host "`n[1/12] User Management Controllers..." -ForegroundColor Yellow
$userControllers = @(
    "app\Http\Controllers\Tenant\Users\TwoFactorController.php",
    "app\Http\Controllers\Tenant\Users\AdminsController.php",
    "app\Http\Controllers\Tenant\Users\ParentsController.php",
    "app\Http\Controllers\Tenant\Users\StaffController.php",
    "app\Http\Controllers\Tenant\Users\StudentsController.php",
    "app\Http\Controllers\Tenant\Admin\UserPasswordController.php",
    "app\Http\Controllers\Tenant\Auth\UserRegistrationController.php"
)
foreach ($file in $userControllers) {
    $src = Join-Path $sourceBase $file
    $dest = Join-Path $targetBase $file
    if (Test-Path $src) { Copy-FileWithStructure $src $dest }
}

# 2. User Models (if any specific)
Write-Host "`n[2/12] User Management Models..." -ForegroundColor Yellow
$userModels = Get-ChildItem "$sourceBase\app\Models" -Filter "*User*.php" -ErrorAction SilentlyContinue
foreach ($file in $userModels) {
    if ($file.Name -ne "User.php") {  # Skip base User model
        $dest = Join-Path "$targetBase\app\Models" $file.Name
        Copy-FileWithStructure $file.FullName $dest
    }
}

# 3. User Requests
Write-Host "`n[3/12] User Management Requests..." -ForegroundColor Yellow
$userRequests = Get-ChildItem "$sourceBase\app\Http\Requests\Tenant" -Recurse -Filter "*User*.php" -ErrorAction SilentlyContinue
foreach ($file in $userRequests) {
    $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
    $dest = Join-Path $targetBase $relativePath
    Copy-FileWithStructure $file.FullName $dest
}

# 4. User Views (admin)
Write-Host "`n[4/12] User Management Views (Admin)..." -ForegroundColor Yellow
if (Test-Path "$sourceBase\resources\views\tenant\admin\users") {
    $adminUserViews = Get-ChildItem "$sourceBase\resources\views\tenant\admin\users" -Recurse -Filter "*.blade.php" -ErrorAction SilentlyContinue
    foreach ($file in $adminUserViews) {
        $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
        $dest = Join-Path $targetBase $relativePath
        Copy-FileWithStructure $file.FullName $dest
    }
}

# 5. User Migrations
Write-Host "`n[5/12] User Management Migrations..." -ForegroundColor Yellow
$userMigrations = Get-ChildItem "$sourceBase\database\migrations" -Filter "*user*" -ErrorAction SilentlyContinue
foreach ($file in $userMigrations) {
    $dest = Join-Path "$targetBase\database\migrations" $file.Name
    if (-not (Test-Path $dest)) {
        Copy-FileWithStructure $file.FullName $dest
    }
}

# 6. User Policies
Write-Host "`n[6/12] User Management Policies..." -ForegroundColor Yellow
$userPolicies = Get-ChildItem "$sourceBase\app\Policies" -Filter "*User*.php" -ErrorAction SilentlyContinue
foreach ($file in $userPolicies) {
    $dest = Join-Path "$targetBase\app\Policies" $file.Name
    Copy-FileWithStructure $file.FullName $dest
}

# ========== NOTIFICATION SYSTEM ==========
Write-Host "`n========== NOTIFICATION SYSTEM ==========" -ForegroundColor Yellow

# 7. Notification Controllers
Write-Host "`n[7/12] Notification Controllers..." -ForegroundColor Yellow
$notifControllers = @(
    "app\Http\Controllers\Tenant\Admin\NotificationsController.php",
    "app\Http\Controllers\Tenant\Student\NotificationsController.php"
)
foreach ($file in $notifControllers) {
    $src = Join-Path $sourceBase $file
    $dest = Join-Path $targetBase $file
    if (Test-Path $src) { Copy-FileWithStructure $src $dest }
}

# 8. Notification Models
Write-Host "`n[8/12] Notification Models..." -ForegroundColor Yellow
$notifModels = @(
    "app\Models\Notification.php",
    "app\Models\NotificationLog.php",
    "app\Models\UserNotification.php"
)
foreach ($file in $notifModels) {
    $src = Join-Path $sourceBase $file
    $dest = Join-Path $targetBase $file
    if (Test-Path $src) { Copy-FileWithStructure $src $dest }
}

# 9. Notification Classes (App\Notifications)
Write-Host "`n[9/12] Notification Classes..." -ForegroundColor Yellow
$notifClasses = Get-ChildItem "$sourceBase\app\Notifications" -Filter "*.php" -ErrorAction SilentlyContinue | Where-Object { $_.Name -notlike "Landlord*" -and $_.Name -notlike "GenericLandlord*" }
foreach ($file in $notifClasses) {
    $dest = Join-Path "$targetBase\app\Notifications" $file.Name
    Copy-FileWithStructure $file.FullName $dest
}

# 10. Notification Views (Admin)
Write-Host "`n[10/12] Notification Views (Admin)..." -ForegroundColor Yellow
if (Test-Path "$sourceBase\resources\views\tenant\admin\notifications") {
    $adminNotifViews = Get-ChildItem "$sourceBase\resources\views\tenant\admin\notifications" -Recurse -Filter "*.blade.php" -ErrorAction SilentlyContinue
    foreach ($file in $adminNotifViews) {
        $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
        $dest = Join-Path $targetBase $relativePath
        Copy-FileWithStructure $file.FullName $dest
    }
}

# 11. Notification Views (Student)
Write-Host "`n[11/12] Notification Views (Student)..." -ForegroundColor Yellow
if (Test-Path "$sourceBase\resources\views\tenant\student\notifications") {
    $studentNotifViews = Get-ChildItem "$sourceBase\resources\views\tenant\student\notifications" -Recurse -Filter "*.blade.php" -ErrorAction SilentlyContinue
    foreach ($file in $studentNotifViews) {
        $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
        $dest = Join-Path $targetBase $relativePath
        Copy-FileWithStructure $file.FullName $dest
    }
}

# 12. Notification Migrations
Write-Host "`n[12/12] Notification Migrations..." -ForegroundColor Yellow
$notifMigrations = Get-ChildItem "$sourceBase\database\migrations" -Filter "*notification*" -ErrorAction SilentlyContinue | Where-Object { $_.Name -notlike "*landlord*" }
foreach ($file in $notifMigrations) {
    $dest = Join-Path "$targetBase\database\migrations" $file.Name
    if (-not (Test-Path $dest)) {
        Copy-FileWithStructure $file.FullName $dest
    }
}

Write-Host "`n======================================" -ForegroundColor Cyan
Write-Host "Transfer Complete!" -ForegroundColor Green
Write-Host "Total files copied: $filescopied" -ForegroundColor White
Write-Host "======================================" -ForegroundColor Cyan
