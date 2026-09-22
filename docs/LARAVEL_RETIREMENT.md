# Laravel retirement

Laravel application sources were removed from this repository. The stack is **Django + DRF + React** only.

## What was removed

- `artisan`, `composer.json` / `composer.lock`, `vendor/`
- PHP `app/`, `bootstrap/`, `routes/`, `resources/` (Blade), `tests/` (PHPUnit)
- Laravel `config/*.php`, `public/` (index.php / Vite build), `storage/`, `database/` (PHP migrations)
- Root Laravel Vite (`vite.config.js`) and Turbo/Tailwind npm deps
- Dual-run helper `scripts/run-laravel.ps1` and Laravel production batch/docs

## What was kept (intentionally)

- **MySQL schema / data** on production (`pradytec_prady`) — adopted via Django `db_table` models
- **`APP_KEY`** in `.env` — still used to decrypt legacy Integration ciphertext
- **Laravel-compatible bcrypt** hasher / auth backend for existing password hashes
- Brand assets moved to `static/images/brand/`
- Historical planning prompt: `PRADYTEC_DJANGO_REACT_MIGRATION_PLANNING_PROMPT.md`

## Ops notes

- Do not reintroduce PHP/Laravel into this tree without a deliberate dual-run plan
- Production cutover and MySQL notes: [CUTOVER.md](CUTOVER.md), [MYSQL_ADOPTION.md](MYSQL_ADOPTION.md)
