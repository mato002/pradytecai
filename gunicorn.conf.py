"""Gunicorn configuration for production.

Inside Docker (`web` service): bind 0.0.0.0:8000.
Host publishes only 127.0.0.1:8100 → container :8000 (Apache proxies here).

Gunicorn is managed exclusively by Docker Compose through the `web` service.
Host systemd units pradytec-gunicorn / pradytecai-gunicorn are NOT used.
"""

import os
from pathlib import Path

try:
    from dotenv import load_dotenv

    load_dotenv(Path(__file__).resolve().parent / ".env")
except ImportError:
    pass

# Container default; override via GUNICORN_BIND in .env if needed.
bind = os.getenv("GUNICORN_BIND", "0.0.0.0:8000")

# Conservative on shared WHM — do not derive from full host CPU count.
workers = int(os.getenv("GUNICORN_WORKERS", "2"))
worker_class = "sync"
timeout = int(os.getenv("GUNICORN_TIMEOUT", "120"))
graceful_timeout = int(os.getenv("GUNICORN_GRACEFUL_TIMEOUT", "30"))
keepalive = 5
accesslog = "-"
errorlog = "-"
capture_output = True
preload_app = False
