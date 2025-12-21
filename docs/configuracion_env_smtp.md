# Configuración del Archivo .env para SMTP

Este documento explica cómo configurar el archivo `.env` de la aplicación para habilitar el envío de correos electrónicos mediante Gmail.

---

## Ubicación del Archivo

El archivo de configuración se encuentra en:

```
config/.env
```

> [!CAUTION]
> **NO confundir** con el archivo `.env` que puede existir en la raíz del proyecto. La aplicación lee específicamente el archivo que está dentro de la carpeta `config/`.

---

## Estructura de Configuración SMTP

Añade o modifica las siguientes líneas en el archivo `config/.env`:

```ini
# ====================================
# CONFIGURACIÓN DE CORREO (GMAIL)
# ====================================

# Servidor SMTP de Gmail
SMTP_HOST=smtp.gmail.com

# Puerto (587 para TLS, 465 para SSL)
SMTP_PORT=587

# Tu dirección de Gmail
SMTP_USER=tu_email@gmail.com

# Contraseña de Aplicación (16 caracteres, SIN ESPACIOS)
SMTP_PASS=abcdefghijklmnop

# Tipo de seguridad (tls recomendado)
SMTP_SECURE=tls

# ====================================
# EMAILS DE DESTINO
# ====================================

# Email que aparecerá como remitente en los correos automáticos
NOREPLY_EMAIL=tu_email@gmail.com

# Email donde llegarán los avisos de leads (tasaciones, contactos)
LEAD_AGENCY_EMAIL=tu_email@gmail.com
```

---

## Descripción de Cada Variable

### Variables SMTP (Obligatorias)

| Variable | Valor | Descripción |
|----------|-------|-------------|
| `SMTP_HOST` | `smtp.gmail.com` | Servidor SMTP de Gmail (no cambiar) |
| `SMTP_PORT` | `587` | Puerto para conexión TLS (recomendado) |
| `SMTP_USER` | Tu email de Gmail | Email completo (ej: `ejemplo@gmail.com`) |
| `SMTP_PASS` | Contraseña de 16 caracteres | **Contraseña de Aplicación** generada en Google (ver [guía](./configuracion_gmail_smtp.md)) |
| `SMTP_SECURE` | `tls` | Tipo de cifrado (`tls` o `ssl`) |

### Variables de Email (Obligatorias)

| Variable | Valor | Descripción |
|----------|-------|-------------|
| `NOREPLY_EMAIL` | Tu email | Email que aparecerá como remitente en correos automáticos |
| `LEAD_AGENCY_EMAIL` | Tu email | Email donde recibirás notificaciones de leads |

---

## Ejemplo Completo

```ini
# ====================================
# CONFIGURACIÓN DE CORREO (GMAIL)
# ====================================
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=inmobiliaria@gmail.com
SMTP_PASS=abcdefghijklmnop
SMTP_SECURE=tls

NOREPLY_EMAIL=inmobiliaria@gmail.com
LEAD_AGENCY_EMAIL=inmobiliaria@gmail.com
```

---

## Pasos para Configurar

### 1. Obtener la Contraseña de Aplicación

Antes de editar el archivo `.env`, debes generar una Contraseña de Aplicación en Google. Sigue la [guía de configuración de Gmail](./configuracion_gmail_smtp.md).

### 2. Editar el Archivo

1. Abre el archivo `config/.env` con un editor de texto
2. Busca la sección de configuración SMTP (o créala si no existe)
3. Sustituye los valores de ejemplo por tus datos reales:
   - `SMTP_USER`: Tu dirección de Gmail completa
   - `SMTP_PASS`: La contraseña de 16 caracteres que generaste (sin espacios)
   - `NOREPLY_EMAIL`: El email que quieres que aparezca como remitente
   - `LEAD_AGENCY_EMAIL`: El email donde quieres recibir las notificaciones

### 3. Guardar el Archivo

> [!IMPORTANT]
> Asegúrate de **guardar el archivo** después de editarlo. Los cambios no tendrán efecto hasta que guardes.

### 4. Verificar la Configuración

Puedes verificar que la configuración funciona correctamente:

1. Ve al formulario de **Tasación Online** en tu sitio web
2. Completa y envía el formulario
3. Deberías recibir dos correos:
   - Uno en `LEAD_AGENCY_EMAIL` (aviso de nuevo lead)
   - Otro en el email del cliente (confirmación)

Si no recibes los correos, consulta la sección de **Solución de Problemas** más abajo.

---

## Notas Importantes

### Espacios en la Contraseña

