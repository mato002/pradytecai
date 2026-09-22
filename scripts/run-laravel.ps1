# Optional: run legacy Laravel on a DIFFERENT port so it cannot collide with Django :8000
param(
  [int]$Port = 8001
)

$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

if (-not (Test-Path (Join-Path $Root "artisan"))) {
  Write-Error "artisan not found"
  exit 1
}

Write-Host "Starting Laravel at http://127.0.0.1:$Port/  (Django should stay on :8000)"
php artisan serve --host=127.0.0.1 --port=$Port
