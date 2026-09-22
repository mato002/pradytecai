# Phase 0 — Dependency pin verification

Verified against Django docs / PyPI (2026-09-22).

| Package | Target | Verified |
|---------|--------|----------|
| Python | 3.14.x preferred; **3.12.x OK locally** | Django 5.2 supports 3.10–3.14 (3.14 from 5.2.8+) |
| Django | `>=5.2.8,<5.3` | LTS; pins in `requirements.txt` |
| DRF | `>=3.15,<4` (installed 3.15.2) | Compatible with Django 5.2 |
| Celery | `>=5.4` | Redis broker |
| django-celery-beat | `>=2.7` | DB scheduler |
| React | 19.x | `/react` |
| Vite | 8.x preferred; **6.x used** (stable with React 19 at scaffold time) | `/react` — bump to Vite 8 when plugin compatibility confirmed |
| Node | 24 LTS preferred; local may differ | Build-time only |
| MySQL driver | PyMySQL (Windows-friendly) | `mysqlclient` preferred on Linux prod |

**Local runtime used for scaffolding:** Python 3.12.10 (machine default). Production may use 3.14.x once available on the host.

**django-celery-results:** not included (plan §20).
