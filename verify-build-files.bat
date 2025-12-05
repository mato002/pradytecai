@echo off
echo ========================================
echo Verifying Build Files for Production
echo ========================================
echo.

if exist "public\build\manifest.json" (
    echo [OK] manifest.json exists
) else (
    echo [ERROR] manifest.json is missing!
    echo You need to run: npm run build
    pause
    exit /b 1
)

if exist "public\build\assets" (
    echo [OK] assets folder exists
    
    dir /b public\build\assets\*.css >nul 2>&1
    if %errorlevel% equ 0 (
        echo [OK] CSS file found
        for %%f in (public\build\assets\*.css) do echo     - %%~nxf
    ) else (
        echo [ERROR] No CSS file found!
    )
    
    dir /b public\build\assets\*.js >nul 2>&1
    if %errorlevel% equ 0 (
        echo [OK] JS file found
        for %%f in (public\build\assets\*.js) do echo     - %%~nxf
    ) else (
        echo [ERROR] No JS file found!
    )
) else (
    echo [ERROR] assets folder is missing!
    echo You need to run: npm run build
    pause
    exit /b 1
)

echo.
echo ========================================
echo Files to Upload to Production:
echo ========================================
echo.
echo Upload the ENTIRE folder: public\build\
echo.
echo This includes:
echo   - public\build\manifest.json
echo   - public\build\assets\*.css
echo   - public\build\assets\*.js
echo.
echo After uploading, clear production cache:
echo   php artisan optimize:clear
echo.
pause

