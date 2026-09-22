# PRADYTEC DJANGO + REACT MIGRATION PLANNING PROMPT

You are working on the existing PradytecAI project:

- Repository: https://github.com/mato002/pradytecai
- Branch: `django`

# MISSION

Perform a deep, read-only inspection of the current codebase and produce a concrete implementation plan for replacing the existing Laravel/Blade application with:

- Python
- Django
- Django REST Framework
- React
- Vite
- MySQL
- Celery
- Celery Worker
- Celery Beat
- Redis
- Gunicorn / WSGI in production

**DO NOT start implementing the migration yet.**

Your output must be an implementation plan grounded in the actual current repository, database structure, routes, models, workflows, deployment scripts, UI, permissions, integrations and production architecture.

The objective is **NOT** to redesign PradytecAI from scratch.

The objective is:

> **REPLACE LARAVEL + BLADE WITH DJANGO + DRF + REACT WHILE PRESERVING THE CURRENT APPLICATION, DATA, URL CONTRACTS, BEHAVIOUR, UI, PERMISSIONS, INTEGRATIONS AND PRODUCTION OPERATIONS.**

---

# 1. FIRST INSPECT THE EXISTING APPLICATION

Before proposing architecture, inspect at minimum:

- `composer.json`
- `package.json`
- `vite.config.js`
- `deploy.sh`
- `.env`
- `.env.example`
- `README.md`
- `routes/web.php`
- `routes/console.php`
- Laravel models
- controllers
- middleware
- jobs
- commands
- config files
- database migrations
- seeders
- Blade layouts
- public website views
- admin views
- Tailwind/CSS
- JavaScript/Turbo behaviour
- tests
- integrations
- uploads/storage
- authentication
- roles and permissions
- product/social-account visibility rules
- any existing queue processing logic
- scheduler logic
- cron assumptions
- server/production deployment documentation

Read the local `.env` for configuration context, but:

**DO NOT output passwords, API keys, APP_KEY, database passwords, tokens or other secrets in the report.**

The existing database credentials and integration credentials must be reused from environment variables rather than hard-coded or replaced.

---

# 2. TARGET PROJECT STRUCTURE

The repository root should become the Django project root.

React MUST live inside:

`/react`

Propose a clean structure similar to:

```text
/
├── manage.py
├── requirements.txt
├── .env
├── .env.example
├── deploy.sh
├── README.md
├── config/
│   ├── settings/
│   ├── urls.py
│   ├── wsgi.py
│   ├── celery.py
│   └── ...
├── apps/
│   ├── accounts/
│   ├── products/
│   ├── leads/
│   ├── campaigns/
│   ├── content/
│   ├── social/
│   ├── analytics/
│   ├── careers/
│   ├── integrations/
│   ├── marketing/
│   ├── audit/
│   └── ...
├── templates/
├── static/
├── staticfiles/
├── media/
└── react/
    ├── package.json
    ├── vite.config.*
    ├── src/
    └── ...
```

Do not blindly use these app names.

Inspect the current Laravel modules first and recommend Django app boundaries that best match the actual business domains.

Avoid creating dozens of tiny Django apps.

---

# 3. TECHNOLOGY BASELINE

Plan around stable production versions.

Preferred baseline:

- Python 3.14.x
- Django 5.2 LTS, latest supported patch
- Django REST Framework 3.18.x
- React 19.x
- Vite 8.x
- Node.js 24 LTS
- Gunicorn
- Celery
- Redis
- `django-celery-beat`
- MySQL using a supported Django MySQL driver
- Tailwind CSS, preserving the existing design system

Before implementation, verify package compatibility and pin suitable versions.

Create:

- `requirements.txt`
- `react/package.json`

Do not introduce unnecessary libraries.

Every dependency must have a clear reason.

The plan should decide whether to use:

- `redis` Python client
- `django-celery-beat`
- `django-celery-results`

Use `django-celery-results` only if persistent database-backed task result storage is genuinely useful. Do not add it automatically.

---

# 4. MYSQL IS THE EXISTING DATA AUTHORITY

THIS IS CRITICAL.

The current MySQL database and its existing business tables must remain authoritative.

We are NOT creating a second clean database and manually re-importing everything.

Inspect all existing Laravel migrations and the actual MySQL schema.

