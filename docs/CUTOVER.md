# Production cutover & rollback

## Before production

1. Verified MySQL backup + media backup + `.env` backup
2. Git tag / commit rollback point
3. Rehearse on DB clone with `--fake-initial`
4. Confirm Redis and systemd/Supervisor units
5. Keep `APP_KEY` for Integration token decrypt
6. Confirm Celery Beat schedules match Laravel UTC times

## Scheduler cutover

1. Celery Worker healthy
2. Beat configured but disabled / stopped
3. Disable Laravel cron `schedule:run`
4. Enable Celery Beat (single instance)
5. Verify pulse / metrics / GA4 ticks
6. Watch for duplicate external actions

## Rollback to Laravel

1. Stop Celery Beat first
2. Drain or revoke outbound Celery tasks (publish / SMS / WhatsApp)
3. Stop Celery Worker
4. Point vhost back to Laravel
5. Re-enable Laravel scheduler only after Beat is fully stopped

Idempotency: published destinations skipped; `lead_communications.status=sent` skips resend; metric unique keys; pulse open-alert upsert.
