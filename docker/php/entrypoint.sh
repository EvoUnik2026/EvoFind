#!/bin/sh
set -e
[ -d vendor ] || composer install --no-interaction
mkdir -p var public/uploads
chown -R www-data:www-data var public/uploads
php bin/console doctrine:schema:update --force --complete >/dev/null 2>&1 || true
exec "$@"
