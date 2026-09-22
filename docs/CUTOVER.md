# Production cutover notes

Laravel application sources are removed from this repo. Production still uses the **existing MySQL schema** and may keep `APP_KEY` for decrypting Integration tokens.

## Before production

1. Verified MySQL backup + media backup + `.env` backup
2. Git tag / commit rollback point
3. Rehearse on DB clone with `--fake-initial`
4. Confirm Redis and systemd/Supervisor units
5. Keep `APP_KEY` for Integration token decrypt
6. Confirm Celery Beat schedules (UTC)

## Scheduler

1. Celery Worker healthy
2. Enable Celery Beat (single instance)
3. Verify pulse / metrics / GA4 ticks
4. Watch for duplicate external actions

## Rollback (app only)

1. Stop Celery Beat first
2. Drain or revoke outbound Celery tasks (publish / SMS / WhatsApp)
3. Stop Celery Worker / Gunicorn
4. Redeploy previous Django git tag (or restore DB backup if schema changed)

Idempotency: published destinations skipped; `lead_communications.status=sent` skips resend; metric unique keys; pulse open-alert upsert.
