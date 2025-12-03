# Explicación del script `ordenarAutomaticoVariasHojas`

```js
function ordenarAutomaticoVariasHojas() {
  const libro = SpreadsheetApp.getActiveSpreadsheet();
  const hojas = ["GALLUR", "QUIQUE", "VICTOR M.", "GOR", "ALVARO", "OFICINA"]; // Nombres de las hojas a ordenar

  const columnaOrden = 3; // Columna por la que se ordena (propietarios)

  hojas.forEach(nombreHoja => {
    const hoja = libro.getSheetByName(nombreHoja);
    if (hoja) { // Verifica que la hoja exista
      const ultimaFila = hoja.getLastRow();
      const ultimaColumna = hoja.getLastColumn();

      if (ultimaFila > 1) { // Evita errores si la hoja está vacía
        const rango = hoja.getRange(2, 1, ultimaFila - 1, ultimaColumna);
        rango.sort({column: columnaOrden, ascending: true});
      }
    }
  });
}
```

---

## 1. ¿Qué hace este script?

En una frase:  
**Ordena automáticamente varias hojas de un mismo libro de Google Sheets por la columna C (columna 3), dejando la fila 1 como cabecera.**

Más concreto:

- Coge el archivo de Google Sheets donde está el script.
- Busca las hojas: `"GALLUR", "QUIQUE", "VICTOR M.", "GOR", "ALVARO", "OFICINA"`.
- En cada una:
  - Toma todos los datos desde la fila 2 hacia abajo (suponiendo que la fila 1 son los encabezados).
  - Ordena esas filas según la columna 3 (C) **en orden ascendente** (A→Z).

---

## 2. Explicación línea por línea

### Firma de la función

```js
function ordenarAutomaticoVariasHojas() {
```

- Defines una función llamada `ordenarAutomaticoVariasHojas`.
- Esta función no recibe parámetros, simplemente actúa sobre el libro activo.

---

### Obtener el libro (el fichero de Sheets)

```js
const libro = SpreadsheetApp.getActiveSpreadsheet();
```

- `SpreadsheetApp` es el servicio de Apps Script para trabajar con Google Sheets.
- `getActiveSpreadsheet()` devuelve **el Google Sheets donde está el script**.
- Lo guardas en la constante `libro`.

---

### Lista de hojas a procesar

```js
const hojas = ["GALLUR", "QUIQUE", "VICTOR M.", "GOR", "ALVARO", "OFICINA"]; // Nombres de las hojas a ordenar
```

- `hojas` es un array de strings.
- Cada string es el **nombre de una pestaña** del libro.
- Solo estas hojas se van a ordenar.
- Importante: los nombres tienen que coincidir exactamente con los de las pestañas (mayúsculas, espacios, puntos, etc.).

---

### Columna por la que se ordena

```js
const columnaOrden = 3; // Columna por la que se ordena (propietarios)
```

- Aquí defines la **columna de ordenación**.
- En Apps Script, las columnas son 1-based:
  - 1 = A
  - 2 = B
  - 3 = C
- Así que `3` significa **columna C**.
- Si mañana quieres ordenar por otra:
  - Columna B → `2`
  - Columna D → `4`, etc.

---

### Recorrer todas las hojas

```js
hojas.forEach(nombreHoja => {
```

- Recorres el array `hojas` con `forEach`.
- En cada iteración, `nombreHoja` será uno de estos valores: `"GALLUR"`, `"QUIQUE"`, etc.

---

### Obtener el objeto hoja

```js
  const hoja = libro.getSheetByName(nombreHoja);
```

- `getSheetByName(nombreHoja)` busca dentro del libro la pestaña cuyo nombre coincide con `nombreHoja`.
- Devuelve un objeto `Sheet` o `null` si no existe.

---

### Comprobar que la hoja existe

```js
  if (hoja) { // Verifica que la hoja exista
```

- Si no existe una hoja con ese nombre, `hoja` sería `null` o `undefined`.
- Este `if` evita errores si cambiaste el nombre de alguna pestaña o borraste una.

---

### Última fila y última columna con datos

```js
    const ultimaFila = hoja.getLastRow();
    const ultimaColumna = hoja.getLastColumn();
```

- `getLastRow()` → número de la última fila que tiene **algún dato**.
- `getLastColumn()` → número de la última columna que tiene **algún dato**.
- Con esto calculas el área de datos real de la hoja.

---

### Comprobar que hay datos debajo de la cabecera

```js
    if (ultimaFila > 1) { // Evita errores si la hoja está vacía
```

- Se asume que:
  - Fila 1 = cabecera.
  - Fila 2 en adelante = datos.
- Si `ultimaFila` es 1 → solo hay cabecera, no hay nada que ordenar.
- Si `ultimaFila` > 1 → hay al menos una fila de datos.

---

### Definir el rango a ordenar

```js
      const rango = hoja.getRange(2, 1, ultimaFila - 1, ultimaColumna);
```

`getRange(filaInicial, columnaInicial, numFilas, numColumnas)`:

- `2` → empieza en la **fila 2** (debajo de la cabecera).
- `1` → empieza en la **columna 1** (columna A).
- `ultimaFila - 1` → número de filas a incluir:
  - Si la última fila con datos es la 10 → `10 - 1 = 9` filas (de la 2 a la 10).
