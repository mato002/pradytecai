#!/usr/bin/env bash
# PradytecAI production deploy — Docker Compose (web / worker / beat / postgres)
#
# Gunicorn runs ONLY inside the Compose `web` service.
# Do NOT systemctl restart pradytec-gunicorn / pradytecai-gunicorn (obsolete).
#
# Usage (production):
#   cd /home/pradytec/pradytecai && ./deploy.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

DEPLOY_BRANCH="${DEPLOY_BRANCH:-django}"
PUBLIC_HTML="${PUBLIC_HTML:-/home/pradytec/pradytecai/public_html}"
COMPOSE="${COMPOSE:-docker compose}"
NODE_IMAGE="${NODE_IMAGE:-node:24-alpine}"
PUBLIC_HEALTH_URL="${PUBLIC_HEALTH_URL:-https://pradytecai.com/up}"
IMAGE_TAG="${IMAGE_TAG:-latest}"

log() { echo "[deploy] $*"; }
fail() { echo "[deploy] ERROR: $*" >&2; exit 1; }

require_cmd() {
  command -v "$1" >/dev/null 2>&1 || fail "'$1' not found"
}

# ---------------------------------------------------------------------------
# 1–3. Prerequisites
# ---------------------------------------------------------------------------
require_cmd git
require_cmd docker
require_cmd curl
require_cmd rsync

docker info >/dev/null 2>&1 || fail "Docker Engine is not running or not accessible"
$COMPOSE version >/dev/null 2>&1 || fail "Docker Compose v2 not available (try: docker compose version)"

[[ -f .env ]] || fail ".env missing — copy from .env.example and configure production secrets"
[[ -f compose.yaml ]] || fail "compose.yaml missing"
[[ -f Dockerfile ]] || fail "Dockerfile missing"

# shellcheck disable=SC1091
set -a
source .env
set +a

: "${POSTGRES_PASSWORD:?POSTGRES_PASSWORD must be set in .env}"
: "${DJANGO_SECRET_KEY:?DJANGO_SECRET_KEY must be set in .env}"

export COMPOSE_PROJECT_NAME="${COMPOSE_PROJECT_NAME:-pradytecai}"
export IMAGE_TAG

REDIS_HOST_CHECK="${REDIS_HOST:-host.docker.internal}"
REDIS_PORT_CHECK="${REDIS_PORT:-6379}"
BROKER_URL_CHECK="${CELERY_BROKER_URL:-redis://${REDIS_HOST_CHECK}:${REDIS_PORT_CHECK}/2}"

# ---------------------------------------------------------------------------
# 4a. Host Redis preflight (best-effort on the host before build)
# ---------------------------------------------------------------------------
log "Preflight: host Redis (redis-cli if available)"
if command -v redis-cli >/dev/null 2>&1; then
  # Prefer loopback from the host; Docker uses host.docker.internal later.
  if ! redis-cli -h 127.0.0.1 -p "${REDIS_PORT_CHECK}" ping 2>/dev/null | grep -qi pong; then
    fail "Host redis-cli could not PING 127.0.0.1:${REDIS_PORT_CHECK}"
  fi
  log "redis-cli PING ok on 127.0.0.1:${REDIS_PORT_CHECK}"
else
  log "redis-cli not installed on host — will verify from Docker after image build"
fi

# ---------------------------------------------------------------------------
# 5–6. Git pull + record previous revision
# ---------------------------------------------------------------------------
OLD_REV="$(git rev-parse --short HEAD)"
log "Current revision: ${OLD_REV}"
log "Pulling origin/${DEPLOY_BRANCH}"
git pull origin "$DEPLOY_BRANCH"
NEW_REV="$(git rev-parse --short HEAD)"
log "New revision: ${NEW_REV} (was ${OLD_REV})"

# ---------------------------------------------------------------------------
# 7. Build application image (shared by web / worker / beat)
# ---------------------------------------------------------------------------
log "Building application image pradytecai-app:${IMAGE_TAG}"
$COMPOSE build web

# ---------------------------------------------------------------------------
# 4b. Redis from Docker (required — worker/beat use host Redis)
# ---------------------------------------------------------------------------
log "Verifying host Redis reachability from application image"
$COMPOSE run --rm --no-deps \
  -e CELERY_BROKER_URL="$BROKER_URL_CHECK" \
  -e REDIS_HOST="$REDIS_HOST_CHECK" \
  -e REDIS_PORT="$REDIS_PORT_CHECK" \
  --entrypoint python \
  web -c "import os,sys,redis; url=os.environ.get('CELERY_BROKER_URL');
r=redis.Redis.from_url(url, socket_connect_timeout=5);
r.ping();
safe=url.split('@')[-1] if '@' in url else url;
print('redis ok ('+safe+')')" \
  || fail "Host Redis unreachable from Docker (worker/beat will crash). Fix Redis bind/ACL/firewall before deploy. Do not expose Redis on 0.0.0.0 without firewall + auth."

# ---------------------------------------------------------------------------
# 8–9. Ephemeral React build (Node does not stay running)
# ---------------------------------------------------------------------------
log "Building React with ephemeral ${NODE_IMAGE}"
mkdir -p react/dist
docker run --rm \
  -v "$ROOT/react:/app" \
  -w /app \
  -e VITE_API_BASE_URL="${VITE_API_BASE_URL:-/api/v1}" \
  "$NODE_IMAGE" \
  sh -c "npm ci && npm run build"

