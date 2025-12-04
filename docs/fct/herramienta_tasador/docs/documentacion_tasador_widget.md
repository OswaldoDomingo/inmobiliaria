# Documentación técnica del widget de **Tasación Online**

Este documento explica el funcionamiento del componente de **Tasación Online** que se integra en la web de la inmobiliaria.  
El foco principal de la documentación es la **lógica JavaScript**, ya que es donde está la mayor parte de la inteligencia de negocio.  
El HTML y el CSS se explican de forma más resumida para dar contexto.

---

## 1. Visión general del widget

El widget permite:

1. **Leer datos de mercado** (precio/m² por código postal, barrio y zona) desde una hoja de cálculo de Google Sheets exportada como CSV.
2. Permitir al usuario introducir datos de su inmueble:
   - Código postal, barrio y zona.
   - Superficie en m².
   - Orientación (exterior/interior).
   - Estado (a reformar, reformado, entrar a vivir).
   - Ascensor (con/sin), planta y características especiales como ático o bajo.
   - Extras (balcón/terraza).
3. Aplicar **reglas de configuración** (también definidas en una hoja de cálculo) que ajustan el precio según esas características.
4. Calcular un **rango de valoración** (precio mínimo y máximo) y mostrarlo al usuario.
5. Pedir los datos de contacto y, a través de **EmailJS**, enviar:
   - Un correo al cliente con el informe de tasación.
   - Un correo a la agencia con los datos de contacto y la tasación.
6. Mostrar un bloque final con la tasación y el listado de características que se han tenido en cuenta.

Todo esto se hace sin recargar la página, únicamente con JavaScript.

---

## 2. Librerías externas utilizadas

En la cabecera se cargan varias librerías por CDN:

```html
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
```

- **TailwindCSS**: librería de utilidades CSS para maquetar rápidamente con clases en el HTML.
- **PapaParse**: librería JavaScript para leer y parsear ficheros CSV (en este caso, la exportación de Google Sheets).
- **EmailJS**: servicio externo que permite enviar correos directamente desde el frontend sin necesitar backend propio.
- **Lucide**: librería de iconos SVG que se insertan dinámicamente en el DOM.
- **Google Fonts (Inter)**: tipografía principal del widget.

Estas librerías permiten que toda la lógica se pueda ejecutar en el navegador sin código PHP ni servidor adicional para esta parte.

---

## 3. Estilos CSS personalizados

En el `<style>` se define:

- Fuente por defecto (`Inter`) y color de fondo general.
- Clases `.hidden-animated` y `.visible-animated` para mostrar/ocultar bloques con transiciones suaves (por ejemplo, la planta sólo cuando no hay ascensor).
- Personalización de los scrollbars de los `<select>` para mejorar la experiencia de usuario.
- Estilos para checkboxes personalizados y el checkbox de GDPR (`.gdpr-checkbox`).
- Animación `fadeIn` para transiciones de entrada suaves de algunos contenedores.
- Un pequeño **spinner loader** (`.loader`) usado cuando se envía el formulario de contacto.

El objetivo del CSS es mejorar la experiencia visual, pero la lógica importante está en el JavaScript.

---

## 4. Estructura HTML del widget

El HTML principal está envuelto en un `div` con id `valuation-widget`.  
Dentro encontramos dos grandes columnas:

1. **Columna izquierda (formulario técnico del inmueble)**  
   Aquí se recogen los datos necesarios para la tasación:
   - Código Postal (`input-cp`).
   - Barrio (`select-barrio`, habilitado cuando se introduce CP).
   - Zona (`select-zona`, habilitado cuando se selecciona barrio).
   - Superficie (`input-surface`).
   - Orientación (`select-orientation`).
   - Estado (`select-state`).
   - Ascensor (`select-elevator`).
   - Planta (`select-floor`, solo visible si no hay ascensor).
   - Extras: balcón/terraza, ático, bajo.

   Al final de esta columna está el botón:
   ```html
   <button id="btn-calculate">TASAR INMUEBLE</button>
   ```
   que inicia el cálculo, pero primero lleva al formulario de contacto.

2. **Columna derecha (contacto y resultado)**  
   Tiene tres estados:
   - `result-placeholder`: mensaje inicial “Listo para tasar”.
   - `contact-form-container`: formulario de contacto que se muestra con el rango de precio estimado.
   - `result-content`: bloque final con la tasación detallada y la lista de características aplicadas.

El JavaScript se encarga de mostrar/ocultar estos bloques según el flujo del usuario.

---

