"""
Django settings for PradytecAI.

Coexistence with Laravel in the same repo:
- Prefer DJANGO_* env vars over Laravel DB_* / APP_* where they conflict.
- Never open Laravel's database/database.sqlite unless explicitly forced.
- Use distinct session/CSRF cookie names so Laravel and Django do not clobber each other.
"""
import os
from pathlib import Path

from dotenv import load_dotenv

BASE_DIR = Path(__file__).resolve().parent.parent.parent

load_dotenv(BASE_DIR / ".env")

SECRET_KEY = os.getenv(
    "DJANGO_SECRET_KEY",
    os.getenv("APP_KEY", "django-insecure-change-me-set-DJANGO_SECRET_KEY"),
)
DEBUG = os.getenv("DEBUG", os.getenv("APP_DEBUG", "true")).lower() in ("1", "true", "yes")
ALLOWED_HOSTS = [
    h.strip()
    for h in os.getenv("ALLOWED_HOSTS", "localhost,127.0.0.1").split(",")
    if h.strip()
]

CSRF_TRUSTED_ORIGINS = [
    o.strip()
    for o in os.getenv(
        "CSRF_TRUSTED_ORIGINS", "http://localhost:8000,http://127.0.0.1:8000"
    ).split(",")
    if o.strip()
]

INSTALLED_APPS = [
    "django.contrib.admin",
    "django.contrib.auth",
    "django.contrib.contenttypes",
    "django.contrib.sessions",
    "django.contrib.messages",
    "django.contrib.staticfiles",
    "rest_framework",
    "django_celery_beat",
    "apps.core",
    "apps.accounts",
    "apps.products",
    "apps.leads",
    "apps.campaigns",
    "apps.content",
    "apps.social",
    "apps.analytics",
    "apps.careers",
    "apps.marketing",
    "apps.tasks",
    "apps.audit",
]

MIDDLEWARE = [
    "django.middleware.security.SecurityMiddleware",
    "django.contrib.sessions.middleware.SessionMiddleware",
    "django.middleware.common.CommonMiddleware",
    "django.middleware.csrf.CsrfViewMiddleware",
    "django.contrib.auth.middleware.AuthenticationMiddleware",
    "django.contrib.messages.middleware.MessageMiddleware",
    "django.middleware.clickjacking.XFrameOptionsMiddleware",
    "apps.core.middleware.ProductFilterMiddleware",
    "apps.core.middleware.CaptureUtmMiddleware",
]

ROOT_URLCONF = "config.urls"

TEMPLATES = [
    {
        "BACKEND": "django.template.backends.django.DjangoTemplates",
        "DIRS": [BASE_DIR / "templates"],
        "APP_DIRS": True,
        "OPTIONS": {
            "context_processors": [
                "django.template.context_processors.request",
                "django.contrib.auth.context_processors.auth",
                "django.contrib.messages.context_processors.messages",
            ],
        },
    },
]

WSGI_APPLICATION = "config.wsgi.application"

# ---------------------------------------------------------------------------
# Database isolation from Laravel
# Prefer DJANGO_DB_* ; fall back to Laravel DB_* only for MySQL cutover.
# ---------------------------------------------------------------------------
_LARAVEL_SQLITE = (BASE_DIR / "database" / "database.sqlite").resolve()
_db_engine = (
    os.getenv("DJANGO_DB_CONNECTION")
    or os.getenv("DB_CONNECTION")
    or "sqlite"
).lower()
_use_mysql = _db_engine in ("mysql", "mariadb") and bool(
    os.getenv("DJANGO_DB_HOST") or os.getenv("DB_HOST")
)

if not _use_mysql:
    _sqlite_name = os.getenv("DJANGO_DB_NAME") or "db.sqlite3"
    _sqlite_path = Path(_sqlite_name)
    if not _sqlite_path.is_absolute():
        _sqlite_path = BASE_DIR / _sqlite_path
    # Refuse Laravel's sqlite file unless explicitly allowed
    if (
        _sqlite_path.resolve() == _LARAVEL_SQLITE
        and os.getenv("DJANGO_USE_LARAVEL_SQLITE", "").lower()
        not in ("1", "true", "yes")
    ):
        _sqlite_path = BASE_DIR / "db.sqlite3"
    DATABASES = {
        "default": {
            "ENGINE": "django.db.backends.sqlite3",
            "NAME": str(_sqlite_path),
        }
    }
else:
    import pymysql

    pymysql.install_as_MySQLdb()
    DATABASES = {
        "default": {
            "ENGINE": "django.db.backends.mysql",
            "NAME": os.getenv("DJANGO_DB_NAME") or os.getenv("DB_DATABASE", "pradytec_prady"),
            "USER": os.getenv("DJANGO_DB_USER") or os.getenv("DB_USERNAME", "pradytec_prady"),
            "PASSWORD": os.getenv("DJANGO_DB_PASSWORD") or os.getenv("DB_PASSWORD", ""),
            "HOST": os.getenv("DJANGO_DB_HOST") or os.getenv("DB_HOST", "127.0.0.1"),
            "PORT": os.getenv("DJANGO_DB_PORT") or os.getenv("DB_PORT", "3306"),
            "OPTIONS": {"charset": "utf8mb4"},
        }
    }

