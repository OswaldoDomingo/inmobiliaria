# Explicación del script `enviarAvisosEmail`

```js
function enviarAvisosEmail() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();

  // Hojas (comerciales)
  const hojasVendedores = ["GALLUR", "QUIQUE", "VICTOR M.", "GOR", "ALVARO", "OFICINA"];

  // Correos electrónicos de cada comercial
  const correos = {
    "GALLUR": "comercial2_@correo.com",
    "QUIQUE": "comercial1_@correo.com",
    "VICTOR M.": "comercial5_@correo.com",
    "GOR": "comercial4_@correo.com",
    "ALVARO": "comercial6_@correo.com",
    "OFICINA": "direccion_@correo.com"
  };

  // Correo del jefe
  const correoJefe = "direccion_@correo.com";

  const telegramChatIdJefe = 'xxxxxxxxx';  // 👈 reemplázalo por el chat_id real
  const telegramToken = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"; // 👈 token de BotFather

  // 🔹 Fecha actual
  const hoy = new Date();
  const diaSemana = hoy.getDay(); // 0 = domingo, 6 = sábado

  // 🔸 Si es sábado o domingo → no ejecuta
  if (diaSemana === 0 || diaSemana === 6) {
    Logger.log("⏸ Fin de semana. No se ejecuta el script.");
    return;
  }

  // 🔹 Crear lista de fechas objetivo
  let fechasObjetivo = [hoy];
  if (diaSemana === 1) { // Lunes → incluir sábado y domingo también
    const ayer = new Date(hoy);
    ayer.setDate(hoy.getDate() - 1);
    const anteayer = new Date(hoy);
    anteayer.setDate(hoy.getDate() - 2);
    fechasObjetivo = [hoy, ayer, anteayer];
  }

  // 🔹 Convertir a cadenas para comparar fácilmente
  const fechasTexto = fechasObjetivo.map(f => f.toDateString());
  const fechaFormateada = Utilities.formatDate(hoy, Session.getScriptTimeZone(), "dd/MM/yyyy");

  let resumenJefe = `📋 RESUMEN DIARIO DE LLAMADAS - ${fechaFormateada}\n\n`;

  hojasVendedores.forEach(nombreVendedor => {
    const hoja = ss.getSheetByName(nombreVendedor);
    if (!hoja) return;

    const correoVendedor = correos[nombreVendedor];
    const datos = hoja.getDataRange().getValues();
    const encabezados = datos.shift();

    const idxLlamar = encabezados.indexOf("LLAMAR");
    const idxDireccion = encabezados.indexOf("DIRECCIÓN");
    const idxPropietario = encabezados.indexOf("PROPIETARIO");
    const idxTelefono = encabezados.indexOf("TELEFONO");

    if (idxLlamar === -1) return;

    let clientesHoy = [];

    datos.forEach(fila => {
      let fechaCelda = fila[idxLlamar];
      if (!(fechaCelda instanceof Date) && fechaCelda) {
        const posibleFecha = new Date(fechaCelda);
        if (!isNaN(posibleFecha)) fechaCelda = posibleFecha;
      }

      if (fechaCelda instanceof Date && fechasTexto.includes(fechaCelda.toDateString())) {
        clientesHoy.push({
          propietario: fila[idxPropietario],
          direccion: fila[idxDireccion],
          telefono: fila[idxTelefono],
          fecha: Utilities.formatDate(fechaCelda, Session.getScriptTimeZone(), "dd/MM/yyyy")
        });
      }
    });

    if (clientesHoy.length > 0) {
      // --- Mensaje para el comercial ---
      let mensaje = `📅 Clientes a contactar (${fechaFormateada}):\n\n`;
      clientesHoy.forEach(c => {
        mensaje += `🏠 Propietario: ${c.propietario || "-"}\n📍 Dirección: ${c.direccion || "-"}\n📞 Teléfono: ${c.telefono || "-"}\n📅 Fecha: ${c.fecha}\n\n`;
      });

      // --- Enviar correo al comercial ---
      if (correoVendedor) {
        MailApp.sendEmail({
          to: correoVendedor,
          subject: `Recordatorio de llamadas (${nombreVendedor}) - ${fechaFormateada}`,
          body: mensaje
        });
      }

      // --- Añadir al resumen del jefe ---
      resumenJefe += `👤 ${nombreVendedor}\n${mensaje}\n`;
    } else {
      resumenJefe += `👤 ${nombreVendedor}: No tiene llamadas programadas para hoy.\n\n`;
    }
  });

  // --- Enviar resumen al jefe ---
  if (correoJefe) {
    MailApp.sendEmail({
      to: correoJefe,
      subject: `Resumen diario de llamadas (${fechaFormateada})`,
      body: resumenJefe
    });
  }

  // --- Enviar resumen al jefe por Telegram ---
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
}
```