## 5. Lógica JavaScript – visión global

Toda la lógica está contenida en un `<script>` al final del HTML.  
A grandes rasgos, el flujo es:

1. **Arranque de la aplicación** (`startApp` → `bootstrap`).
2. Obtener referencias a los elementos del DOM (`collectDomElements`).
3. Inicializar iconos (`ensureIcons`), EmailJS (`initEmailJS`) y cargar datos de Google Sheets (`initData`).
4. Configurar todos los manejadores de eventos (`setupEventListeners`).
5. Validar el formulario técnico y permitir tasar.
6. Calcular la tasación (`prepareCalculation`).
7. Validar el formulario de contacto y permitir enviar.
8. Enviar emails y mostrar resultado (`sendEmailsAndShowResult` + `renderFinalResult`).

A continuación se detalla cada bloque de la lógica JavaScript.

---

## 6. Función `ensureIcons` – Inicialización de iconos Lucide

```js
function ensureIcons(retries = 50) {
    if (window.lucide && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
        return;
    }
    if (retries > 0) {
        setTimeout(() => ensureIcons(retries - 1), 100);
    }
}
```

### ¿Qué hace?

- Comprueba si la librería `lucide` está cargada y si dispone de la función `createIcons`.
- Si está disponible, llama a `lucide.createIcons()` para que sustituya los elementos `<i data-lucide="...">` por los SVG reales.
- Si no está disponible (por ejemplo, si aún no ha terminado de cargarse el script de Lucide), reintenta hasta **50 veces** con un retardo de 100ms entre intentos.

### ¿Por qué es necesaria?

Como la carga de los scripts por CDN no es instantánea ni está garantizado el orden exacto, esta función asegura que **cuando el DOM ya contiene los iconos**, se invoque `createIcons()` en cuanto la librería esté lista, evitando errores.

---

## 7. Configuración de datos y EmailJS

```js
const URL_DATOS_CSV = 'https://docs.google.com/...&output=csv';
const URL_CONFIG_CSV = 'https://docs.google.com/...&output=csv';
```

- `URL_DATOS_CSV`: URL de la hoja de Google Sheets con el listado de precios de mercado (CP, barrio, zona, precio_m2).
- `URL_CONFIG_CSV`: URL con las reglas de configuración (modificadores de precio según características).

```js
const EMAIL_CONFIG = {
    PUBLIC_KEY: 'XXXX',
    SERVICE_ID: 'XXXX',
    TEMPLATE_ID_CLIENTE: 'template_4xqkmut',
    TEMPLATE_ID_AGENCIA: 'template_52mn2m6'
};
```

- `EMAIL_CONFIG`: objeto con los datos necesarios para trabajar con EmailJS:
  - `PUBLIC_KEY`: clave pública para inicializar la librería en el frontend.
  - `SERVICE_ID`: identificador del servicio configurado en EmailJS (por ejemplo, cuenta de Gmail o SMTP).
  - `TEMPLATE_ID_CLIENTE`: plantilla de email que se envía al cliente.
  - `TEMPLATE_ID_AGENCIA`: plantilla de email que se envía a la agencia.

```js
const MOCK_DATOS_PROPIEDADES = [ ... ];
const MOCK_CONFIGURACION = [ ... ];
```

- **Mocks** de datos y configuración que se usan como **plan B** si falla la carga desde Google Sheets.  
  Esto garantiza que el widget pueda seguir funcionando (al menos en entorno de pruebas o demo) incluso si hay un problema externo con las URLs.

```js
let marketData = [];
let configRules = [];
let currentCalculation = null;
let els = null;
```

- `marketData`: array con los registros de precios por zona (cargados del CSV).
- `configRules`: array con las reglas de modificación de precio (balcón, exterior, etc.).
- `currentCalculation`: objeto donde se guarda el resultado de la última tasación calculada.
- `els`: objeto donde se guardarán todas las referencias a elementos del DOM, para no llamar `document.getElementById` constantemente.

---

## 8. Función `collectDomElements` – Referencias al DOM

