@echo off
echo Starting Grand Horizon Hotel development services...
start "Laravel - :8000" cmd /k "cd /d D:\wamp64\www\hotel && C:\Users\BLADE\.config\herd-lite\bin\php.exe artisan serve --port=8000"
start "Vite" cmd /k "cd /d D:\wamp64\www\hotel && npm run dev"
start "Queue Worker" cmd /k "cd /d D:\wamp64\www\hotel && C:\Users\BLADE\.config\herd-lite\bin\php.exe artisan queue:work database --sleep=3 --tries=3 --max-time=3600"
start "Laravel Scheduler" cmd /k "cd /d D:\wamp64\www\hotel && C:\Users\BLADE\.config\herd-lite\bin\php.exe artisan schedule:work"
echo Laravel, Vite, queue, and scheduler services are launching.
 
 