# Production Server — Docker Compose + PostgreSQL

**Project root:** `/home/pradytec/pradytecai`  
**Compose project:** `pradytecai`  
**Published backend:** `127.0.0.1:8100` → container Gunicorn `:8100`  
**WSGI:** `config.wsgi:application` inside Docker service `web`

---

## Authoritative architecture

```text
HOST
├── WHM / Apache
├── existing Redis (:6379)
└── Docker Engine
      └── Compose: pradytecai
             ├── web      → Gunicorn + Django
             ├── worker   → Celery Worker
             ├── beat     → Celery Beat
             └── postgres → PostgreSQL 17 (named volume)
```

React is a **build artifact** deployed to `public_html` (no permanent Node process).

> **`pradytec-gunicorn.service` is not used.**  
> **`pradytecai-gunicorn.service` is not used.**  
> Gunicorn is managed exclusively by Docker Compose through the `web` service.

Historical unit files live under `legacy/systemd/` and must **not** be installed for Docker production.

---

## Requirements

| Item | Notes |
|------|--------|
| WHM / Apache | TLS + static + proxy |
| Docker Engine | required |
| Docker Compose v2 | `docker compose` |
| Host Redis | reuse existing instance |
| Git | deploy from `django` branch |
| Document root | `/home/pradytec/pradytecai/public_html` |

Python 3.12 runs **inside** the application image. You do **not** need the host `env/` venv for Docker production.

---

## First-time setup

```bash
cd /home/pradytec/pradytecai
git pull origin django
cp -n .env.example .env
# Edit .env: DEBUG=false, secrets, PostgreSQL, Redis host.docker.internal, Celery URLs
```

### Production `.env` essentials

```env
COMPOSE_PROJECT_NAME=pradytecai
DEBUG=false
ALLOWED_HOSTS=www.pradytecai.com,pradytecai.com,127.0.0.1
CSRF_TRUSTED_ORIGINS=https://www.pradytecai.com,https://pradytecai.com
SESSION_SECURE_COOKIE=true

POSTGRES_DB=pradytecai
POSTGRES_USER=pradytecai
POSTGRES_PASSWORD=…strong…

DJANGO_DB_ENGINE=postgresql
DJANGO_DB_NAME=pradytecai
DJANGO_DB_USER=pradytecai
DJANGO_DB_PASSWORD=…same…
DJANGO_DB_HOST=postgres
DJANGO_DB_PORT=5432

REDIS_HOST=host.docker.internal
REDIS_PORT=6379
CELERY_BROKER_URL=redis://host.docker.internal:6379/2
CELERY_RESULT_BACKEND=redis://host.docker.internal:6379/3

GUNICORN_BIND=0.0.0.0:8100
GUNICORN_WORKERS=2
VITE_API_BASE_URL=/api/v1
```

Inspect Redis DB usage before choosing `/2` and `/3`. Keep MySQL intact until PostgreSQL migration is verified ([POSTGRES_MIGRATION.md](POSTGRES_MIGRATION.md)).

### Host Redis ↔ Docker

Compose uses `extra_hosts: host.docker.internal:host-gateway`.

If Redis only listens on `127.0.0.1`, containers **cannot** reach it via `host.docker.internal` (that address is the docker bridge IP, not loopback).

Safe options (pick one; do **not** bind Redis to `0.0.0.0` without firewall + auth):

1. Add the docker bridge IP to Redis `bind` (keep `protected-mode yes` / require a password)
2. Or a host firewall DNAT from the bridge IP:6379 → 127.0.0.1:6379

Verify before cutover:

```bash
ss -ltnp | grep 6379
redis-cli ping
docker compose run --rm --no-deps web python -c "import redis,os; redis.Redis.from_url(os.environ['CELERY_BROKER_URL']).ping()"
```

If this fails, **stop** and fix Redis reachability — do not report a successful deploy while worker/beat crash-loop.

### Apache

Apply proxy rules from `deploy/apache/pradytecai-proxy.conf.example`:

* Serve `/`, `/assets/*`, `/static/*`, `/media/*` from `public_html`
* Proxy `/api/v1/*`, `/up`, `/health`, `/t/*` → `http://127.0.0.1:8100`

Do **not** open port 8100 publicly if Apache terminates TLS.

### Start

```bash
chmod +x deploy.sh scripts/postgres-backup.sh scripts/postgres-restore.sh docker/entrypoint.sh
./deploy.sh
```

---

## Normal updates

```bash
cd /home/pradytec/pradytecai
./deploy.sh
```

`deploy.sh` builds the app image, builds React in ephemeral Node, migrates, collectstatic, safely syncs `public_html`, and runs `docker compose up -d --remove-orphans`.

It does **not** run `systemctl` for Gunicorn or Celery.

---

## Operations

```bash
docker compose ps
docker compose logs -f web
docker compose logs -f worker
docker compose logs -f beat
docker compose logs -f postgres
docker compose restart web
docker compose exec web python manage.py shell
docker stats
```

### Backups

```bash
./scripts/postgres-backup.sh
# NEVER casually: docker compose down -v
```

### Reboot behaviour

Docker `restart: unless-stopped` + postgres healthchecks bring the stack back after reboot. No manual Gunicorn/Celery/Postgres start.

---

## Resource limits

Defaults (override in `.env`):

| Service | CPUs | Memory |
|---------|------|--------|
| web | 0.75 | 512m |
| worker | 0.50 | 384m |
| beat | 0.20 | 192m |
| postgres | 1.00 | 768m |

Tune from `docker stats`; keep headroom for WHM, Apache, host Redis, and other sites.

---

## Warnings

* `docker compose down -v` destroys `pradytecai_postgres_data`
* Do not install `legacy/systemd/*.service` on Docker production
* Do not add Redis/Nginx/Traefik Compose services by default
* Do not delete MySQL until PostgreSQL cutover is formally accepted
