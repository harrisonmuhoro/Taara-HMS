$ErrorActionPreference = 'Stop'

$projectRoot = 'D:\wamp64\www\hotel'
$queueTask = 'Grand Horizon Hotel - Queue Worker'
$schedulerTask = 'Grand Horizon Hotel - Scheduler'
$queueLauncher = Join-Path $projectRoot 'scripts\queue-worker.bat'
$schedulerLauncher = Join-Path $projectRoot 'scripts\scheduler-worker.bat'

foreach ($path in @($queueLauncher, $schedulerLauncher)) {
    if (-not (Test-Path -LiteralPath $path)) {
        throw "Launcher not found: $path"
    }
}

$startupScript = Join-Path $projectRoot 'scripts\start-background-services.ps1'

function Register-BackgroundTask([string] $taskName, [string] $launcher) {
    $taskAction = "cmd.exe /c `"$launcher`""
    & schtasks.exe /Create /TN $taskName /SC ONLOGON /TR $taskAction /F | Out-Host
    if ($LASTEXITCODE -ne 0) {
        throw "Could not register scheduled task: $taskName"
    }
}

$tasksRegistered = $true
try {
    Register-BackgroundTask $queueTask $queueLauncher
    Register-BackgroundTask $schedulerTask $schedulerLauncher
} catch {
    $tasksRegistered = $false
}

if (-not $tasksRegistered) {
    $runKey = 'HKCU:\Software\Microsoft\Windows\CurrentVersion\Run'
    $command = "powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$startupScript`""
    try {
        New-ItemProperty -Path $runKey -Name 'GrandHorizonHotelBackgroundServices' -Value $command -PropertyType String -Force | Out-Null
        Write-Host 'Task Scheduler access was denied; per-user startup automation was registered instead.' -ForegroundColor Yellow
    } catch {
        Write-Error 'Windows denied both Task Scheduler and startup registration. Re-run this script from an elevated PowerShell window.'
        exit 1
    }
} else {
    Write-Host 'Task Scheduler automation was registered.' -ForegroundColor Green
}

Write-Host ''
Write-Host 'Background services registered successfully.' -ForegroundColor Green
Write-Host "- $queueTask"
Write-Host "- $schedulerTask"
Write-Host 'They will start automatically at your next Windows login.'
