# Django run helper
param(
  [int]$Port = 8000
)

$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

$VenvActivate = Join-Path $Root ".venv\Scripts\Activate.ps1"
if (-not (Test-Path $VenvActivate)) {
  Write-Error "Missing .venv. Create it with: python -m venv .venv && .\.venv\Scripts\pip install -r requirements.txt"
  exit 1
}

$busy = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
if ($busy) {
  Write-Host "Port $Port is already in use (PID $($busy.OwningProcess))."
  Write-Host "Stop that process, or run: .\scripts\run-django.ps1 -Port 8001"
}

. $VenvActivate

if (-not (Test-Path (Join-Path $Root "react\dist\assets\index.js"))) {
  Write-Host "React build missing — building once..."
  Push-Location (Join-Path $Root "react")
  npm run build
  Pop-Location
}

$env:DJANGO_SETTINGS_MODULE = "config.settings"
Write-Host "Starting Django at http://127.0.0.1:$Port/"
python manage.py runserver $Port
