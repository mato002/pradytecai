#!/usr/bin/env bash
# PradytecAI production deploy — Docker Compose (web / worker / beat / postgres)
#
# Gunicorn runs ONLY inside the Compose `web` service.
# Do NOT systemctl restart pradytec-gunicorn / pradytecai-gunicorn (obsolete).
#
# Redis/Celery: if host Redis is unreachable, deploy continues with web+postgres
# only and leaves worker/beat stopped (CELERY off).
#
# Usage (production):
#   cd /home/pradytec/pradytecai && ./deploy.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

DEPLOY_BRANCH="${DEPLOY_BRANCH:-django}"
# ONLY this path — never the account-wide /home/pradytec/public_html (other apps live there).
PUBLIC_HTML="${PUBLIC_HTML:-/home/pradytec/pradytecai/public_html}"
COMPOSE="${COMPOSE:-docker compose}"
NODE_IMAGE="${NODE_IMAGE:-node:24-alpine}"
PUBLIC_HEALTH_URL="${PUBLIC_HEALTH_URL:-https://pradytecai.com/up}"
IMAGE_TAG="${IMAGE_TAG:-latest}"
APP_IMAGE="pradytecai-app:${IMAGE_TAG}"
# Force Celery off even if Redis pings: ENABLE_CELERY=0 ./deploy.sh
ENABLE_CELERY="${ENABLE_CELERY:-auto}"
# Rebuild app image even if it already exists: FORCE_BUILD=1 ./deploy.sh
FORCE_BUILD="${FORCE_BUILD:-0}"
# Always git pull even when images exist: FORCE_PULL=1 ./deploy.sh
# Default: skip git pull when app image already exists (faster redeploy).
FORCE_PULL="${FORCE_PULL:-0}"

log() { echo "[deploy] $*"; }
fail() { echo "[deploy] ERROR: $*" >&2; exit 1; }

require_cmd() {
  command -v "$1" >/dev/null 2>&1 || fail "'$1' not found"
}

image_exists() {
  docker image inspect "$1" >/dev/null 2>&1
}

# Refuse dangerous account-wide docroot
if [[ "$PUBLIC_HTML" == "/home/pradytec/public_html" || "$PUBLIC_HTML" == "/home/pradytec/public_html/" ]]; then
  fail "PUBLIC_HTML must NOT be /home/pradytec/public_html (shared with other apps). Use /home/pradytec/pradytecai/public_html"
fi

# Create site document root early (not the nested Laravel folder ./pradytecai/)
if [[ ! -d "$PUBLIC_HTML" ]]; then
  log "Creating PUBLIC_HTML=${PUBLIC_HTML}"
  mkdir -p "$PUBLIC_HTML" || fail "Could not create ${PUBLIC_HTML}"
  if [[ "$(id -u)" -eq 0 ]] && id pradytec >/dev/null 2>&1; then
    chown pradytec:pradytec "$PUBLIC_HTML" 2>/dev/null || true
  fi
fi
log "Frontend target: ${PUBLIC_HTML}"

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

# Strip Windows CRLF if .env was edited on Windows (avoids: $'\r': command not found)
if grep -q $'\r' .env 2>/dev/null; then
  log "Normalizing CRLF → LF in .env"
  sed -i 's/\r$//' .env
fi

# shellcheck disable=SC1091
set -a
# shellcheck source=/dev/null
source <(sed 's/\r$//' .env)
set +a

: "${POSTGRES_PASSWORD:?POSTGRES_PASSWORD must be set in .env}"
: "${DJANGO_SECRET_KEY:?DJANGO_SECRET_KEY must be set in .env}"

export COMPOSE_PROJECT_NAME="${COMPOSE_PROJECT_NAME:-pradytecai}"
export IMAGE_TAG="${IMAGE_TAG:-latest}"
APP_IMAGE="pradytecai-app:${IMAGE_TAG}"