AUTH_USER_MODEL = "accounts.User"

AUTH_PASSWORD_VALIDATORS = [
    {"NAME": "django.contrib.auth.password_validation.UserAttributeSimilarityValidator"},
    {"NAME": "django.contrib.auth.password_validation.MinimumLengthValidator"},
    {"NAME": "django.contrib.auth.password_validation.CommonPasswordValidator"},
    {"NAME": "django.contrib.auth.password_validation.NumericPasswordValidator"},
]

PASSWORD_HASHERS = [
    "apps.accounts.hashers.LaravelBcryptPasswordHasher",
    "django.contrib.auth.hashers.BCryptSHA256PasswordHasher",
    "django.contrib.auth.hashers.BCryptPasswordHasher",
    "django.contrib.auth.hashers.PBKDF2PasswordHasher",
    "django.contrib.auth.hashers.PBKDF2SHA1PasswordHasher",
    "django.contrib.auth.hashers.Argon2PasswordHasher",
]

AUTHENTICATION_BACKENDS = [
    "apps.accounts.backends.LaravelCompatibleBackend",
    "django.contrib.auth.backends.ModelBackend",
]

LANGUAGE_CODE = "en-us"
TIME_ZONE = "Africa/Nairobi"
USE_I18N = True
USE_TZ = True

STATIC_URL = "/static/"
STATIC_ROOT = BASE_DIR / "staticfiles"
STATICFILES_DIRS = [BASE_DIR / "static"]
_REACT_DIST = BASE_DIR / "react" / "dist"
if _REACT_DIST.exists():
    STATICFILES_DIRS.append(_REACT_DIST)

MEDIA_URL = "/media/"
MEDIA_ROOT = BASE_DIR / "media"

DEFAULT_AUTO_FIELD = "django.db.models.BigAutoField"

# Distinct cookies so a concurrent Laravel session cannot collide
SESSION_ENGINE = "django.contrib.sessions.backends.db"
SESSION_COOKIE_NAME = os.getenv("SESSION_COOKIE_NAME", "pradytecai_django_session")
CSRF_COOKIE_NAME = os.getenv("CSRF_COOKIE_NAME", "pradytecai_django_csrftoken")
SESSION_COOKIE_AGE = int(os.getenv("SESSION_LIFETIME", "120")) * 60
SESSION_COOKIE_HTTPONLY = True
SESSION_COOKIE_SAMESITE = "Lax"
SESSION_COOKIE_SECURE = os.getenv("SESSION_SECURE_COOKIE", "false").lower() in (
    "1",
    "true",
    "yes",
)
# Production HTTPS: set SESSION_SECURE_COOKIE=true (and DEBUG=false)
if not DEBUG and os.getenv("SESSION_SECURE_COOKIE") is None:
    SESSION_COOKIE_SECURE = True
CSRF_COOKIE_SAMESITE = "Lax"
CSRF_COOKIE_SECURE = SESSION_COOKIE_SECURE
# Behind Apache/Nginx TLS termination
SECURE_PROXY_SSL_HEADER = ("HTTP_X_FORWARDED_PROTO", "https")
USE_X_FORWARDED_HOST = os.getenv("USE_X_FORWARDED_HOST", "true").lower() in (
    "1",
    "true",
    "yes",
)

REST_FRAMEWORK = {
    "DEFAULT_AUTHENTICATION_CLASSES": [
        "rest_framework.authentication.SessionAuthentication",
    ],
    "DEFAULT_PERMISSION_CLASSES": [
        "rest_framework.permissions.IsAuthenticated",
    ],
    "DEFAULT_PAGINATION_CLASS": "rest_framework.pagination.PageNumberPagination",
    "PAGE_SIZE": 25,
}

REDIS_HOST = os.getenv("REDIS_HOST", "127.0.0.1")
REDIS_PORT = os.getenv("REDIS_PORT", "6379")
_raw_redis_pw = (os.getenv("REDIS_PASSWORD") or "").strip()
REDIS_PASSWORD = (
    None
    if not _raw_redis_pw or _raw_redis_pw.lower() in ("null", "none", "nil")
    else _raw_redis_pw
)
_redis_auth = f":{REDIS_PASSWORD}@" if REDIS_PASSWORD else ""
CELERY_BROKER_URL = os.getenv(
    "CELERY_BROKER_URL",
    f"redis://{_redis_auth}{REDIS_HOST}:{REDIS_PORT}/0",
)
CELERY_RESULT_BACKEND = os.getenv(
    "CELERY_RESULT_BACKEND",
    f"redis://{_redis_auth}{REDIS_HOST}:{REDIS_PORT}/1",
)
CELERY_TIMEZONE = "UTC"
CELERY_ENABLE_UTC = True
CELERY_ACCEPT_CONTENT = ["json"]
CELERY_TASK_SERIALIZER = "json"
CELERY_RESULT_SERIALIZER = "json"
CELERY_TASK_ACKS_LATE = True
CELERY_TASK_REJECT_ON_WORKER_LOST = True
CELERY_BEAT_SCHEDULER = "django_celery_beat.schedulers:DatabaseScheduler"