Map existing tables into Django models using appropriate:

- `db_table`
- `db_column`
- primary keys
- foreign keys
- indexes
- unique constraints
- nullable fields
- defaults
- decimals
- timestamps
- JSON fields
- enums/status fields
- pivot tables
- polymorphic relationships

Use:

```bash
python manage.py inspectdb
```

as an investigation/bootstrap tool where useful, but **DO NOT accept the generated models blindly**.

Reconcile generated models against:

1. Laravel migrations
2. Eloquent models
3. actual production schema

Produce a table-by-table migration matrix:

```text
Laravel table
→ Django app
→ Django model
→ special mapping concerns
→ migration strategy
```

---

# 5. DO NOT LET DJANGO DESTROY OR RECREATE EXISTING TABLES

Design a safe database-adoption strategy.

The plan must explain how Django migrations will begin managing the application WITHOUT dropping, recreating or corrupting existing business tables.

Consider an approach involving:

- schema inspection
- Django initial model state
- exact `db_table` mappings
- initial migrations matching the existing schema
- controlled `--fake-initial` or state-only migration strategy where appropriate
- schema verification before Django is permitted to execute structural changes

Do not simply mark every model `managed=False` permanently unless there is a justified reason.

The eventual goal is for Django migrations to safely manage future schema changes.

Laravel's `migrations` table may remain as historical information.

Django can have its own migration metadata.

Framework-specific tables such as `django_migrations`, Celery Beat tables, and other Django support tables may be added where necessary, but existing business tables must remain intact.

---

# 6. AUTHENTICATION IS A MIGRATION RISK

Inspect the current `users` table and authentication logic.

The current Laravel user records must remain usable.

The current system contains Laravel password hashes.

DO NOT:

- reset every user's password without justification
- silently replace existing hashes
- modify password hashes during the planning stage
- break the possibility of rolling back to Laravel during migration

Design a compatibility strategy allowing existing users to authenticate from Django during the transition.

Investigate Laravel bcrypt hash compatibility and propose a safe custom Django authentication backend/password compatibility layer.

Preserve existing:

- users
- roles
- permissions
- super-admin behaviour
- HR manager behaviour
- remember/login behaviour where appropriate

After the Laravel rollback window has been closed, a later controlled password-rehash strategy may be considered.

Do not make it part of the first cutover unless necessary.

---

# 7. PRESERVE THE EXISTING PERMISSION MODEL

Do NOT simply replace the current authorization model with vanilla Django permissions.

The existing application uses:

- roles
- permissions
- Spatie permission tables
- `role`
- `is_super_admin`
- `user_access_scopes`
- product-level visibility
- social-account-level visibility

Inspect these structures carefully.

Map them into Django models and DRF permission services.

The existing permission names such as:

- `dashboard.view`
- `products.view`
- `products.manage`
- `content.view`
- `content.create`
- `content.edit`
- `content.approve`
- `content.publish`
- `campaigns.view`
- `analytics.view`
- `leads.view`
- `demo_requests.view`
- `users.manage`
- etc.

must remain meaningful.

Very important:

Authorization cannot stop at:

> "Can this user access this API endpoint?"

The API query itself must enforce product/social account scope.

For example, a product-restricted user must not retrieve unauthorized products by changing an API URL manually.

Design a reusable visibility/queryset layer equivalent to the current:

- `visibleTo()`
- `scopedProductIds()`
- `canAccessProduct()`
- `canAccessSocialAccount()`

logic.

---

# 8. SPECIAL DATABASE RELATIONSHIPS

Inspect and explicitly plan for existing Eloquent relationships including:

- `campaign_product`
- `content_item_media`
- product → campaigns
- product → social accounts
- product → content
- campaigns → content
- campaigns → leads/demo requests
- users → owned campaigns
- content authors/approvers
- demo request assignees

Use explicit Django `through=` models when pivot tables contain additional fields such as timestamps or `sort_order`.

Do not rename existing pivot tables unnecessarily.

---

# 9. ELOQUENT POLYMORPHIC DATA

Inspect tables such as metric snapshots that currently use Eloquent polymorphic relationships.

Do NOT blindly convert Laravel morph columns into Django ContentType references because that could invalidate existing rows.

Preserve existing:

- morph type values
- morph IDs
- existing records

