# SRH LINK TODA IONIC - Fast Direct Deployment Script
param(
    [switch]$SkipBuild,
    [switch]$FullReset,
    [string]$RemoteHost = "161.118.237.125",
    [string]$RemoteUser = "ubuntu",
    [string]$RemoteRoot = "/var/www/srh-link-toda-ionic"
)

# Call root deploy.ps1 if present
$rootDeploy = Join-Path $PSScriptRoot "..\deploy.ps1"
if (Test-Path $rootDeploy) {
    & $rootDeploy -SkipBuild:$SkipBuild -FullReset:$FullReset -RemoteHost $RemoteHost -RemoteUser $RemoteUser -RemoteRoot $RemoteRoot
    exit $LASTEXITCODE
}

# Fallback standalone
$SSH_KEY = "C:\Users\rfer3\Documents\SSH KEY\ssh-key-2026-08-08.key"
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\lenovo\.ssh\ssh-key-2026-08-08.key"
}
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\lenovo\OneDrive\Documents\SSH KEY\ssh-key-2026-08-08.key"
}

$IONIC_DIR = $PSScriptRoot
$BACKEND_DIR = Join-Path $PSScriptRoot "..\laravel_backend"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "SRH LINK TODA IONIC - Direct Fast Deploy" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan

if (-not $SkipBuild) {
    Write-Host "Building Ionic Frontend (Production)..." -ForegroundColor Yellow
    Push-Location "$IONIC_DIR"
    npm run build
    Pop-Location
}

$scratchDir = Join-Path $BACKEND_DIR "scratch"
if (-not (Test-Path $scratchDir)) { New-Item -ItemType Directory -Path $scratchDir -Force | Out-Null }
$tempDir = Join-Path $scratchDir "deploy_temp"
if (Test-Path $tempDir) { Remove-Item -Recurse -Force $tempDir }
New-Item -ItemType Directory -Path $tempDir | Out-Null

$dirs = @('app', 'bootstrap', 'config', 'database', 'routes', 'public')
foreach ($d in $dirs) {
    $srcPath = Join-Path $BACKEND_DIR $d
    if (Test-Path $srcPath) {
        Copy-Item -Path $srcPath -Destination "$tempDir\$d" -Recurse -Force
    }
}
$files = @('artisan', 'composer.json', 'composer.lock')
foreach ($f in $files) {
    $filePath = Join-Path $BACKEND_DIR $f
    if (Test-Path $filePath) {
        Copy-Item -Path $filePath -Destination "$tempDir\$f" -Force
    }
}

if (Test-Path "$tempDir\bootstrap\cache") {
    Get-ChildItem -Path "$tempDir\bootstrap\cache" -Exclude ".gitignore" | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
}

if (Test-Path "$IONIC_DIR\www") {
    Copy-Item -Path "$IONIC_DIR\www" -Destination "$tempDir\ionic_www" -Recurse -Force
}

$tarOutput = Join-Path $scratchDir "deploy_ionic.tar.gz"
if (Test-Path $tarOutput) { Remove-Item -Force $tarOutput }
tar -czf "$tarOutput" -C "$tempDir" .
Remove-Item -Recurse -Force $tempDir

Write-Host "Uploading bundle to Oracle Server..." -ForegroundColor Yellow
Invoke-Expression "scp -i `"$SSH_KEY`" -o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL `"$tarOutput`" `"${RemoteUser}@${RemoteHost}:${RemoteRoot}/deploy_ionic.tar.gz`""

Write-Host "Applying remote updates..." -ForegroundColor Yellow
$remoteCmd = "cd $RemoteRoot && tar -xzf deploy_ionic.tar.gz && rm -f deploy_ionic.tar.gz && php artisan up 2>/dev/null && php artisan migrate --force && php artisan config:clear 2>/dev/null && php artisan route:clear 2>/dev/null && php artisan view:clear 2>/dev/null && sudo chown -R ${RemoteUser}:www-data $RemoteRoot && sudo chmod -R 775 $RemoteRoot/storage $RemoteRoot/bootstrap/cache"
Invoke-Expression "ssh -i `"$SSH_KEY`" -o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL `"${RemoteUser}@${RemoteHost}`" `"$remoteCmd`""

if (Test-Path $tarOutput) { Remove-Item -Force $tarOutput }

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "DEPLOYMENT COMPLETE! https://srh-link-toda-ionic.duckdns.org" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