# ---------------------------------------------------------------------------
# Celery resource limits — keep workers from overwhelming the server
# Override via .env (see .env.example). Defaults suit a shared WHM/VPS.
# ---------------------------------------------------------------------------
CELERY_WORKER_CONCURRENCY = int(os.getenv("CELERY_WORKER_CONCURRENCY", "1"))
CELERY_WORKER_PREFETCH_MULTIPLIER = int(os.getenv("CELERY_WORKER_PREFETCH_MULTIPLIER", "1"))
CELERY_TASK_ACKS_ON_FAILURE_OR_TIMEOUT = True
CELERY_TASK_SOFT_TIME_LIMIT = int(os.getenv("CELERY_TASK_SOFT_TIME_LIMIT", "240"))  # seconds
CELERY_TASK_TIME_LIMIT = int(os.getenv("CELERY_TASK_TIME_LIMIT", "300"))
CELERY_WORKER_MAX_TASKS_PER_CHILD = int(os.getenv("CELERY_WORKER_MAX_TASKS_PER_CHILD", "50"))
# Kilobytes; recycle child after ~200MB RSS growth tendencies
CELERY_WORKER_MAX_MEMORY_PER_CHILD = int(
    os.getenv("CELERY_WORKER_MAX_MEMORY_PER_CHILD", "200000")
)
CELERY_BROKER_POOL_LIMIT = int(os.getenv("CELERY_BROKER_POOL_LIMIT", "5"))
CELERY_REDIS_MAX_CONNECTIONS = int(os.getenv("CELERY_REDIS_MAX_CONNECTIONS", "10"))
CELERY_TASK_DEFAULT_RATE_LIMIT = os.getenv("CELERY_TASK_DEFAULT_RATE_LIMIT", "20/m")
CELERY_WORKER_SEND_TASK_EVENTS = False
CELERY_TASK_SEND_SENT_EVENT = False
# Do not store large results forever
CELERY_RESULT_EXPIRES = int(os.getenv("CELERY_RESULT_EXPIRES", "3600"))
CELERY_TASK_IGNORE_RESULT = os.getenv("CELERY_TASK_IGNORE_RESULT", "true").lower() in (
    "1",
    "true",
    "yes",
)

EMAIL_BACKEND = os.getenv(
    "EMAIL_BACKEND",
    "django.core.mail.backends.console.EmailBackend"
    if DEBUG
    else "django.core.mail.backends.smtp.EmailBackend",
)
EMAIL_HOST = os.getenv("MAIL_HOST", "127.0.0.1")
EMAIL_PORT = int(os.getenv("MAIL_PORT", "2525"))
EMAIL_HOST_USER = os.getenv("MAIL_USERNAME", "")
EMAIL_HOST_PASSWORD = os.getenv("MAIL_PASSWORD", "")
EMAIL_USE_TLS = os.getenv("MAIL_SCHEME", "") == "tls"
DEFAULT_FROM_EMAIL = os.getenv("MAIL_FROM_ADDRESS", "hello@example.com")

BULKSMS_CRM_ENABLED = os.getenv("BULKSMS_CRM_ENABLED", "false").lower() in (
    "1",
    "true",
    "yes",
)
BULKSMS_API_URL = os.getenv("BULKSMS_API_URL", "https://crm.pradytecai.com/api")
BULKSMS_API_KEY = os.getenv("BULKSMS_API_KEY", "")
BULKSMS_CLIENT_ID = os.getenv("BULKSMS_CLIENT_ID", "1")
BULKSMS_SENDER_ID = os.getenv("BULKSMS_SENDER_ID", "")

ULTRAMSG_API_URL = os.getenv("ULTRAMSG_API_URL", "https://api.ultramsg.com")
ULTRAMSG_INSTANCE_ID = os.getenv("ULTRAMSG_INSTANCE_ID", "")
ULTRAMSG_TOKEN = os.getenv("ULTRAMSG_TOKEN", "")

BUFFER_ENABLED = os.getenv("BUFFER_ENABLED", "false").lower() in ("1", "true", "yes")
BUFFER_CLIENT_ID = os.getenv("BUFFER_CLIENT_ID", "")
BUFFER_CLIENT_SECRET = os.getenv("BUFFER_CLIENT_SECRET", "")
BUFFER_ACCESS_TOKEN = os.getenv("BUFFER_ACCESS_TOKEN", "")

GA4_PROPERTY_ID = os.getenv("GA4_PROPERTY_ID", "")
GA4_CREDENTIALS_JSON = os.getenv("GA4_CREDENTIALS_JSON", "")

LARAVEL_APP_KEY = os.getenv("APP_KEY", "")

LOGIN_URL = "/login"
LOGIN_REDIRECT_URL = "/admin"
LOGOUT_REDIRECT_URL = "/login"
