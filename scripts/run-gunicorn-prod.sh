#!/usr/bin/env bash
# Manual Gunicorn start (production). Prefer systemd: pradytec-gunicorn.service
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"
# shellcheck disable=SC1091
source "${VENV:-$ROOT/env}/bin/activate"
export GUNICORN_BIND="${GUNICORN_BIND:-127.0.0.1:8100}"
exec gunicorn --config "$ROOT/gunicorn.conf.py" config.wsgi:application
