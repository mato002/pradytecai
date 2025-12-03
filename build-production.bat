@echo off
echo ========================================
echo Building Pradytecai Assets for Production
echo ========================================
echo.

echo Step 1: Building assets with Vite...
call npm run build
if %errorlevel% neq 0 (
    echo.
    echo ERROR: npm build failed!
    echo Make sure Node.js and npm are installed.
    pause
    exit /b 1
)

echo.
echo Step 2: Clearing Laravel caches...
call php artisan optimize:clear
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache

echo.
echo ========================================
echo Build Complete!
echo ========================================
echo.
echo Assets are now compiled in: public/build/
echo Copy the entire 'public/build' folder to other PCs
echo.
echo Make sure .env has: APP_ENV=production
echo.
pause

