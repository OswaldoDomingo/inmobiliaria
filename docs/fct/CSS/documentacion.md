# Documentación técnica – Hoja de estilos personalizada (Portal Inmobiliario Magnus)

## 1. Introducción

Este documento describe la hoja de estilos CSS utilizada para personalizar el portal inmobiliario de Magnus sobre la plantilla estándar del CRM Inmovilla.

Los objetivos principales de esta capa de CSS son:

- Aplicar la **identidad visual corporativa de Magnus** (colores, tipografías, estilo de botones).
- Unificar el diseño de **cabecera, menú, buscadores, módulos de contenido y pie de página**.
- Mejorar la **experiencia de usuario en móvil** sin modificar el HTML original del CRM.
- Eliminar o atenuar elementos visuales propios de Inmovilla que no aportan al branding de Magnus.

Toda la personalización se realiza únicamente mediante CSS, aprovechando selectores por ID, clases, pseudo-elementos y media queries, sin necesidad de tocar plantillas PHP/HTML.

---

## 2. Alcance

La hoja de estilos afecta principalmente a:

- **Elementos globales**:
  - Icono RSS (`img[src*="rss2.png"]`).
  - Texto “Diseñado por CRM Inmovilla” (`span:has(a[href*="facebook.com/crminmovilla"])`).

- **Cabecera y menú principal**:
  - `#modulo-cabecera-1`, `#modulo-cabecera-2`
  - `#header-bloquemenu nav::before`
  - `.header-menu`, elementos activos (`.activa`) y estados hover/focus.

- **Módulo “Valoramos tu piso”**:
  - `#modulo-valoramostupiso-2`
  - `.modulo-valoramostupiso`

- **Buscador principal del slider**:
  - `#slider-buscador`, inputs y botones internos.

- **Selector de tipo de propiedad**:
  - `#inmotipos.buscadorTipos.custom-select-html.enviarPostHog` y sus pseudo-elementos.

- **Buscador por áreas**:
  - `#slider-bloque-buscador`
  - `.custom-select::after`

- **Módulo de alertas**:
  - `#modulo-alertas-1`, `#alertas`, `.blockquote`, `.bloqueAlertaContenedores`, botón `.enviar_alerta`.

- **Pie de página corporativo**:
  - `#modulo-pie-3`, `#pie-fila1`, `#pie-subfila1`, `#pie-menu-3`, `#pie-filaSocial`.
  - Formulario de contacto (`.contacto`, inputs, textarea, botón `.botonEnviar`).
  - Textos legales (`.textolegal`).
  - Iconos sociales y menú de enlaces.

- **Otros módulos específicos**:
  - `#modulo-equipo`
  - `#comentariosgoogle-bloquedatosagencia`
  - `.componentes-v3 .buscador-areas__desplegable`
  - `#modulo-empresa-2`
  - `#modulo-personalizadobanner-1`
  - `.enviarPropiedad`
  - `#modulo-alerta-3`

- **Regla condicional**:
  - `body:has(#idParaOcultarBuscador) #modulo-slider-1` para ocultar el slider completo en determinadas páginas.

---

## 3. Paleta corporativa

En el footer se define una serie de **variables CSS** que recogen la paleta corporativa de Magnus:

```css
#modulo-pie-3 {
  --c-base:        #191A2E;  /* fondo principal */
  --c-base-2:      #202B45;  /* paneles / bloques */
  --c-input-bg:    #222E4B;  /* fondo inputs */
  --c-border:      #3C4B6A;  /* bordes suaves */
  --c-ink:         #F5FAFC;  /* texto principal */
  --c-ink-soft:    #E9F1F8;  /* texto tenue / legal */
  --c-link:        #A9C2FF;  /* enlaces */
  --c-link-hover:  #C9DAFF;  /* hover enlaces */
  --c-ring:        rgba(169,194,255,0.28); /* foco accesible */
  --c-panel-glass: rgba(255,255,255,0.03); /* efecto “suspendido” */
}
```

Estas variables se reutilizan para:

- Fondo de bloques (`--c-base`, `--c-base-2`).
- Apariencia de formularios (`--c-input-bg`, `--c-border`).
- Texto y enlaces (`--c-ink`, `--c-ink-soft`, `--c-link`, `--c-link-hover`).
- Efectos de foco accesible (`--c-ring`).

---

## 4. Detalle por bloques

### 4.1. Limpieza de elementos ajenos al branding

- Se oculta el icono RSS con:

  ```css
  img[src*="rss2.png"] {
    display: none !important;
  }
  ```

- Se oculta el texto “Diseñado por CRM Inmovilla” utilizando `:has` sobre el span que contiene el enlace a la página de Facebook de Inmovilla:

  ```css
  span:has(a[href*="facebook.com/crminmovilla"]) {
    display: none !important;
  }
  ```

Esto permite mantener el HTML original pero eliminar referencias visuales que no aportan a la marca Magnus.

---

### 4.2. Cabecera y menú principal

- Se unifican `#modulo-cabecera-1` y `#modulo-cabecera-2` con fondo oscuro (`#191A2E`) y texto blanco, tanto para títulos como párrafos y spans.
- Todos los enlaces de la cabecera pasan a ser blancos, con un efecto de hover en gris claro.
- En móvil (`@media (max-width: 900px)`), se fuerza:
  - Fondo uniforme en `#modulo-cabecera-1`, `#header-bloque2` y `.bg-color1`.
  - Eliminación de posibles overlays (`::before`).
  - Tipografía clara para todos los elementos de cabecera.
  - Botón de teléfono centrado con aspecto de “pill button”.
  - Colores coherentes en el menú (normal, activo y hover).