```js
function collectDomElements() {
    return {
        cp: document.getElementById('input-cp'),
        barrio: document.getElementById('select-barrio'),
        zona: document.getElementById('select-zona'),
        surface: document.getElementById('input-surface'),
        orientation: document.getElementById('select-orientation'),
        state: document.getElementById('select-state'),
        elevator: document.getElementById('select-elevator'),
        floor: document.getElementById('select-floor'),
        floorContainer: document.getElementById('container-floor'),
        checkBalcony: document.getElementById('check-balcony'),
        checkPenthouse: document.getElementById('check-penthouse'),
        penthouseContainer: document.getElementById('container-penthouse'),
        checkGround: document.getElementById('check-ground'),
        groundContainer: document.getElementById('container-ground'),
        // Botones y paneles
        btnCalculate: document.getElementById('btn-calculate'),
        resultPlaceholder: document.getElementById('result-placeholder'),
        resultContent: document.getElementById('result-content'),
        // Contacto
        contactContainer: document.getElementById('contact-form-container'),
        inputEmail: document.getElementById('input-email'),
        inputPhone: document.getElementById('input-phone'),
        checkPrivacy: document.getElementById('check-privacy'),
        checkCommercial: document.getElementById('check-commercial'),
        btnSendLead: document.getElementById('btn-send-lead'),
        previewPriceRange: document.getElementById('preview-price-range'),
        sendErrorMsg: document.getElementById('send-error-msg'),
        // Resultados
        priceMin: document.getElementById('price-min'),
        priceMax: document.getElementById('price-max'),
        modifiersList: document.getElementById('modifiers-list')
    };
}
```

### ¿Qué hace?

- Centraliza la obtención de todos los elementos HTML que el script va a utilizar.
- Devuelve un objeto con propiedades bien nombradas (cp, barrio, zona, etc.) para trabajar más cómodo y legible.

### ¿Por qué es buena práctica?

- Evita repetir código (`document.getElementById(...)` en muchos sitios).
- Deja muy claro qué elementos del DOM forman parte del widget.
- Facilita el mantenimiento y la lectura del código.

---

## 9. Inicialización de EmailJS: `initEmailJS`

```js
function initEmailJS() {
    if (window.emailjs && EMAIL_CONFIG.PUBLIC_KEY && EMAIL_CONFIG.PUBLIC_KEY.length > 5) {
        try {
            emailjs.init(EMAIL_CONFIG.PUBLIC_KEY);
            console.log("EmailJS inicializado");
        } catch (e) {
            console.warn("Error al inicializar EmailJS:", e);
        }
    } else {
        console.warn("EmailJS no configurado o clave vacía.");
    }
}
```

### ¿Qué hace?

- Comprueba que la librería `emailjs` está disponible en el objeto global `window` y que la clave pública (`PUBLIC_KEY`) no está vacía.
- Si todo es correcto, inicializa EmailJS con `emailjs.init(...)`.
- Muestra mensajes en la consola tanto en caso de éxito como de fallo.

### ¿Por qué?

- Evita errores en tiempo de ejecución si, por ejemplo, la clave no está configurada en producción o si el script de EmailJS no se ha cargado correctamente.
- Permite hacer **entornos de prueba** donde el envío real de correos puede estar desactivado, pero el resto del flujo sigue funcionando.

---

## 10. Carga de datos desde Google Sheets: `initData` y `fetchCSV`

### 10.1. `initData`

```js
async function initData() {
    try {
        const [dataRaw, configRaw] = await Promise.all([
            fetchCSV(URL_DATOS_CSV, MOCK_DATOS_PROPIEDADES, 'Datos'),
            fetchCSV(URL_CONFIG_CSV, MOCK_CONFIGURACION, 'Config')
        ]);
        marketData = dataRaw;
        configRules = configRaw;
    } catch (e) {
        console.error("Error cargando datos", e);
        marketData = MOCK_DATOS_PROPIEDADES;
        configRules = MOCK_CONFIGURACION;
    }
}
```

### ¿Qué hace?

- Llama en paralelo a `fetchCSV` para:
  - Cargar la tabla de **datos de mercado** (precios por zona).
  - Cargar la tabla de **configuración de reglas** (porcentajes y valores fijos de modificación).
- Usa `Promise.all` para que ambas peticiones se hagan a la vez y el tiempo de carga sea menor.
- Si todo va bien, guarda la información en `marketData` y `configRules`.
- Si hay un error (problema de red, CSV vacío, etc.), usa los **datos de mock** definidos al inicio, garantizando que el widget siga funcionando.

---

### 10.2. `fetchCSV` – Utilización de PapaParse

