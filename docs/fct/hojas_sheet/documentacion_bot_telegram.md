# Guía: Crear y configurar un bot de Telegram para enviar avisos desde Google Apps Script

Esta documentación explica, paso a paso, cómo crear un **bot de Telegram** y conectarlo con tu script de **Google Apps Script** para enviar los avisos (por ejemplo, el `resumenJefe` de llamadas).

---

## 1. Requisitos previos

Antes de empezar, necesitas:

- Una cuenta de **Telegram** (en el móvil o en el ordenador).
- Acceso al **editor de Apps Script** asociado a tu Google Sheets.
- Conexión a internet, claro 🙂.

---

## 2. Crear el bot en Telegram con BotFather

En Telegram, los bots se crean y gestionan mediante otro bot oficial llamado **BotFather**.

### 2.1. Abrir BotFather

1. Abre Telegram.
2. En el buscador, escribe: `@BotFather`.
3. Selecciona el bot verificado **BotFather** (tiene un check azul).

### 2.2. Crear un nuevo bot

1. Pulsa en **Start** o escribe `/start`.
2. Escribe el comando:
   ```text
   /newbot
   ```
3. BotFather te pedirá:
   - **Nombre del bot** (Name):  
     Es el nombre visible, por ejemplo:  
     `Avisos Extramurs`
   - **Usuario del bot** (Username):  
     Debe terminar en `bot`. Por ejemplo:  
     `extramurs_avisos_bot`

4. Cuando todo esté correcto, BotFather responderá con un mensaje que incluye:

   - El **enlace** al bot (por ejemplo, `https://t.me/extramurs_avisos_bot`).
   - El **token de acceso** del bot, algo con este formato:

     ```text
     1234567890:ABCDefghIJkl-MNOPqrstuvWXyz123456789
     ```

   Ese texto es tu **`telegramToken`** para usar en Apps Script.

> ⚠️ Importante:  
> El token es como una contraseña del bot. No lo compartas públicamente ni lo subas a repositorios públicos.

---

## 3. Obtener el `chat_id` donde quieres recibir los avisos

El **`chat_id`** identifica el chat de Telegram donde el bot enviará los mensajes. Puede ser:

- Un chat **individual** (tú con el bot).
- Un **grupo** donde el bot esté añadido.
- Un **canal**.

A continuación se explica el caso más sencillo: chat individual contigo.

### 3.1. Iniciar conversación con el bot

1. Pulsa en el enlace del bot (ejemplo: `https://t.me/extramurs_avisos_bot`).
2. En Telegram se abrirá el bot.
3. Pulsa en **Start** o escribe cualquier mensaje (por ejemplo: `Hola bot`).

Esto es necesario para que Telegram registre el chat y luego se pueda obtener el `chat_id`.

### 3.2. Obtener el `chat_id` usando la API de Telegram

1. En tu navegador, abre una URL como esta (sustituye `TU_TOKEN_AQUI` por tu token real del bot):

   ```text
   https://api.telegram.org/botTU_TOKEN_AQUI/getUpdates
   ```

   Ejemplo (no real):
   ```text
   https://api.telegram.org/bot1234567890:ABCDefghIJkl-MNOPqrstuvWXyz123456789/getUpdates
   ```

2. Verás una respuesta en formato JSON. Dentro, habrá algo similar a:

   ```json
   {
     "ok": true,
     "result": [
       {
         "update_id": 123456789,
         "message": {
           "message_id": 1,
           "from": {
             "id": 111111111,
             "is_bot": false,
             "first_name": "TuNombre",
             ...
           },
           "chat": {
             "id": 111111111,
             "first_name": "TuNombre",
             "type": "private"
           },
           "date": 1700000000,
           "text": "Hola bot"
         }
       }
     ]
   }
   ```

3. El **`chat.id`** que aparece dentro de `"chat"` es el que necesitas:

   ```json
   "chat": {
     "id": 111111111,
     "first_name": "TuNombre",
     "type": "private"
   }
   ```

   En este ejemplo:
   - `chat_id` = `111111111`

Ese número es el que tendrás que poner en tu script como `telegramChatIdJefe`.

---

## 4. (Opcional) Obtener `chat_id` de un grupo

Si quieres que los avisos lleguen a un **grupo**:

1. Crea un grupo en Telegram (por ejemplo, "Avisos Oficina Extramurs").
2. Añade al bot al grupo (como si fuera un contacto más).
3. Escribe un mensaje en el grupo (por ejemplo: `Probando avisos`).
4. Vuelve a llamar a la URL:

   ```text
   https://api.telegram.org/botTU_TOKEN_AQUI/getUpdates
   ```

