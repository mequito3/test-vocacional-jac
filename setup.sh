#!/usr/bin/env bash
set -Eeuo pipefail

# La aplicacion queda privada en ~/jac. Solo public/ se publica en public_html.
APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PUBLIC_ROOT="${PUBLIC_ROOT:-$HOME/domains/jac2000.americolabs.com/public_html}"
TEST_PUBLIC_ROOT="${TEST_PUBLIC_ROOT:-$PUBLIC_ROOT/test}"

cleanup() {
    if [[ -f "$APP_ROOT/artisan" && -f "$APP_ROOT/vendor/autoload.php" ]]; then
        cd "$APP_ROOT"
        php artisan up || true
    fi
}
trap cleanup EXIT

if [[ ! -d "$PUBLIC_ROOT" ]]; then
    echo "ERROR: no existe $PUBLIC_ROOT." >&2
    exit 1
fi

for command_name in php composer rsync git; do
    if ! command -v "$command_name" >/dev/null 2>&1; then
        echo "ERROR: $command_name no esta disponible en el servidor." >&2
        exit 1
    fi
done

php -r 'if (version_compare(PHP_VERSION, "8.2.0", "<")) { fwrite(STDERR, "ERROR: se requiere PHP 8.2 o superior.\n"); exit(1); }'

# Migracion unica desde el despliegue antiguo, donde .env estaba expuesto bajo
# public_html. Nunca se sobrescribe un .env privado que ya exista.
if [[ ! -f "$APP_ROOT/.env" && -f "$PUBLIC_ROOT/.env" ]]; then
    cp "$PUBLIC_ROOT/.env" "$APP_ROOT/.env"
    chmod 600 "$APP_ROOT/.env"
    echo "Configuracion movida a $APP_ROOT/.env"
fi

# Conserva posibles archivos subidos del esquema anterior.
if [[ -d "$PUBLIC_ROOT/storage/app" ]]; then
    mkdir -p "$APP_ROOT/storage/app"
    rsync -a "$PUBLIC_ROOT/storage/app/" "$APP_ROOT/storage/app/"
fi

if [[ ! -f "$APP_ROOT/.env" ]]; then
    echo "ERROR: falta $APP_ROOT/.env." >&2
    exit 1
fi

cd "$APP_ROOT"

# Evita publicar una mezcla accidental de archivos confirmados y cambios locales.
if ! git diff --quiet || ! git diff --cached --quiet; then
    echo "ERROR: el clon tiene cambios de codigo sin confirmar." >&2
    exit 1
fi

php artisan down --retry=30 || true

# Dependencias y caches se construyen fuera de la carpeta publica.
rm -f bootstrap/cache/*.php
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

php artisan optimize:clear

# No permitir un deploy con configuracion insegura o incompleta.
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$password = config("app.admin_password");
$safe = app()->environment("production")
    && config("app.debug") === false
    && is_string(config("app.key")) && config("app.key") !== ""
    && is_string($password) && strlen($password) >= 12;
if (! $safe) {
    fwrite(STDERR, "ERROR: revisa APP_ENV, APP_DEBUG, APP_KEY y ADMIN_PASSWORD en .env.\n");
    exit(1);
}
'

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# public_html pasa a contener exclusivamente el contenido de public/. Se conserva
# .well-known porque Hostinger puede usarlo para validaciones SSL.
rsync -a --delete \
    --exclude='/.well-known/' \
    --exclude='/test/' \
    "$APP_ROOT/public/" "$PUBLIC_ROOT/"

mkdir -p "$TEST_PUBLIC_ROOT"
rsync -a --delete \
    --exclude='/.well-known/' \
    "$APP_ROOT/public/" "$TEST_PUBLIC_ROOT/"

# Elimina restos sensibles conocidos del esquema de despliegue anterior.
rm -f "$PUBLIC_ROOT/.env" "$PUBLIC_ROOT/diag.txt" "$PUBLIC_ROOT/setup.sh"

echo "Deploy terminado: Laravel esta en $APP_ROOT y solo public/ esta en la web."
