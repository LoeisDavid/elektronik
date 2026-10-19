@echo off
title Laravel - Elektronik

cd /d "C:\laragon\www\elektronik"

start "" http://127.0.0.1:8000

php artisan serve --host=127.0.0.1 --port=8000

pause
