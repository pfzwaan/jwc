#!/bin/sh
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate --force --ansi
fi

echo "Waiting for database at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
until php artisan db:show >/dev/null 2>&1; do
    sleep 2
done
echo "Database is up."

php artisan migrate --force

# Seed only on first boot — re-seeding on every restart would silently
# overwrite real content (settings/pages/diensten) staff have since edited.
if [ ! -f storage/.seeded ]; then
    php artisan db:seed --force
    touch storage/.seeded
fi

php artisan storage:link || true

if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec php artisan serve --host=0.0.0.0 --port=8002