[[ -f react/dist/index.html ]] || fail "React build missing dist/index.html"
[[ -d react/dist/assets ]] || fail "React build missing dist/assets/"
ls react/dist/assets/*.js >/dev/null 2>&1 || fail "React build missing JS under dist/assets/"
log "React build verified"

# ---------------------------------------------------------------------------
# 10. Ensure PostgreSQL healthy
# ---------------------------------------------------------------------------
log "Starting postgres (if needed) and waiting for healthy"
$COMPOSE up -d postgres
for i in $(seq 1 36); do
  if $COMPOSE exec -T postgres pg_isready -U "${POSTGRES_USER:-pradytecai}" -d "${POSTGRES_DB:-pradytecai}" >/dev/null 2>&1; then
    log "postgres healthy"
    break
  fi
  if [[ "$i" -eq 36 ]]; then
    fail "postgres did not become ready"
  fi
  sleep 2
done

# ---------------------------------------------------------------------------
# 11–13. Django checks, migrations, collectstatic
# ---------------------------------------------------------------------------
log "Django production checks"
$COMPOSE run --rm --no-deps web python manage.py check --deploy \
  || $COMPOSE run --rm --no-deps web python manage.py check

log "Running migrations"
$COMPOSE run --rm --no-deps web python manage.py migrate --noinput

log "collectstatic → ./staticfiles"
mkdir -p staticfiles media logs backups
$COMPOSE run --rm --no-deps web python manage.py collectstatic --noinput

# ---------------------------------------------------------------------------
# 14. Safe React → public_html (never rsync --delete the whole document root)
# ---------------------------------------------------------------------------
if [[ -d "$PUBLIC_HTML" ]]; then
  log "Deploying React to ${PUBLIC_HTML} (preserving .well-known, static, media)"
  mkdir -p "$PUBLIC_HTML/assets" "$PUBLIC_HTML/static" "$PUBLIC_HTML/media"

  rsync -a react/dist/assets/ "$PUBLIC_HTML/assets/"

  while IFS= read -r -d '' f; do
    rel="${f#react/dist/}"
    case "$rel" in
      index.html|assets|assets/*) continue ;;
      .well-known|.well-known/*|static|static/*|media|media/*) continue ;;
    esac
    dest="$PUBLIC_HTML/$rel"
    mkdir -p "$(dirname "$dest")"
    cp -a "$f" "$dest"
  done < <(find react/dist -type f -print0)

  cp -a react/dist/index.html "$PUBLIC_HTML/index.html.new"
  mv -f "$PUBLIC_HTML/index.html.new" "$PUBLIC_HTML/index.html"
  log "React index.html replaced atomically"
else
  log "WARN: PUBLIC_HTML=$PUBLIC_HTML missing — skipped frontend copy"
fi

# ---------------------------------------------------------------------------
# 15. Django static → public_html/static
# ---------------------------------------------------------------------------
if [[ -d "$PUBLIC_HTML" && -d staticfiles ]]; then
  mkdir -p "$PUBLIC_HTML/static"
  rsync -a --delete staticfiles/ "$PUBLIC_HTML/static/" \
    || cp -a staticfiles/. "$PUBLIC_HTML/static/"
  log "Django static synced to ${PUBLIC_HTML}/static/"
fi

# ---------------------------------------------------------------------------
# 16–17. Bring stack up (preserve postgres volume; no `down -v`)
# ---------------------------------------------------------------------------
log "docker compose up -d --remove-orphans"
$COMPOSE up -d --remove-orphans

log "Waiting for services"
sleep 8

# ---------------------------------------------------------------------------
# 18–22. Verify containers + Redis from worker + /up
# ---------------------------------------------------------------------------
log "Compose status"
$COMPOSE ps

running="$($COMPOSE ps --status running --services | tr '\n' ' ')"
echo "$running" | grep -qw web || fail "web service is not running"
echo "$running" | grep -qw worker || fail "worker service is not running"
echo "$running" | grep -qw beat || fail "beat service is not running"
echo "$running" | grep -qw postgres || fail "postgres service is not running"

log "Verifying Redis from worker container"
$COMPOSE exec -T worker python - <<'PY' || fail "worker cannot reach host Redis"
import os
import django
os.environ.setdefault("DJANGO_SETTINGS_MODULE", "config.settings")
django.setup()
import sys
import redis
from django.conf import settings
url = settings.CELERY_BROKER_URL
try:
    redis.Redis.from_url(url, socket_connect_timeout=5).ping()
except Exception as exc:
    print(f"redis ping failed: {exc}", file=sys.stderr)
    sys.exit(1)
safe = url.split("@")[-1] if "@" in url else url
print(f"worker redis ok ({safe})")
PY

log "Celery worker ping"
$COMPOSE exec -T worker celery -A config inspect ping -t 10 \
  || fail "Celery worker did not respond to inspect ping"

log "Internal health http://127.0.0.1:8100/up"
curl -fsS "http://127.0.0.1:8100/up" >/dev/null \
  || fail "internal /up failed"

log "Public health ${PUBLIC_HEALTH_URL}"
curl -fsS "$PUBLIC_HEALTH_URL" >/dev/null \
  || log "WARN: public /up not reachable yet (check Apache proxy / DNS)"

cat <<EOF

[deploy] SUCCESS
  project:     ${COMPOSE_PROJECT_NAME}
  revision:    ${OLD_REV} → ${NEW_REV}
  image:       pradytecai-app:${IMAGE_TAG}
  services:    web worker beat postgres
  gunicorn:    inside Docker service \`web\` (no host systemd)
  listen:      127.0.0.1:8100 → container :8000
  frontend:    ${PUBLIC_HTML}
  rollback:    git checkout ${OLD_REV} && ./deploy.sh
  NEVER:       docker compose down -v   # destroys postgres_data

EOF