and design a compatibility model/service around the existing database representation unless a separate controlled data migration is justified.

---

# 10. ENCRYPTED INTEGRATION DATA

This is another critical migration risk.

Inspect the existing `Integration` model.

Laravel currently encrypts fields such as:

- `access_token`
- `refresh_token`

using Laravel encryption.

These encrypted database values will not automatically become readable by Django.

The implementation plan must identify:

- every Laravel-encrypted DB field
- which Laravel key/config encrypted it
- whether Django needs immediate access to it
- how compatibility/decryption will work
- whether a one-time controlled re-encryption migration is necessary

DO NOT overwrite existing encrypted values until:

1. they have been successfully decrypted,
2. validated,
3. backed up,
4. and the rollback strategy is understood.

Never expose the tokens in logs or planning output.

---

# 11. REACT ARCHITECTURE

All new React source code must live under:

`/react`

Use Vite.

React should replace both:

1. the public website UI
2. the administrative Marketing Command Centre UI

Consider two logical React surfaces inside the same React project.

## PUBLIC APPLICATION

Routes including:

- `/`
- `/about`
- `/services`
- `/products`
- `/careers`
- `/contact`
- `/blog`
- `/blog/:slug`
- `/faq`
- `/policies`
- `/search`

## ADMIN APPLICATION

Routes including:

- `/login`
- `/admin`
- `/admin/products`
- `/admin/enquiries`
- `/admin/users`
- `/admin/roles`
- `/admin/blog`
- `/admin/positions`
- `/admin/applications`
- `/admin/profile`
- `/admin/settings`
- `/admin/campaigns`
- `/admin/content/*`
- `/admin/social-accounts`
- `/admin/integrations`
- `/admin/analytics/*`
- `/admin/demos`
- `/admin/pulse`
- `/admin/tasks`
- `/admin/subscribers`

Inspect the current routes and produce a COMPLETE old-route → new-route matrix.

Do not lose URLs that are currently indexed, bookmarked, used in campaigns, or linked externally.

---

# 12. LOGIN CONTRACT

The intended behaviour should be:

`https://pradytecai.com/login`

→ React login screen

Successful authentication:

→ `/admin`

If already authenticated, visiting `/login` should redirect appropriately.

Admin React routes must be protected.

An unauthenticated request to `/admin/*` should return/redirect to `/login` appropriately.

The React frontend must not itself be treated as the security boundary.

Django/DRF is the authority.

---

# 13. API DESIGN

Use Django REST Framework.

Propose a clean API namespace such as:

`/api/v1/`

The React application should consume DRF APIs.

Plan endpoints module-by-module for:

- authentication/session
- current user
- permissions
- products
- leads/enquiries
- demo requests
- campaigns
- content
- calendar
- social accounts
- analytics
- integrations
- marketing pulse
- tasks
- careers
- job applications
- blog
- users
- roles
- settings
- subscribers
- search
- activity/audit logs

Do NOT mechanically create CRUD endpoints for every model.

Design APIs around the workflows currently implemented.

---

# 14. AUTH BETWEEN REACT AND DJANGO

Because the React app and Django backend will be served from the same domain, investigate using secure Django session authentication with DRF.

Prefer same-origin authentication rather than introducing JWT unnecessarily.

Plan:

- CSRF handling
- session cookies
- secure cookies
- SameSite
- HTTPS
- login
- logout
- current-user endpoint
- permission payload
- 401/403 handling

Only recommend JWT if there is an actual requirement for external/mobile/API clients.

---

# 15. PRESERVE THE PUBLIC UI

The React migration must reproduce the current website before redesigning it.

Inspect:

- `resources/views/home.blade.php`
- marketing components
- header
- footer
- hero
- product cards
- services
- featured product sections
- CTA
- chatbot
- forms
- responsive behaviour
- CSS
- typography
- spacing
- breakpoints

Current visual identity includes colors such as:

- `#053171`
- `#0A4E99`
- `#0557A6`
- `#00398C`
- `#19A7EF`
- `#0487CD`
- `#EDF6FC`
- `#F7F9FC`

and the existing blue gradient identity.

Preserve the Prady design system.

The initial React conversion is a parity exercise, NOT an excuse for a completely new design.

---

# 16. PRESERVE THE ADMIN UI

Inspect the complete existing admin interface.

