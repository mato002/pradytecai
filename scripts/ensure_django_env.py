"""Append Django isolation keys to .env if missing. Do not print secret values."""
from pathlib import Path
import secrets

p = Path(".env")
text = p.read_text(encoding="utf-8") if p.exists() else ""


def need(key: str) -> bool:
    return not any(line.startswith(key + "=") for line in text.splitlines())


additions = []
if need("DJANGO_SECRET_KEY"):
    additions.append("DJANGO_SECRET_KEY=" + secrets.token_urlsafe(50))
if need("DEBUG"):
    additions.append("DEBUG=true")
if need("DJANGO_DB_CONNECTION"):
    additions.append("DJANGO_DB_CONNECTION=sqlite")
if need("DJANGO_DB_NAME"):
    additions.append("DJANGO_DB_NAME=db.sqlite3")
if need("SESSION_COOKIE_NAME"):
    additions.append("SESSION_COOKIE_NAME=pradytecai_django_session")
if need("CSRF_COOKIE_NAME"):
    additions.append("CSRF_COOKIE_NAME=pradytecai_django_csrftoken")
if need("ALLOWED_HOSTS"):
    additions.append("ALLOWED_HOSTS=localhost,127.0.0.1")
if need("CSRF_TRUSTED_ORIGINS"):
    additions.append(
        "CSRF_TRUSTED_ORIGINS=http://localhost:8000,http://127.0.0.1:8000"
    )

if additions:
    block = (
        "\n\n# --- Django isolation (auto-appended; safe alongside Laravel) ---\n"
        + "\n".join(additions)
        + "\n"
    )
    p.write_text(text.rstrip() + block, encoding="utf-8")
    print(f"appended {len(additions)} Django keys to .env")
else:
    print("Django isolation keys already present")