---

## 1. ¿Qué hace este script, en resumen?

En una frase:

> Recorre varias hojas (una por comercial), busca las filas con una fecha de llamada en la columna **LLAMAR** igual a **hoy** (y si es lunes, también las del sábado y domingo), envía un correo a cada comercial con sus clientes a llamar y, además, envía al jefe un resumen por **correo** y por **Telegram**.

---

## 2. Inicio de la función y obtención del libro

```js
function enviarAvisosEmail() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
```

- Se declara la función `enviarAvisosEmail`.
- `SpreadsheetApp.getActiveSpreadsheet()` devuelve el **libro de Google Sheets** en el que se está ejecutando el script.
- Ese libro se guarda en la constante `ss`.

---

## 3. Hojas de los vendedores y correos asociados

```js
  // Hojas (comerciales)
  const hojasVendedores = ["GALLUR", "QUIQUE", "VICTOR M.", "GOR", "ALVARO", "OFICINA"];

  // Correos electrónicos de cada comercial
  const correos = {
    "GALLUR": "comercial2_@correo.com",
    "QUIQUE": "comercial1_@correo.com",
    "VICTOR M.": "comercial5_@correo.com",
    "GOR": "comercial4_@correo.com",
    "ALVARO": "comercial6_@correo.com",
    "OFICINA": "direccion_@correo.com"
  };
```

- `hojasVendedores` es un **array** con los nombres de las pestañas que corresponden a cada comercial.
- `correos` es un **objeto** que actúa como un mapa:  
  clave = nombre de la hoja / comercial,  
  valor = su correo electrónico.
- De esta forma, con `correos["GALLUR"]` se obtiene el email del comercial GALLUR.

---

## 4. Datos del jefe y configuración de Telegram

```js
  // Correo del jefe
  const correoJefe = "direccion_@correo.com";

  const telegramChatIdJefe = 'XXXXXXXXXXXXXXX';  // 👈 reemplázalo por el chat_id real
  const telegramToken = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"; // 👈 token de BotFather
```

- `correoJefe` es el correo al que se enviará el resumen global diario.
- `telegramChatIdJefe` es el identificador del chat de Telegram del jefe (numérico).
- `telegramToken` es el token del bot de Telegram que se utiliza para mandar mensajes a través de la API de Telegram.

---

## 5. Fecha actual y día de la semana

```js
  // 🔹 Fecha actual
  const hoy = new Date();
  const diaSemana = hoy.getDay(); // 0 = domingo, 6 = sábado
```

- `hoy` es la fecha y hora actuales.
- `getDay()` devuelve el día de la semana:
  - 0 → domingo
  - 1 → lunes
  - …
  - 6 → sábado.
- `diaSemana` se usará para decidir si se ejecuta el script y qué fechas se van a revisar.

---

## 6. Saltarse el fin de semana

```js
  // 🔸 Si es sábado o domingo → no ejecuta
  if (diaSemana === 0 || diaSemana === 6) {
    Logger.log("⏸ Fin de semana. No se ejecuta el script.");
    return;
  }
```

