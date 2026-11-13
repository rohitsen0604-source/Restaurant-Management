#!/bin/bash
set -e

PORT="${PORT:-80}"

echo "Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen [0-9]*/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure writable directories exist and have proper permissions
mkdir -p /var/www/html/uploads/excel /var/www/html/application/cache /var/www/html/application/logs
chown -R www-data:www-data /var/www/html/uploads /var/www/html/application/cache /var/www/html/application/logs
chmod -R 775 /var/www/html/uploads /var/www/html/application/cache /var/www/html/application/logs

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground

