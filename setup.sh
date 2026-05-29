#!/bin/bash
# Deploy + diagnostico: escribe el resultado en public/diag.txt (accesible por web).

DOCROOT=~/domains/jac2000.americolabs.com/public_html
cd "$DOCROOT" || { echo "!! No existe $DOCROOT"; exit 1; }
echo ">> Carpeta: $(pwd)"

# .env de produccion
cat > .env <<'ENVEOF'
APP_NAME="Test Vocacional CHASIDE"
APP_ENV=production
APP_KEY=base64:uZwlGl/9Cyt+8pcA1nrpmm7TF6yEDmYuEqWT7kY246A=
APP_DEBUG=true
APP_URL=https://jac2000.americolabs.com

LOG_CHANNEL=stack
LOG_LEVEL=debug

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

cat > .htaccess <<'HTEOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTEOF

rm -f bootstrap/cache/*.php
echo ">> .env / .htaccess / cache OK"

# Reparar vendor: el del zip llego incompleto. Usamos el de ~/jac (composer install limpio).
if [ ! -f vendor/symfony/deprecation-contracts/function.php ] || [ ! -f vendor/autoload.php ]; then
  echo ">> vendor incompleto -> reemplazando con el de ~/jac"
  if [ -f ~/jac/vendor/symfony/deprecation-contracts/function.php ]; then
    rm -rf vendor
    cp -r ~/jac/vendor ./vendor
    echo ">> vendor reemplazado desde ~/jac"
  else
    echo ">> ~/jac/vendor tambien incompleto -> composer install"
    composer install --no-dev --optimize-autoloader 2>&1 | tail -5
  fi
else
  echo ">> vendor OK"
fi

# === Diagnostico hacia public/diag.txt (lo leo por web) ===
{
  echo "=== DIAG $(date -u) ==="
  echo "dir: $(pwd)"
  echo "php: $(php -v 2>&1 | head -1)"
  echo ""
  echo "--- artisan --version (con errores visibles) ---"
  timeout 25 php -d display_errors=1 -d error_reporting=E_ALL artisan --version 2>&1
  echo "exit=$?"
  echo ""
  echo "--- PDO host=localhost ---"
  timeout 15 php -r 'try{new PDO("mysql:host=localhost;dbname=u636084353_jac2000","u636084353_jac2000","JacBolivia2000");echo "DB OK\n";}catch(Throwable $e){echo "DB ERROR: ".$e->getMessage()."\n";}' 2>&1
  echo "--- PDO host=127.0.0.1 ---"
  timeout 15 php -r 'try{new PDO("mysql:host=127.0.0.1;dbname=u636084353_jac2000","u636084353_jac2000","JacBolivia2000");echo "DB OK\n";}catch(Throwable $e){echo "DB ERROR: ".$e->getMessage()."\n";}' 2>&1
  echo ""
  echo "--- migrate (con errores visibles) ---"
  timeout 60 php -d display_errors=1 -d error_reporting=E_ALL artisan migrate --force 2>&1
  echo "exit=$?"
  echo ""
  echo "--- laravel.log (tail) ---"
  tail -25 storage/logs/laravel.log 2>&1
  echo ""
  echo "=== END ==="
} > public/diag.txt 2>&1

chmod 644 public/diag.txt 2>/dev/null
chmod -R 775 storage bootstrap/cache 2>/dev/null
echo ">> Diagnostico guardado. Abre: https://jac2000.americolabs.com/diag.txt"
echo "===FIN==="
