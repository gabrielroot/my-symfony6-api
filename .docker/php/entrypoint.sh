#!/bin/sh
set -e

# Fix permissions for var/ directory (cache, logs, sessions)
# Run as root if possible, otherwise just ensure dirs exist
if [ -d /var/www/var ]; then
    chmod -R 777 /var/www/var 2>/dev/null || true
fi

# Clear and rebuild cache on container start (fixes stale cache from volume)
cd /var/www && APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup --env=prod 2>/dev/null || true

# Ensure cache directory exists and is writable
mkdir -p /var/www/var/cache/prod 2>/dev/null || true
chmod -R 777 /var/www/var/cache 2>/dev/null || true

# Fix permissions for public/ directory (assets)
if [ -d /var/www/public ]; then
    chmod -R 775 /var/www/public 2>/dev/null || true
fi

php bin/console d:m:migrate -n --env=prod

# Execute the main container command (e.g., php-fpm)
exec "$@"