# Re-apply docroot after .env (still refuse account-wide public_html)
PUBLIC_HTML="${PUBLIC_HTML:-/home/pradytec/pradytecai/public_html}"
if [[ "$PUBLIC_HTML" == "/home/pradytec/public_html" || "$PUBLIC_HTML" == "/home/pradytec/public_html/" ]]; then
  fail "PUBLIC_HTML must NOT be /home/pradytec/public_html. Use /home/pradytec/pradytecai/public_html"
fi
if [[ ! -d "$PUBLIC_HTML" ]]; then
  log "Creating PUBLIC_HTML=${PUBLIC_HTML}"
  mkdir -p "$PUBLIC_HTML" || fail "Could not create ${PUBLIC_HTML}"
  if [[ "$(id -u)" -eq 0 ]] && id pradytec >/dev/null 2>&1; then
    chown pradytec:pradytec "$PUBLIC_HTML" 2>/dev/null || true
  fi
fi

REDIS_HOST_CHECK="${REDIS_HOST:-host.docker.internal}"
REDIS_PORT_CHECK="${REDIS_PORT:-6379}"
BROKER_URL_CHECK="${CELERY_BROKER_URL:-redis://${REDIS_HOST_CHECK}:${REDIS_PORT_CHECK}/2}"

CELERY_OK=0

# ---------------------------------------------------------------------------
# 4a. Host Redis preflight (non-fatal — Celery skipped if Redis is down)
# ---------------------------------------------------------------------------
log "Preflight: host Redis (redis-cli if available)"
if [[ "${ENABLE_CELERY}" == "0" || "${ENABLE_CELERY}" == "false" || "${ENABLE_CELERY}" == "no" ]]; then
  log "ENABLE_CELERY=${ENABLE_CELERY} — Celery worker/beat will stay OFF"
  CELERY_OK=0
elif command -v redis-cli >/dev/null 2>&1; then
  if redis-cli -h 127.0.0.1 -p "${REDIS_PORT_CHECK}" ping 2>/dev/null | grep -qi pong; then
    log "redis-cli PING ok on 127.0.0.1:${REDIS_PORT_CHECK}"
    CELERY_OK=1
  else
    log "WARN: redis-cli could not PING 127.0.0.1:${REDIS_PORT_CHECK} — continuing without Celery"
    CELERY_OK=0
  fi
else
  log "redis-cli not installed on host — will probe from Docker after image build"
  CELERY_OK=1  # optimistic; Docker probe may demote to 0
fi

# ---------------------------------------------------------------------------
# 5–6. Git pull + record previous revision
# ---------------------------------------------------------------------------
OLD_REV="$(git rev-parse --short HEAD)"
log "Current revision: ${OLD_REV}"
if image_exists "$APP_IMAGE" && [[ "$FORCE_PULL" != "1" && "$FORCE_BUILD" != "1" ]]; then
  log "App image ${APP_IMAGE} already present — skipping git pull (FORCE_PULL=1 or FORCE_BUILD=1 to pull)"
  NEW_REV="$OLD_REV"
else
  log "Pulling origin/${DEPLOY_BRANCH}"
  git pull origin "$DEPLOY_BRANCH"
  NEW_REV="$(git rev-parse --short HEAD)"
  log "New revision: ${NEW_REV} (was ${OLD_REV})"
fi

# ---------------------------------------------------------------------------
# 7. Build application image (shared by web / worker / beat)
# ---------------------------------------------------------------------------
if image_exists "$APP_IMAGE" && [[ "$FORCE_BUILD" != "1" ]]; then
  log "App image ${APP_IMAGE} already exists — skipping docker build (FORCE_BUILD=1 to rebuild)"
else
  log "Building application image ${APP_IMAGE}"
  $COMPOSE build web
fi

# ---------------------------------------------------------------------------
# 4b. Redis from Docker (non-fatal)
# ---------------------------------------------------------------------------
if [[ "${ENABLE_CELERY}" == "0" || "${ENABLE_CELERY}" == "false" || "${ENABLE_CELERY}" == "no" ]]; then
  CELERY_OK=0