It currently includes concepts such as:

- fixed/collapsible sidebar
- responsive mobile sidebar
- sticky header
- search
- profile menu
- product switcher
- permission-aware navigation
- dashboard cards
- tables
- forms
- modals
- flash messages
- light UI
- responsive behaviour
- current Tailwind styles

Rebuild these as reusable React components.

The goal is visual and behavioural parity first.

Do not reproduce the existing Blade/Turbo implementation mechanism; reproduce the USER EXPERIENCE using React.

Replace Turbo frames/modals with proper React routing/state/components.

---

# 17. PUBLIC WEBSITE SEO MUST NOT REGRESS

Because this is a marketing website, a naïve client-only SPA could damage page metadata and discoverability.

The plan must explicitly address:

- `<title>`
- meta descriptions
- canonical URLs
- Open Graph metadata
- structured data where already applicable
- sitemap
- robots.txt
- blog URLs
- product URLs
- share previews
- crawlability

If pure React SPA rendering is retained, propose an appropriate prerender/metadata strategy.

Do not unnecessarily introduce Next.js because the requested frontend framework is React + Vite unless there is an exceptional and clearly explained reason.

---

# 18. SERVER-SIDE ROUTES THAT SHOULD REMAIN DJANGO CONTROLLED

Not every path should be swallowed by React Router.

Identify server-authoritative routes such as:

- `/api/...`
- tracked links such as `/t/{code}`
- media/download endpoints
- health check
- webhook/callback endpoints
- authentication operations where appropriate

React SPA fallback routing must explicitly exclude these.

---

# 19. FORMS AND FILE UPLOADS

Preserve all current forms and workflows including:

- contact enquiry
- newsletter
- demo request
- careers
- job application
- resume upload
- admin content/media uploads

Document:

- validation
- multipart upload handling
- maximum sizes
- allowed MIME types
- storage paths
- permissions
- download security

Preserve existing uploaded files and URLs wherever possible.

---

# 20. EXISTING INTEGRATIONS

Inventory and preserve all existing external integrations.

Known integrations include:

- BulkSMS CRM
- UltraMsg WhatsApp
- Buffer
- Google Analytics / GA4

Inspect the code rather than assuming their implementation.

For each integration document:

- existing service/classes
- environment variables
- database tables
- credentials storage
- outgoing requests
- callbacks/webhooks if any
- retry/error behaviour
- scheduled synchronization
- Django replacement service
- whether the work should run synchronously or in Celery

Do not change external credentials merely because the backend changes.

---

# 21. CELERY BACKGROUND PROCESSING ARCHITECTURE

Celery is a REQUIRED part of the target architecture.

Use:

- Django
- Celery
- Redis
- Celery Worker
- Celery Beat

The plan must define a clean Celery architecture rather than merely installing the package.

Expected baseline:

```text
Django / DRF
    │
    ├── synchronous web requests
    │
    └── Celery tasks
            │
            ├── Redis broker
            ├── Celery Worker
            └── Celery Beat
```

Inspect all current Laravel jobs, queues, commands and scheduled operations and classify them into:

1. synchronous Django request
2. Celery background task
3. Celery Beat scheduled task
4. Django management command
5. deployment/maintenance command

Produce a Laravel → Celery task migration matrix.

Examples of operations that may belong in Celery include:

- social publishing
- social metric synchronization
- GA4 synchronization
- marketing pulse evaluation
- email sending
- BulkSMS operations
- WhatsApp operations
- analytics refreshes
- campaign metric refresh
- integration synchronization
- long-running imports/exports
- other slow external API calls

Do not move tiny database operations to Celery unnecessarily.

---

# 22. CELERY TASK DESIGN

For every proposed background task define:

- task name
- Django app
- trigger
- inputs
- expected runtime
- retry policy
- timeout
- rate limit if relevant
- idempotency strategy
- logging
- failure behaviour
- whether result persistence is needed
- which user/system action initiated it where applicable

Do not pass large ORM objects through Celery.

Pass stable IDs and re-fetch data inside the task.

Avoid serializing secrets into task arguments.

---

# 23. CELERY IDEMPOTENCY

This is important for publishing, messaging, metrics and integration work.

The plan must ensure retried Celery tasks cannot accidentally:

- publish the same social post twice
- send duplicate SMS messages
- send duplicate WhatsApp messages
- duplicate analytics snapshots
- create duplicate alerts
- execute duplicate external actions

Identify existing business keys and design idempotency keys where necessary.

For externally-triggered side effects, prefer a durable task/job record where useful.

---

# 24. CELERY RETRIES AND FAILURES

Define a consistent retry policy.

Differentiate between:

## TRANSIENT FAILURES

Examples:

- timeout
- provider temporarily unavailable
- rate limit
- network failure

These may retry with exponential backoff.

## PERMANENT FAILURES

Examples:

- invalid credentials
- malformed payload
- forbidden account
- missing configuration

These should not retry indefinitely.

Plan:

- maximum retry count
- exponential backoff
- jitter where appropriate
- provider rate limits
- task timeout
- error recording
- admin visibility into failed jobs where useful

---

# 25. CELERY BEAT

Celery Beat becomes the scheduling authority for application background schedules.

Inventory current Laravel scheduler entries.

Known current schedules include:

- `marketing:pulse` hourly
- social metric synchronization at approximately 02:00
- GA4 synchronization at approximately 02:30

Map these into Celery Beat.

Prefer `django-celery-beat` so schedules can be stored in the Django database and later administered safely.

Prevent duplicate scheduling ownership.

Do NOT run the same task from both:

- cron
- Celery Beat

unless explicitly required.

Cron may still be used for infrastructure-level tasks such as:

- backups
- log rotation
- deployment health scripts

Application scheduling should primarily belong to Celery Beat.

---

# 26. CELERY TIMEZONE

The project operates in Kenya.

Inspect current timezone settings.

The plan should explicitly configure Django and Celery timezone behaviour so scheduled jobs run at the intended local times.

Consider:

```python
TIME_ZONE = "Africa/Nairobi"
USE_TZ = True
```

and compatible Celery timezone settings.

Document whether existing Laravel timestamps are stored in UTC or local time before changing behaviour.

Do not silently shift existing scheduled times.

---

# 27. REDIS

Redis should be used as the Celery broker unless the actual server inspection identifies a compelling reason not to.

Plan environment variables such as:

```env
CELERY_BROKER_URL=redis://127.0.0.1:6379/0
CELERY_RESULT_BACKEND=redis://127.0.0.1:6379/1
```

If task results are not required, explain whether the result backend can be disabled.

Also plan:

- Redis authentication if configured
- network binding
- persistence expectations
- memory limits
- eviction considerations
- production permissions/security

Redis must not be publicly exposed to the internet.

---

# 28. CELERY WORKER PROCESS

The production plan must include a persistent Celery Worker.

Example conceptual command:

```bash
celery -A config worker --loglevel=INFO
```

But do not blindly use defaults.

Recommend:

- worker concurrency appropriate to the server
- queue names if useful
- `prefetch_multiplier`
- max tasks per child if justified
- task time limits
- memory considerations
- graceful shutdown/restart
- log files

Do not over-tune before measuring workload.

---

# 29. CELERY BEAT PROCESS

The production plan must include a persistent Celery Beat service.

Example conceptual command:

```bash
celery -A config beat --loglevel=INFO
```

If using `django-celery-beat`, use the database scheduler.

Plan only ONE active Beat scheduler in production unless a distributed scheduling mechanism is intentionally designed.

Running multiple Beat instances must not result in duplicate scheduled task dispatch.

---

# 30. CELERY PROCESS SUPERVISION

The server is a WHM/Linux-style deployment.

The plan must investigate whether:

- systemd
- Supervisor
- WHM service management

is the best production mechanism.

At minimum, production should supervise:

- Gunicorn
- Celery Worker
- Celery Beat

Conceptually:

```text
pradytec-gunicorn
pradytec-celery-worker
pradytec-celery-beat
```

For each process define:

- working directory
- virtualenv executable
- environment loading
- user/group
- restart policy
- stop timeout
- logs
- startup order
- deployment restart/reload behaviour

---

# 31. CELERY MONITORING

The plan should provide a practical monitoring strategy.

At minimum include:

- worker process running
- beat process running
- Redis reachable
- queue backlog visibility
- failed task logs
- task runtime visibility
- retries
- stale jobs

Do NOT add heavyweight monitoring infrastructure unnecessarily.

