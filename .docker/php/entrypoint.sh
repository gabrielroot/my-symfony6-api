#!/bin/sh
set -e

# Fix permissions for var/ directory (cache, logs, sessions)
# Run as root if possible, otherwise just ensure dirs exist
if [ -d /var/www/symfony/var ]; then
    chmod -R 777 /var/www/symfony/var 2>/dev/null || true
fi

# Ensure cache directory exists and is writable
mkdir -p /var/www/symfony/var/cache/prod 2>/dev/null || true
chmod -R 777 /var/www/symfony/var/cache 2>/dev/null || true

php bin/console d:m:migrate -n

# Execute the main container command (e.g., php-fpm)
exec "$@"
