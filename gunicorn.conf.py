"""Gunicorn configuration for production (WHM / Linux).

Default: bind 127.0.0.1:8100 (port 8000 is taken by another app).
Override with GUNICORN_BIND / GUNICORN_WORKERS in .env.
"""

import multiprocessing
import os
from pathlib import Path

try:
    from dotenv import load_dotenv

    load_dotenv(Path(__file__).resolve().parent / ".env")
except ImportError:
    pass

# Behind Apache/Nginx: 127.0.0.1:8100
# Direct IP access: 0.0.0.0:8100 (open CSF if needed)
bind = os.getenv("GUNICORN_BIND", "127.0.0.1:8100")

# Keep workers modest on shared VPS
_default_workers = min(3, max(2, multiprocessing.cpu_count()))
workers = int(os.getenv("GUNICORN_WORKERS", str(_default_workers)))
worker_class = "sync"
timeout = int(os.getenv("GUNICORN_TIMEOUT", "120"))
graceful_timeout = 30
keepalive = 5
accesslog = "-"
errorlog = "-"
capture_output = True
preload_app = False
