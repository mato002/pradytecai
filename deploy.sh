#!/usr/bin/env bash
# PradytecAI Django + React production deploy (WHM Linux)
# Python: /usr/local/bin/python3.12  |  venv: ./env  |  Gunicorn: :8100
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

DEPLOY_BRANCH="${DEPLOY_BRANCH:-django}"
# Production uses "env" (Python 3.12). Do not use system /usr/bin/python3 (3.9 / WHM).
PYTHON312="${PYTHON312:-/usr/local/bin/python3.12}"
VENV="${VENV:-$ROOT/env}"
PUBLIC_HTML="${PUBLIC_HTML:-/home/pradytec/pradytecai/public_html}"
GUNICORN_BIND="${GUNICORN_BIND:-127.0.0.1:8100}"

log() { echo "[deploy] $*"; }
fail() { echo "[deploy] ERROR: $*" >&2; exit 1; }

command -v git >/dev/null || fail "git not found"
command -v npm >/dev/null || fail "npm not found"
[[ -x "$PYTHON312" ]] || fail "Python 3.12 not found at $PYTHON312 (do not use /usr/bin/python3)"

log "Pulling origin/${DEPLOY_BRANCH}"
git pull origin "$DEPLOY_BRANCH"

if [[ ! -d "$VENV" ]]; then
  log "Creating virtualenv at $VENV with $PYTHON312"
  "$PYTHON312" -m venv "$VENV"
fi
# shellcheck disable=SC1091
source "$VENV/bin/activate"

# Guard: refuse system Python 3.9
PYVER="$(python -c 'import sys; print("%d.%d"%sys.version_info[:2])')"
[[ "$PYVER" == "3.12" || "$PYVER" == "3.13" || "$PYVER" == "3.14" ]] \
  || fail "venv Python is $PYVER — need 3.12+ from $PYTHON312 (never WHM python3.9)"

python -m pip install --upgrade pip
pip install -r requirements.txt

log "Django checks"
python manage.py check --deploy || python manage.py check

log "Building React"
(cd react && npm ci && npm run build)
[[ -f react/dist/.vite/manifest.json || -f react/dist/manifest.json ]] || fail "Vite manifest missing"
[[ -f react/dist/assets/index.js ]] || fail "React build assets missing"

log "Migrations (safe)"
python manage.py migrate --noinput

log "collectstatic"
python manage.py collectstatic --noinput

mkdir -p media staticfiles

# Redis check (default port 6379)
python - <<'PY' || fail "Redis unreachable on configured CELERY_BROKER_URL"
import os
import redis
from dotenv import load_dotenv
load_dotenv()
url = os.getenv("CELERY_BROKER_URL", "redis://127.0.0.1:6379/0")
redis.Redis.from_url(url).ping()
print("redis ok", url.split("@")[-1] if "@" in url else url)
PY

reload_unit() {
  local unit="$1"
  if command -v systemctl >/dev/null && systemctl list-unit-files 2>/dev/null | grep -q "^${unit}"; then
    sudo systemctl reload-or-restart "$unit" || sudo systemctl restart "$unit"
    log "restarted $unit"
  elif command -v supervisorctl >/dev/null; then
    sudo supervisorctl restart "$unit" || true
    log "supervisor restart $unit"
  else
    log "WARN: no supervisor for $unit — restart manually"
  fi
}

reload_unit pradytec-gunicorn
reload_unit pradytec-celery-worker
reload_unit pradytec-celery-beat

if [[ -d "$PUBLIC_HTML" ]]; then
  mkdir -p "$PUBLIC_HTML/static"
  rsync -a --delete staticfiles/ "$PUBLIC_HTML/static/" || cp -a staticfiles/. "$PUBLIC_HTML/static/"
fi

log "Health check on ${GUNICORN_BIND}"
curl -fsS "http://127.0.0.1:8100/up" >/dev/null \
  || curl -fsS "https://pradytecai.com/up" >/dev/null \
  || log "WARN: health endpoint not reachable yet"

log "Verify listener:"
ss -tulpn | grep ':8100' || log "WARN: nothing listening on :8100 yet"

log "Deploy complete."