```js
function fetchCSV(url, mockFallback, type) {
    return new Promise((resolve) => {
        if (!url || url.includes('PON_AQUI') || url.length < 10) {
            resolve(mockFallback);
            return;
        }
        let finalUrl = url;
        if (url.includes('docs.google.com') && url.includes('/pubhtml')) {
            finalUrl = url.replace('/pubhtml', '/pub');
            finalUrl += finalUrl.includes('?') ? '&output=csv' : '?output=csv';
        }

        Papa.parse(finalUrl, {
            download: true, header: true, dynamicTyping: true, skipEmptyLines: true,
            complete: (results) => {
                if (results.data && results.data.length > 0) {
                    const cleanData = results.data
                        .filter(row => row && Object.keys(row).length > 0) 
                        .map(row => {
                            const newRow = {};
                            for (let key in row) {
                                if(!key) continue;
                                const cleanKey = key.trim().toLowerCase();
                                let val = row[key];
                                if (typeof val === 'string') val = val.trim();
                                newRow[cleanKey] = val;
                            }
                            return newRow;
                        });
                    resolve(cleanData);
                } else {
                    resolve(mockFallback);
                }
            },
            error: (err) => resolve(mockFallback)
        });
    });
}
```

### ¿Qué hace, paso a paso?

1. **Valida la URL**:
   - Si no hay URL, está vacía o es una URL de placeholder (contiene `PON_AQUI`), devuelve directamente los datos de mock (`mockFallback`).

2. **Ajusta la URL de Google Sheets si hace falta**:
   - Si la URL viene en formato `/pubhtml`, la convierte a `/pub` y añade `output=csv` para solicitarla directamente como CSV.

3. **Llama a PapaParse**:
   - `download: true`: indica que PapaParse debe descargar el archivo desde la URL.
   - `header: true`: la primera fila del CSV se considera cabecera y se usa para las claves de cada columna.
   - `dynamicTyping: true`: convierte a número, booleano, etc. cuando es posible.
   - `skipEmptyLines: true`: ignora líneas vacías para evitar registros basura.

4. **Procesa el resultado** (`complete`):
   - Comprueba que hay datos.
   - Filtra las filas vacías.
   - Normaliza las claves (`key.trim().toLowerCase()`) y limpia espacios en los valores de tipo texto.
   - Devuelve un array de objetos limpio, listo para usar desde el resto del código.

5. **Manejo de errores**:
   - Si PapaParse lanza un error, se devuelve el `mockFallback` sin romper la aplicación.

### ¿Por qué está hecho así?

- Permite **desacoplar completamente** el widget de la base de datos: los datos vienen desde una hoja de Google Sheets gestionable por marketing/comercial.
- El uso de PapaParse simplifica mucho el manejo del CSV.
- La limpieza de claves y valores evita errores por espacios, mayúsculas/minúsculas, etc.

---

## 11. Configuración de eventos: `setupEventListeners`

Esta función conecta todos los elementos del formulario con su lógica de negocio.

```js
function setupEventListeners() {
    // Filtros CP
    els.cp.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        els.barrio.innerHTML = '<option value="">Seleccione...</option>';
        els.barrio.disabled = true;
        els.zona.innerHTML = '<option value="">Seleccione Barrio...</option>';
        els.zona.disabled = true;
        els.btnCalculate.disabled = true;

        if (val.length >= 4) { 
            const barrios = [...new Set(marketData
                .filter(d => d && d.cp != null && String(d.cp).includes(val))
                .map(d => d.barrio))];
            
            if (barrios.length > 0) {
                barrios.forEach(b => {
                    if(!b) return;
                    const opt = document.createElement('option');
                    opt.value = b;
                    opt.textContent = b;
                    els.barrio.appendChild(opt);
                });
                els.barrio.disabled = false;
            }
        }
    });
```

### 11.1. Cambio en el código postal

- Cada vez que el usuario escribe en el input de CP:
  - Se reinician los selectores de barrio y zona.
  - Se deshabilita el botón de calcular.
  - Si el CP tiene al menos 4 caracteres, se buscan en `marketData` todos los barrios asociados a ese CP.
  - Se eliminan duplicados con `new Set` y se generan opciones en el `<select>` de barrio.
  - Si hay barrios, se habilita el selector de barrio.

La lógica usa `String(d.cp).includes(val)` para permitir que con 4 dígitos ya se sugieran barrios aunque el CP sea de 5 dígitos.

---

### 11.2. Cambio en el barrio

```js
els.barrio.addEventListener('change', (e) => {
    const barrio = e.target.value;
    const cp = els.cp.value.trim();
    els.zona.innerHTML = '<option value="">Seleccione...</option>';
    els.zona.disabled = true;

    if (barrio) {
        const zonas = [...new Set(marketData
            .filter(d => d && d.cp != null && String(d.cp).includes(cp) && d.barrio === barrio)
            .map(d => d.zona))];
        if (zonas.length > 0) {
            zonas.forEach(z => {
                if(!z) return;
                const opt = document.createElement('option');
                opt.value = z;
                opt.textContent = z;
                els.zona.appendChild(opt);
            });
            els.zona.disabled = false;
        }
    }
    validateCalculatorForm();
});
```

