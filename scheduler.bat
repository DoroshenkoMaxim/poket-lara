@echo off
title Laravel Scheduler - Currency Parser
echo Starting Laravel Scheduler for Currency Parser...
echo Press Ctrl+C to stop
echo.

cd /d C:\OSPanel\domains\laravel

REM Поиск правильного пути к PHP
if exist "C:\OSPanel\modules\php\PHP_8.2\php.exe" (
    set PHP_PATH=C:\OSPanel\modules\php\PHP_8.2\php.exe
) else if exist "C:\OSPanel\modules\php\PHP_8.1\php.exe" (
    set PHP_PATH=C:\OSPanel\modules\php\PHP_8.1\php.exe
) else if exist "C:\OSPanel\modules\php\PHP_8.0\php.exe" (
    set PHP_PATH=C:\OSPanel\modules\php\PHP_8.0\php.exe
) else (
    echo ERROR: PHP not found in OSPanel modules
    echo Please check your OSPanel installation
    pause
    exit
)

echo Using PHP: %PHP_PATH%
echo.

:loop
echo [%date% %time%] Running scheduler...
"%PHP_PATH%" artisan schedule:run
if errorlevel 1 (
    echo ERROR: Failed to run scheduler
    echo Check that Laravel is properly installed
)
timeout /t 60 /nobreak >nul
goto loop 