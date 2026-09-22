# Laravel + Django coexistence (dual-run)

Both stacks live in one repo until Laravel retirement. Isolation rules:

## Ports

| Environment | App | Port |
|-------------|-----|------|
| **Production (WHM)** | Django Gunicorn | **8100** |
| Production | Other app on same host | 8000 (taken — do not use) |
| Local Windows | Django `runserver` | **8000** |
| Local | Laravel `artisan serve` (optional) | **8001** |

```powershell
# Local Django
.\scripts\run-django.ps1
# or: python manage.py runserver 8000

# Local Laravel (legacy only)
.\scripts\run-laravel.ps1
```

Never bind production Gunicorn to **8000**. Never run `php artisan serve` on 8000 while local Django is running.

Full host guide: [PRODUCTION_SERVER.md](PRODUCTION_SERVER.md).

## Python / venv

| Environment | Python | Venv folder |
|-------------|--------|-------------|
| Production | `/usr/local/bin/python3.12` only | **`env/`** |
| Local Windows | your Python 3.12+ | **`.venv/`** |

Do **not** use `/usr/bin/python3` (WHM 3.9) for this app. Do not install packages globally as root.

## Database

| App | Local default |
|-----|----------------|
| Django | `db.sqlite3` at repo root (`DJANGO_DB_NAME`) |
| Laravel | `database/database.sqlite` |

Django **refuses** to open Laravel’s sqlite file unless `DJANGO_USE_LARAVEL_SQLITE=true`.

For MySQL cutover, set `DJANGO_DB_HOST` / `DJANGO_DB_NAME` (or Laravel `DB_*` after intentional switch). See [MYSQL_ADOPTION.md](MYSQL_ADOPTION.md).

## Cookies

Django uses:

- `pradytecai_django_session`
- `pradytecai_django_csrftoken`

so Laravel’s session cookie cannot overwrite Django’s when both are tested on localhost.

## Frontend builds

| Command | Builds |
|---------|--------|
| `npm run build` (repo root) | **React** (`react/`) for Django |
| `npm run build:laravel` | Legacy Laravel Vite → `public/build` |

## Config folder

`config/` contains **both** Laravel `*.php` and Django Python (`settings/`, `urls.py`, …). PHP and Python ignore each other’s files. Do not delete Laravel PHP configs until retirement.

## Env

Keep Laravel `APP_KEY` for encrypted integration tokens. Also set:

- `DJANGO_SECRET_KEY`
- `DJANGO_DB_NAME=db.sqlite3` (local) or MySQL keys (production)
- `GUNICORN_BIND=127.0.0.1:8100` (production)

See `.env.example` and [PRODUCTION_SERVER.md](PRODUCTION_SERVER.md).
