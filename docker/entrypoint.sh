#!/bin/sh
set -e

# Support dynamic PORT environment variable (Railway, Cloud Run, Render, Heroku)
PORT="${PORT:-80}"
echo "Configuring Nginx to listen on port ${PORT}..."
sed -i "s/listen 80/listen ${PORT}/g" /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default 2>/dev/null || true
sed -i "s/listen \[::\]:80/listen \[::\]:${PORT}/g" /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default 2>/dev/null || true

# Prepare Laravel environment file if missing
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        echo "Creating .env from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    fi
fi

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Ensure storage symlink exists
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link 2>/dev/null || true
fi

# Ensure build symlink exists
if [ ! -L /var/www/html/build ]; then
    ln -s /var/www/html/public/build /var/www/html/build 2>/dev/null || true
fi

# Fix permissions for Laravel storage and cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run database migrations if explicitly enabled
if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Cache configuration if running in production
if [ "${APP_ENV}" = "production" ]; then
    echo "Optimizing application for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

exec "$@"
