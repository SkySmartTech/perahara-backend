#!/bin/bash
set -e

echo "Starting Laravel Application Setup..."

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "vendor directory not found, running composer install..."
    composer install --no-interaction --optimize-autoloader
fi

echo "Running frontend build with npm..."
npm install --legacy-peer-deps
npm run build



echo "Waiting for MySQL to be ready..."
echo "Environment variables:"
echo "DB_HOST: $DB_HOST"
echo "DB_USERNAME: $DB_USERNAME"
echo "DB_DATABASE: $DB_DATABASE"

until mariadb -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl -e "SELECT 1" >/dev/null 2>&1; do
    echo "MySQL is unavailable - sleeping for 2 seconds..."
    sleep 2
done
echo "MySQL is ready!"

chown -R application:application /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Clearing all caches..."
rm -rf /var/www/html/bootstrap/cache/*.php 2>/dev/null || true
rm -rf /var/www/html/storage/framework/cache/data/* 2>/dev/null || true
rm -rf /var/www/html/storage/framework/views/* 2>/dev/null || true
php artisan config:clear 2>/dev/null || true

echo "Ensuring application key is properly set..."
php artisan key:generate --force
echo "Application key generated and verified!"

echo "Verifying application key is loaded..."
APP_KEY_CONFIG=$(php artisan config:show app.key 2>/dev/null || echo "")
if [[ -z "$APP_KEY_CONFIG" || "$APP_KEY_CONFIG" == "app.key" ]]; then
    echo "ERROR: Application key still not properly loaded after generation!"
    exit 1
else
    echo "Application key successfully verified: ${APP_KEY_CONFIG:0:20}..."
fi

echo "Checking database status..."
set +e
php artisan migrate:status >/dev/null 2>&1
DB_CHECK_EXIT=$?
set -e

if [ "$DB_CHECK_EXIT" -ne 0 ]; then
    # migrations table does not exist → fresh database
    echo "Fresh database detected. Running migrate:fresh..."
    php artisan migrate:fresh --force --no-interaction
    echo "Seeding database..."
    php artisan db:seed --force --no-interaction
else
    # migrations table exists → only apply pending migrations
    echo "Existing database detected. Checking for pending migrations..."
    php artisan migrate --force --no-interaction
fi

echo "Clearing application cache..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true  
php artisan view:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || echo "Cache clear skipped (cache table may not exist yet)"

echo "Caching configuration for production..."
php artisan config:cache
if [ $? -eq 0 ]; then
    echo "Configuration cached successfully!"
else
    echo "Warning: Configuration caching failed!"
fi

php artisan route:cache
if [ $? -eq 0 ]; then
    echo "Routes cached successfully!"
else
    echo "Warning: Route caching failed!"
fi

php artisan view:cache
if [ $? -eq 0 ]; then
    echo "Views cached successfully!"
else
    echo "Warning: View caching failed!"
fi

echo "Laravel application setup completed successfully!"

exec "$@"