- Cuando cambia el barrio:
  - Se limpian las opciones de zona.
  - Se buscan las zonas disponibles para ese CP y barrio.
  - Se rellenan las opciones del select de zona.
  - Se llama a `validateCalculatorForm()` para comprobar si ya se puede habilitar el botón de tasar.

---

### 11.3. Validación de formulario técnico

```js
els.zona.addEventListener('change', validateCalculatorForm);
els.surface.addEventListener('input', validateCalculatorForm);
```

- Cada vez que se selecciona una zona o se introduce la superficie, se valida el formulario.

```js
function validateCalculatorForm() {
    const isValid = els.cp.value && els.barrio.value && els.zona.value && els.surface.value > 0;
    els.btnCalculate.disabled = !isValid;
}
```

- El botón "TASAR INMUEBLE" sólo se habilita si:
  - Hay CP, barrio, zona y superficie con valor mayor que 0.
- Esto evita lanzar un cálculo con datos incompletos.

---

### 11.4. Lógica de ascensor y planta

```js
els.elevator.addEventListener('change', (e) => {
    const hasElevator = e.target.value === 'yes';
    if (hasElevator) {
        els.floorContainer.className = 'hidden-animated';
        els.penthouseContainer.className = 'visible-animated flex items-center space-x-3 cursor-pointer group select-none';
        els.groundContainer.className = 'visible-animated flex items-center space-x-3 cursor-pointer group select-none';
    } else {
        els.floorContainer.className = 'visible-animated flex flex-col gap-1 w-full';
        els.penthouseContainer.className = 'hidden-animated';
        els.checkPenthouse.checked = false;
        els.groundContainer.className = 'hidden-animated';
        els.checkGround.checked = false; 
    }
});
```

- Si hay ascensor:
  - Se oculta el selector de planta (`floorContainer`).
  - Se muestran los checkboxes de **ático** y **bajo**.
- Si **no** hay ascensor:
  - Se muestra el selector de planta.
  - Se ocultan los checkboxes de ático y bajo y se desmarcan.

```js
els.checkGround.addEventListener('change', (e) => { if (e.target.checked) els.checkPenthouse.checked = false; });
els.checkPenthouse.addEventListener('change', (e) => { if (e.target.checked) els.checkGround.checked = false; });
```

- Asegura que la vivienda no pueda ser **a la vez ático y bajo**: si se marca uno, se desmarca el otro.

---

### 11.5. Validación del formulario de contacto

```js
const validateContact = () => {
    const emailValid = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(els.inputEmail.value);
    const phoneValid = els.inputPhone.value.length > 6;
    const privacy = els.checkPrivacy.checked;
    const commercial = els.checkCommercial.checked;
    
    if (emailValid && phoneValid && privacy && commercial) {
        els.btnSendLead.disabled = false;
    } else {
        els.btnSendLead.disabled = true;
    }
};
```

- Comprueba que:
  - El email tiene formato válido (expresión regular estándar).
  - El teléfono tiene al menos 7 caracteres.
  - Se han aceptado las casillas de privacidad y comunicaciones comerciales (cumplimiento GDPR).
- El botón **VER INFORME COMPLETO** sólo se habilita si todo lo anterior es correcto.

Se añaden los event listeners para ejecutar esta validación en cada cambio:

```js
els.inputEmail.addEventListener('input', validateContact);
els.inputPhone.addEventListener('input', validateContact);
els.checkPrivacy.addEventListener('change', validateContact);
els.checkCommercial.addEventListener('change', validateContact);
```

---

### 11.6. Acciones principales

```js
els.btnCalculate.addEventListener('click', prepareCalculation);
els.btnSendLead.addEventListener('click', sendEmailsAndShowResult);
```

- **btnCalculate** → `prepareCalculation`: calcula la tasación y muestra el formulario de contacto.
- **btnSendLead** → `sendEmailsAndShowResult`: envía los correos (si EmailJS está configurado) y muestra el resultado final.

---

## 12. Cálculo de la tasación: `prepareCalculation`

