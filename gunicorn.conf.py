"""Gunicorn configuration for production.

Inside Docker (`web` service): bind 0.0.0.0:8100 (internal).
Host publishes only 127.0.0.1:8000 → container :8100 (Apache proxies here).

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

# Container internal port 8100; host maps 127.0.0.1:8000:8100
bind = os.getenv("GUNICORN_BIND", "0.0.0.0:8100")

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