- `ultimaColumna` → el número total de columnas del rango, desde la A hasta la última con datos.

En resumen:  
👉 **Selecciona todo el bloque de datos (sin contar la fila de títulos) desde A2 hasta la última fila y última columna con datos.**

---

### Ordenar el rango

```js
      rango.sort({column: columnaOrden, ascending: true});
```

- `rango.sort(...)` ordena todas las filas de ese rango.
- `{ column: columnaOrden, ascending: true }`:
  - `column: columnaOrden` → ordena por la columna que has definido antes (`3` → C).
  - `ascending: true` → orden ascendente (A→Z, menor→mayor).
- Puedes cambiar a descendente si quieres:
  - `ascending: false`.

---

### Cierre de bloques

```js
    }
  });
}
```

- Cierran el `if (hoja)`, el `forEach`, y por último la función.

---

## 3. ¿Para qué sirve en la práctica?

Escenario típico:

- Tienes varias hojas, una por comercial / gestor: `GALLUR`, `QUIQUE`, etc.
- Cada hoja tiene una tabla con:
  - Fila 1 → cabecera (Propietario, Teléfono, Zona, etc.).
  - Fila 2 en adelante → registros.
- Quieres que **todas las hojas estén siempre ordenadas**, por ejemplo:
  - Por nombre de propietario (columna C).
  - O por alguna otra columna fija.

Este script te ahorra:

- Tener que ordenar manualmente hoja por hoja.
- Que cada comercial tenga su hoja desordenada.
- Inconsistencias entre pestañas.

---

## 4. ¿Cómo usarlo en tu Google Sheets?

Te lo dejo paso a paso:

### 4.1. Crear el script

1. Abre tu Google Sheets.
2. Menú: **Extensiones → Apps Script**.
3. Se abrirá el editor de Scripts en una pestaña nueva.
4. Borra el código que haya (si es el típico `myFunction`) y pega:

   ```js
   function ordenarAutomaticoVariasHojas() {
     const libro = SpreadsheetApp.getActiveSpreadsheet();
     const hojas = ["GALLUR", "QUIQUE", "VICTOR M.", "GOR", "ALVARO", "OFICINA"];

     const columnaOrden = 3;

     hojas.forEach(nombreHoja => {
       const hoja = libro.getSheetByName(nombreHoja);
       if (hoja) {
         const ultimaFila = hoja.getLastRow();
         const ultimaColumna = hoja.getLastColumn();

         if (ultimaFila > 1) {
           const rango = hoja.getRange(2, 1, ultimaFila - 1, ultimaColumna);
           rango.sort({column: columnaOrden, ascending: true});
         }
       }
     });
   }
   ```

5. Guarda el proyecto (Ctrl+S o icono de guardar).

---

### 4.2. Ejecutarlo manualmente

1. En la parte superior del editor, selecciona la función `ordenarAutomaticoVariasHojas` en el desplegable.
2. Haz clic en el botón ▶ Ejecutar.
3. La primera vez te pedirá permisos:
   - Elige tu cuenta.
   - Acepta los permisos (Apps Script necesita acceder a tus hojas para ordenarlas).
4. Vuelve al Google Sheets y verás las hojas ordenadas por la columna C.

---

### 4.3. Ejecutarlo desde un botón (opcional)

Si quieres que alguien lo use sin entrar a Apps Script:

1. En el Sheets, inserta un dibujo:  
   **Insertar → Dibujo → Nuevo** (o una imagen).
2. Crea un botón (un rectángulo con texto “Ordenar todo”).
3. Guarda y coloca el dibujo en la hoja.
4. Haz clic derecho sobre el dibujo → **Asignar script…**
5. Escribe el nombre de la función:

   ```text
   ordenarAutomaticoVariasHojas
   ```

6. A partir de ahí, cada vez que pulsen el botón, se ejecutará el ordenado.

---

### 4.4. Ejecutarlo automáticamente (disparador / trigger) (opcional)

Si quieres que se ordene solo, por ejemplo cada vez que alguien edita algo:

1. En Apps Script, en el menú lateral, ve a **Desencadenadores** (Triggers).
2. Añade un nuevo desencadenador:
   - Elige la función: `ordenarAutomaticoVariasHojas`.
   - Tipo de evento:
     - `Al editar` (on edit) → cada vez que se edite la hoja.
     - o `Basado en tiempo` → cada X minutos/horas.
3. Guarda.

**Mi opinión:**  
Para muchas ediciones, un trigger “Al editar” puede ser pesado (ejecuta en cada cambio). Yo usaría uno **basado en tiempo** (por ejemplo, cada 15 minutos o cada hora) o simplemente un botón manual si el volumen de datos no es enorme.

---

## 5. Cómo adaptarlo a lo que necesites

Algunas variaciones típicas:

- Ordenar por otra columna:
  ```js
  const columnaOrden = 5; // columna E
  ```
- Ordenar en descendente:
  ```js
  rango.sort({column: columnaOrden, ascending: false});
  ```
- Añadir más hojas:
  ```js
  const hojas = ["GALLUR", "QUIQUE", "NUEVA HOJA", "OTRA HOJA"];
  ```
