# Local / production notes (post-Laravel)

Laravel has been removed from this repo. Django + React is the only app stack.

## Ports

| Environment | App | Port |
|-------------|-----|------|
| **Production (WHM)** | Django Gunicorn | **8100** |
| Production | Other app on same host | 8000 (taken — do not use) |
| Local Windows | Django `runserver` | **8000** |

```powershell
.\scripts\run-django.ps1
# or: python manage.py runserver 8000
```

Never bind production Gunicorn to **8000**.

Full host guide: [PRODUCTION_SERVER.md](PRODUCTION_SERVER.md).

## Python / venv

| Environment | Python | Venv folder |
|-------------|--------|-------------|
| Production | `/usr/local/bin/python3.12` only | **`env/`** |
| Local Windows | your Python 3.12+ | **`.venv/`** |

Do **not** use `/usr/bin/python3` (WHM 3.9) for this app.

## Database

| Environment | Default |
|-------------|---------|
| Local | `db.sqlite3` at repo root (`DJANGO_DB_NAME`) |
| Production | MySQL `pradytec_prady` (existing schema) |

See [MYSQL_ADOPTION.md](MYSQL_ADOPTION.md).

## Cookies

- `pradytecai_django_session`
- `pradytecai_django_csrftoken`

## Frontend

| Command | Builds |
|---------|--------|
| `npm run build` (repo root) | React (`react/`) |
| `cd react && npm run build` | Same |

## Env

Keep legacy `APP_KEY` if you still decrypt Integration tokens encrypted by Laravel. Also set:

- `DJANGO_SECRET_KEY`
- `DJANGO_DB_NAME=db.sqlite3` (local) or MySQL keys (production)
- `GUNICORN_BIND=127.0.0.1:8100` (production)

See `.env.example` and [PRODUCTION_SERVER.md](PRODUCTION_SERVER.md).
