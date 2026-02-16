#!/bin/sh
set -e

# Fix permissions on mounted volumes (they may be owned by root)
echo "Setting storage permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if it doesn't exist
php artisan storage:link 2>/dev/null || true

# Wait for MySQL to be ready (use artisan to attempt a real connection)
echo "Waiting for database..."
max_retries=30
count=0
until php artisan migrate:status > /dev/null 2>&1 || [ $count -ge $max_retries ]; do
    echo "Database not ready yet... retrying ($((count+1))/$max_retries)"
    count=$((count+1))
    sleep 2
done

if [ $count -ge $max_retries ]; then
    echo "Warning: Could not confirm database is ready, proceeding anyway..."
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders if requested
if [ "$DB_SEED" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force
fi

echo "Application ready."

exec "$@"
