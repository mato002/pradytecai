# Legacy host-level systemd units
#
# NOT USED BY DOCKER PRODUCTION DEPLOYMENT.
#
# Gunicorn runs inside the Docker Compose `web` service.
# Celery Worker runs inside the Docker Compose `worker` service.
# Celery Beat runs inside the Docker Compose `beat` service.
# PostgreSQL runs inside the Docker Compose `postgres` service.
#
# Do NOT install or enable these units on production:
#   pradytec-gunicorn.service
#   pradytecai-gunicorn.service
#   pradytec-celery-worker.service
#   pradytec-celery-beat.service
#
# Correct operations:
#   docker compose up -d
#   docker compose restart web
#   docker compose logs -f web
