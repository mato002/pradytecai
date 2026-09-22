# Laravel retirement criteria

Do **not** delete Laravel until all are true:

- [ ] Database parity verified on production
- [ ] Auth + Spatie permissions + visibility work in Django
- [ ] Public UI parity accepted
- [ ] Admin module parity accepted
- [ ] Integrations (BulkSMS, UltraMsg, Buffer, GA4) validated in staging
- [ ] Redis + Celery Worker + Celery Beat stable in production
- [ ] Idempotency / retry behaviour verified
- [ ] `deploy.sh` + Gunicorn units used successfully
- [ ] Rollback window expired with no critical regressions

Then remove candidates: `artisan`, `composer.json`, `composer.lock`, `app/` (PHP), Blade views, Laravel `config/*.php`, `bootstrap/`, `routes/*.php`, `vendor/`.

Keep Laravel migration history under `database/migrations/` as reference until certified obsolete.
