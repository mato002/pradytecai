# Production Upload Guide - Styling Files

## Problem
Styling only works on your PC because Vite assets need to be compiled and uploaded to production.

## Solution: Upload These Files

### Step 1: Build Assets (Run on Your PC)
```bash
npm run build
```
OR use the batch file:
```bash
build-production.bat
```

This creates compiled CSS/JS files in `public/build/`

### Step 2: Upload to Production Server

**Upload the ENTIRE directory:**
```
public/build/
```

This directory contains:
- `assets/` folder (with compiled CSS and JS files)
- `manifest.json` (tells Laravel which files to load)

### Step 3: Verify Production .env

Make sure your production `.env` file has:
```env
APP_ENV=production
APP_DEBUG=false
```

### Step 4: Clear Production Cache

After uploading, run on production server:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

## Quick Checklist

- [ ] Run `npm run build` on your PC
- [ ] Upload entire `public/build/` folder to production
- [ ] Verify production `.env` has `APP_ENV=production`
- [ ] Clear production cache
- [ ] Test the website on production

## Important Notes

1. **Don't upload `public/hot` file** - This is only for development
2. **Upload the entire `public/build/` folder** - Not just individual files
3. **File names change** - Each build creates new file names (like `app-CAiCLEjY.js`), so always upload the entire folder
4. **After any CSS/JS changes** - Rebuild and re-upload `public/build/`

## File Structure on Production

Your production server should have:
```
public/
  ├── build/
  │   ├── assets/
  │   │   ├── app-XXXXX.js
  │   │   └── app-XXXXX.css
  │   └── manifest.json
  ├── index.php
  └── ... (other public files)
```