> [!WARNING]
> Google muestra la contraseña con espacios (`xxxx xxxx xxxx xxxx`), pero debes copiarla **SIN espacios** en el archivo `.env`.

**Incorrecto:**
```ini
SMTP_PASS=abcd efgh ijkl mnop
```

**Correcto:**
```ini
SMTP_PASS=abcdefghijklmnop
```

La aplicación elimina automáticamente los espacios, pero es mejor evitarlos desde el principio.

### Usar el Mismo Email

Puedes usar el mismo email de Gmail para todas las variables:

```ini
SMTP_USER=miempresa@gmail.com
NOREPLY_EMAIL=miempresa@gmail.com
LEAD_AGENCY_EMAIL=miempresa@gmail.com
```

O usar emails diferentes si lo prefieres:

```ini
SMTP_USER=noreply@miempresa.com
NOREPLY_EMAIL=noreply@miempresa.com
LEAD_AGENCY_EMAIL=info@miempresa.com
```

> [!NOTE]
> Si usas emails diferentes, asegúrate de que `SMTP_USER` y `SMTP_PASS` correspondan a la cuenta donde generaste la Contraseña de Aplicación.

---

## Solución de Problemas

### Error: "No se ha configurado un email remitente"

**Causa:** Falta alguna variable obligatoria en el archivo `.env`

**Solución:**
1. Verifica que todas las variables SMTP estén presentes
2. Asegúrate de que `SMTP_USER` y `NOREPLY_EMAIL` tengan valores
3. Guarda el archivo y vuelve a intentarlo

### Error: "Could not authenticate"

**Causa:** La contraseña de aplicación es incorrecta o tiene espacios

**Solución:**
1. Verifica que `SMTP_PASS` tenga exactamente 16 caracteres sin espacios
2. Asegúrate de que `SMTP_USER` coincida con el email donde generaste la contraseña
3. Si el problema persiste, genera una nueva Contraseña de Aplicación en Google

### Los correos no llegan

**Posibles causas:**
- El archivo no se guardó correctamente
- Los correos están en la carpeta de Spam
- Hay un error en la configuración

**Solución:**
1. Revisa la carpeta de **Spam** del destinatario
2. Verifica el archivo `logs/mail.log` para ver errores específicos
3. Asegúrate de que el archivo `config/.env` esté guardado

### Verificar qué valores está leyendo la aplicación

Si necesitas verificar qué configuración está cargando la aplicación, puedes crear un script de prueba:

```php
<?php
require_once '../app/Autoloader.php';
\App\Autoloader::register('../');

use App\Core\Config;
use App\Core\Env;

Env::load('../config/.env');
$config = require '../config/config.php';
Config::init($config);

echo "SMTP_HOST: " . Config::get('smtp.host') . "\n";
echo "SMTP_PORT: " . Config::get('smtp.port') . "\n";
echo "SMTP_USER: " . Config::get('smtp.user') . "\n";
echo "NOREPLY_EMAIL: " . Config::get('emails.noreply') . "\n";
```

Guárdalo como `public/test_config.php` y ejecútalo desde el navegador o terminal.

---

## Seguridad

### Protección del Archivo .env

> [!CAUTION]
> El archivo `.env` contiene información sensible (contraseñas). **Nunca** lo subas a un repositorio público.

El archivo `.gitignore` del proyecto ya incluye `config/.env` para evitar que se suba accidentalmente a Git.

### Cambiar la Contraseña

Si crees que la contraseña ha sido comprometida:

1. Ve a Google > Seguridad > Contraseñas de aplicaciones
2. Elimina la contraseña antigua
3. Genera una nueva
4. Actualiza `SMTP_PASS` en `config/.env`

---

## Configuración Alternativa (Otros Proveedores)

Aunque esta guía se centra en Gmail, puedes usar otros proveedores SMTP modificando las variables:

### Ejemplo con otro proveedor SMTP

```ini
SMTP_HOST=smtp.tuproveedor.com
SMTP_PORT=587
SMTP_USER=tu_usuario
SMTP_PASS=tu_contraseña
SMTP_SECURE=tls
```

Consulta la documentación de tu proveedor de correo para obtener los valores correctos.

---

## Referencias

- [Configuración de Gmail](./configuracion_gmail_smtp.md) - Cómo generar la Contraseña de Aplicación
- [Documentación de PHPMailer](https://github.com/PHPMailer/PHPMailer) - Librería utilizada para envío de correos
- `app/Services/MailService.php` - Código fuente del servicio de correo