Se añade además un pseudo-elemento `nav::before` en móvil que muestra la palabra **“Menu”** sobre la navegación, mejorando la claridad del patrón de navegación.

---

### 4.3. Buscador principal del slider

El bloque `#slider-buscador` se rediseña para comportarse como una **caja de búsqueda principal**:

- Esquinas redondeadas (`border-radius`).
- `overflow: hidden` para que ningún input/botón sobresalga.
- Sombra suave para dar sensación de card flotante.
- Inputs y botones internos con bordes redondeados y espaciado homogéneo.

Esto facilita la lectura y uso del buscador tanto en escritorio como en móvil.

---

### 4.4. Selector de tipo de propiedad

El selector `#inmotipos.buscadorTipos.custom-select-html.enviarPostHog` suele estar implementado con pseudo-elementos que simulan una caja y una flecha.

La hoja de estilos:

- Redondea el contenedor principal.
- Ajusta el borde y el fondo.
- Pone `overflow: visible !important` para que no se corte el contenido del desplegable.
- Hace que `::before` y `::after` sean únicamente decorativos (`pointer-events: none`), evitando que intercepten clics y bloqueen la apertura del select.

---

### 4.5. Buscador por áreas

El bloque `#slider-bloque-buscador` y `.custom-select::after` se estilizan para:

- Redondear inputs del buscador por áreas.
- Usar tipografía consistente con el resto del portal.
- Dibujar una flecha personalizada en el pseudo-elemento `::after`, con aspecto más limpio y sin interferir con la interacción del usuario.

---

### 4.6. Módulo “Valoramos tu piso”

- `#modulo-valoramostupiso-2` se separa de la cabecera con `margin-top` y `margin-left`.
- `.modulo-valoramostupiso` ajusta el tratamiento de la imagen de fondo:
  - `background-size: contain`
  - `background-position: center`
  - `background-repeat: no-repeat`
  - Fondo blanco de seguridad

Esto evita distorsiones o recortes extraños de la imagen de cabecera del módulo.

---

### 4.7. Módulo de alertas

En el módulo de alertas se realizan varios cambios:

- Se oculta el título por defecto con `display: none`.
- Se eliminan comillas decorativas (`.textoComillas::before`).
- Se crea un nuevo “cartel” con un texto propio mediante `p::after`, centrado y con borde redondeado, explicando al usuario qué hace el formulario de alertas.
- El contenedor `.bloqueAlertaContenedores` se mejora como card con borde, padding y `border-radius`.

El resultado es un módulo de alertas mucho más claro y alineado con el tono del portal.

---

### 4.8. Pie de página corporativo

Se redefine por completo `#modulo-pie-3` y sus subbloques:

- Fondo oscuro corporativo.
- Textos claros, títulos con mayor peso tipográfico.
- Formulario de contacto “suspendido” con efecto glassmorphism ligero.
- Inputs y textarea adaptados a la paleta y con foco accesible.
- Botón de envío con efecto hover y transición suave.
- Menú del pie alineado a la derecha, sin viñetas, con posibilidad de ocultar el enlace “Inicio”.
- Iconos sociales en monocromo con hover que los “activa” y con borde redondeado.

---

### 4.9. Otros módulos

Se ajustan también:

- `#modulo-equipo > h1`: centrado y jerarquía visual adecuada.
- `#comentariosgoogle-bloquedatosagencia`: maquetado con flexbox para centrar el bloque superior de reseñas de Google.
- `.componentes-v3 .buscador-areas__desplegable`: se fuerza la visibilidad del formulario.
- `#modulo-empresa-2`, `#modulo-personalizadobanner-1 .parte-der`: textos en blanco sobre fondo corporativo.
- Botones de envío en alertas y formulario “Enviar propiedad” con borde redondeado coherente.

---

### 4.10. Regla condicional de ocultación del slider

La regla:

```css
body:has(#idParaOcultarBuscador) #modulo-slider-1 {
  display: none !important;
}
```

permite **ocultar el módulo de slider** cuando en el `body` o en la página se introduce un elemento marcador con `id="idParaOcultarBuscador"`.

Es útil para páginas donde no interesa mostrar el buscador principal (por ejemplo, una página de empresa, contacto o landing específica).

---

## 5. Criterios de implementación

- Se hace uso intensivo de `!important` porque el CSS debe sobrescribir estilos existentes del tema Inmovilla sin poder editarlos.
- Se utilizan selectores por ID (`#modulo-*`) y clases oficiales del CRM para evitar colisiones con otros sitios.
- No se modifica el HTML, lo que garantiza compatibilidad con futuras actualizaciones del CRM mientras mantenga la misma estructura de IDs y clases.

---

## 6. Mantenimiento y extensiones

Recomendaciones:

1. **No mezclar** este CSS con otros ficheros genéricos; mantenerlo como “capa Magnus” para Inmovilla.
2. Si Inmovilla actualiza la estructura de IDs o clases, revisar especialmente:
   - Cabecera (`#modulo-cabecera-*`, `.header-menu`).
   - Footer (`#modulo-pie-3`).
   - Buscadores (`#slider-buscador`, `#inmotipos`, `#slider-bloque-buscador`).
3. Cuando se añadan nuevos módulos, reutilizar la paleta y principios de diseño definidos aquí para mantener la coherencia visual.