- Si `diaSemana` es 0 (domingo) o 6 (sábado):
  - Se escribe un mensaje en el log (`Logger.log`).
  - `return;` sale de la función y **no hace nada más**.
- Esto evita que el script envíe correos los fines de semana.

---

## 7. Construcción de la lista de fechas objetivo

```js
  // 🔹 Crear lista de fechas objetivo
  let fechasObjetivo = [hoy];
  if (diaSemana === 1) { // Lunes → incluir sábado y domingo también
    const ayer = new Date(hoy);
    ayer.setDate(hoy.getDate() - 1);
    const anteayer = new Date(hoy);
    anteayer.setDate(hoy.getDate() - 2);
    fechasObjetivo = [hoy, ayer, anteayer];
  }
```

- Por defecto, `fechasObjetivo` contiene solo la fecha de **hoy**.
- Si `diaSemana === 1`, significa que es **lunes**:
  - Se crea una nueva fecha `ayer` = hoy - 1 día (domingo).
  - Se crea `anteayer` = hoy - 2 días (sábado).
  - Entonces `fechasObjetivo` pasa a ser `[hoy, ayer, anteayer]`.
- Resultado:
  - De martes a viernes: se revisan solo clientes con fecha de llamada = hoy.
  - Los lunes: se revisan clientes con fecha de llamada = hoy, domingo y sábado (para no perder llamadas programadas el fin de semana).

---

## 8. Conversión de fechas a texto y formato para mostrar

```js
  // 🔹 Convertir a cadenas para comparar fácilmente
  const fechasTexto = fechasObjetivo.map(f => f.toDateString());
  const fechaFormateada = Utilities.formatDate(hoy, Session.getScriptTimeZone(), "dd/MM/yyyy");
```

- `fechasTexto` es un array de cadenas: se transforma cada fecha de `fechasObjetivo` a texto con `toDateString()`.
  - Esto facilita comparar fechas ignorando la hora.
- `fechaFormateada` es la fecha de hoy en formato `dd/MM/yyyy` (por ejemplo, `03/12/2025`), usando:
  - `Utilities.formatDate` para formatear.
  - `Session.getScriptTimeZone()` para respetar la zona horaria definida en el script.

---

## 9. Inicio del resumen para el jefe

```js
  let resumenJefe = `📋 RESUMEN DIARIO DE LLAMADAS - ${fechaFormateada}\n\n`;
```

- Se inicializa una cadena `resumenJefe` que agrupará:
  - El listado de clientes a llamar, separados por comercial.
- La cabecera incluye un emoji y la fecha formateada.

---

## 10. Recorrido por cada hoja de vendedor

```js
  hojasVendedores.forEach(nombreVendedor => {
    const hoja = ss.getSheetByName(nombreVendedor);
    if (!hoja) return;
```

- Se recorre el array `hojasVendedores` con `forEach`.
- Para cada `nombreVendedor`:
  - Se obtiene la hoja correspondiente con `getSheetByName`.
  - Si no existe la hoja (por ejemplo, se ha borrado o renombrado), `!hoja` será verdadero y el script hace `return;` **solo de esta iteración** del `forEach`.

---

## 11. Lectura de datos y localización de columnas

```js
    const correoVendedor = correos[nombreVendedor];
    const datos = hoja.getDataRange().getValues();
    const encabezados = datos.shift();

    const idxLlamar = encabezados.indexOf("LLAMAR");
    const idxDireccion = encabezados.indexOf("DIRECCIÓN");
    const idxPropietario = encabezados.indexOf("PROPIETARIO");
    const idxTelefono = encabezados.indexOf("TELEFONO");

    if (idxLlamar === -1) return;
```

- `correoVendedor` obtiene el correo asociando el nombre del vendedor al mapa `correos`.
- `getDataRange().getValues()`:
  - `getDataRange()` toma todo el rango que contiene datos en la hoja.
  - `getValues()` devuelve una **matriz** con todas las filas y columnas.
