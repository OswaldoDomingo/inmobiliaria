# Configuración de Gmail para Envío de Correos

Este documento explica paso a paso cómo configurar una cuenta de Gmail para que la aplicación pueda enviar correos electrónicos a través de SMTP.

## ¿Por qué necesito una Contraseña de Aplicación?

Google no permite que aplicaciones externas usen tu contraseña normal de Gmail por razones de seguridad. En su lugar, debes generar una **Contraseña de Aplicación** específica para esta aplicación.

---

## Requisitos Previos

- Tener una cuenta de Gmail activa
- Tener acceso a la configuración de seguridad de tu cuenta de Google

---

## Paso 1: Activar la Verificación en Dos Pasos

> [!IMPORTANT]
> Las Contraseñas de Aplicación solo están disponibles si tienes activada la verificación en dos pasos.

1. Ve a tu cuenta de Google: [myaccount.google.com](https://myaccount.google.com)
2. En el menú lateral, haz clic en **"Seguridad"**
3. Busca la sección **"Cómo inicias sesión en Google"**
4. Haz clic en **"Verificación en dos pasos"**
5. Sigue las instrucciones para activarla (necesitarás tu teléfono móvil)

---

## Paso 2: Generar una Contraseña de Aplicación

1. Una vez activada la verificación en dos pasos, vuelve a **Seguridad**
2. Busca la opción **"Contraseñas de aplicaciones"** (puede estar en la sección "Cómo inicias sesión en Google")
   - Si no la encuentras, usa el buscador de la configuración de Google y escribe "contraseñas de aplicaciones"
3. Es posible que te pida verificar tu identidad (introduce tu contraseña de Gmail)
4. En la pantalla de Contraseñas de Aplicaciones:
   - En el campo **"Selecciona la app"**, elige **"Correo"** o **"Otra (nombre personalizado)"**
   - Si eliges "Otra", ponle un nombre descriptivo como **"CRM Inmobiliaria"**
   - En **"Selecciona el dispositivo"**, elige **"Otro (nombre personalizado)"** y pon **"Servidor Web"**
5. Haz clic en **"Generar"**

---

## Paso 3: Copiar la Contraseña Generada

Google te mostrará una contraseña de **16 caracteres** en formato:

```
xxxx xxxx xxxx xxxx
```

> [!WARNING]
> Esta contraseña solo se muestra UNA VEZ. Si la pierdes, tendrás que generar una nueva.

**Importante:**
- Copia la contraseña **sin los espacios** (solo las 16 letras)
- Guárdala en un lugar seguro temporalmente (la necesitarás en el siguiente paso)

---

## Paso 4: Configurar la Aplicación

Ahora que tienes la contraseña, ve a la sección [Configuración del Archivo .env](./configuracion_env_smtp.md) para completar la configuración en la aplicación.

---

## Solución de Problemas

### No encuentro "Contraseñas de aplicaciones"

- Asegúrate de tener activada la **Verificación en dos pasos** primero
- Usa el buscador de la configuración de Google
- Algunas cuentas corporativas o educativas pueden tener esta opción deshabilitada por el administrador

### Error "Could not authenticate"

- Verifica que hayas copiado la contraseña **sin espacios**
- Asegúrate de que el email en `SMTP_USER` coincida con la cuenta de Gmail donde generaste la contraseña
- Genera una nueva contraseña de aplicación y vuelve a intentarlo

### Los correos no llegan

- Revisa la carpeta de **Spam** del destinatario
- Verifica que el archivo `config/.env` esté guardado correctamente
- Consulta el archivo `logs/mail.log` para ver errores específicos

---

## Revocar una Contraseña de Aplicación

Si necesitas revocar el acceso:

1. Ve a **Seguridad** > **Contraseñas de aplicaciones**
2. Busca la contraseña que creaste (por ejemplo, "CRM Inmobiliaria")
3. Haz clic en el icono de la papelera o **"Eliminar"**
4. La aplicación dejará de poder enviar correos hasta que configures una nueva

---

## Referencias

- [Ayuda oficial de Google sobre Contraseñas de Aplicaciones](https://support.google.com/accounts/answer/185833)
- [Verificación en dos pasos de Google](https://www.google.com/landing/2step/)
