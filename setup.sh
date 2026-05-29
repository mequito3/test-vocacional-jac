#!/bin/bash
# Deploy + diagnostico del Test Vocacional CHASIDE en Hostinger.

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
DB_PASSWORD="Jac2000."

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
echo ">> .env / .htaccess / cache  OK"

echo ""
echo ">> ARTISAN VERSION:"
php artisan --version 2>&1 | tail -5

echo ""
echo ">> TEST DB host=localhost:"
php -r 'try{new PDO("mysql:host=localhost;dbname=u636084353_jac2000","u636084353_jac2000","Jac2000.");echo "   DB OK\n";}catch(Throwable $e){echo "   DB ERROR: ".$e->getMessage()."\n";}'

echo ">> TEST DB host=127.0.0.1:"
php -r 'try{new PDO("mysql:host=127.0.0.1;dbname=u636084353_jac2000","u636084353_jac2000","Jac2000.");echo "   DB OK\n";}catch(Throwable $e){echo "   DB ERROR: ".$e->getMessage()."\n";}'

echo ""
echo ">> MIGRATE:"
php artisan migrate --force 2>&1 | tail -30

echo ""
echo ">> LOG (ultimas lineas de laravel.log):"
tail -15 storage/logs/laravel.log 2>&1

chmod -R 775 storage bootstrap/cache 2>/dev/null
echo ""
echo "===FIN==="