- `const encabezados = datos.shift();`:
  - `datos` es un array de arrays: la primera fila será el encabezado (nombres de columnas).
  - `shift()` saca la primera fila de `datos` y la guarda en `encabezados`.
  - Después de esto, `datos` contiene **solo las filas de datos**, sin la cabecera.

- `indexOf("LLAMAR")`, etc.:
  - Se busca en `encabezados` en qué posición está cada columna relevante:
    - `"LLAMAR"` → fecha de llamada.
    - `"DIRECCIÓN"` → dirección del inmueble.
    - `"PROPIETARIO"` → nombre del propietario.
    - `"TELEFONO"` → teléfono de contacto.
- `idxLlamar === -1` significa que no se ha encontrado la columna `"LLAMAR"`.  
  En ese caso se hace `return;` y esa hoja se ignora.

---

## 12. Preparar la lista de clientes del día para ese vendedor

```js
    let clientesHoy = [];

    datos.forEach(fila => {
      let fechaCelda = fila[idxLlamar];
      if (!(fechaCelda instanceof Date) && fechaCelda) {
        const posibleFecha = new Date(fechaCelda);
        if (!isNaN(posibleFecha)) fechaCelda = posibleFecha;
      }

      if (fechaCelda instanceof Date && fechasTexto.includes(fechaCelda.toDateString())) {
        clientesHoy.push({
          propietario: fila[idxPropietario],
          direccion: fila[idxDireccion],
          telefono: fila[idxTelefono],
          fecha: Utilities.formatDate(fechaCelda, Session.getScriptTimeZone(), "dd/MM/yyyy")
        });
      }
    });
```

- `clientesHoy` será un array de objetos con los clientes que ese comercial debe llamar hoy (y, si es lunes, también los del fin de semana).
- Se recorre cada `fila` de `datos`:
  - `fechaCelda = fila[idxLlamar];` obtiene el valor de la columna **LLAMAR** para esa fila.
  - Se comprueba: `!(fechaCelda instanceof Date) && fechaCelda`:
    - Si la celda **no** es un objeto `Date` pero tiene algún valor:
      - Se intenta crear una fecha con `new Date(fechaCelda)`.
      - Si la fecha resultante no es `NaN`, se reemplaza `fechaCelda` por el objeto `Date` convertido.
- Después:
  - Si `fechaCelda` es una `Date` y su `toDateString()` está dentro de `fechasTexto`, significa que coincide con **alguna de las fechas objetivo** (hoy, o también sábado/domingo si es lunes).
  - En ese caso se hace `push` a `clientesHoy` con un objeto que contiene:
    - `propietario`
    - `direccion`
    - `telefono`
    - `fecha` formateada como `dd/MM/yyyy`.

---

## 13. Construcción del mensaje para el comercial y envío de correo

```js
    if (clientesHoy.length > 0) {
      // --- Mensaje para el comercial ---
      let mensaje = `📅 Clientes a contactar (${fechaFormateada}):\n\n`;
      clientesHoy.forEach(c => {
        mensaje += `🏠 Propietario: ${c.propietario || "-"}\n📍 Dirección: ${c.direccion || "-"}\n📞 Teléfono: ${c.telefono || "-"}\n📅 Fecha: ${c.fecha}\n\n`;
      });

      // --- Enviar correo al comercial ---
      if (correoVendedor) {
        MailApp.sendEmail({
          to: correoVendedor,
          subject: `Recordatorio de llamadas (${nombreVendedor}) - ${fechaFormateada}`,
          body: mensaje
        });
      }

      // --- Añadir al resumen del jefe ---
      resumenJefe += `👤 ${nombreVendedor}\n${mensaje}\n`;
    } else {
      resumenJefe += `👤 ${nombreVendedor}: No tiene llamadas programadas para hoy.\n\n`;
    }
```

