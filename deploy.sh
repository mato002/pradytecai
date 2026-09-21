#!/usr/bin/env bash
# Manual production deploy for pradytecai.
# Does NOT run on git push or cron — trigger it yourself:
#   cd /home/pradytec/pradytecai && ./deploy.sh
#
# Optional overrides:
#   PUBLIC_HTML=/path/to/public_html DEPLOY_BRANCH=main ./deploy.sh

set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PUBLIC_HTML="${PUBLIC_HTML:-/home/pradytec/pradytecai/public_html}"
BRANCH="${DEPLOY_BRANCH:-main}"

cd "$APP_DIR"

echo "==> Deploying from $(pwd) (branch: ${BRANCH})"
echo "    Web root: ${PUBLIC_HTML}"
echo

echo "==> 1/7 git pull"
git pull origin "${BRANCH}"

echo "==> 2/7 composer install"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> 3/7 migrate"
php artisan migrate --force

echo "==> 4/7 build frontend assets"
if ! command -v npm >/dev/null 2>&1; then
  echo "ERROR: npm not found. Install Node.js on the server, or build locally and place public/build/ here first."
  exit 1
fi
if [[ -f package-lock.json ]]; then
  npm ci
else
  npm install
fi
npm run build

if [[ ! -f public/build/manifest.json ]]; then
  echo "ERROR: public/build/manifest.json missing after build."
  exit 1
fi

echo "==> 5/7 sync public/build -> ${PUBLIC_HTML}/build"
mkdir -p "${PUBLIC_HTML}/build"
if command -v rsync >/dev/null 2>&1; then
  rsync -a --delete "${APP_DIR}/public/build/" "${PUBLIC_HTML}/build/"
else
  rm -rf "${PUBLIC_HTML}/build"
  mkdir -p "${PUBLIC_HTML}"
  cp -a "${APP_DIR}/public/build" "${PUBLIC_HTML}/build"
fi

echo "==> 6/7 clear caches"
php artisan optimize:clear

echo "==> 7/7 rebuild caches"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo
echo "Deploy complete."