```js
function prepareCalculation() {
    const cp = els.cp.value;
    const barrio = els.barrio.value;
    const zona = els.zona.value;
    const surface = parseFloat(els.surface.value);

    const record = marketData.find(d => 
        d && d.cp != null && String(d.cp).includes(cp) && d.barrio === barrio && d.zona === zona
    );

    if (!record) { alert("Error: No se encontró precio para esta zona."); return; }

    let basePrice = record.precio_m2 * surface;
    let currentPrice = basePrice;
    let modifiersList = [];
    let modifiersText = [];

    const apply = (key, condition, labelOverride = null) => {
        if (!condition) return;
        const cleanKey = key.trim().toLowerCase();
        const rule = configRules.find(c => c && c.clave && String(c.clave).trim().toLowerCase() === cleanKey);
        if (rule) {
            if (rule.tipo_operacion === 'porcentaje') currentPrice += basePrice * (rule.valor / 100);
            else currentPrice += rule.valor;
            
            const label = labelOverride || key.replace(/_/g, ' ');
            modifiersList.push({ label: label });
            modifiersText.push(label);
        }
    };

    // Aplicar reglas
    apply('balcon_terraza', els.checkBalcony.checked, 'Con Balcón/Terraza');
    apply('exterior', els.orientation.value === 'exterior', 'Exterior');
    apply('interior', els.orientation.value === 'interior', 'Interior');
    const isGround = (els.elevator.value === 'yes' && els.checkGround.checked) || (els.elevator.value === 'no' && parseInt(els.floor.value) === 0);
    apply('bajo', isGround, 'Es un Bajo');
    
    if (els.elevator.value === 'yes') {
        apply('atico', els.checkPenthouse.checked, 'Es un Ático');
    } else {
        const floor = parseInt(els.floor.value);
        if (floor >= 1 && floor <= 6) apply(`sin_ascensor_piso_${floor}`, true, `Sin ascensor (${floor}º)`);
        else if (floor >= 7) apply('sin_ascensor_piso_7+', true, 'Sin ascensor (7º+)');
    }
    
    apply('estado_reformar', els.state.value === 'reformar', 'A Reformar');
    apply('estado_reformado', els.state.value === 'reformado', 'Reformado');
    apply('estado_entrar', els.state.value === 'entrar', 'Entrar a vivir');

    const priceMin = currentPrice * 0.90;
    const priceMax = currentPrice * 1.10;
    const fmt = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 });

    currentCalculation = {
        min: fmt.format(priceMin),
        max: fmt.format(priceMax),
        rawMin: priceMin,
        rawMax: priceMax,
        modifiers: modifiersList,
        modifiersString: modifiersText.join(', ') || 'Estándar',
        cp, barrio, zona, surface
    };

    els.previewPriceRange.textContent = `${fmt.format(priceMin)} - ${fmt.format(priceMax)}`;
    els.resultPlaceholder.classList.add('hidden');
    els.contactContainer.classList.remove('hidden');
    els.contactContainer.classList.add('flex');
}
```

### Paso a paso

1. **Recoger datos del formulario técnico**: CP, barrio, zona y superficie.
2. **Buscar el registro de mercado** en `marketData` que coincida con CP, barrio y zona.
3. Si no se encuentra, se avisa al usuario y se aborta.

4. **Calcular precio base**:
   - `basePrice = record.precio_m2 * surface;`
   - `currentPrice` empieza siendo igual al precio base y luego se va modificando con reglas.

5. **Definir función auxiliar `apply`**:
   - Recibe:
     - `key`: nombre de la regla (ej. `balcon_terraza`, `exterior`...).
     - `condition`: condición booleana que indica si esa regla aplica o no.
     - `labelOverride`: texto descriptivo amigable para mostrar al usuario.
   - Si la condición es verdadera:
     - Busca en `configRules` la regla cuya `clave` coincida.
     - Si `tipo_operacion` es `porcentaje`, suma al `currentPrice` el porcentaje del `basePrice` indicado en `rule.valor`.
     - Si es una operación fija, suma directamente `rule.valor`.
     - Añade la etiqueta de modificación a `modifiersList` y `modifiersText` para mostrarla en el informe.

6. **Aplicar reglas concretas** según la entrada del usuario:
   - Balcón/Terraza, exterior/interior.
   - Es un bajo (con o sin ascensor, dependiendo de planta o checkbox).
   - Es ático (solo con ascensor).
   - Penalizaciones por pisos altos sin ascensor (según planta).
   - Estado del inmueble: reformar, reformado, entrar a vivir.

7. **Calcular rango de precios**:
   - `priceMin = currentPrice * 0.90;` (−10%).
   - `priceMax = currentPrice * 1.10;` (+10%).
   - Se formatean con `Intl.NumberFormat` para mostrar valores en euros en formato español.

