# Production Deployment Checklist for pradytecai.com

## Pre-Deployment Steps

### 1. Build Assets for Production ⚠️ CRITICAL
**This is why styling doesn't work on other PCs!**

```bash
# Option 1: Use the batch file (Windows)
build-production.bat

# Option 2: Manual commands
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**IMPORTANT:** The `public/build/` folder MUST be uploaded to your server!

### 2. Environment Configuration ✓
Your `.env` is correctly set:
- ✅ `APP_ENV=production`
- ✅ `APP_DEBUG=false`
- ✅ `APP_URL=https://pradytecai.com`

**Note:** `APP_NAME=BulkSMSPlatform` but your site shows "Pradytecai". Consider updating:
```env
APP_NAME=Pradytecai
```

### 3. Files to Upload to Server
- ✅ All application files (except `node_modules`, `.git`, `.env`)
- ✅ `public/build/` folder (compiled assets - CRITICAL!)
- ✅ `.env` file (with your production credentials)
- ✅ `vendor/` folder (run `composer install --no-dev --optimize-autoloader` on server)
- ✅ `storage/` folder (ensure write permissions)
- ✅ `bootstrap/cache/` folder (ensure write permissions)

### 4. Server Requirements
- PHP 8.3+ (you're using 8.3.26 ✓)
- MySQL/MariaDB
- Node.js & npm (for building assets locally, not needed on server)
- Composer
- mod_rewrite enabled (Apache) or nginx rewrite rules

### 5. Server Setup Commands
After uploading files to server:

```bash
# Install dependencies (production only)
composer install --no-dev --optimize-autoloader

# Generate application key (if not set)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Cache everything for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. File Permissions (Linux/Unix servers)
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Verify Deployment
- [ ] Visit https://pradytecai.com - styling should work
- [ ] Check all pages load correctly
- [ ] Test contact form submission
- [ ] Test newsletter subscription
- [ ] Verify database connection
- [ ] Check admin panel access

## Common Issues & Solutions

### Issue: No styling on production
**Solution:** 
1. Run `npm run build` locally
2. Upload `public/build/` folder to server
3. Clear caches: `php artisan optimize:clear`

### Issue: 500 errors
**Solution:**
1. Check file permissions
2. Check `.env` file exists and is correct
3. Check `storage/logs/laravel.log` for errors
4. Run: `php artisan config:clear`

### Issue: Assets return 404
**Solution:**
1. Ensure `public/build/manifest.json` exists
2. Check `APP_URL` in `.env` matches your domain
3. Verify `.htaccess` is in `public/` folder

## Quick Deploy Script

Create `deploy.sh` on your server:

```bash
#!/bin/bash
cd /path/to/your/project
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Notes
- Build assets locally (requires Node.js)
- Upload `public/build/` to server
- Never commit `.env` to git
- Always test in staging before production

