# 03. Tareas realizadas durante la FCT

Este documento recoge las tareas principales realizadas durante el periodo de FCT, tanto a nivel técnico como organizativo. Se presentan agrupadas por bloques y en forma de tabla de registro.

## 1. Resumen por bloques de trabajo

### 1.1. Análisis y diseño

- Revisión de las necesidades de la empresa en cuanto a captación de propietarios y tasación online.
- Análisis de herramientas similares presentes en otros portales inmobiliarios.
- Definición de los datos mínimos necesarios para obtener una estimación (CP, m², estado, orientación, extras…).
- Revisión y adaptación de los diseños en Figma para la calculadora.

### 1.2. Desarrollo frontend

- Maquetación del formulario de tasación con HTML5 y Tailwind CSS.
- Creación de componentes reutilizables (inputs, selectores, tarjetas de resultado).
- Implementación de estados visuales: placeholder, formulario de contacto, resultado final.
- Adaptación responsiva para distintos tamaños de pantalla.

### 1.3. Lógica de negocio y JavaScript

- Implementación del flujo completo de cálculo:
  - Captura de datos de entrada.
  - Búsqueda de precio/m² según CP, barrio y zona.
  - Aplicación de reglas de ajuste mediante configuración.
- Gestión de estados del formulario (habilitar/deshabilitar botones según validación).
- Manejo de posibles errores: datos no encontrados, CSV inaccesible, etc.

### 1.4. Integraciones externas

- Lectura de ficheros CSV publicados desde Google Sheets utilizando PapaParse.
- Configuración inicial y pruebas con EmailJS para el envío de correos.
- Organización de la configuración de URLs y claves en una sección separada del código.

### 1.5. Documentación y soporte

- Redacción de la documentación técnica de la calculadora.
- Elaboración del manual de usuario para personal no técnico.
- Preparación de capturas de pantalla y material para la presentación ante el tribunal.

## 2. Registro de tareas (formato tabla)

> _Nota: esta tabla puede ampliarse con todas las tareas reales realizadas durante la FCT._

```markdown
| Fecha       | Horas | Descripción de la tarea                                   | Tipo        | Tecnologías / Herramientas        |
|------------|-------|------------------------------------------------------------|------------|-----------------------------------|
| 2025-11-10 | 4     | Reunión inicial con el tutor de empresa y definición del   | Reunión    | —                                 |
|            |       | alcance del proyecto de tasación online.                   |            |                                   |
| 2025-11-11 | 3     | Maquetación inicial del formulario de tasación (CP, m²,    | Frontend   | HTML, Tailwind, Figma            |
|            |       | orientación, estado).                                      |            |                                   |
| 2025-11-12 | 5     | Integración de CSV de precios por zona desde Google Sheets | Lógica     | JavaScript, PapaParse, CSV       |
| 2025-11-13 | 4     | Implementación de las reglas de ajuste (extras, estado,    | Lógica     | JavaScript                       |
|            |       | ascensor, altura) y generación de horquilla de precios.    |            |                                   |
| 2025-11-14 | 3     | Validación del formulario de contacto y estados del botón  | Frontend   | JavaScript, HTML                 |
|            |       | “Ver informe completo”.                                    |            |                                   |
| 2025-11-17 | 4     | Pruebas internas de la calculadora y corrección de errores | Pruebas    | Navegador, DevTools              |
| 2025-11-18 | 3     | Redacción de documentación técnica y manual de usuario.    | Documentac.| Markdown, GitHub                 |
```

Se recomienda seguir rellenando esta tabla con:

- Fechas reales.
- Horas efectivas dedicadas.
- Descripción breve pero clara de la tarea.
- Tipo de actividad:
  - Frontend, Backend, BD, Lógica, Pruebas, Reunión, Documentación, etc.
- Tecnologías o herramientas empleadas.

## 3. Conclusiones sobre las tareas realizadas

Durante la FCT he podido:

- Participar en un proyecto real alineado con las necesidades de la empresa.
- Cubrir todas las fases básicas de un desarrollo web:
  - Análisis.
  - Diseño.
  - Implementación.
  - Pruebas.
  - Documentación.
- Trabajar con tecnologías y herramientas utilizadas en entornos profesionales:
  - Control de versiones con Git.
  - Integraciones con servicios externos (Google Sheets, EmailJS).
  - Diseño apoyado en Figma.

Este registro servirá como base para la memoria final y para la defensa ante el tribunal.
