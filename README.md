# PradytecAI

Django 5.2 + DRF + React (Vite) marketing site and Marketing Command Centre.

**Authoritative production runtime is Docker Compose.** Full host guide: [docs/PRODUCTION_SERVER.md](docs/PRODUCTION_SERVER.md).

## Architecture

```text
HOST
├── WHM / Apache          (TLS, React public_html, reverse proxy)
├── existing Redis        (host :6379 — not a Compose service)
└── Docker Engine
      │
      └── Compose: pradytecai
             ├── web
             │    └── Gunicorn + Django   → host 127.0.0.1:8100
             ├── worker
             │    └── Celery Worker
             ├── beat
             │    └── Celery Beat (DatabaseScheduler → PostgreSQL)
             └── postgres
                  └── PostgreSQL 17 (volume: pradytecai_postgres_data)
```

Frontend:

```text
Vite build → public_html → Apache
  /assets/*  React hashed bundles
  /static/*  Django collectstatic
```

Backend:

```text
/api/v1 /up /health /t/*  → Apache → 127.0.0.1:8100 → Docker web → Gunicorn → Django
```

> **`pradytec-gunicorn.service` is not used.**
> **`pradytecai-gunicorn.service` is not used.**
> **Gunicorn runs inside the Docker Compose `web` service. PradytecAI does not use a host-level Gunicorn systemd service.**
> Gunicorn is managed exclusively by Docker Compose through the `web` service.

Legacy host systemd unit files (historical only): `legacy/systemd/`.

## Requirements (production)

* WHM / cPanel + Apache
* Git
* Docker Engine + Docker Compose v2
* Existing host Redis
* Document root: `/home/pradytec/pradytecai/public_html`

## First-time production setup

1. Clone/pull the `django` branch to `/home/pradytec/pradytecai`
2. Copy `.env.example` → `.env` and set secrets (`DJANGO_SECRET_KEY`, `POSTGRES_PASSWORD`, …)
3. Set production DB/Redis (PostgreSQL + `REDIS_HOST=host.docker.internal`, Celery DBs e.g. `/2` `/3`)
4. Inspect host Redis (bind, auth, memory, existing DB usage) — do **not** expose Redis on `0.0.0.0` without firewall + auth
5. Configure Apache proxy (see `deploy/apache/pradytecai-proxy.conf.example`)
6. Migrate MySQL → PostgreSQL carefully (see [docs/POSTGRES_MIGRATION.md](docs/POSTGRES_MIGRATION.md)); keep MySQL intact until validated
7. Run `./deploy.sh`
8. Verify: `docker compose ps`, `curl -fsS http://127.0.0.1:8100/up`, public `/up`

## Normal update

```bash
# laptop
git add . && git commit -m "Description" && git push origin django

# server
cd /home/pradytec/pradytecai
./deploy.sh
```

Do not use FTP as the normal release process. Do not restart host Gunicorn/Celery systemd units.

## Daily operations

```bash
docker compose ps
docker compose logs -f
docker compose logs -f web
docker compose logs -f worker
docker compose logs -f beat
docker compose logs -f postgres
docker compose restart web
docker compose restart worker
docker compose exec web python manage.py shell
docker compose exec web python manage.py showmigrations
docker compose exec web python manage.py check
docker stats
```

After a normal host reboot, Docker restart policies bring `postgres` → `web` / `worker` / `beat` back. You do **not** manually start Gunicorn, Celery, or PostgreSQL.

## Resource monitoring

Use `docker stats` and adjust `.env` limits (`DOCKER_WEB_MEMORY`, etc.), then `docker compose up -d`. Do **not** remove limits as the default fix — this is a shared WHM host.

## Backups

```bash
./scripts/postgres-backup.sh
# restore to staging first:
./scripts/postgres-restore.sh backups/pradytecai-….dump pradytecai_restore_test
```

## Production warnings

Never casually run:

* `python manage.py runserver` (production)
* `npm run dev` (production)
* `docker compose down -v` — **destroys the PostgreSQL volume**
* `docker system prune --volumes` — can delete `postgres_data`

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

Local port **8000** is fine on your laptop. Production publishes **127.0.0.1:8100** only.

## API / auth

* `/api/v1/…` session + CSRF (`VITE_API_BASE_URL=/api/v1`)
* `/login` → `/admin`
* Health: `/up`
* Laravel bcrypt hashes still verify where adopted

## Docs

* [docs/PRODUCTION_SERVER.md](docs/PRODUCTION_SERVER.md)
* [docs/POSTGRES_MIGRATION.md](docs/POSTGRES_MIGRATION.md)
* [docs/MYSQL_ADOPTION.md](docs/MYSQL_ADOPTION.md) (historical MySQL adoption)
* [docs/CUTOVER.md](docs/CUTOVER.md)
* [docs/COEXISTENCE.md](docs/COEXISTENCE.md)
* [docs/LARAVEL_RETIREMENT.md](docs/LARAVEL_RETIREMENT.md)
