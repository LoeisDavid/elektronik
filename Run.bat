
@echo off
setlocal
title Elektronik - Laravel Launcher

cd /d "%~dp0"

if not exist "artisan" (
    echo ERROR: File artisan tidak ditemukan.
    pause
    exit /b 1
)

set "PHP="

for %%P in (
    "C:\laragon\bin\php\php-*\php.exe"
    "D:\laragon\bin\php\php-*\php.exe"
    "%~dp0runtime\php\php.exe"
) do (
    if exist "%%~P" set "PHP=%%~P"
)

if not defined PHP (
    where php >nul 2>&1
    if not errorlevel 1 set "PHP=php"
)

if not defined PHP (
    echo PHP tidak ditemukan.
    echo Instal Laragon atau siapkan PHP portable.
    pause
    exit /b 1
)

if not exist "vendor\autoload.php" (
    echo Dependensi Laravel belum tersedia.
    pause
    exit /b 1
)

start "" http://127.0.0.1:8000
"%PHP%" artisan serve --host=127.0.0.1 --port=8000

pause
