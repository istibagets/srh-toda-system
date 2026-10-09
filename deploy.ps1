# SRH LINK TODA - Direct Oracle Cloud Fast Deployment Script
param(
    [switch]$SkipBuild,
    [switch]$FullReset,
    [string]$RemoteHost = "161.118.237.125",
    [string]$RemoteUser = "ubuntu",
    [string]$RemoteRoot = "/var/www/srh-link-toda-ionic"
)

# Resolve SSH Key location
$SSH_KEY = "C:\Users\rfer3\Documents\SSH KEY\ssh-key-2026-08-08.key"
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\lenovo\.ssh\ssh-key-2026-08-08.key"
}
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\lenovo\OneDrive\Documents\SSH KEY\ssh-key-2026-08-08.key"
}
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "$env:USERPROFILE\.ssh\id_rsa"
}

# Resolve Local Directories
$ROOT_DIR = $PSScriptRoot
$BACKEND_DIR = Join-Path $ROOT_DIR "laravel_backend"
$IONIC_DIR = Join-Path $ROOT_DIR "ionic_frontend"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "SRH LINK TODA - Direct Oracle Deploy" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Backend Path : $BACKEND_DIR" -ForegroundColor Gray
Write-Host "Frontend Path: $IONIC_DIR" -ForegroundColor Gray
Write-Host "SSH Key Path : $SSH_KEY" -ForegroundColor Gray
Write-Host "Remote Host  : $RemoteUser@$RemoteHost:$RemoteRoot" -ForegroundColor Gray
Write-Host "------------------------------------------------" -ForegroundColor Cyan

if (-not (Test-Path $SSH_KEY)) {
    Write-Host "Error: SSH Key not found at $SSH_KEY" -ForegroundColor Red
    exit 1
}

# 1. Build Ionic Frontend (if not skipped)
if (-not $SkipBuild) {
    Write-Host "[1/4] Building Ionic Frontend (Production)..." -ForegroundColor Yellow
    Push-Location "$IONIC_DIR"
    try {
        npm run build
        if ($LASTEXITCODE -ne 0) {
            Write-Host "Error: Ionic build failed!" -ForegroundColor Red
            Pop-Location
            exit 1
        }
    } finally {
        Pop-Location
    }
} else {
    Write-Host "[1/4] Skipping Ionic Frontend build (-SkipBuild specified)..." -ForegroundColor DarkGray
}

# 2. Package Deployment Bundle
Write-Host "[2/4] Packaging deployment bundle..." -ForegroundColor Yellow
$scratchDir = Join-Path $ROOT_DIR "scratch"
if (-not (Test-Path $scratchDir)) { New-Item -ItemType Directory -Path $scratchDir -Force | Out-Null }

$tempDir = Join-Path $scratchDir "deploy_temp"
if (Test-Path $tempDir) { Remove-Item -Recurse -Force $tempDir }
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Copy Backend Core Directories
$dirs = @('app', 'bootstrap', 'config', 'database', 'routes', 'public')
foreach ($d in $dirs) {
    $srcPath = Join-Path $BACKEND_DIR $d
    if (Test-Path $srcPath) {
        Copy-Item -Path $srcPath -Destination "$tempDir\$d" -Recurse -Force
    }
}

# Copy Essential Root Backend Files
$files = @('artisan', 'composer.json', 'composer.lock')
foreach ($f in $files) {
    $filePath = Join-Path $BACKEND_DIR $f
    if (Test-Path $filePath) {
        Copy-Item -Path $filePath -Destination "$tempDir\$f" -Force
    }
}

# Clean local bootstrap cache files
if (Test-Path "$tempDir\bootstrap\cache") {
    Get-ChildItem -Path "$tempDir\bootstrap\cache" -Exclude ".gitignore" | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
}

# Copy Compiled Ionic Frontend (www -> ionic_www)
$ionicWww = Join-Path $IONIC_DIR "www"
if (Test-Path $ionicWww) {
    Copy-Item -Path $ionicWww -Destination "$tempDir\ionic_www" -Recurse -Force
} else {
    Write-Host "Warning: $ionicWww not found! Make sure frontend build completed." -ForegroundColor Yellow
}

$tarOutput = Join-Path $scratchDir "deploy_ionic.tar.gz"
if (Test-Path $tarOutput) { Remove-Item -Force $tarOutput }
tar -czf "$tarOutput" -C "$tempDir" .
Remove-Item -Recurse -Force $tempDir

# 3. Fast Upload via SCP
Write-Host "[3/4] Uploading bundle to Oracle Cloud Server..." -ForegroundColor Yellow
$scpArgs = @(
    "-i", "`"$SSH_KEY`"",
    "-o", "StrictHostKeyChecking=no",
    "-o", "UserKnownHostsFile=NUL",
    "`"$tarOutput`"",
    "`"${RemoteUser}@${RemoteHost}:${RemoteRoot}/deploy_ionic.tar.gz`""
)
$scpCmd = "scp -i `"$SSH_KEY`" -o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL `"$tarOutput`" `"${RemoteUser}@${RemoteHost}:${RemoteRoot}/deploy_ionic.tar.gz`""
Invoke-Expression $scpCmd
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: SCP file upload failed!" -ForegroundColor Red
    exit 1
}

# 4. Remote Unpack & Apply Updates
Write-Host "[4/4] Extracting bundle and updating remote services..." -ForegroundColor Yellow
if ($FullReset) {
    $remoteScript = "cd $RemoteRoot && tar -xzf deploy_ionic.tar.gz && rm -f deploy_ionic.tar.gz && php artisan migrate:fresh --seed --force && php artisan config:clear 2>/dev/null && php artisan route:clear 2>/dev/null && php artisan view:clear 2>/dev/null && sudo chown -R ${RemoteUser}:www-data $RemoteRoot && sudo chmod -R 775 $RemoteRoot/storage $RemoteRoot/bootstrap/cache"
} else {
    $remoteScript = "cd $RemoteRoot && tar -xzf deploy_ionic.tar.gz && rm -f deploy_ionic.tar.gz && php artisan up 2>/dev/null && php artisan migrate --force && php artisan config:clear 2>/dev/null && php artisan route:clear 2>/dev/null && php artisan view:clear 2>/dev/null && sudo chown -R ${RemoteUser}:www-data $RemoteRoot && sudo chmod -R 775 $RemoteRoot/storage $RemoteRoot/bootstrap/cache"
}

$sshCmd = "ssh -i `"$SSH_KEY`" -o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL `"${RemoteUser}@${RemoteHost}`" `"$remoteScript`""
Invoke-Expression $sshCmd

# Cleanup Local Archive
if (Test-Path $tarOutput) { Remove-Item -Force $tarOutput }

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "DEPLOYMENT COMPLETE! Directly deployed to Oracle VM" -ForegroundColor Green
Write-Host "URL: https://srh-link-toda-ionic.duckdns.org" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
