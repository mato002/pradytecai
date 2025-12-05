# Production Styling Fix - Upload Guide

## Problem
After uploading the public folder, styling is lost because the build files need to match the manifest.json exactly.

## Solution: Rebuild and Upload Correctly

### Step 1: Rebuild Assets on Your Local PC

Run this command to rebuild assets with fresh hashes:
```bash
npm run build
```

OR use the batch file:
```bash
build-production.bat
```

This will create new files in `public/build/` with updated hash names.

### Step 2: Upload to Production

**You MUST upload the ENTIRE `public/build/` folder structure:**

```
public/
  └── build/
      ├── assets/
      │   ├── app-XXXXX.js    (new hash name)
      │   └── app-XXXXX.css   (new hash name)
      └── manifest.json       (references the files above)
```

### Step 3: Verify File Structure on Production

After uploading, your production server should have:
```
public/
  ├── build/
  │   ├── assets/
  │   │   ├── app-XXXXX.js
  │   │   └── app-XXXXX.css
  │   └── manifest.json
  ├── index.php
  └── .htaccess
```

### Step 4: Clear Production Cache

SSH into your production server and run:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

### Step 5: Verify .env on Production

Make sure production `.env` has:
```env
APP_ENV=production
APP_DEBUG=false
```

## Important Notes

1. **File names change with each build** - The hash in filenames (like `app-HNO-nXWj.css`) changes every time you rebuild. Always upload the entire `public/build/` folder after rebuilding.

2. **manifest.json must match files** - The manifest.json file tells Laravel which CSS/JS files to load. If the files don't exist, styling breaks.

3. **Upload the entire folder** - Don't just upload individual files. Upload the complete `public/build/` directory structure.

4. **After any CSS/JS changes** - Always rebuild (`npm run build`) and re-upload `public/build/` to production.

## Quick Checklist

- [ ] Run `npm run build` on local PC
- [ ] Upload entire `public/build/` folder to production
- [ ] Verify `public/build/assets/` folder exists on production
- [ ] Verify `public/build/manifest.json` exists on production
- [ ] Clear production cache
- [ ] Hard refresh browser (Ctrl+Shift+R)

