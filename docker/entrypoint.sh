#!/bin/sh
# =============================================================================
# Docker Entrypoint Script for Laravel Octane + FrankenPHP
# Cashier POS API
# =============================================================================

set -e

echo "🚀 Starting Cashier POS API - ${CLIENT_NAME:-default}"
echo "📍 Environment: ${APP_ENV:-production}"

# Wait for database to be ready
if [ -n "$DB_HOST" ]; then
    echo "⏳ Waiting for database..."
    max_attempts=30
    attempt=0
    while ! nc -z "$DB_HOST" "${DB_PORT:-5432}" 2>/dev/null; do
        attempt=$((attempt + 1))
        if [ $attempt -ge $max_attempts ]; then
            echo "❌ Database connection timeout after ${max_attempts} attempts"
            exit 1
        fi
        echo "   Attempt $attempt/$max_attempts..."
        sleep 2
    done
    echo "✅ Database is ready"
fi

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Always run migrations on deployment
echo "🔄 Running database migrations..."
php artisan migrate --force

# Clear and cache configurations in production
if [ "${APP_ENV}" = "production" ]; then
    echo "⚡ Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "🎯 Starting FrankenPHP Octane Server..."
echo "   Workers: ${OCTANE_WORKERS:-4}"
echo "   Max Requests: ${OCTANE_MAX_REQUESTS:-1000}"

# Execute the main command
exec "$@"
