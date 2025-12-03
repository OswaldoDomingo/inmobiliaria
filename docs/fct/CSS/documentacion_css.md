# Documentación CSS – Portal Inmobiliario Magnus

## 1. Objetivo del documento

Este documento recoge la **documentación técnica específica de la hoja de estilos CSS** utilizada para personalizar el portal inmobiliario de Magnus sobre la plantilla estándar del CRM Inmovilla.

Su finalidad es:

- Entender **qué hace** cada bloque de reglas CSS.
- Facilitar el **mantenimiento y ampliación** del diseño.
- Servir como **referencia rápida** para desarrolladores y para la memoria del proyecto.

---

## 2. Estructura lógica del CSS

La hoja de estilos está organizada, de forma conceptual, en los siguientes bloques:

1. **Ajustes globales**
   - Ocultación de iconos y textos que no interesan a nivel de marca:
     - Icono RSS (`img[src*="rss2.png"]`).
     - Texto “Diseñado por CRM Inmovilla” (`span:has(a[href*="facebook.com/crminmovilla"])`).

2. **Cabecera y navegación**
   - Unificación de colores y fondo en:
     - `#modulo-cabecera-1`
     - `#modulo-cabecera-2`
   - Estilos para enlaces de cabecera:
     - Color base blanco, hover en gris claro.
   - Pseudoelemento `#header-bloquemenu nav::before`:
     - Muestra el texto **“Menu”** en móvil.
     - Solo se activa bajo `@media (max-width: 900px)`.
   - Estilos específicos para versión móvil:
     - Fondo corporativo en bloques de cabecera.
     - Tipografía clara (texto blanco).
     - Botón de teléfono centrado con aspecto de “pill-button”.

3. **Reset del subrayado amarillo del menú**
   - Algunos temas de Inmovilla añaden subrayados decorativos (color amarillo) mediante bordes, `background-image`, `box-shadow` o pseudo-elementos.
   - El CSS actual:
     - Elimina cualquier subrayado residual en:
       - `#modulo-cabecera-1 .header-menu ...`
       - `#modulo-cabecera-2 .header-menu ...`
     - Fuerza:
       - `text-decoration: none`
       - `border: 0`
       - `box-shadow: none`
       - `background: transparent`
       - `::before` y `::after` con `content: none`
     - Define una **nueva línea de activación limpia**:
       - En hover / activo:
         - Color de texto azul claro corporativo (`#C9DAFF`).
         - `border-bottom` de 2px del mismo color.

4. **Módulo “Valoramos tu piso”**
   - Ajuste de separación respecto a la cabecera:
     - `#modulo-valoramostupiso-2` utiliza `margin-top` y `margin-left`.
   - Tratamiento de la imagen de fondo del módulo:
     - `.modulo-valoramostupiso`:
       - `background-size: contain`
       - `background-position: center`
       - `background-repeat: no-repeat`
       - `background-color: #fff` como fondo de seguridad.

