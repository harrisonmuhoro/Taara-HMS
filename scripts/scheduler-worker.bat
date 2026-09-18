@echo off
setlocal
cd /d D:\wamp64\www\hotel
"C:\Users\BLADE\.config\herd-lite\bin\php.exe" artisan schedule:work
