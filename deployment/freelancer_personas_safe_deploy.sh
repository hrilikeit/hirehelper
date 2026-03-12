#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/home/trustreview/web/hirehelper.ai/public_html/laravel_app"
PUBLIC_DIR="/home/trustreview/web/hirehelper.ai/public_html"
PHP_BIN="/usr/bin/php8.4"
APP_USER="trustreview"
WEB_GROUP="www-data"

if [ ! -d "$APP_DIR" ]; then
    echo "STOP: app directory not found: $APP_DIR"
    exit 1
fi

if [ ! -f "$APP_DIR/artisan" ]; then
    echo "STOP: artisan file not found in: $APP_DIR"
    exit 1
fi

if [ ! -f "$APP_DIR/.env" ]; then
    echo "STOP: .env file is missing in: $APP_DIR"
    exit 1
fi

cd "$APP_DIR"

echo "[1/8] Putting the site into maintenance mode"
runuser -u "$APP_USER" -- "$PHP_BIN" artisan down --render="errors::503" || true

echo "[2/8] Making sure writable folders exist"
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

chown -R "$APP_USER":"$WEB_GROUP" storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

echo "[3/8] Clearing old caches"
runuser -u "$APP_USER" -- "$PHP_BIN" artisan optimize:clear

echo "[4/8] Running migrations"
runuser -u "$APP_USER" -- "$PHP_BIN" artisan migrate --force

echo "[5/8] Rebuilding public storage link"
rm -f "$PUBLIC_DIR/storage"
ln -s "$APP_DIR/storage/app/public" "$PUBLIC_DIR/storage"
chown -h "$APP_USER":"$WEB_GROUP" "$PUBLIC_DIR/storage"

echo "[6/8] Rebuilding production caches"
runuser -u "$APP_USER" -- "$PHP_BIN" artisan config:cache
runuser -u "$APP_USER" -- "$PHP_BIN" artisan route:cache
runuser -u "$APP_USER" -- "$PHP_BIN" artisan view:cache

echo "[7/8] Reloading services"
systemctl reload php8.4-fpm || true
systemctl reload nginx || true

echo "[8/8] Bringing the site back online"
runuser -u "$APP_USER" -- "$PHP_BIN" artisan up || true

echo
echo "DONE"
echo
echo "One-time optional cleanup command for old demo freelancers:"
echo "runuser -u $APP_USER -- $PHP_BIN artisan db:seed --class=RemoveDemoFreelancersSeeder --force"
echo
echo "If anything still fails, check this file immediately:"
echo "$APP_DIR/storage/logs/laravel.log"
