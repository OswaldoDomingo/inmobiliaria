# Documentación de Implementación — Google Tag Manager (GTM)
**Archivo:** `documentacion_implementacion_GTA.md`  
**Fecha:** 04 de diciembre de 2025  
**Plataforma:** Inmovilla CRM (Web)  
**ID de contenedor GTM:** `GTM-XXXXXXXX`

---

## 0. Objetivo del documento

Este documento sirve como **informe técnico** para justificar ante auditoría/tribunal que:

1) **Google Tag Manager (GTM)** está correctamente integrado en la web,  
2) el contenedor (`GTM-XXXXXXXX`) **se carga en producción** de forma estable, y  
3) el sitio queda **preparado para instrumentación de medición** (GA4, píxeles de terceros, eventos, etc.) sin volver a tocar el código fuente.

> Nota: En algunos entornos se confunde el nombre y se menciona “Google Task Manager”. **La herramienta correcta es Google Tag Manager (GTM)**, orientada a gestión de etiquetas (tags) y analítica.

---

## 1. Resumen ejecutivo

Se ha realizado una auditoría del código fuente/DOM renderizado para verificar la integración de GTM en el entorno de producción.

**Resultado**: la implementación es **correcta**, el contenedor **está desplegado** y su ubicación/forma de carga es compatible con buenas prácticas (script en `<head>` y `<noscript>` inmediatamente tras `<body>`).

---

## 2. Alcance y metodología

### 2.1 Alcance
- Validación de la **presencia**, **ubicación** y **consistencia** del snippet oficial de GTM.
- Confirmación del **ID de contenedor** y del **modo de inserción**.
- Revisión de variables/indicadores del CRM que puedan provocar **duplicidad de medición**.

### 2.2 Metodología empleada
- Inspección del **HTML renderizado** (DOM).
- Revisión del **código fuente** servido en producción.
- Identificación del snippet en:
  - `<head>` (script principal)
  - y `<body>` (bloque `<noscript>` de respaldo)

---

## 3. Análisis de la implementación

### 3.1 Método de inserción

La implementación **no** se ha realizado mediante:
- Inyección dinámica (DOM Injection) desde JavaScript,
- ni por widgets externos que reescriban el DOM.

En su lugar, se ha integrado a nivel de **plantilla** (hard-coded), que es la forma recomendada porque:
- reduce el riesgo de que el contenedor se cargue tarde,
- minimiza pérdida de datos (eventos antes de que GTM exista),
- evita condiciones de carrera con otros scripts del CRM.

### 3.2 Ubicación de los fragmentos

- **Script principal (`gtm.js`)**: ubicado en `<head>`, previo a recursos críticos (CSS/JS del sitio), garantizando que el contenedor se inicialice lo antes posible.
- **Bloque `<noscript>`**: ubicado inmediatamente tras la apertura de `<body>`, tal como recomienda Google.

---

## 4. Evidencia de código auditado (producción)

A continuación se adjunta el snippet oficial, con el ID de contenedor verificado: `GTM-XXXXXXXX`.



```html
<!-- Google Tag Manager -->
<script>
  (function(w,d,s,l,i){
    w[l]=w[l]||[];
    w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
    var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),
        dl=l!='dataLayer'?'&l='+l:'';
    j.async=true;
    j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
    f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-XXXXXXXX');
</script>
<!-- End Google Tag Manager -->

<!-- Google Tag Manager (noscript) -->
<noscript>
  <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXXX"
          height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
```

---

## 5. Qué hace cada parte (explicación técnica)

### 5.1 `dataLayer`: el bus de eventos de GTM
- `dataLayer` es un **array global** (por defecto `window.dataLayer`) donde se “empujan” eventos y datos.
- GTM escucha ese array para:
  - disparar **triggers** (Page View, Click, etc.),
  - y pasar **variables** a etiquetas (por ejemplo, `page_path`, `form_name`, `transaction_id`, etc.).

### 5.2 `w[l].push({'gtm.start': ..., event:'gtm.js'})`
- Inserta un evento inicial con marca de tiempo.
- Permite a GTM medir y ordenar la secuencia de carga.

### 5.3 Creación del `<script async>` a `gtm.js`
- Se crea dinámicamente la etiqueta `<script>`:
  - `j.async = true;` evita bloquear el renderizado.
  - `j.src = 'https://www.googletagmanager.com/gtm.js?id='+i+dl;` carga el contenedor asociado al ID.

### 5.4 `noscript` (fallback)
- Si el usuario tiene JavaScript deshabilitado:
  - el `<iframe>` permite que GTM registre al menos una visita básica (pageview) mediante un request al endpoint `ns.html`.
- No sustituye la funcionalidad completa, pero aporta cobertura mínima.

---

## 6. Compatibilidad con el entorno Inmovilla CRM

Se detecta la declaración de variable global del CRM:

```js
var tienegooglanalitics = 0;
```

Interpretación técnica:
- El “toggle”/flag nativo del CRM para Google Analytics está **desactivado** (valor `0`).
- Esto reduce el riesgo de **double-counting** (duplicidad) si antes existía una medición nativa paralela.
- Permite que GTM sea el **punto único de control** para la analítica.

---

## 7. Conclusión

✅ **La infraestructura de medición está correctamente implementada.**  
No se requieren cambios adicionales en el desarrollo web ni intervención de soporte de Inmovilla para comenzar a configurar medición mediante GTM.

El contenedor está preparado para:
- Google Analytics 4 (GA4),
- píxeles de terceros (Meta, LinkedIn, etc.),
- y eventos personalizados (formularios, clics a WhatsApp, llamadas, etc.),
sin necesidad de volver a modificar el HTML del sitio.

---

## 8. Próximos pasos recomendados (operativos)

1) **Publicar una versión inicial del contenedor** en Google Tag Manager  
   - Si el contenedor está vacío o en modo borrador, no disparará etiquetas útiles en producción.

2) **Configurar GA4 desde GTM**  
   - Crear la etiqueta de configuración de GA4 (Google tag / GA4 Configuration según el caso).
   - Definir el Measurement ID correspondiente.

3) **Depuración / QA de medición**
   - Usar **Preview / Tag Assistant** para validar:
     - Page View,
     - Click (links/botones),
     - Scroll (si aplica),
     - Submit de formularios (si aplica).
   - Validar en GA4 **DebugView** que los eventos llegan como se espera.

---

## 9. Checklist de validación rápida (para anexos)

- [x] Snippet GTM presente en `<head>`
- [x] Bloque `<noscript>` presente tras `<body>`
- [x] ID de contenedor correcto: `GTM-XXXXXXXX`
- [x] Carga asíncrona (`async`) habilitada
- [x] `dataLayer` inicializado
- [x] Sin indicios de inyección DOM externa
- [x] Flag del CRM (`tienegooglanalitics`) desactivado ⇒ reduce duplicidad

---
