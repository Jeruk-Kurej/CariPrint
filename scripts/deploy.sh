#!/usr/bin/env bash
# Deploy the latest main to the CloudPanel server. Run on the server, from the site folder.
set -euo pipefail

SITE_DIR="${SITE_DIR:-$HOME/htdocs/cariprint.lius.my.id}"
cd "$SITE_DIR"

echo "==> Pull latest main"
git pull --ff-only origin main

echo "==> Install PHP dependencies (no dev packages)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Run migrations"
php artisan migrate --force

echo "==> Build frontend"
npm ci
npm run build

echo "==> Refresh caches"
php artisan optimize:clear
php artisan optimize

echo "Deploy selesai."
