#!/bin/bash
set -e

mkdir -p \
    /var/www/storage/app/public \
    /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/testing \
    /var/www/storage/framework/views \
    /var/www/storage/logs \
    /var/www/bootstrap/cache

# The project is bind-mounted from the host during development, so the
# container cannot rely on image-layer ownership. Grant explicit write access
# to Laravel runtime directories for php-fpm workers.
chmod -R 0777 /var/www/storage /var/www/bootstrap/cache

echo "Aguardando MySQL..."
while ! php -r "new PDO('mysql:host=mysql;port=3306;dbname=catalogo', 'laravel', 'secret');" 2>/dev/null; do
    sleep 1
done
echo "MySQL disponível."

php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache

exec php-fpm
