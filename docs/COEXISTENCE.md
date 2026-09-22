# Local / production notes (post-Laravel)

Laravel has been removed from this repo. Django + React is the only app stack.

## Ports

| Environment | App | Port |
|-------------|-----|------|
| **Production (WHM + Docker)** | Compose `web` (Gunicorn) published as | **127.0.0.1:8100** |
| Production | Other app on same host | **8000** (taken — do not use) |
| Local Windows | Django `runserver` | **8000** |

```powershell
.\scripts\run-django.ps1
# or: python manage.py runserver 8000
```

Production Docker: container binds `0.0.0.0:8100`; host maps only `127.0.0.1:8100:8100`.

Full host guide: [PRODUCTION_SERVER.md](PRODUCTION_SERVER.md).

## Python / runtime

| Environment | Runtime |
|-------------|---------|
| Production | Docker image `pradytecai-app` (Python 3.12) |
| Local Windows | `.venv/` with Python 3.12+ |

Do **not** use `/usr/bin/python3` (WHM 3.9) for this app. Host `env/` venv is obsolete under Docker production.

## Database

| Environment | Default |
|-------------|---------|
| Local | `db.sqlite3` at repo root |
| Production (target) | PostgreSQL in Compose (`postgres` service) |
| Production (legacy until cutover) | MySQL `pradytec_prady` |

See [POSTGRES_MIGRATION.md](POSTGRES_MIGRATION.md) and [MYSQL_ADOPTION.md](MYSQL_ADOPTION.md).

## Cookies

Django uses isolated cookie names (`SESSION_COOKIE_NAME`, `CSRF_COOKIE_NAME`) so they do not collide with any leftover PHP session cookies.

## Env keys

- `DJANGO_SECRET_KEY`, `DEBUG`, `ALLOWED_HOSTS`, `CSRF_TRUSTED_ORIGINS`
- `DJANGO_DB_ENGINE` / `DJANGO_DB_*` (sqlite locally; postgresql in Docker)
- Redis / Celery URLs (`REDIS_HOST=host.docker.internal` in Docker)
- `DOCKER_*` resource limits
- Keep `APP_KEY` while Laravel-encrypted Integration tokens matter

## Frontend

Production: Vite `base: "/"` → `public_html` (hashed `/assets/*`). Django `collectstatic` → `public_html/static/`.
