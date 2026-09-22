# Production cutover notes

Laravel application sources are removed from this repo. Production is moving to
**Docker Compose + PostgreSQL**. MySQL may still hold authoritative data until
PostgreSQL migration is verified — see [POSTGRES_MIGRATION.md](POSTGRES_MIGRATION.md).

## Before production

1. Verified MySQL backup + media backup + `.env` backup + Postgres backup tooling
2. Git tag / commit rollback point
3. Rehearse MySQL → PostgreSQL on a clone; reconcile row counts / logins
4. Confirm host Redis reachable from Docker (`host.docker.internal`)
5. Confirm Apache proxies `/api/v1`, `/up`, `/health`, `/t/*` → `127.0.0.1:8000`
6. Keep `APP_KEY` for Integration token decrypt
7. Confirm Celery Beat schedules (UTC) after DatabaseScheduler on Postgres

## Scheduler

1. `docker compose up -d` — exactly one `beat` replica
2. Verify pulse / metrics / GA4 ticks
3. Watch for duplicate external actions

## Rollback (app only)

1. `docker compose stop beat` then `worker` (drain outbound tasks)
2. `docker compose stop web` or redeploy previous git revision via `./deploy.sh`
3. Restore Postgres from `scripts/postgres-restore.sh` only if schema/data changed
4. MySQL remains available for data rollback until formally retired

**Do not** use host systemd units `pradytec-gunicorn` / `pradytecai-gunicorn` /
`pradytec-celery-*` under the Docker architecture.

Idempotency: published destinations skipped; `lead_communications.status=sent` skips resend; metric unique keys; pulse open-alert upsert.