- Si `clientesHoy.length > 0`, es decir, si hay clientes a llamar:
  - Se crea el texto `mensaje` para ese comercial, con una cabecera y un listado de clientes.
  - En el `forEach` de `clientesHoy` se va concatenando texto con:
    - Propietario
    - Dirección
    - Teléfono
    - Fecha (de la llamada)
    - Si alguno no existe, se pone `"-"` gracias al operador `|| "-"`.
- Luego:
  - Si `correoVendedor` existe, se envía un email mediante `MailApp.sendEmail` con:
    - `to`: correo del comercial.
    - `subject`: asunto con el nombre del comercial y la fecha.
    - `body`: el mensaje generado.
- Además:
  - Se añade ese mismo bloque al `resumenJefe`, precedido de `👤 nombreVendedor`.
- Si **no** hay clientes (`clientesHoy.length === 0`):
  - Se añade al resumen del jefe una línea indicando que ese comercial no tiene llamadas para hoy.

---

## 14. Envío del resumen por correo al jefe

```js
  // --- Enviar resumen al jefe ---
  if (correoJefe) {
    MailApp.sendEmail({
      to: correoJefe,
      subject: `Resumen diario de llamadas (${fechaFormateada})`,
      body: resumenJefe
    });
  }
```

- Después de procesar todas las hojas:
  - Si `correoJefe` tiene valor, se manda un email al jefe con:
    - `to`: correo del jefe.
    - `subject`: resumen diario de llamadas con la fecha.
    - `body`: todo el contenido de `resumenJefe`, que incluye la cabecera y el detalle de cada comercial.

---

## 15. Envío del resumen por Telegram al jefe

```js
  // --- Enviar resumen al jefe por Telegram ---
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
}
```

- Si existen `telegramChatIdJefe` y `telegramToken`, se construye una llamada a la API de Telegram:
  - `url` es el endpoint estándar de `sendMessage` de Telegram para ese bot (`bot${telegramToken}`).
  - `payload` es el cuerpo del mensaje:
    - `chat_id`: el identificador del chat donde se enviará el mensaje.
    - `text`: el texto del mensaje (el mismo `resumenJefe` que se ha mandado por email).
    - `parse_mode: "Markdown"` indica que Telegram interpretará el texto como Markdown (negritas, etc.).
  - `params` define:
    - `method: "post"` → la petición será POST.
    - `contentType: "application/json"` → se enviará JSON.
    - `payload: JSON.stringify(payload)` → el cuerpo se pasa a JSON.
- Finalmente, `UrlFetchApp.fetch(url, params);` realiza la llamada HTTP a la API de Telegram:
  - El resultado es que el jefe recibe el resumen en Telegram.

---

## 16. Flujo completo de ejecución

1. El script se ejecuta mediante un disparador diario.
2. Comprueba el día de la semana:
   - Si es sábado o domingo: no hace nada.
   - Si es lunes: mirará sábado, domingo y lunes.
   - Si es otro día entre semana: solo mira la fecha de hoy.
3. Para cada comercial:
   - Carga su hoja.
   - Localiza las columnas `LLAMAR`, `DIRECCIÓN`, `PROPIETARIO`, `TELEFONO`.
   - Recorre todas las filas de datos:
     - Convierte la columna `LLAMAR` a fecha si no lo es.
     - Si la fecha coincide con una **fecha objetivo**, recoge los datos del cliente.
   - Si hay clientes:
     - Prepara un mensaje con todos ellos.
     - Envía un correo al comercial.
     - Añade ese bloque al resumen del jefe.
   - Si no hay clientes:
     - Añade al resumen del jefe que ese comercial no tiene llamadas para hoy.
4. Al final:
   - Envía por correo al jefe el resumen completo.
   - Envía el mismo resumen como mensaje de Telegram.

Este es el comportamiento del script línea por línea, sin modificar nada del código original.