8. **Guardar el resultado** en `currentCalculation` para poder usarlo después (en el formulario de contacto y en el informe final).

9. **Actualizar la UI**:
   - Se muestra el rango en `previewPriceRange` dentro del bloque del formulario de contacto.
   - Se oculta el placeholder inicial y se muestra el contenedor de contacto.

Este enfoque separa claramente el **cálculo de negocio** de la **presentación**, lo cual es una buena práctica de diseño.

---

## 13. Envío de correos y transición al resultado: `sendEmailsAndShowResult`

```js
async function sendEmailsAndShowResult() {
    const btn = els.btnSendLead;
    const originalText = btn.innerHTML;
    
    // UI Loading
    btn.disabled = true;
    btn.innerHTML = '<div class="loader"></div><span class="ml-2">Procesando...</span>';
    els.sendErrorMsg.classList.add('hidden');

    const params = {
        to_email: els.inputEmail.value,
        user_phone: els.inputPhone.value,
        cp: currentCalculation.cp,
        barrio: currentCalculation.barrio,
        zona: currentCalculation.zona,
        superficie: currentCalculation.surface,
        precio_min: currentCalculation.min,
        precio_max: currentCalculation.max,
        caracteristicas: currentCalculation.modifiersString,
        date: new Date().toLocaleDateString('es-ES', {  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }),
        reply_to: els.inputEmail.value
    };

    try {
        if (EMAIL_CONFIG.PUBLIC_KEY && EMAIL_CONFIG.PUBLIC_KEY.length > 5 && !EMAIL_CONFIG.PUBLIC_KEY.includes('PON_AQUI')) {
            const p1 = emailjs.send(EMAIL_CONFIG.SERVICE_ID, EMAIL_CONFIG.TEMPLATE_ID_AGENCIA, params);
            const p2 = emailjs.send(EMAIL_CONFIG.SERVICE_ID, EMAIL_CONFIG.TEMPLATE_ID_CLIENTE, params);
            
            await Promise.all([p1, p2]);
            console.log("Emails enviados correctamente");
        } else {
            console.warn("EmailJS no está configurado completamente. Simulando envío.");
            await new Promise(r => setTimeout(r, 1500)); // Simular retardo
        }
        
        els.contactContainer.classList.add('hidden');
        renderFinalResult();

    } catch (error) {
        console.error("Error intentando enviar email (continuando flujo):", error);
        els.contactContainer.classList.add('hidden');
        renderFinalResult();
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}
```

### ¿Qué hace?

1. **Feedback de carga en el botón**:
   - Deshabilita el botón y modifica su contenido para mostrar el spinner (`.loader`) y el texto “Procesando...”.

2. **Construye `params`**:
   - Datos de contacto (email, teléfono).
   - Datos del inmueble y de la tasación (`cp`, `barrio`, `zona`, `superficie`, `precio_min`, `precio_max`, `caracteristicas`).
   - Fecha del cálculo formateada en español.

3. **Envío con EmailJS**:
   - Comprueba que la clave de EmailJS parece válida.
   - Si lo está, lanza dos peticiones en paralelo:
     - Una para la agencia (`TEMPLATE_ID_AGENCIA`).
     - Otra para el cliente (`TEMPLATE_ID_CLIENTE`).
   - Usa `Promise.all` para esperar a que ambas terminen.

4. **Simulación si no hay EmailJS configurado**:
   - Si la clave no está configurada, en vez de fallar, simula un retardo de 1.5 segundos y continúa el flujo.
   - Esto es útil para entornos demo o desarrollo.

5. **Gestión de errores**:
   - Si `emailjs.send` falla lanza un error, pero el flujo no se rompe.
   - En el `catch` se escribe un error en consola y **aun así** se llama a `renderFinalResult()` para no perder al usuario.

6. **Restaurar estado del botón** y mostrar el resultado final.

Este diseño prioriza la experiencia de usuario: incluso si el envío de emails falla, el usuario no pierde la tasación y puede verla igualmente.

---

## 14. Renderizado del resultado final: `renderFinalResult`

