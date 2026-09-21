# PradytecAI

Corporate website and **Marketing Command Centre** for [PradytecAI](https://pradytecai.com) — portfolio products, careers/HR, blog, lead inbox, campaigns, content calendar, social publishing shell, analytics stubs, and marketing pulse alerts.

---

## About the project

### Public site
- Home, about, services, products, blog, FAQ, policies, search
- Contact form with product + UTM attribution
- Newsletter subscribe
- Careers listings and job applications
- Tracked short links (`/t/{code}`)

### Admin (`/admin`)
Permission-gated admin for:
- **Products** — DB-backed portfolio catalog
- **Leads / enquiries / demos** — contact inbox and demo requests
- **Campaigns** — product-linked campaigns with UTM context
- **Content** — library, calendar, approvals, schedule/publish
- **Social accounts** — Buffer-first adapter (stub-capable)
- **Integrations** — Buffer / GA4 shell
- **Analytics** — metric snapshots overview
- **Careers** — positions, applications, interviews
- **Blog, users, roles, settings, activity logs**
- **Marketing Pulse** — health alerts on the dashboard

### Auth & access
- Spatie Laravel Permission (roles + permissions)
- Product-scoped access via `user_access_scopes`
- Seeded roles include `super_admin`, `hr_manager`, and marketing roles defined in `config/marketing_permissions.php`

---

## Tech stack

| Layer | Technology |
|--------|------------|
| Backend | PHP 8.2+, Laravel 12 |
| Auth / ACL | Spatie Permission |
| Frontend | Blade, Vite 7, Tailwind CSS 4 |
| Icons | Blade Heroicons |
| Database | MySQL / MariaDB (production), SQLite OK for local |
| Queues | Database queue driver |
| Scheduler | Laravel Schedule (`marketing:pulse` hourly, metric sync jobs daily) |
| Integrations | BulkSMS CRM, UltraMsg WhatsApp, Buffer, GA4 (env-driven; some stubs) |
| Tests | PHPUnit 11 |

---

## Requirements

- PHP 8.2+ with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- Composer 2
- Node.js 18+ and npm
- MySQL/MariaDB (or SQLite for local)
- Optional: [Laravel Herd](https://herd.laravel.com/) (Windows/macOS) — this project commonly runs at `http://pradytecai.test`

---

## Local setup

### 1. Clone and install

```bash
git clone <your-repo-url> pradytecai
cd pradytecai

composer install
cp .env.example .env
php artisan key:generate
```

Or one-shot Composer setup (installs deps, `.env`, key, migrate, npm build):

```bash
composer run setup
```

### 2. Configure `.env`

Minimum local values:

```env
APP_NAME=PradytecAI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://pradytecai.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pradytecai
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
MAIL_MAILER=log
```

For SQLite instead:

```env
DB_CONNECTION=sqlite
# create empty file: database/database.sqlite
```

Optional integrations (see `.env.example`):

```env
BULKSMS_CRM_ENABLED=true
BULKSMS_API_URL=https://crm.pradytecai.com/api
BULKSMS_API_KEY=
ULTRAMSG_INSTANCE_ID=
ULTRAMSG_TOKEN=
BUFFER_ENABLED=true
BUFFER_ACCESS_TOKEN=
GA4_PROPERTY_ID=
GA4_CREDENTIALS_JSON=
```

### 3. Database, storage, seed

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Seeded users (change passwords in production):

| Role | Email | Password |
|------|--------|----------|
| Super admin | `admin@pradytecai.com` | `admin123` |
| HR manager | `hr@pradytecai.com` | `hr123` |

Also seeds permissions/roles, portfolio products, and blog posts.

### 4. Frontend assets

**Dev (Vite HMR):**

```bash
npm install
npm run dev
```

**Or run everything together:**

```bash
composer run dev
```

That starts HTTP server, queue worker, log tail (`pail`), and Vite.

**Herd:** point the site at this folder; open `http://pradytecai.test` (and keep `npm run dev` or a production build for assets).

**Without Herd:**

```bash
php artisan serve
```

### 5. Queue + scheduler (local)

```bash
php artisan queue:work
php artisan schedule:work
```

Scheduled jobs (from `routes/console.php`):

- `marketing:pulse` — hourly
- `SyncSocialMetricsJob` — daily 02:00
- `SyncGa4Job` — daily 02:30

Manual pulse:

```bash
php artisan marketing:pulse
```

### 6. Tests

```bash
php artisan test
php artisan test --filter=MarketingAdminAuthTest
```

---

## Production setup

### Server layout (typical shared / cPanel style)

This repo’s `deploy.sh` assumes:

- App code: e.g. `/home/pradytec/pradytecai`
- Web document root: e.g. `/home/pradytec/pradytecai/public_html` (or your host’s `public_html` symlink/copy of `public`)

Point the vhost / domain document root at Laravel’s **`public`** directory (or keep syncing built assets into `public_html` as the script does).

### 1. First-time server install

```bash
cd /home/pradytec/pradytecai   # or your path
git clone <your-repo-url> .
composer install --no-dev --optimize-autoloader
cp .env.example .env
# edit .env for production
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

Production `.env` essentials:

```env
APP_NAME=PradytecAI
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pradytecai.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

MAIL_MAILER=smtp
# ... real mail credentials
```

Permissions:

```bash
chmod -R 775 storage bootstrap/cache
chown -R <web-user>:<web-user> storage bootstrap/cache
```

### 2. Build assets

On the server (if Node is available) `deploy.sh` runs `npm ci` + `npm run build`.

Or build locally / on CI and upload `public/build/` (must include `manifest.json`):

```bash
npm ci
npm run build
```

Windows helper: `build-production.bat`.

### 3. Cron (required)

Laravel scheduler (every minute):

```cron
* * * * * cd /home/pradytec/pradytecai && php artisan schedule:run >> /dev/null 2>&1
```

Queue worker (Supervisor or equivalent):

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### 4. Cache for production

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Deploy with `deploy.sh`

`deploy.sh` is a **manual production deploy** script. It does **not** run on git push by itself — you trigger it on the server (SSH, cron, or a webhook).

### What it does (7 steps)

1. `git pull origin main` (or `DEPLOY_BRANCH`)
2. `composer install --no-dev --optimize-autoloader`
3. `php artisan migrate --force`
4. `npm ci` / `npm install` + `npm run build` (requires Node on the server)
5. Sync `public/build/` → `PUBLIC_HTML/build` (rsync or copy)
6. `php artisan optimize:clear`
7. Rebuild config / route / view caches

### Run it

```bash
cd /home/pradytec/pradytecai
chmod +x deploy.sh
./deploy.sh
```

Optional overrides:

```bash
PUBLIC_HTML=/path/to/public_html DEPLOY_BRANCH=main ./deploy.sh
```

Defaults:

- `PUBLIC_HTML=/home/pradytec/pradytecai/public_html`
- `DEPLOY_BRANCH=main`

### “Auto” deploy options

**A. SSH after push (simplest)**

```bash
ssh user@server 'cd /home/pradytec/pradytecai && ./deploy.sh'
```

**B. Cron (scheduled pull — use carefully)**

```cron
# example: every night at 3:00
0 3 * * * cd /home/pradytec/pradytecai && ./deploy.sh >> /home/pradytec/logs/deploy.log 2>&1
```

**C. Git webhook / CI**

On push to `main`, CI SSHes into the server and runs `./deploy.sh`. There is no GitHub Actions workflow in this repo yet — add one if you want push-triggered deploys.

**D. Local Windows build, then server deploy**

If the server has no Node:

1. Run `npm run build` (or `build-production.bat`) locally
2. Commit/upload `public/build/` **or** rsync it to the server
3. On server, temporarily skip the npm step or ensure `public/build/manifest.json` already exists before running a slimmed deploy

---

## Useful Artisan commands

| Command | Purpose |
|---------|---------|
| `php artisan marketing:pulse` | Evaluate marketing health rules; upsert alerts |
| `php artisan migrate` | Run migrations |
| `php artisan db:seed` | Seed roles, admin/HR users, products, blog |
| `php artisan queue:work` | Process publish / mail / sync jobs |
| `php artisan schedule:work` | Run scheduler in the foreground (local) |
| `php artisan optimize:clear` | Clear all caches |
| `php artisan test --filter=MarketingAdminAuthTest` | Auth / permission regression tests |

---

## Project docs (extra)

| File | Topic |
|------|--------|
| `DEPLOYMENT_CHECKLIST.md` | Production checklist & common fixes |
| `PRODUCTION_BUILD_INSTRUCTIONS.md` | Why styling breaks without `public/build` |
| `PRODUCTION_UPLOAD_GUIDE.md` / `PRODUCTION_UPLOAD_FIX.md` | Upload / asset sync notes |
| `AUTHENTICATION_SETUP.md` | Login / admin auth notes |
| `COMMUNICATION_SETUP.md` | BulkSMS / UltraMsg setup |

---

## Security notes

- Never commit `.env`, API keys, or production passwords
- Change seeded `admin123` / `hr123` immediately on any shared or production environment
- Keep `APP_DEBUG=false` in production
- Restrict `/admin` via strong passwords + least-privilege roles

---

## License

Proprietary — PradytecAI. Internal use unless otherwise agreed.
