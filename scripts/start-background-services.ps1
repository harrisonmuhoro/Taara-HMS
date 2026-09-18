$ErrorActionPreference = 'Stop'

$projectRoot = 'D:\wamp64\www\hotel'
$php = 'C:\Users\BLADE\.config\herd-lite\bin\php.exe'
$logDirectory = Join-Path $projectRoot 'storage\logs'

if (-not (Test-Path -LiteralPath $php)) {
    exit 1
}

New-Item -ItemType Directory -Force -Path $logDirectory | Out-Null

Start-Process -FilePath $php -ArgumentList @('artisan', 'queue:work', 'database', '--sleep=3', '--tries=3', '--max-time=3600') -WorkingDirectory $projectRoot -WindowStyle Hidden -RedirectStandardOutput (Join-Path $logDirectory 'queue-worker.log') -RedirectStandardError (Join-Path $logDirectory 'queue-worker-error.log')
Start-Process -FilePath $php -ArgumentList @('artisan', 'schedule:work') -WorkingDirectory $projectRoot -WindowStyle Hidden -RedirectStandardOutput (Join-Path $logDirectory 'scheduler-worker.log') -RedirectStandardError (Join-Path $logDirectory 'scheduler-worker-error.log')