```js
function renderFinalResult() {
    els.priceMin.textContent = currentCalculation.min;
    els.priceMax.textContent = currentCalculation.max;

    els.modifiersList.innerHTML = '';
    if (currentCalculation.modifiers.length > 0) {
        const header = document.createElement('p');
        header.className = "text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-2";
        header.textContent = "Características del inmueble";
        els.modifiersList.appendChild(header);

        currentCalculation.modifiers.forEach(mod => {
            const row = document.createElement('div');
            row.className = "flex items-center text-sm text-gray-600 mb-2";
            row.innerHTML = `<i data-lucide="check-circle-2" class="w-4 h-4 text-indigo-500 mr-2"></i><span class="capitalize">${mod.label}</span>`;
            els.modifiersList.appendChild(row);
        });
        ensureIcons();
    }

    els.resultContent.classList.remove('hidden');
    els.resultContent.classList.add('flex');
}
```

### ¿Qué hace?

1. Actualiza los elementos `priceMin` y `priceMax` con las cantidades formateadas.
2. Limpia el contenedor `modifiersList` y, si hay modificadores:
   - Crea un encabezado “Características del inmueble”.
   - Para cada modificador, genera una fila con un icono Lucide (`check-circle-2`) y el texto de la característica.
   - Vuelve a llamar a `ensureIcons()` para que Lucide reemplace los `<i data-lucide="...">` por SVG.

3. Muestra el bloque `resultContent` (estaba oculto hasta este momento).

Con esto, el usuario ve un resumen claro del rango de precio y de las características que han influido en la tasación.

---

## 15. Arranque seguro de la aplicación: `bootstrap` y `startApp`

```js
function bootstrap() {
    const widget = document.getElementById('valuation-widget');
    if (!widget) {
        setTimeout(bootstrap, 50);
        return;
    }

    els = collectDomElements();
    if (!els || !els.cp || !els.barrio || !els.zona || !els.surface) {
        setTimeout(bootstrap, 50);
        return;
    }

    ensureIcons();
    initEmailJS();
    initData();
    setupEventListeners();
}

function startApp() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
}

startApp();
```

### ¿Qué hace `bootstrap`?

- Comprueba que el contenedor del widget (`valuation-widget`) existe en el DOM.
- Si aún no está, reintenta cada 50ms.
- Cuando existe, llama a `collectDomElements` para rellenar `els`.
- Si por alguna razón alguna referencia importante aún no está, vuelve a reintentar.
- Cuando todo está correcto:
  - Inicializa iconos (`ensureIcons`).
  - Inicializa EmailJS (`initEmailJS`).
  - Carga datos de Google Sheets (`initData`).
  - Configura listeners (`setupEventListeners`).

### ¿Qué hace `startApp`?

- Comprueba el estado del documento:
  - Si el DOM todavía está cargándose, se suscribe al evento `DOMContentLoaded`.
  - Si el DOM ya está listo, llama directamente a `bootstrap`.

### ¿Por qué este diseño?

- Este patrón hace que el widget sea **robusto** incluso cuando se inyecta dinámicamente en páginas que ya están cargadas o si cambia el orden de los scripts.
- Evita errores típicos como *"Cannot read properties of null (reading 'addEventListener')"* porque siempre se asegura que los elementos existen antes de trabajar con ellos.

---

## 16. Resumen técnico para el tribunal

- El widget implementa una **mini arquitectura de capas** dentro del frontend:
  - **Capa de datos**: carga desde Google Sheets en formato CSV usando PapaParse, con mocks de respaldo.
  - **Capa de configuración**: reglas de negocio parametrizadas (porcentajes e importes) que se pueden mantener sin tocar código.
  - **Capa de lógica de negocio**: funciones como `prepareCalculation` aplican las reglas sobre el precio base, en función de las características de la vivienda.
  - **Capa de presentación**: JavaScript actualiza el DOM (mostrar/ocultar bloques, rellenar listas, formatear precios).
  - **Capa de integración externa**: envío de correos a través de EmailJS, con gestión de errores y modo simulación.

- Se siguen buenas prácticas de JavaScript:
  - Separación de responsabilidades por función (`initData`, `setupEventListeners`, `prepareCalculation`, etc.).
  - Uso de funciones auxiliares (`apply`, `validateContact`, `validateCalculatorForm`).
  - Manejo de estados (`currentCalculation`, `marketData`, `configRules`) y del DOM (`els`).
  - Manejo de errores y fallbacks (try/catch, mocks, logs de consola).
  - Carga segura y progresiva (`startApp` + `bootstrap`).

- El resultado es un widget **desacoplado del backend**, configurable desde hojas de cálculo y que puede integrarse en diferentes páginas o CRMs incrustando el HTML y el script.

Este documento puede acompañar al código fuente para justificar las decisiones técnicas y explicar el entendimiento del flujo completo de la tasación y la captura de datos.
