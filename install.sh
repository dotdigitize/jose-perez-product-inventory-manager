#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
command -v php >/dev/null || { echo 'PHP is required.' >&2; exit 1; }
command -v composer >/dev/null || { echo 'Composer is required.' >&2; exit 1; }
php -r 'exit(PHP_VERSION_ID >= 80300 ? 0 : 1);' || { echo 'PHP 8.3 or newer is required.' >&2; exit 1; }
if [ ! -f .env ]; then
  printf 'APP_NAME="Product Inventory Manager"\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost\nLOG_CHANNEL=stack\nLOG_LEVEL=debug\nDB_CONNECTION=sqlite\nSESSION_DRIVER=file\nCACHE_STORE=file\nQUEUE_CONNECTION=database\n' > .env
fi
composer install --no-interaction --prefer-dist
php artisan key:generate --force
mkdir -p storage/app/private
[ -f storage/app/private/products.json ] || printf '[]\n' > storage/app/private/products.json
chmod 0750 storage/app/private
chmod 0640 storage/app/private/products.json
chmod -R ug+rwX storage bootstrap/cache
echo 'Ready. Run php artisan serve.'
