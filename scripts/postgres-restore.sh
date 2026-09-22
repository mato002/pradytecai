#!/usr/bin/env bash
# PostgreSQL restore for PradytecAI — deliberate, non-automatic overwrite.
#
# Usage:
#   ./scripts/postgres-restore.sh /path/to/pradytecai-YYYYMMDD.dump TARGET_DB
#
# Safeguards:
#   - Requires explicit backup path + destination database name
#   - Refuses to restore into production DB name unless CONFIRM_PRODUCTION_RESTORE=YES
#   - Recommend restoring into a staging DB first
#
# Example (staging):
#   ./scripts/postgres-restore.sh backups/pradytecai-20260101T120000Z.dump pradytecai_restore_test
#
# Example (production — dangerous):
#   CONFIRM_PRODUCTION_RESTORE=YES ./scripts/postgres-restore.sh backups/....dump pradytecai
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

COMPOSE="${COMPOSE:-docker compose}"

log() { echo "[postgres-restore] $*"; }
fail() { echo "[postgres-restore] ERROR: $*" >&2; exit 1; }

BACKUP="${1:-}"
TARGET_DB="${2:-}"

[[ -n "$BACKUP" && -n "$TARGET_DB" ]] || fail "Usage: $0 <backup.dump> <destination_database>"
[[ -f "$BACKUP" ]] || fail "backup not found: $BACKUP"
SIZE="$(wc -c < "$BACKUP" | tr -d ' ')"
[[ "$SIZE" -gt 0 ]] || fail "backup is empty: $BACKUP"

[[ -f .env ]] || fail ".env missing"
# shellcheck disable=SC1091
set -a
source .env
set +a

PROD_DB="${POSTGRES_DB:-pradytecai}"
USER="${POSTGRES_USER:-pradytecai}"

if [[ "$TARGET_DB" == "$PROD_DB" ]]; then
  if [[ "${CONFIRM_PRODUCTION_RESTORE:-}" != "YES" ]]; then
    fail "Refusing to overwrite production database '${PROD_DB}'. Re-run with CONFIRM_PRODUCTION_RESTORE=YES after testing on a staging DB."
  fi
  log "WARNING: restoring into PRODUCTION database '${PROD_DB}'"
fi

$COMPOSE ps --status running --services 2>/dev/null | grep -qw postgres \
  || fail "postgres service is not running"

log "Ensuring destination database '${TARGET_DB}' exists"
$COMPOSE exec -T postgres \
  psql -U "$USER" -d postgres -v ON_ERROR_STOP=1 -c \
  "SELECT 1 FROM pg_database WHERE datname='${TARGET_DB}'" | grep -q 1 \
  || $COMPOSE exec -T postgres \
    psql -U "$USER" -d postgres -v ON_ERROR_STOP=1 -c \
    "CREATE DATABASE \"${TARGET_DB}\" OWNER \"${USER}\";"

log "Restoring ${BACKUP} → ${TARGET_DB}"
# Drop/recreate objects inside target; does not drop other databases.
$COMPOSE exec -T postgres \
  pg_restore -U "$USER" -d "$TARGET_DB" --clean --if-exists --no-owner --no-acl \
  < "$BACKUP" \
  || log "WARN: pg_restore exited non-zero (common with --clean on empty DBs); verify manually"

log "Restore attempted into '${TARGET_DB}'. Verify with:"
log "  docker compose exec postgres psql -U ${USER} -d ${TARGET_DB} -c '\\dt'"
log "Recommend: point a staging stack at this DB before any production cutover."
