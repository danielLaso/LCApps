#!/bin/bash
set -e

echo "⏳ Waiting for database to be ready..."

until php -r "new PDO('mysql:host=mysql;dbname=LCApps', 'LCApps', 'LCApps');"; do
  echo "⏳ Not ready, waiting 2s..."
  sleep 2
done

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

/usr/local/bin/docker-php-entrypoint php-fpm
