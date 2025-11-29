## Resumen de cambios aplicados (seguridad y configuración)

- Se centralizó la configuración con `.env` + `config/config.php` y se creó `App\Core\Config` y `App\Core\Env` para cargar variables sin exponer secretos en código.
- Se endureció la sesión (cookies `httponly`, `samesite=Lax`, `secure` en producción) y se desactiva la muestra de errores en producción.
- Se añadió protección CSRF en login, CRUD de usuarios y envío de tasación (`App\Core\Csrf` + tokens en formularios y cabecera `X-CSRF-TOKEN`).
- Tasación ahora usa credenciales SMTP leídas desde configuración y oculta mensajes sensibles fuera de modo debug.
- `.env` actualizado para declarar claves esperadas (`APP_ENV`, `APP_BASE_URL`, `DB_*`, `SMTP_*`); las contraseñas reales deben ponerse localmente y no versionarse.

## Cómo levantar en local y URL a usar

1. Revisar/editar `.env` en la raíz del proyecto con tus datos locales (DB y SMTP). Ejemplo incluido:
   - `APP_ENV="local"`
   - `APP_BASE_URL="http://localhost"`
   - `DB_HOST/DB_USER/DB_PASS/DB_NAME`
   - `SMTP_HOST/SMTP_PORT/SMTP_USER/SMTP_PASS`
2. Arranca Apache apuntando el DocumentRoot a `public/` del proyecto.
3. Accede en el navegador a: `http://localhost/` (o el host/virtualhost que tengas configurado). Si usas subcarpeta, será `http://localhost/inmobiliaria/public/`.
