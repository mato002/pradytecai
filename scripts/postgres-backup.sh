#!/usr/bin/env bash
# PostgreSQL logical backup for PradytecAI Compose stack.
# Uses pg_dump custom format (-Fc). Does not echo passwords.
#
# Usage:
#   ./scripts/postgres-backup.sh
#   BACKUP_DIR=/path/to/backups ./scripts/postgres-backup.sh
#
# Retention: keep recent dumps under backups/; prune older files manually
# or via cron (example: find "$BACKUP_DIR" -name 'pradytecai-*.dump' -mtime +14 -delete).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

COMPOSE="${COMPOSE:-docker compose}"
BACKUP_DIR="${BACKUP_DIR:-$ROOT/backups}"
STAMP="$(date -u +%Y%m%dT%H%M%SZ)"
OUT="${BACKUP_DIR}/pradytecai-${STAMP}.dump"

log() { echo "[postgres-backup] $*"; }
fail() { echo "[postgres-backup] ERROR: $*" >&2; exit 1; }

[[ -f .env ]] || fail ".env missing"
# shellcheck disable=SC1091
set -a
source .env
set +a

DB="${POSTGRES_DB:-${DJANGO_DB_NAME:-pradytecai}}"
USER="${POSTGRES_USER:-${DJANGO_DB_USER:-pradytecai}}"

mkdir -p "$BACKUP_DIR"
$COMPOSE ps --status running --services 2>/dev/null | grep -qw postgres \
  || fail "postgres service is not running (docker compose up -d postgres)"

log "Dumping database '${DB}' → ${OUT}"
# PGPASSWORD is set inside the container from Compose env; do not print it.
$COMPOSE exec -T postgres \
  pg_dump -U "$USER" -d "$DB" -Fc --no-owner --no-acl \
  > "$OUT"

[[ -f "$OUT" ]] || fail "backup file was not created"
SIZE="$(wc -c < "$OUT" | tr -d ' ')"
[[ "$SIZE" -gt 0 ]] || fail "backup file is empty: $OUT"

log "OK: ${OUT} (${SIZE} bytes)"
log "Retention tip: delete dumps older than your policy, e.g. 14 days."
echo "$OUT"
