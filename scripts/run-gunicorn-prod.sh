#!/usr/bin/env bash
# LEGACY — NOT USED BY DOCKER PRODUCTION DEPLOYMENT.
#
# Gunicorn runs inside the Docker Compose `web` service:
#   docker compose up -d web
#   docker compose logs -f web
#
# Do NOT prefer this script or pradytec-gunicorn.service on Docker hosts.
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"
echo "[legacy] Prefer: docker compose up -d web" >&2
# shellcheck disable=SC1091
source "${VENV:-$ROOT/env}/bin/activate"
export GUNICORN_BIND="${GUNICORN_BIND:-127.0.0.1:8000}"
exec gunicorn --config "$ROOT/gunicorn.conf.py" config.wsgi:application
