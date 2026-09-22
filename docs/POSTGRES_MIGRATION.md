# MySQL → PostgreSQL migration

**Status:** tooling and Docker PostgreSQL are in place; **production data cutover is NOT complete** until explicitly executed and reconciled.

Until then:

* MySQL (`pradytec_prady`) remains the data authority / rollback source
* Do **not** delete MySQL data, drop the Laravel schema, or discard `APP_KEY`
* Docker Compose `postgres` is the **target** runtime database for Django once migration is verified

## Goals

Preserve:

* primary IDs, users, password hashes (Laravel bcrypt), roles, permissions, scopes
* products, campaigns, content, social accounts, leads, demos, careers, blog, tasks
* analytics, timestamps, JSON, pivot relationships, integration metadata

Investigate carefully before cutover:

* Laravel bcrypt hashes and encrypted integration tokens (`APP_KEY`)
* polymorphic records, JSON mappings, enums, booleans
* unsigned integers, decimal precision, collations / case sensitivity, timestamps

## Recommended sequence

1. Backup MySQL (host) and take a Postgres dump baseline after empty migrate
2. Stand up Compose postgres; run `python manage.py migrate` against empty PostgreSQL
3. Rehearse data copy on a **clone** (row counts, FK integrity, login with known users)
4. Keep dual-read verification period; MySQL stays online
5. Point production `.env` at PostgreSQL only after reconciliation sign-off
6. Retain MySQL untouched for the rollback window

## Django engines

```env
# Target
DJANGO_DB_ENGINE=postgresql
DJANGO_DB_HOST=postgres

# Temporary source / tooling only
# DJANGO_DB_ENGINE=mysql
# DJANGO_DB_HOST=127.0.0.1
```

PyMySQL remains in `requirements.txt` for migration tooling. Remove later when unused.

## Rollback

1. Stop Compose `beat`, then `worker`, then `web` (or `docker compose stop`)
2. Point `.env` back at MySQL if still authoritative, **or** restore Postgres from `scripts/postgres-restore.sh` into a verified backup
3. Redeploy previous git revision with `./deploy.sh`

Final authority after successful cutover:

```text
PostgreSQL → Django migrations
```
