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
* Docker Engine + Docker Compose v2 (`docker compose`)
* Existing host Redis
* Document root: `/home/pradytec/pradytecai/public_html`

---

## First-time production setup

Do this **once** on a new WHM host (or when moving from the old host systemd/Gunicorn stack to Docker).

### 1. Install Docker Engine + Compose v2

Run as root (or with `sudo`). Prefer the official Docker packages — **not** the outdated `docker` package from some OS defaults.

**AlmaLinux / Rocky / RHEL / CloudLinux (typical WHM):**

```bash
# Remove conflicting packages if present
sudo dnf remove -y docker docker-client docker-client-latest docker-common \
  docker-latest docker-latest-logrotate docker-logrotate docker-engine \
  podman runc 2>/dev/null || true

sudo dnf -y install dnf-plugins-core
sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

sudo dnf -y install docker-ce docker-ce-cli containerd.io \
  docker-buildx-plugin docker-compose-plugin

sudo systemctl enable --now docker
sudo usermod -aG docker pradytec   # log out/in (or newgrp docker) afterward
```

**Ubuntu / Debian:**

```bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
  -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Adjust "ubuntu" → "debian" and the codename if needed
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] \
https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
  | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io \
  docker-buildx-plugin docker-compose-plugin

sudo systemctl enable --now docker
sudo usermod -aG docker "$USER"
```

**Verify:**

```bash
docker --version
docker compose version
docker info
sudo systemctl status docker --no-pager
```

You need Compose **v2** (`docker compose …`), not the old Python `docker-compose` v1 binary.

### 2. Clone the repository

```bash
# Example production path
sudo mkdir -p /home/pradytec/pradytecai
sudo chown -R pradytec:pradytec /home/pradytec/pradytecai
su - pradytec
cd /home/pradytec
git clone -b django https://github.com/mato002/pradytecai.git pradytecai
cd /home/pradytec/pradytecai
```

If the tree already exists:

```bash
cd /home/pradytec/pradytecai
git fetch origin
git checkout django
git pull origin django
```

### 3. Configure `.env`

```bash
cd /home/pradytec/pradytecai
cp -n .env.example .env
# Or use your prepared production file:
# cp -n production.env .env
chmod 600 .env
nano .env   # or: vi .env
```

Minimum production values:

```env
COMPOSE_PROJECT_NAME=pradytecai
DEBUG=false
ALLOWED_HOSTS=www.pradytecai.com,pradytecai.com,127.0.0.1
CSRF_TRUSTED_ORIGINS=https://www.pradytecai.com,https://pradytecai.com
SESSION_SECURE_COOKIE=true

DJANGO_SECRET_KEY=…long-random…
POSTGRES_DB=pradytecai
POSTGRES_USER=pradytecai
POSTGRES_PASSWORD=…strong…
DJANGO_DB_ENGINE=postgresql
DJANGO_DB_NAME=pradytecai
DJANGO_DB_USER=pradytecai
DJANGO_DB_PASSWORD=…same-as-POSTGRES_PASSWORD…
DJANGO_DB_HOST=postgres
DJANGO_DB_PORT=5432

REDIS_HOST=host.docker.internal
REDIS_PORT=6379
CELERY_BROKER_URL=redis://host.docker.internal:6379/2
CELERY_RESULT_BACKEND=redis://host.docker.internal:6379/3

GUNICORN_BIND=0.0.0.0:8000
GUNICORN_WORKERS=2
VITE_API_BASE_URL=/api/v1
```

Keep legacy MySQL credentials in `.env` only for migration/rollback until PostgreSQL cutover is accepted. Do **not** delete MySQL yet. See [docs/POSTGRES_MIGRATION.md](docs/POSTGRES_MIGRATION.md).

### 4. Inspect host Redis (required)

```bash
ss -ltnp | grep 6379
redis-cli ping
redis-cli INFO keyspace
```

Confirm logical DBs **2** and **3** are free (or pick unused numbers). Do **not** bind Redis to `0.0.0.0` without firewall + auth.

If Redis only listens on `127.0.0.1`, Docker cannot reach it via `host.docker.internal` until you allow the docker bridge safely (see [docs/PRODUCTION_SERVER.md](docs/PRODUCTION_SERVER.md)). Fix that **before** the first successful deploy.

