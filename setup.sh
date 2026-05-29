#!/bin/bash
# Despliegue del Test Vocacional CHASIDE en Hostinger (verboso, sin set -e).

DOCROOT=~/domains/jac2000.americolabs.com/public_html
cd "$DOCROOT" || { echo "!! No existe $DOCROOT"; exit 1; }
echo ">> Carpeta: $(pwd)"
echo ">> PHP: $(php -v | head -1)"

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

# 3. Limpiar TODA cache compilada
rm -f bootstrap/cache/*.php
echo ">> cache limpia"

# 4. Probar conexion a la base (muestra el error si lo hay)
echo ">> ===== PRUEBA DE CONEXION DB ====="
php artisan migrate --force 2>&1 | tail -40
echo ">> ================================="

# 5. Permisos
chmod -R 775 storage bootstrap/cache 2>/dev/null

echo ""
echo "===FIN=== (si arriba dice 'Nothing to migrate' o lista migraciones DONE, las tablas estan OK)"
