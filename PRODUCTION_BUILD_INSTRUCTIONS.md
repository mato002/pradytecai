# Production Build Instructions - Fix Styling on Other PCs

## Problem
Styling works on your PC but not on other PCs because:
- Vite dev server is running on your PC (`npm run dev`)
- Other PCs can't access the dev server
- Assets need to be compiled for production

## Solution

### Step 1: Build Assets for Production

Open Command Prompt (CMD) or PowerShell as Administrator and run:

```bash
npm run build
```

This will compile all CSS and JS files into the `public/build` directory.

### Step 2: Set Environment to Production

Make sure your `.env` file has:

```env
APP_ENV=production
APP_DEBUG=false
```

### Step 3: Clear All Caches

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Verify Build Files Exist

Check that these files exist:
- `public/build/manifest.json`
- `public/build/assets/app-*.css`
- `public/build/assets/app-*.js`

### Step 5: Deploy to Other PCs

Copy these directories/files to other PCs:
- `public/build/` (entire directory)
- `.env` file (with APP_ENV=production)

## Alternative: If npm run build fails

If you get PowerShell execution policy errors, use Command Prompt (cmd.exe) instead:

1. Open Command Prompt (not PowerShell)
2. Navigate to your project: `cd C:\xampp\htdocs\Pradytecai`
3. Run: `npm run build`

## Quick Fix Script

Create a file `build-production.bat`:

```batch
@echo off
echo Building assets for production...
call npm run build
echo.
echo Clearing Laravel caches...
call php artisan optimize:clear
call php artisan config:cache
call php artisan view:cache
echo.
echo Build complete! Assets are in public/build/
pause
```

Then double-click `build-production.bat` to build everything.

## Important Notes

- **Always run `npm run build` before deploying** to other PCs
- The `public/build` directory must be included when copying files
- Never commit `public/build` to git (add to .gitignore), but include it when deploying
- After building, the `@vite` directive will automatically use the compiled assets instead of the dev server