If recommending Flower, classify it as optional unless there is a clear operational requirement.

---

# 32. BACKGROUND WORK AND SCHEDULING MIGRATION

The current Laravel application contains scheduled jobs and may contain queued work.

Inventory ALL:

- jobs
- scheduled tasks
- external API calls
- email sending
- publishing operations
- sync operations
- analytics jobs

Then produce a definitive mapping into:

- Celery Worker
- Celery Beat
- synchronous DRF/Django
- management commands

Celery is now part of the required architecture, so do not propose cron as the primary replacement for Laravel application scheduling.

---

# 33. STATIC FILE ARCHITECTURE

The React source remains:

`/react`

Production deployment should conceptually perform:

```text
react/
→ Vite production build
→ hashed frontend assets
→ Django/static production location
→ collectstatic
→ web server serves static files directly
```

Django should use:

```bash
python manage.py collectstatic --noinput
```

Design the Vite/Django integration clearly.

Possible approach:

- Vite manifest enabled
- React build artifacts written to a controlled static source/output
- Django template loads appropriate Vite production assets
- `collectstatic --noinput`
- Apache/Nginx serves `/static/` directly
- Gunicorn serves Django dynamic requests

Do not make Gunicorn inefficiently serve production static assets.

---

# 34. PRODUCTION SERVER

This application runs on a server/WHM-style Linux environment.

Production Django must use:

Gunicorn + WSGI

behind the existing web server/reverse proxy.

Plan:

```text
Browser
→ HTTPS
→ Apache/Nginx
→ Gunicorn
→ Django
```

Background architecture:

```text
Django / DRF
      │
      └── Redis
           │
           ├── Celery Worker
           └── Celery Beat
```

Static/media requests should bypass Gunicorn where practical.

Include:

- Gunicorn config
- worker strategy
- bind/socket/port
- process ownership
- logging
- restart
- Supervisor/systemd approach appropriate to the actual server
- reverse proxy configuration
- trusted proxy headers
- `ALLOWED_HOSTS`
- `CSRF_TRUSTED_ORIGINS`
- secure proxy SSL configuration
- Celery process supervision
- Redis supervision/security

Inspect the actual production assumptions before selecting exact service configuration.

---

# 35. DEPLOY.SH

Replace the Laravel-oriented `deploy.sh` with a Django/React deployment script having similar simplicity.

The target script should roughly handle:

1. determine project directory
2. pull the configured Git branch
3. verify/create Python virtual environment when appropriate
4. activate venv
5. upgrade/install Python dependencies from `requirements.txt`
6. run Django deployment checks
7. install React packages using `npm ci`
8. build React production assets
9. verify Vite build output/manifest
10. run safe Django migrations
11. run `collectstatic --noinput`
12. ensure required media/static directories
13. restart/reload Gunicorn
14. restart Celery Worker
15. restart Celery Beat
16. confirm Redis availability
17. perform application health check
18. verify worker responsiveness
19. verify Beat process status
20. clearly fail on errors

The plan should improve the deployment script with sensible safety controls such as:

- `set -euo pipefail`
- deployment branch variable
- environment checks
- missing Node/Python detection
- missing Redis detection
- migration failure protection
- manifest/build verification
- service restart checks
- health check
- useful logs

Do not hard-code secrets into `deploy.sh`.

Avoid killing an active worker in a way that corrupts in-flight work.

Plan graceful Celery worker restarts.

---

# 36. VIRTUAL ENVIRONMENT

README must explain clearly:

```bash
python3.14 -m venv .venv
source .venv/bin/activate
python -m pip install --upgrade pip
pip install -r requirements.txt
```

Windows local instructions should also be included where appropriate.

The plan should choose one canonical venv folder:

`.venv`

and ensure it is gitignored.

---

# 37. ENVIRONMENT CONFIGURATION

Create/update `.env.example` for Django.

Reuse the existing production `.env` values wherever semantically possible.

Map old Laravel settings into Django names carefully.

Do not arbitrarily rename every variable if retaining an existing environment variable avoids production mistakes.

Plan environment groups for:

- Django secret key
- debug
- allowed hosts
- application URL
- MySQL
- mail
- BulkSMS
- UltraMsg
- Buffer
- GA4
- static/media
- proxy/security
- Redis
- Celery broker
- Celery result backend
- Celery timezone
- worker settings where necessary

