#!/bin/bash
# Despliegue automatico del Test Vocacional CHASIDE en Hostinger.
set -e

DOCROOT=~/domains/jac2000.americolabs.com/public_html
cd "$DOCROOT"
echo ">> Carpeta: $(pwd)"

# 1. .env de produccion (con APP_KEY valida)
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
echo ">> .env creado"

# 2. .htaccess que envia todo a public/
cat > .htaccess <<'HTEOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTEOF
echo ">> .htaccess creado"

# 3. Limpiar caches viejas
rm -f bootstrap/cache/*.php
echo ">> cache limpia"

# 4. Migraciones + permisos
php artisan migrate --force
chmod -R 775 storage bootstrap/cache

echo ""
echo "================================="
echo "  LISTO  ->  recarga el sitio"
echo "  https://jac2000.americolabs.com"
echo "================================="
