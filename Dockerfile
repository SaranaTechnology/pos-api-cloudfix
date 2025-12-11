# =============================================================================
# Laravel Octane + FrankenPHP Production Dockerfile
# Cashier POS API - Multi-client deployment via ECR
# =============================================================================

# Build stage for dependencies
FROM dunglas/frankenphp:php8.3-alpine AS builder

# Install build dependencies
RUN apk add --no-cache \
    curl \
    unzip \
    git \
    nodejs \
    npm \
    $PHPIZE_DEPS

# Install PHP extensions
RUN install-php-extensions \
    bcmath \
    pdo_pgsql \
    pdo_mysql \
    xml \
    mbstring \
    zip \
    curl \
    pcntl \
    gd \
    opcache \
    redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package files for npm
COPY package.json package-lock.json* ./
RUN npm ci --only=production || npm install --only=production

# Copy application code
COPY . .

# Generate optimized autoloader and build assets
RUN composer dump-autoload --optimize \
    && npm run build 2>/dev/null || true

# =============================================================================
# Production stage
# =============================================================================
FROM dunglas/frankenphp:php8.3-alpine AS production

# Build arguments for client configuration
ARG CLIENT_NAME=default
ARG APP_ENV=production
ARG OCTANE_WORKERS=4
ARG OCTANE_MAX_REQUESTS=1000

# Environment variables
ENV SERVER_NAME=":8000" \
    APP_ENV=${APP_ENV} \
    APP_DEBUG=false \
    OCTANE_SERVER=frankenphp \
    OCTANE_WORKERS=${OCTANE_WORKERS} \
    OCTANE_MAX_REQUESTS=${OCTANE_MAX_REQUESTS} \
    CLIENT_NAME=${CLIENT_NAME} \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info

# Install runtime PHP extensions
RUN install-php-extensions \
    bcmath \
    pdo_pgsql \
    pdo_mysql \
    xml \
    mbstring \
    zip \
    curl \
    pcntl \
    gd \
    opcache \
    redis

# Install runtime dependencies
RUN apk add --no-cache \
    curl \
    ca-certificates \
    tzdata \
    netcat-openbsd

# Create non-root user for security
RUN addgroup -g 1000 appgroup \
    && adduser -u 1000 -G appgroup -s /bin/sh -D appuser

WORKDIR /app

# Copy application from builder
COPY --from=builder --chown=appuser:appgroup /app /app

# Create necessary directories with proper permissions
RUN mkdir -p /app/storage/logs \
    /app/storage/framework/cache/data \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/bootstrap/cache \
    && chown -R appuser:appgroup /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Copy custom PHP configuration
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy entrypoint script before switching user
COPY --chmod=755 docker/entrypoint.sh /entrypoint.sh

# Health check configuration
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:8000/api/health || exit 1

# Expose port
EXPOSE 8000

# Set ownership for entrypoint
RUN chown appuser:appgroup /entrypoint.sh

# Switch to non-root user
USER appuser

# Use entrypoint for migrations
ENTRYPOINT ["/entrypoint.sh"]

# Default command - FrankenPHP with Octane
CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000"]