### 5. Disable obsolete host systemd app units (if installed)

```bash
sudo systemctl disable --now pradytec-gunicorn pradytec-celery-worker pradytec-celery-beat 2>/dev/null || true
sudo systemctl disable --now pradytecai-gunicorn 2>/dev/null || true
```

Gunicorn/Celery/Postgres for this app are Compose-only going forward.

### 6. Configure Apache proxy

Apply rules from `deploy/apache/pradytecai-proxy.conf.example` (WHM Include Editor / VirtualHost):

* DocumentRoot → `/home/pradytec/pradytecai/public_html`
* Proxy `/api/v1/*`, `/up`, `/health`, `/t/*` → `http://127.0.0.1:8100`
* Serve `/`, `/assets/*`, `/static/*`, `/media/*` from disk (not Gunicorn)

Reload Apache after changes (`/scripts/rebuildhttpdconf` + restart via WHM, or your usual reload).

### 7. Script permissions + first deploy

```bash
cd /home/pradytec/pradytecai
chmod +x deploy.sh docker/entrypoint.sh \
  scripts/postgres-backup.sh scripts/postgres-restore.sh

./deploy.sh
```

`deploy.sh` will: verify Docker/Compose/`.env`/Redis → `git pull` → build `pradytecai-app` → ephemeral React build → start Postgres → migrate → collectstatic → safe `public_html` sync → `docker compose up -d` → health checks.

### 8. Verify

```bash
docker compose ps
curl -fsS http://127.0.0.1:8100/up
curl -fsS https://pradytecai.com/up
docker compose logs --tail=50 web
docker compose exec web python manage.py showmigrations
```

Expected: `web` / `worker` / `beat` running, `postgres` healthy, `/up` returns JSON `{"status":"ok",…}`.

Optional after first migrate:

```bash
docker compose exec web python manage.py seed_products
docker compose exec web python manage.py ensure_admin \
  --email admin@pradytecai.com --password 'ChooseAStrongPassword'
```

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

## Product catalog & enquiries

The **database** is the single source of truth for products. Admin CRUD updates Django → public React pages read the same catalog via `/api/v1/public/products/`.

### Product initialization

After migrations (also run automatically by `./deploy.sh`):

```bash
# local
python manage.py seed_products

# production (Docker)
docker compose exec web python manage.py seed_products
```

Idempotent: creates the 9 default Pradytec products by **stable slug** only. Re-running does **not** duplicate rows or overwrite admin edits.

### Product management

* Admin UI: `/admin/products` (requires `products.view` / `products.manage`)
* API: `/api/v1/products/` (authenticated) — create, update, delete, poster upload
* Public list: `/api/v1/public/products/` (active only, ordered)
* Public detail: `/api/v1/public/products/<slug>/` and React route `/products/<slug>`

### Product images

* Stored under `MEDIA_ROOT/products/posters/` (repo `./media`, Docker volume `./media:/app/media`)
* Served at `/media/…` (Apache from `public_html/media`, preferably symlinked to `./media`; Django also serves `/media/` as fallback)
* JPEG / PNG / WebP / GIF, max 5 MB
* Public cards use `poster_url` from the API (icon fallback when missing)

### Enquiries

* Public forms (`/contact`, product detail CTAs) POST `/api/v1/public/contact/`
* Product-originated requests attach `product` (+ optional `DemoRequest` / preferred date)
* Admin UI: `/admin/enquiries` — view details and update status (`new`, `contacted`, `in_progress`, `completed`, `closed`)

### Deployment

```bash
./deploy.sh   # migrate → seed_products → collectstatic → React → restart
```

Safe to re-run. Manual seed after a partial deploy: `docker compose exec web python manage.py seed_products`

## Docs

* [docs/PRODUCTION_SERVER.md](docs/PRODUCTION_SERVER.md)
* [docs/POSTGRES_MIGRATION.md](docs/POSTGRES_MIGRATION.md)
* [docs/MYSQL_ADOPTION.md](docs/MYSQL_ADOPTION.md) (historical MySQL adoption)
* [docs/CUTOVER.md](docs/CUTOVER.md)
* [docs/COEXISTENCE.md](docs/COEXISTENCE.md)
* [docs/LARAVEL_RETIREMENT.md](docs/LARAVEL_RETIREMENT.md)