Never commit the real `.env`.

---

# 38. README.MD

The final conversion must produce a fully rewritten `README.md` containing at minimum:

## Project overview

## Architecture

Django + DRF + React + Vite + MySQL + Redis + Celery + Gunicorn

## Requirements

Exact supported:

- Python
- Node
- MySQL
- Redis
- npm
- system packages

## Clone

## Python venv creation

## requirements installation

## React installation

```bash
cd react
npm ci
```

## `.env` setup

## existing database setup

## Django migrations

## React development

## Django development

## Redis development setup

## Celery Worker development

Example:

```bash
celery -A config worker --loglevel=INFO
```

## Celery Beat development

Example:

```bash
celery -A config beat --loglevel=INFO
```

or the database scheduler equivalent when using `django-celery-beat`.

## building React

## collectstatic

## creating/administering users

## running tests

## API structure

## production deployment

## Gunicorn

## Celery Worker

## Celery Beat

## Redis

## server/reverse proxy

## background jobs

## scheduler

## static/media

## `deploy.sh`

## troubleshooting

Do not leave obsolete Laravel/Composer/Artisan instructions in the final README.

---

# 39. TESTING STRATEGY

Create an explicit parity test strategy.

Before Laravel can be removed, Django/React must demonstrate parity for:

## PUBLIC

- homepage
- navigation
- products
- services
- contact
- demos
- newsletter
- careers
- applications
- blog
- search
- tracked links

## ADMIN

- login/logout
- permissions
- scoped data visibility
- products
- leads
- demo requests
- campaigns
- content
- approvals
- scheduling/publishing
- social accounts
- integrations
- analytics
- careers
- users
- roles
- settings
- activity logs
- tasks
- subscribers
- Marketing Pulse

## INTEGRATIONS

- BulkSMS
- UltraMsg
- Buffer
- GA4

## BACKGROUND PROCESSING

Test:

- Celery task dispatch
- worker execution
- Beat scheduling
- retries
- idempotency
- failed task handling
- provider timeout handling
- duplicate prevention
- worker restart
- Beat restart
- Redis disconnect/recovery
- queue backlog behaviour

## DATABASE

Compare important counts and representative records between Laravel and Django.

No table should silently lose data.

---

# 40. CELERY TESTING

Provide specific tests for Celery.

Include:

- unit tests for task business logic
- task idempotency tests
- retry tests
- external API mocking
- scheduled task registration tests
- timezone tests
- duplicate Beat dispatch prevention assumptions
- task failure logging
- stale/long-running task behaviour

For development/test environments, determine where Celery eager mode is appropriate and where true worker integration tests are required.

Do not make all tests depend on a running Redis instance unless necessary.

---

# 41. MIGRATION SHOULD BE PHASED

Do NOT propose a one-shot:

> "delete Laravel and rewrite everything"

migration.

Design phases similar to:

## PHASE 0 — Audit and freeze contracts

## PHASE 1 — Django foundation and configuration

## PHASE 2 — Existing MySQL model adoption

## PHASE 3 — Authentication + roles + permissions + visibility

## PHASE 4 — DRF APIs by module

## PHASE 5 — Redis + Celery foundation

## PHASE 6 — Laravel jobs/schedules → Celery Worker + Beat

## PHASE 7 — React public-site parity

## PHASE 8 — React login/admin shell

## PHASE 9 — Admin modules

## PHASE 10 — Integrations and background workflows

## PHASE 11 — static/media/deployment/Gunicorn/Celery production services

## PHASE 12 — parity testing

## PHASE 13 — staging migration

## PHASE 14 — production cutover

## PHASE 15 — Laravel retirement after rollback window

Improve the phases after inspecting the actual system.

Every phase should contain:

- objective
- files/modules affected
- database impact
- implementation work
- Celery impact where relevant
- tests
- acceptance criteria
- rollback considerations
- dependencies on previous phases

---

# 42. ZERO-DATA-LOSS CUTOVER

The plan must include a production cutover strategy.

Before touching production:

- create verified database backup
- create repository/commit rollback point
- record current Laravel application version
- verify file/media backup
- verify `.env`
- verify integration credentials
- verify Redis availability
- test Django against a clone/snapshot of production data
- test Celery against non-production provider credentials or safely mocked providers
- verify all scheduled Laravel jobs have an equivalent Celery schedule

