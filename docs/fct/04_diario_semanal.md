# 04. Diario semanal de la FCT

Este documento recoge, de forma resumida, las principales actividades realizadas cada semana durante el periodo de FCT relacionado con el proyecto inmobiliario y la calculadora de tasación online.

> _Nota: Las fechas son orientativas. Sustituir por las fechas reales de inicio y fin de cada semana._

---

## Semana 1 ([dd/mm/aaaa] – [dd/mm/aaaa])

**Objetivos de la semana:**

- Conocer la empresa, su forma de trabajo y el entorno tecnológico.
- Entender las necesidades del proyecto de tasación online.
- Revisar diseños y prototipos iniciales.

**Actividades realizadas:**

- Presentación en la empresa y reunión con el tutor de FCT.
- Revisión del portal inmobiliario actual y de sus puntos de mejora.
- Análisis de ejemplos de calculadoras de tasación en otras webs.
- Revisión de bocetos y diseños en Figma para la futura calculadora.
- Planificación del trabajo y definición de prioridades.

**Resultado:**

- Queda claro el alcance funcional mínimo de la calculadora.
- Se acuerda que la herramienta debe ser útil para captación de propietarios.
- Se documentan los requisitos iniciales en un esquema de trabajo.

---

## Semana 2 ([dd/mm/aaaa] – [dd/mm/aaaa])

**Objetivos de la semana:**

- Maquetar la interfaz básica de la calculadora.
- Dejar preparado el formulario con los campos principales.

**Actividades realizadas:**

- Estructura HTML del widget de tasación.
- Aplicación de estilos con Tailwind CSS siguiendo el diseño de Figma.
- Maquetación de:
  - Campos de CP, barrio, zona y superficie.
  - Campos de estado, orientación, ascensor y extras.
  - Panel derecho con área de resultado y placeholder inicial.
- Pruebas de visualización en distintas resoluciones (desktop y móvil).

**Resultado:**

- Primera versión navegable del formulario de tasación.
- Estructura visual clara y alineada con la imagen de la empresa.

---

## Semana 3 ([dd/mm/aaaa] – [dd/mm/aaaa])

**Objetivos de la semana:**

- Conectar la calculadora con datos reales de precios por zona.
- Implementar la lógica de cálculo principal.

**Actividades realizadas:**

- Configuración de Google Sheets con datos de precio/m² por CP, barrio y zona.
- Exportación de la hoja como CSV y pruebas de acceso.
- Integración de PapaParse para cargar y parsear el CSV desde el navegador.
- Desarrollo de la función de búsqueda de registro base (CP + barrio + zona).
- Cálculo del precio base y aplicación de reglas de incremento/descuento en JavaScript.

**Resultado:**

- La calculadora ya puede devolver una horquilla de precios para combinaciones conocidas.
- El sistema de reglas se centraliza en una hoja de configuración editable.

---

## Semana 4 ([dd/mm/aaaa] – [dd/mm/aaaa])

**Objetivos de la semana:**

- Añadir el formulario de contacto y la parte de captación de datos.
- Preparar el envío de información a la empresa y al usuario.

**Actividades realizadas:**

- Implementación del formulario de contacto (email, teléfono, checkboxes legales).
- Validaciones en cliente: formato de email, longitud de teléfono, campos obligatorios.
- Integración con EmailJS para el envío de correos:
  - Plantilla para la agencia (lead de captación).
  - Plantilla para el cliente (confirmación de tasación).
- Manejo de estados de la interfaz:
  - Placeholder inicial.
  - Vista intermedia de resumen.
  - Vista final con resultado completo.

**Resultado:**

- Flujo completo operativo: desde la introducción de datos hasta el envío de información.
- La herramienta queda en un estado utilizable en entorno real (o de pruebas).

---

## Semana 5 y siguientes ([dd/mm/aaaa] – [dd/mm/aaaa])

**Objetivos de la semana:**

- Mejorar detalles de usabilidad y mensajes al usuario.
- Documentar el proyecto y preparar la defensa ante el tribunal.

**Actividades realizadas:**

- Revisión de textos y mensajes de error para hacerlos más claros.
- Pequeñas mejoras visuales (iconos, espaciados, estados de botones).
- Redacción de:
  - Documentación técnica (`documentacion.md`).
  - Manual de usuario (`manual_usuario.md`).
  - Documentos de FCT (empresa, tareas, diario y aprendizajes).
- Preparación de capturas de pantalla y materiales para la presentación.

**Resultado:**

- Proyecto listo para ser presentado al tribunal.
- Empresa con una herramienta documentada que puede seguir usando y mejorando.

---

## Conclusión del diario semanal

El diario semanal muestra una evolución progresiva desde el análisis y diseño hasta la puesta en marcha de una herramienta funcional. Esta estructura de trabajo refleja una forma de trabajar similar a la de un entorno profesional:

1. Entender el problema.
2. Proponer una solución.
3. Implementarla por fases.
4. Probar, corregir y documentar.