5. **Buscador principal (#slider-buscador)**
   - `#slider-buscador` se convierte en una “card” principal:
     - `border-radius` para esquinas redondeadas.
     - `overflow: hidden` para evitar que inputs/botones sobresalgan.
     - `box-shadow` suave tipo tarjeta.
   - Inputs y botones dentro del bloque:
     - `border-radius` coherente.
     - Margen y alineaciones para mejorar usabilidad.

6. **Selector de tipo de propiedad (#inmotipos)**
   - Selector principal:
     - `#inmotipos.buscadorTipos.custom-select-html.enviarPostHog`.
   - Objetivos:
     - Unificar aspecto visual (borde, radio, fondo blanco).
     - Evitar que el `overflow` corte el desplegable (`overflow: visible !important`).
   - Pseudo-elementos `::before` y `::after`:
     - Se utilizan solo como soporte visual.
     - Se les aplica `pointer-events: none` para que **no bloqueen el click**.

7. **Buscador por áreas**
   - Estilo del bloque `#slider-bloque-buscador` y elementos internos:
     - Inputs con tipografía uniforme y negrita suave.
     - `border-radius` para redondear esquinas.
   - Pseudo-elemento `.custom-select::after`:
     - Dibuja la flecha del desplegable (`▼`).
     - Se cuida que:
       - No interfiera con el click (`pointer-events: none`).
       - Mantenga un fondo transparente para integrarse bien con el campo.

8. **Módulo de alertas**
   - Ocultación de título y comillas decorativas:
     - `#modulo-alertas-1 > .tituloAlerta { display: none; }`
     - `.textoComillas::before { display: none; }`
   - Rediseño del mensaje principal:
     - `#alertas > .blockquote > p` se vuelve transparente.
     - Se inserta un texto informativo mediante `::after`, centrado y dentro de un recuadro con borde y `border-radius`.
   - Contenedor `.bloqueAlertaContenedores`:
     - Se trata como card:
       - Borde de color corporativo.
       - `padding` y `border-radius`.
       - Separación vertical (`margin-top`).
   - Botón `button.enviar_alerta`:
     - Borde acorde a la paleta corporativa.
     - Comportamiento hover sin cambios bruscos de color.

9. **Footer corporativo (#modulo-pie-3)**
   - Definición de variables CSS para la paleta:
     - `--c-base`, `--c-base-2`, `--c-input-bg`, `--c-border`, `--c-ink`, `--c-ink-soft`, `--c-link`, `--c-link-hover`, `--c-ring`, `--c-panel-glass`.
   - Aplicación de estilos base:
     - Fondo oscuro (`--c-base`) en los bloques del pie (`#pie-fila1`, `#pie-subfila1`, `.pie-agencia`, `#pie-menu-3`, `#pie-filaSocial`).
     - Texto en color claro (`--c-ink`).
   - Enlaces:
     - Color base `--c-link`.
     - Hover con `--c-link-hover`, subrayado y transición suave.
   - Títulos:
     - Color blanco, mayor peso tipográfico y ligero `letter-spacing`.
   - Formulario de contacto:
     - Cajas (`fieldset`, `.pie-datosagenciaBloque1`) con efecto glassmorphism ligero.
     - `.contacto` como tarjeta suspendida:
       - `border-radius`, sombra, `backdrop-filter: blur`.
       - Transición en hover que refuerza la sensación de elevación.
     - Inputs y `textarea`:
       - Fondo `--c-input-bg`.
       - Bordes `--c-border`.
       - Foco accesible con `box-shadow` usando `--c-ring`.
   - Botón de envío `.botonEnviar`:
     - Fondo `--c-base-2`, borde suave y transición en `hover` (ligero `brightness` y `translateY`).
   - Menú del pie (`#pie-menu-3`):
     - Alineación a la derecha.
     - Lista sin viñetas (`list-style: none`).
     - El enlace `Inicio` (`a[href="index.php"]`) se oculta para simplificar el menú.
   - Iconos sociales:
     - Inicialmente en escala de grises con ligera mejora de brillo.
     - En hover se activan: pierden el gris y aumentan el brillo.
     - Se define `border-radius` para redondear logos como el de Instagram.

10. **Otros módulos**
    - `#modulo-equipo > h1`:
      - Justificación centrada, tamaño y peso adecuadamente destacados.
    - `#comentariosgoogle-bloquedatosagencia`:
      - Uso de flexbox (`display: flex`, `flex-direction: column`, `align-items: center`) para centrar el bloque de reseñas de Google.
    - `.componentes-v3 .buscador-areas__desplegable`:
      - Se fuerza `visibility: visible !important` para garantizar que el formulario no quede oculto.
    - `#modulo-empresa-2`:
      - Texto global en blanco, sin alterar el color de los enlaces dentro de botones.
    - `#modulo-personalizadobanner-1`:
      - Se elimina borde superior.
      - `.parte-der` se pinta con fondo corporativo y texto blanco.
      - `.botonIr` mantiene su color original (no se fuerza a blanco).
    - `.enviarPropiedad`:
      - Botón de envío con borde corporativo y `border-radius`.
    - `#modulo-alerta-3`:
      - Ajuste de `margin-bottom` para separación visual final.

11. **Regla condicional de ocultación del slider**
    - Uso del selector relacional `:has()`:
      ```css
      body:has(#idParaOcultarBuscador) #modulo-slider-1 {
          display: none !important;
      }
      ```
    - Si en el árbol del `body` existe un elemento con `id="idParaOcultarBuscador"`, el módulo `#modulo-slider-1` se oculta.
    - Esto permite desactivar el slider **sin tocar el CSS** ni duplicar plantillas, solo añadiendo un marcador en la vista.

---

## 3. Criterios de diseño y buenas prácticas

- **No se toca el HTML**: todas las personalizaciones se hacen vía CSS.
- **Uso controlado de `!important`**:
  - Muchas reglas del tema original utilizan `!important`; para sobrescribirlas sin acceso a la hoja original, se ha recurrido al mismo mecanismo.
- **Selectores específicos**:
  - Se priorizan selectores por ID y clases propias de Inmovilla (`#modulo-*`, `.header-menu`, `#modulo-pie-*`, etc.) para no interferir con otros proyectos o estilos genéricos.
- **Pseudo-elementos solo cuando aportan valor**:
  - Ejemplo: etiqueta “Menu” en móvil, mensaje explicativo en alertas.
  - En los casos en que podrían bloquear interacción, se usa `pointer-events: none`.

---

## 4. Puntos sensibles a revisar en futuras modificaciones

1. **Cambios en la plantilla de Inmovilla**
   - Si el proveedor cambia IDs o clases de:
     - Cabecera
     - Menús
     - Footer
     - Buscadores
   - Será necesario actualizar los selectores de este CSS.

2. **Selector `:has()`**
   - No está soportado en navegadores muy antiguos.
   - Si se requiere retrocompatibilidad máxima, habría que sustituirlo por una solución basada en clases o por lógica del lado del servidor.

3. **Compatibilidad entre media queries**
   - Las principales:
     - `@media (max-width: 900px)` – cabecera móvil, botón de teléfono, `nav::before`.
     - `@media (max-width: 1024px)` – menú móvil (colores y estados).
   - Si se añaden nuevas reglas responsive, conviene revisar que no entren en conflicto con estas.

---

## 5. Changelog orientativo (según comentarios de fecha)

- **27-10-2025**
  - Ajustes estéticos en el buscador del slider (redondeo, sombras).

- **28-10-2025**
  - Ajustes en títulos del módulo de equipo.
  - Mejora en el buscador por áreas (`border-radius`, tipografía y flecha de desplegable).

- **29-10-2025**
  - Asegurar visibilidad del formulario “Encontramos la casa de tus sueños”.

- **03-11-2025**
  - Redondeo de iconos sociales (Instagram) en el pie.
  - Integración con el filtro monocromo y efecto hover.

- **13-11-2025**
  - Ajustes en módulos “Empresa” y banner personalizado.
  - Corrección de colores en `#modulo-cabecera-2` para que el buscador en línea sea legible.

- **26-11-2025**
  - Introducción de la regla condicional con `:has` para ocultar el slider en páginas específicas.

---

## 6. Recomendación de mantenimiento

- Mantener este CSS como **fichero independiente** asociado al portal Magnus/Inmovilla.
- Documentar futuras modificaciones añadiendo:
  - Comentario con fecha.
  - Breve explicación del cambio.
- Antes de añadir nuevas reglas para un mismo módulo, revisar si ya existe un bloque para ese módulo y **ampliarlo** en lugar de crear reglas duplicadas.

Con este documento, cualquier desarrollador puede entender la intención de la personalización y extenderla sin romper la identidad visual ni la experiencia de usuario del portal inmobiliario Magnus.
