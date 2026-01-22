#!/bin/sh
set -e

yarn dev

# Fix permissions for var/ directory (cache, logs, sessions)
# Run as root if possible, otherwise just ensure dirs exist
if [ -d /var/www/symfony/var ]; then
    chmod -R 777 /var/www/symfony/var 2>/dev/null || true
fi

# Ensure cache directory exists and is writable
mkdir -p /var/www/symfony/var/cache/dev 2>/dev/null || true
chmod -R 777 /var/www/symfony/var/cache 2>/dev/null || true

# Clear and rebuild cache on container start (fixes stale cache from volume)
rm -rf /var/www/symfony/var/cache/* 2>/dev/null || true
cd /var/www/symfony && APP_ENV=dev APP_DEBUG=0 php bin/console cache:warmup 2>/dev/null || true

# Fix permissions for public/ directory (assets)
if [ -d /var/www/symfony/public ]; then
    chmod -R 775 /var/www/symfony/public 2>/dev/null || true
fi

php bin/console d:m:migrate -n

# Execute the main container command (e.g., php-fpm)
exec "$@"