During initial database adoption, prefer read-only verification before allowing Django writes.

Do not use production as the first migration test.

---

# 43. CUTOVER OF SCHEDULERS

The production cutover must explicitly prevent BOTH Laravel Scheduler and Celery Beat from running the same business schedule simultaneously.

Define the cutover sequence.

For example:

1. confirm Celery Worker healthy
2. confirm Celery Beat configuration
3. pause/disable Laravel application scheduler
4. enable Celery Beat
5. verify scheduled task dispatch
6. verify task completion
7. monitor duplicate prevention

Do not leave dual scheduling active accidentally.

---

# 44. ROLLBACK AND CELERY

The rollback plan must account for background work.

If rolling back to Laravel:

- stop Celery Beat first
- stop or drain Celery Worker safely
- ensure no duplicated outbound actions remain queued
- determine whether queued tasks should be revoked, drained or retained
- restore Laravel scheduler only after Celery scheduling is disabled
- ensure idempotency keys protect external actions during transition

A rollback must not cause duplicate posts, messages or sync operations.

---

# 45. LARAVEL REMOVAL

Do not delete Laravel immediately.

The plan should identify when Laravel files can safely be removed.

Only after:

- database parity is proven
- authentication works
- permissions work
- public UI parity works
- admin parity works
- integrations work
- Redis works
- Celery Worker works
- Celery Beat works
- background retries and idempotency work
- deployment works
- production smoke tests pass
- rollback window has expired

should obsolete Laravel files be removed.

At that point identify removal candidates such as:

- `artisan`
- `composer.json`
- `composer.lock`
- `app/`
- Blade views
- Laravel config
- Laravel bootstrap
- PHP routes
- vendor dependencies

but retain any historical migration/reference material that is useful until the transition is fully certified.

---

# 46. DO NOT OVERENGINEER

This is a production corporate/marketing platform.

Do not introduce:

- Kubernetes
- microservices
- GraphQL
- Kafka
- unnecessary containers
- complex event buses
- unnecessary authentication systems
- SSR frameworks not requested

unless the actual repository proves a genuine requirement.

Prefer:

> **a clean modular Django monolith + DRF + React + MySQL + Redis + Celery**

Celery is intentionally part of the architecture because background processing and scheduled marketing/integration work are core requirements.

---

# 47. REQUIRED OUTPUT

Do not write code yet.

Return a document titled:

`PRADYTEC-DJANGO-REACT-MIGRATION-PLAN-01`

It must contain:

1. Executive summary
2. Current Laravel architecture discovered
3. Current module inventory
4. Current route inventory
5. Current database/table inventory
6. Existing auth/role/permission model
7. Integration inventory
8. Existing scheduled job/queue inventory
9. Current frontend/UI architecture
10. Current deployment architecture
11. Risks discovered
12. Proposed Django project architecture
13. Proposed Django app/module map
14. Proposed React architecture
15. Old route → new route matrix
16. Old Laravel model/table → Django model matrix
17. Authentication compatibility plan
18. Permission + visibility migration plan
19. encrypted-data compatibility plan
20. Redis architecture
21. Celery architecture
22. Laravel jobs → Celery task matrix
23. Celery Worker design
24. Celery Beat schedule matrix
25. Celery retry strategy
26. Celery idempotency strategy
27. Celery monitoring strategy
28. static/media strategy
29. API architecture
30. deployment/Gunicorn architecture
31. process supervision architecture
32. proposed `deploy.sh` workflow
33. environment-variable mapping
34. exact recommended dependencies
35. phased implementation plan
36. database safety/cutover plan
37. scheduler cutover plan
38. rollback strategy
39. testing/parity matrix
40. Celery/background-processing test matrix
41. README changes required
42. Laravel retirement criteria
43. unresolved questions found through inspection
44. final GO / CONDITIONAL GO / STOP assessment for beginning implementation

For every important recommendation, reference the actual current:

- file
- class
- model
- table
- route
- job
- command
- scheduler entry

that caused the recommendation.

Do not make assumptions where the repository can give you the answer.

The final plan should be detailed enough that a second AI agent could implement the migration phase-by-phase without having to rediscover the architecture.
