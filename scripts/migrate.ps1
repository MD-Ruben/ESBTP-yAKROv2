# PowerShell script to run database migrations
Set-Location -Path $PSScriptRoot\..
Write-Host "Running database migrations..."
php artisan migrate
Write-Host "Database migrations completed!"
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown") 