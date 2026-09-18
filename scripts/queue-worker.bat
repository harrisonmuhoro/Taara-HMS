@echo off
setlocal
cd /d D:\wamp64\www\hotel
"C:\Users\BLADE\.config\herd-lite\bin\php.exe" artisan queue:work database --sleep=3 --tries=3 --max-time=3600
