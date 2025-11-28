$source = "C:\wamp5\www\skolaris-academic-reports"
$dest = "C:\wamp5\www\skolariscloud3\packages\skolaris\academic-reports"

Write-Host "Syncing files from $source to $dest..."
Copy-Item -Path "$source\*" -Destination $dest -Recurse -Force
Write-Host "Sync complete!"