5. En la respuesta JSON, busca una sección `"chat"` donde `"type"` sea `"group"` o `"supergroup"`, por ejemplo:

   ```json
   "chat": {
     "id": -222222222,
     "title": "Avisos Oficina Extramurs",
     "type": "group"
   }
   ```

   En este caso:
   - `chat_id` = `-222222222` (ojo, con el signo **negativo**).

Usarás ese `chat_id` en lugar del individual.

---

## 5. Probar el envío desde Google Apps Script

Una vez que tienes:

- `telegramToken` → el token del bot.
- `telegramChatId` → el id del chat (privado o grupo).

Puedes hacer una **prueba rápida** en un proyecto de Apps Script:

```js
function pruebaTelegram() {
  const telegramToken = "TU_TOKEN_AQUI";
  const chatId = TU_CHAT_ID_AQUI; // número, sin comillas si quieres usarlo como número

  const mensaje = "🔔 Prueba de envío desde Google Apps Script.";

  const url = `https://api.telegram.org/bot${telegramToken}/sendMessage`;
  const payload = {
    chat_id: chatId,
    text: mensaje,
    parse_mode: "Markdown"
  };

  const params = {
    method: "post",
    contentType: "application/json",
    payload: JSON.stringify(payload)
  };

  UrlFetchApp.fetch(url, params);
}
```

### Pasos para probarlo

1. Abre **Extensiones → Apps Script** en tu Google Sheets.
2. Crea una función `pruebaTelegram` con el código anterior.
3. Sustituye:
   - `"TU_TOKEN_AQUI"` por el token de tu bot.
   - `TU_CHAT_ID_AQUI` por el número de `chat_id`.
4. Guarda el script.
5. Ejecuta la función `pruebaTelegram` desde el menú desplegable (▶ Run / Ejecutar).
6. Acepta los permisos que te pida Google la primera vez.
7. Comprueba en Telegram que te ha llegado el mensaje.

Si te llega, el bot está bien configurado y conectado con Apps Script.

---

## 6. Integrar el bot con tu script de avisos

En tu script de avisos (`enviarAvisosEmail`), ya tienes algo como esto:

```js
const telegramChatIdJefe = 8249090022;  // chat_id real
const telegramToken = "XXXXXXXXXX:YYYYYYYYYYYYYYYYYYYYYYYYYYYY"; // token de BotFather
```

Y al final del script:

```js
if (telegramChatIdJefe && telegramToken) {
  const url = `https://api.telegram.org/bot${telegramToken}/sendMessage`;
  const payload = {
    chat_id: telegramChatIdJefe,
    text: resumenJefe,
    parse_mode: "Markdown"
  };

  const params = {
    method: "post",
    contentType: "application/json",
    payload: JSON.stringify(payload)
  };

  UrlFetchApp.fetch(url, params);
}
```

El flujo es:

1. El script genera el texto `resumenJefe`.
2. Envía el resumen por correo usando `MailApp.sendEmail`.
3. Envía el mismo resumen por Telegram llamando a la API con `UrlFetchApp.fetch`.

Mientras `telegramToken` y `telegramChatIdJefe` sean los correctos, el jefe recibirá el resumen diario en su Telegram.

---

## 7. Recomendaciones de seguridad

Aunque el script funcione, es buena práctica:

- **No dejar el token en texto plano en repositorios públicos.**
- Si usas control de versiones:
  - Evitar subir el token a GitHub.
  - Usar **Properties Service** de Apps Script para guardar valores sensibles:
    - `File → Project properties → Script properties`.

Ejemplo rápido usando propiedades del script:

```js
function getTelegramConfig() {
  const props = PropertiesService.getScriptProperties();
  return {
    token: props.getProperty("TELEGRAM_TOKEN"),
    chatId: props.getProperty("TELEGRAM_CHAT_ID")
  };
}
```

Y luego, en tu función principal:

```js
const { token: telegramToken, chatId: telegramChatIdJefe } = getTelegramConfig();
```

Así el token no aparece directamente en el código.

---

## 8. Resumen

1. Creas el bot con **BotFather** → obtienes el **token**.
2. Hablas con el bot (o lo añades a un grupo) y usas `getUpdates` para obtener el **chat_id**.
3. Compruebas con una función `pruebaTelegram` en Apps Script que el envío funciona.
4. Integras el envío en tu script de avisos (`enviarAvisosEmail`).
5. Opcionalmente, proteges el token usando las propiedades del script (Script Properties).

Con esto tienes toda la cadena montada:  
**Google Sheets → Apps Script → Bot de Telegram → Mensajes de avisos al jefe.**
