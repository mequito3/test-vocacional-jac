#!/bin/bash
# Deploy del Test Vocacional CHASIDE: sincroniza codigo desde ~/jac al docroot,
# repara vendor, configura .env/.htaccess, migra y deja diagnostico en public/diag.txt.

SRC=~/jac
DOCROOT=~/domains/jac2000.americolabs.com/public_html
cd "$DOCROOT" || { echo "!! No existe $DOCROOT"; exit 1; }
echo ">> Carpeta: $(pwd)"

# 1. Sincronizar codigo fuente desde ~/jac (sin tocar vendor / .env / .htaccess / storage)
echo ">> Sincronizando codigo desde $SRC ..."
for d in app config database resources routes; do
  if [ -d "$SRC/$d" ]; then
    rm -rf "./$d" && cp -r "$SRC/$d" "./$d" && echo "   - $d"
  fi
done

# 2. .env de produccion
cat > .env <<'ENVEOF'
APP_NAME="Test Vocacional CHASIDE"
APP_ENV=production
APP_KEY=base64:uZwlGl/9Cyt+8pcA1nrpmm7TF6yEDmYuEqWT7kY246A=
APP_DEBUG=false
APP_URL=https://jac2000.americolabs.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u636084353_jac2000
DB_USERNAME=u636084353_jac2000
DB_PASSWORD=JacBolivia2000

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
ENVEOF

# 3. .htaccess que envia todo a public/
cat > .htaccess <<'HTEOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTEOF
echo ">> .env / .htaccess OK"

# 4. Reparar vendor si esta incompleto (usa el de ~/jac)
if [ ! -f vendor/symfony/deprecation-contracts/function.php ] || [ ! -f vendor/autoload.php ]; then
  echo ">> vendor incompleto -> reemplazando con el de $SRC"
  rm -rf vendor && cp -r "$SRC/vendor" ./vendor
fi

# 5. Limpiar TODA cache compilada (config, rutas, vistas blade)
rm -f bootstrap/cache/*.php
rm -f storage/framework/views/*.php
echo ">> cache limpia"

# 6. Migraciones + permisos
php artisan migrate --force 2>&1 | tail -15
chmod -R 775 storage bootstrap/cache 2>/dev/null

# 7. Diagnostico breve (por si algo falla, lo leo por web)
{
  echo "=== DIAG $(date -u) ==="
  php -d display_errors=1 artisan --version 2>&1
  echo "migrate exit anterior: ver arriba"
} > public/diag.txt 2>&1
chmod 644 public/diag.txt 2>/dev/null

echo ""
echo "===FIN=== Recarga https://jac2000.americolabs.com"
