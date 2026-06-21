# Despliegue seguro en Hostinger

La aplicacion Laravel vive en `~/jac`. El dominio sigue apuntando a
`~/domains/jac2000.americolabs.com/public_html`, pero esa carpeta contiene
unicamente los archivos de `public/`.

En hPanel, seleccionar PHP 8.2 o una version posterior antes de desplegar.

## Preparacion unica

Antes del primer despliegue, conectarse por SSH y guardar la configuracion fuera
de `public_html`:

```bash
cd ~/jac
cp ~/domains/jac2000.americolabs.com/public_html/.env .env
chmod 600 .env
nano .env
```

Comprobar como minimo estas variables, usando valores reales y nuevos:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://jac2000.americolabs.com
ADMIN_PASSWORD=una-clave-nueva-de-al-menos-12-caracteres
SESSION_SECURE_COOKIE=true
SHOW_COLEGIO_FIELD=false
ENABLE_TEST_BUTTON=false
```

Cambiar en hPanel la contrasena de MySQL que estuvo expuesta y actualizar
`DB_PASSWORD` en `~/jac/.env`. Tambien generar una nueva `APP_KEY` porque la
anterior estuvo versionada:

```bash
cd ~/jac
php artisan key:generate --force
```

Cambiar `APP_KEY` invalida las sesiones existentes, pero no elimina estudiantes
ni resultados.

## Publicar una revision

El clon debe quedar limpio antes de ejecutar el script:

```bash
cd ~/jac
git pull --ff-only origin main
bash setup.sh
```

El script instala dependencias, ejecuta migraciones, crea caches, conserva
`.well-known` y sincroniza solamente `~/jac/public/` hacia `public_html`.

## Verificacion

```bash
curl -I https://jac2000.americolabs.com/
curl -I https://jac2000.americolabs.com/.env
curl -I https://jac2000.americolabs.com/setup.sh
```

La pagina principal debe responder normalmente. `.env` y `setup.sh` deben
responder `403` o `404`, nunca mostrar contenido.
