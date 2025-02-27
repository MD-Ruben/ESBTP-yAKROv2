# PowerShell script to start the PHP development server
Set-Location -Path $PSScriptRoot\..
Write-Host "Starting PHP development server at http://localhost:8000"
php -S localhost:8000 -t public 