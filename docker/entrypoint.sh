#!/bin/sh
# Shared entrypoint for web / worker / beat.
# Postgres readiness is enforced by Compose healthchecks + depends_on.
set -eu

if [ "${DJANGO_WAIT_FOR_DB:-1}" = "1" ]; then
  python - <<'PY'
import os
import sys
import time

engine = (
    os.getenv("DJANGO_DB_ENGINE")
    or os.getenv("DJANGO_DB_CONNECTION")
    or os.getenv("DB_CONNECTION")
    or ""
).lower()
if engine not in ("postgresql", "postgres"):
    sys.exit(0)

host = os.getenv("DJANGO_DB_HOST") or os.getenv("DB_HOST") or "postgres"
port = int(os.getenv("DJANGO_DB_PORT") or os.getenv("DB_PORT") or "5432")
user = os.getenv("DJANGO_DB_USER") or os.getenv("DB_USERNAME") or "pradytecai"
password = os.getenv("DJANGO_DB_PASSWORD") or os.getenv("DB_PASSWORD") or ""
dbname = os.getenv("DJANGO_DB_NAME") or os.getenv("DB_DATABASE") or "pradytecai"
deadline = time.time() + int(os.getenv("DJANGO_DB_WAIT_SECONDS", "60"))

import psycopg

while True:
    try:
        with psycopg.connect(
            host=host,
            port=port,
            user=user,
            password=password,
            dbname=dbname,
            connect_timeout=3,
        ) as conn:
            conn.execute("SELECT 1")
        print(f"[entrypoint] postgres ready at {host}:{port}/{dbname}", flush=True)
        break
    except Exception as exc:
        if time.time() >= deadline:
            print(f"[entrypoint] postgres not ready: {exc}", file=sys.stderr, flush=True)
            sys.exit(1)
        time.sleep(1)
PY
fi

exec "$@"
