# Copy Landlord Module Script
$sourceBase = "C:\wamp5\www\skolariscloud2"
$targetBase = "C:\wamp5\www\skolariscloud3"

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Landlord Module Transfer" -ForegroundColor Cyan
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

# 1. Console Commands
Write-Host "`n[1/12] Console Commands..." -ForegroundColor Yellow
$commands = @(
    "app\Console\Commands\CreateLandlordUser.php",
    "app\Console\Commands\ProcessLandlordInvoices.php"
)
foreach ($file in $commands) {
    $src = Join-Path $sourceBase $file
    $dest = Join-Path $targetBase $file
    if (Test-Path $src) { Copy-FileWithStructure $src $dest }
}

# 2. Controllers
Write-Host "`n[2/12] Controllers..." -ForegroundColor Yellow
$controllerFiles = Get-ChildItem "$sourceBase\app\Http\Controllers\Landlord" -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
foreach ($file in $controllerFiles) {
    $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
    $dest = Join-Path $targetBase $relativePath
    Copy-FileWithStructure $file.FullName $dest
}

# 3. Requests
Write-Host "`n[3/12] Requests..." -ForegroundColor Yellow
$requestFiles = Get-ChildItem "$sourceBase\app\Http\Requests\Landlord" -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
foreach ($file in $requestFiles) {
    $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
    $dest = Join-Path $targetBase $relativePath
    Copy-FileWithStructure $file.FullName $dest
}

# 4. Models
Write-Host "`n[4/12] Models..." -ForegroundColor Yellow
$models = @(
    "app\Models\LandlordAuditLog.php",
    "app\Models\LandlordDunningPolicy.php",
    "app\Models\LandlordInvoice.php",
    "app\Models\LandlordInvoiceItem.php",
    "app\Models\LandlordNotification.php"
)
foreach ($file in $models) {
    $src = Join-Path $sourceBase $file
    $dest = Join-Path $targetBase $file
    if (Test-Path $src) { Copy-FileWithStructure $src $dest }
}

# 5. Notifications
Write-Host "`n[5/12] Notifications..." -ForegroundColor Yellow
$notifications = @(
    "app\Notifications\GenericLandlordMessage.php",
    "app\Notifications\LandlordInvoiceSuspended.php",
    "app\Notifications\LandlordInvoiceTerminated.php",
    "app\Notifications\LandlordInvoiceWarning.php"
)
foreach ($file in $notifications) {
    $src = Join-Path $sourceBase $file
    $dest = Join-Path $targetBase $file
    if (Test-Path $src) { Copy-FileWithStructure $src $dest }
}

# 6. Services
Write-Host "`n[6/12] Services..." -ForegroundColor Yellow
$serviceFiles = Get-ChildItem "$sourceBase\app\Services\LandlordBilling" -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
foreach ($file in $serviceFiles) {
    $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
    $dest = Join-Path $targetBase $relativePath
    Copy-FileWithStructure $file.FullName $dest
}

# 7. Factories
Write-Host "`n[7/12] Factories..." -ForegroundColor Yellow
$factories = @(
    "database\factories\LandlordInvoiceFactory.php",
    "database\factories\LandlordInvoiceItemFactory.php"
)
foreach ($file in $factories) {
    $src = Join-Path $sourceBase $file
    $dest = Join-Path $targetBase $file
    if (Test-Path $src) { Copy-FileWithStructure $src $dest }
}

# 8. Migrations
Write-Host "`n[8/12] Migrations..." -ForegroundColor Yellow
$migrations = Get-ChildItem "$sourceBase\database\migrations" -Filter "*landlord*" -ErrorAction SilentlyContinue
foreach ($file in $migrations) {
    $dest = Join-Path "$targetBase\database\migrations" $file.Name
    Copy-FileWithStructure $file.FullName $dest
}

# 9. Views
Write-Host "`n[9/12] Views..." -ForegroundColor Yellow
$viewFiles = Get-ChildItem "$sourceBase\resources\views\landlord" -Recurse -Filter "*.blade.php" -ErrorAction SilentlyContinue
foreach ($file in $viewFiles) {
    $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
    $dest = Join-Path $targetBase $relativePath
    Copy-FileWithStructure $file.FullName $dest
}

# 10. Mail Views
Write-Host "`n[10/12] Mail Views..." -ForegroundColor Yellow
$mailFiles = Get-ChildItem "$sourceBase\resources\views\mail\landlord" -Recurse -Filter "*.blade.php" -ErrorAction SilentlyContinue
foreach ($file in $mailFiles) {
    $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
    $dest = Join-Path $targetBase $relativePath
    Copy-FileWithStructure $file.FullName $dest
}

# 11. Tests
Write-Host "`n[11/12] Tests..." -ForegroundColor Yellow
$testFiles = Get-ChildItem "$sourceBase\tests\Feature" -Recurse -Filter "*Landlord*" -ErrorAction SilentlyContinue
foreach ($file in $testFiles) {
    $relativePath = $file.FullName.Substring($sourceBase.Length + 1)
    $dest = Join-Path $targetBase $relativePath
    Copy-FileWithStructure $file.FullName $dest
}

# 12. Documentation
Write-Host "`n[12/12] Documentation..." -ForegroundColor Yellow
$docFiles = Get-ChildItem "$sourceBase\docs" -Filter "*landlord*" -ErrorAction SilentlyContinue
foreach ($file in $docFiles) {
    $dest = Join-Path "$targetBase\docs" $file.Name
    Copy-FileWithStructure $file.FullName $dest
}

Write-Host "`n======================================" -ForegroundColor Cyan
Write-Host "Transfer Complete!" -ForegroundColor Green
Write-Host "Total files copied: $filescopied" -ForegroundColor White
Write-Host "======================================" -ForegroundColor Cyan
