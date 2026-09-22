# MySQL adoption (fake-initial)

Production database (same as Laravel): **`pradytec_prady`** on `127.0.0.1:3306` (user `pradytec_prady`). Do not create a separate Django database.

On a **clone** of production MySQL:

1. Point `.env` at the clone (`DJANGO_DB_CONNECTION=mysql` or `DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE=pradytec_prady`, etc.).
2. Ensure Django models' `Meta.db_table` match existing tables (already done).
3. Run:

```bash
python manage.py migrate --fake-initial
python manage.py migrate  # applies only new Django tables (sessions, celery beat, auth contenttypes, etc.)
```

4. Verify row counts for key tables (`users`, `products`, `contact_messages`, …).
5. Do **not** drop Laravel `migrations` table; Django uses `django_migrations`.

## Controlled schema additions

- Django may add `users.last_login` (nullable) via AbstractBaseUser — apply only after backup.
- Celery Beat tables are new and safe.
- Never recreate business tables.

## Rollback

Keep Laravel code and vhost until the rollback window expires. Stop Celery Beat before re-enabling Laravel `schedule:run`.
