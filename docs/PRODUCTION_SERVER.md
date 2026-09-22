# Production Server — Django Environment & Deployment

**Project root:** `/home/pradytec/pradytecai`  
**Gunicorn port:** **8100** (port 8000 is used by another app on this server)  
**WSGI module:** `config.wsgi:application`

---

## 1. Server environment overview

| Item | Value |
|------|--------|
| Project root | `/home/pradytec/pradytecai` |
| Assigned port | **8100** |
| System Python (WHM) | `/usr/bin/python3` → **3.9.25** |
| App Python | `/usr/local/bin/python3.12` → **3.12.0** |
| Production venv | `/home/pradytec/pradytecai/env` |

### Critical system notice

Do **not** modify, re-link, or uninstall `/usr/bin/python3` or `/usr/bin/python`.  
WHM/cPanel depends on system Python 3.9.

Always use **Python 3.12** only inside the project virtualenv:

```bash
/usr/local/bin/python3.12 -m venv env
```

Do **not** `pip install` packages globally as root.

---

## 2. Django / Python compatibility

Django 5.x requires Python ≥ 3.10. This app runs in an isolated **Python 3.12** venv named `env`.

---

## 3. First-time setup checklist

Run from `/home/pradytec/pradytecai`:

### Step 1 — Create and activate venv

```bash
cd /home/pradytec/pradytecai
/usr/local/bin/python3.12 -m venv env
source env/bin/activate
```

### Step 2 — Install dependencies

```bash
pip install --upgrade pip
pip install -r requirements.txt
```

### Step 3 — Env, migrate, static

```bash
cp -n .env.example .env   # then edit production secrets / MySQL / hosts
python manage.py migrate --noinput
python manage.py collectstatic --noinput
# optional first admin (if MySQL users not yet adopted):
# python manage.py ensure_admin --email admin@pradytecai.com --password '…'
# python manage.py seed_marketing
```

### Step 4 — Build React

```bash
cd react && npm ci && npm run build && cd ..
python manage.py collectstatic --noinput
```

### Step 5 — Launch Gunicorn on **8100**

**Behind Apache/Nginx proxy (recommended):**

```bash
/home/pradytec/pradytecai/env/bin/gunicorn \
  --config /home/pradytec/pradytecai/gunicorn.conf.py \
  config.wsgi:application
```

Default bind in `gunicorn.conf.py` / `.env`:

```env
GUNICORN_BIND=127.0.0.1:8100
```

**Direct browser access via IP:8100** (only if CSF allows):

```bash
GUNICORN_BIND=0.0.0.0:8100 /home/pradytec/pradytecai/env/bin/gunicorn \
  --config gunicorn.conf.py \
  config.wsgi:application
```

Or:

```bash
/home/pradytec/pradytecai/env/bin/gunicorn --bind 0.0.0.0:8100 config.wsgi:application
```

---

## 4. CSF firewall (direct IP:8100 only)

If accessing `http://YOUR_SERVER_IP:8100` without a reverse proxy:

```bash
csf -a 8100
csf -r
```

If Apache/Nginx proxies HTTPS → `127.0.0.1:8100`, you usually **do not** need to open 8100 publicly.

---

## 5. Verify

```bash
ss -tulpn | grep :8100
curl -fsS http://127.0.0.1:8100/up
```

Expect a listener on `127.0.0.1:8100` or `0.0.0.0:8100`, and JSON `{"status":"ok",...}` from `/up`.

---

## 6. systemd units

Install from `deploy/`:

- `pradytec-gunicorn.service` → Gunicorn on **8100** using `env/bin/gunicorn`
- `pradytec-celery-worker.service`
- `pradytec-celery-beat.service`

```bash
sudo cp deploy/pradytec-*.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now pradytec-gunicorn pradytec-celery-worker pradytec-celery-beat
```

Deploy helper: `./deploy.sh` (uses `env` + Python 3.12 on this host).

---

## 7. Production `.env` essentials

```env
DEBUG=false
ALLOWED_HOSTS=pradytecai.com,www.pradytecai.com,YOUR_SERVER_IP
CSRF_TRUSTED_ORIGINS=https://pradytecai.com,https://www.pradytecai.com
SESSION_SECURE_COOKIE=true
GUNICORN_BIND=127.0.0.1:8100
GUNICORN_WORKERS=2
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
CELERY_BROKER_URL=redis://127.0.0.1:6379/0
CELERY_WORKER_CONCURRENCY=1
```

MySQL: set `DJANGO_DB_HOST` / `DB_*` and adopt existing tables with `--fake-initial` on a clone first (see `docs/MYSQL_ADOPTION.md`).

---

## 8. Local Windows vs production Linux

| | Local (dev) | Production (this server) |
|--|-------------|---------------------------|
| Python | whatever `.venv` was created with | **`/usr/local/bin/python3.12` only** |
| Venv folder | `.venv` | **`env`** |
| HTTP | `runserver` :8000 | **Gunicorn :8100** |
| Never touch | — | **`/usr/bin/python3` (3.9 / WHM)** |
