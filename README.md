# PradytecAI

Django + DRF + React (Vite) marketing site and Marketing Command Centre.

Full production host guide: **[docs/PRODUCTION_SERVER.md](docs/PRODUCTION_SERVER.md)**

## Architecture

```text
Browser → HTTPS → Apache/Nginx → Gunicorn 127.0.0.1:8100 → config.wsgi
                              → /static /media
Django → Redis :6379 → Celery Worker + Beat
React: /react → Vite build → collectstatic
```

## Production server (WHM)

| Item | Value |
|------|--------|
| Root | `/home/pradytec/pradytecai` |
| Port | **8100** (8000 taken by another app) |
| App Python | `/usr/local/bin/python3.12` → venv **`env/`** |
| System Python | `/usr/bin/python3` (3.9) — **do not touch** |
| WSGI | `config.wsgi:application` |

```bash
cd /home/pradytec/pradytecai
/usr/local/bin/python3.12 -m venv env
source env/bin/activate
pip install -U pip && pip install -r requirements.txt
# configure .env (GUNICORN_BIND=127.0.0.1:8100, MySQL, secrets)
python manage.py migrate --noinput
(cd react && npm ci && npm run build)
python manage.py collectstatic --noinput
./scripts/run-gunicorn-prod.sh
# or: systemctl start pradytec-gunicorn pradytec-celery-worker pradytec-celery-beat
ss -tulpn | grep :8100
curl -fsS http://127.0.0.1:8100/up
```

Deploy: `./deploy.sh` · Units: `deploy/pradytec-*.service`

## Local Windows (dev)

```powershell
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
copy .env.example .env
python manage.py migrate
python manage.py ensure_admin --email admin@pradytecai.com --password "YourPassword"
cd react; npm ci; npm run build; cd ..
python manage.py runserver 8000
```

Local port **8000** is fine on your laptop. Production must use **8100**.

## Local notes

See [docs/COEXISTENCE.md](docs/COEXISTENCE.md). Root `npm run build` → React (`react/`).

Laravel sources have been removed; see [docs/LARAVEL_RETIREMENT.md](docs/LARAVEL_RETIREMENT.md).

## API / auth

- `/api/v1/…` session + CSRF
- `/login` → `/admin`
- Health: `/up`
- Existing Laravel bcrypt password hashes still verify (`ensure_admin` only for fresh/local users)

## Celery / Redis

Redis default **6379**. Worker concurrency defaults to **1** — see `.env.example` Celery limits.

## Docs

- [docs/PRODUCTION_SERVER.md](docs/PRODUCTION_SERVER.md)
- [docs/MYSQL_ADOPTION.md](docs/MYSQL_ADOPTION.md)
- [docs/CUTOVER.md](docs/CUTOVER.md)
- [docs/COEXISTENCE.md](docs/COEXISTENCE.md)
- [docs/LARAVEL_RETIREMENT.md](docs/LARAVEL_RETIREMENT.md)