elif [[ "$CELERY_OK" -eq 1 ]] || ! command -v redis-cli >/dev/null 2>&1; then
  log "Verifying host Redis reachability from application image"
  if $COMPOSE run --rm --no-deps \
    -e CELERY_BROKER_URL="$BROKER_URL_CHECK" \
    -e REDIS_HOST="$REDIS_HOST_CHECK" \
    -e REDIS_PORT="$REDIS_PORT_CHECK" \
    --entrypoint python \
    web -c "import os,redis; url=os.environ.get('CELERY_BROKER_URL');
r=redis.Redis.from_url(url, socket_connect_timeout=5);
r.ping();
safe=url.split('@')[-1] if '@' in url else url;
print('redis ok ('+safe+')')"; then
    CELERY_OK=1
  else
    log "WARN: Host Redis unreachable from Docker — continuing WITHOUT Celery (worker/beat OFF)"
    CELERY_OK=0
  fi
fi

if [[ "$CELERY_OK" -eq 0 ]]; then
  log "Celery mode: OFF (web + postgres only). Fix Redis later, then: docker compose up -d worker beat"
fi

# ---------------------------------------------------------------------------
# 8–9. Ephemeral React build (Node does not stay running)
# ---------------------------------------------------------------------------
log "Building React with ephemeral ${NODE_IMAGE}"
mkdir -p react/dist
NODE_PULL_ARGS=()
if image_exists "$NODE_IMAGE"; then
  log "Node image ${NODE_IMAGE} already present — not pulling"
  NODE_PULL_ARGS=(--pull=never)
else
  log "Node image ${NODE_IMAGE} missing — will pull once"
fi
docker run --rm \
  "${NODE_PULL_ARGS[@]}" \
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

log "Seeding default products (idempotent)"
$COMPOSE run --rm --no-deps web python manage.py seed_products \
  || log "WARN: seed_products failed (non-fatal)"

log "collectstatic → ./staticfiles"
mkdir -p staticfiles media logs backups
# Container app user is uid/gid 1000. WHM/SELinux often blocks writes otherwise.
if [[ "$(id -u)" -eq 0 ]]; then
  chown -R 1000:1000 staticfiles media logs backups 2>/dev/null || true
  if command -v getenforce >/dev/null 2>&1 && [[ "$(getenforce 2>/dev/null)" == "Enforcing" ]]; then
    chcon -Rt container_file_t staticfiles media logs backups 2>/dev/null \
      || chcon -Rt svirt_sandbox_file_t staticfiles media logs backups 2>/dev/null \
      || true
  fi
else
  chown -R 1000:1000 staticfiles media logs backups 2>/dev/null \
    || log "WARN: could not chown bind mounts to 1000:1000"
fi
$COMPOSE run --rm --no-deps --user root web python manage.py collectstatic --noinput
if [[ "$(id -u)" -eq 0 ]]; then
  chown -R 1000:1000 staticfiles media logs backups 2>/dev/null || true
fi

# ---------------------------------------------------------------------------
# 14. Safe React → /home/pradytec/pradytecai/public_html only
#     (NOT /home/pradytec/public_html — NOT the nested Laravel ./pradytecai/)
# ---------------------------------------------------------------------------
if [[ ! -d "$PUBLIC_HTML" ]]; then
  log "Creating PUBLIC_HTML=${PUBLIC_HTML}"
  mkdir -p "$PUBLIC_HTML" || fail "Could not create PUBLIC_HTML=${PUBLIC_HTML}"
  if [[ "$(id -u)" -eq 0 ]] && id pradytec >/dev/null 2>&1; then
    chown pradytec:pradytec "$PUBLIC_HTML" 2>/dev/null || true
  fi
fi

log "Deploying React to ${PUBLIC_HTML} (preserving .well-known, static, media)"
mkdir -p "$PUBLIC_HTML/assets" "$PUBLIC_HTML/static" "$PUBLIC_HTML/media"
mkdir -p media

if [[ -L "$PUBLIC_HTML/media" ]]; then
  log "public_html/media already symlinked"
