@echo off
echo Starting Hotel Management System dev environment...
start "Laravel API - :8000" cmd /k "cd /d D:\wamp64\www\hotel\backend && D:\wamp64\bin\php\php8.3.28\php.exe artisan serve --port=8000"
start "React Frontend - :5173" cmd /k "cd /d D:\wamp64\www\hotel\frontend && npm run dev"
echo Both servers launching in separate windows.