elif [[ ! -e "$PUBLIC_HTML/media" ]]; then
  ln -sfn "$(pwd)/media" "$PUBLIC_HTML/media"
  log "Linked ${PUBLIC_HTML}/media → $(pwd)/media"
elif [[ -d "$PUBLIC_HTML/media" ]]; then
  rsync -a "$PUBLIC_HTML/media/" media/ || true
  rsync -a media/ "$PUBLIC_HTML/media/" || true
  log "Synced ./media ↔ ${PUBLIC_HTML}/media (directory already present)"
fi

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
log "React index.html replaced atomically at ${PUBLIC_HTML}/index.html"

# ---------------------------------------------------------------------------
# 15. Django static → public_html/static
# ---------------------------------------------------------------------------
if [[ -d staticfiles ]]; then
  mkdir -p "$PUBLIC_HTML/static"
  rsync -a --delete staticfiles/ "$PUBLIC_HTML/static/" \
    || cp -a staticfiles/. "$PUBLIC_HTML/static/"
  log "Django static synced to ${PUBLIC_HTML}/static/"
  if [[ "$(id -u)" -eq 0 ]] && id pradytec >/dev/null 2>&1; then
    chown -R pradytec:pradytec "$PUBLIC_HTML/assets" "$PUBLIC_HTML/static" \
      "$PUBLIC_HTML/index.html" 2>/dev/null || true
  fi
fi

# ---------------------------------------------------------------------------
# 16–17. Bring stack up (preserve postgres volume; no `down -v`)
# ---------------------------------------------------------------------------
if [[ "$CELERY_OK" -eq 1 ]]; then
  log "docker compose up -d --remove-orphans (web worker beat postgres)"
  $COMPOSE up -d --remove-orphans
else
  log "docker compose up -d postgres web (Celery OFF)"
  $COMPOSE up -d --remove-orphans postgres web
  log "Stopping worker/beat if present"
  $COMPOSE stop worker beat 2>/dev/null || true
  $COMPOSE rm -f worker beat 2>/dev/null || true
fi

log "Waiting for services"
sleep 8

# ---------------------------------------------------------------------------
# 18–22. Verify containers + optional Celery + /up
# ---------------------------------------------------------------------------
log "Compose status"
$COMPOSE ps

running="$($COMPOSE ps --status running --services | tr '\n' ' ')"
echo "$running" | grep -qw web || fail "web service is not running"
echo "$running" | grep -qw postgres || fail "postgres service is not running"

if [[ "$CELERY_OK" -eq 1 ]]; then
  echo "$running" | grep -qw worker || fail "worker service is not running"
  echo "$running" | grep -qw beat || fail "beat service is not running"

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
  CELERY_STATUS="worker+beat RUNNING"
else
  log "SKIP Celery checks — worker/beat are OFF (Redis unavailable or ENABLE_CELERY=0)"
  CELERY_STATUS="OFF (Redis down / skipped)"
fi

log "Internal health http://127.0.0.1:8000/up"
curl -fsS "http://127.0.0.1:8000/up" >/dev/null \
  || fail "internal /up failed"

log "Public health ${PUBLIC_HEALTH_URL}"
curl -fsS "$PUBLIC_HEALTH_URL" >/dev/null \
  || log "WARN: public /up not reachable yet (check Apache proxy / DNS)"

cat <<EOF

[deploy] SUCCESS
  project:     ${COMPOSE_PROJECT_NAME}
  revision:    ${OLD_REV} → ${NEW_REV}
  image:       pradytecai-app:${IMAGE_TAG}
  services:    web postgres (+ Celery: ${CELERY_STATUS})
  gunicorn:    inside Docker service \`web\` (no host systemd)
  listen:      127.0.0.1:8000 → container :8100
  frontend:    ${PUBLIC_HTML}
  celery:      ${CELERY_STATUS}
  enable later: fix Redis, then: docker compose up -d worker beat
  rollback:    git checkout ${OLD_REV} && ./deploy.sh
  NEVER:       docker compose down -v   # destroys postgres_data

EOF
