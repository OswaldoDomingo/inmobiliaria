# 02. Proyecto desarrollado en la empresa

## 1. Título del proyecto

**Proyecto:** Portal inmobiliario con calculadora de tasación online  
**Alumno:** Oswaldo Domingo Pérez – Ciclo Formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW)

## 2. Descripción general del proyecto

El proyecto consiste en el desarrollo de un **portal web inmobiliario** y una **calculadora de tasación online** integrada en dicho portal.  
Los objetivos principales de la solución son:

- Permitir que cualquier usuario pueda obtener una **estimación automática del valor de su vivienda** introduciendo unos pocos datos.
- Utilizar esa tasación como **herramienta de captación de clientes**, recogiendo sus datos de contacto.
- Facilitar al personal de la inmobiliaria una estimación inicial de precio que pueda ser revisada y ajustada posteriormente.

La calculadora combina:

- **Datos de mercado** (precio/m² por código postal, barrio y zona).
- **Características concretas del inmueble** (superficie, estado, orientación, extras, altura, ascensor).
- Un conjunto de **reglas configurables** que ajustan el precio según la realidad del mercado local.

## 3. Encaje del proyecto en la empresa

El proyecto se alinea con varias necesidades de la empresa:

1. **Captación de nuevos propietarios**
   - Ofrecer una herramienta gratuita de tasación online anima a los propietarios a dejar sus datos para recibir un asesoramiento más completo.
2. **Imagen de marca moderna**
   - Un portal con una calculadora propia transmite profesionalidad, tecnología y conocimiento del mercado.
3. **Apoyo al equipo comercial**
   - La estimación obtenida puede servir como base para la conversación entre el agente y el propietario.

De esta forma, el proyecto no es un ejercicio aislado, sino una herramienta que la empresa puede **usar de verdad** en su día a día.

## 4. Alcance funcional

A nivel funcional, el proyecto incluye:

- **Landing / Módulo de tasación online**:
  - Formulario de entrada de datos:
    - Código postal, barrio y zona.
    - Metros cuadrados.
    - Estado (reformado, a reformar, entrar a vivir).
    - Orientación (exterior / interior).
    - Extras (bajo, ático, balcón/terraza, ascensor).
  - Cálculo de una **horquilla de precios** (mínimo–máximo).
  - Pantalla intermedia de resumen antes de pedir datos personales.
  - Formulario de contacto (email, teléfono, aceptación de privacidad y comunicaciones comerciales).
  - Envío de datos mediante **EmailJS** (o integración que defina la empresa).

- **Integración con datos externos**:
  - Lectura de precios por zona desde **Google Sheets** exportado como CSV.
  - Lectura de reglas de ajuste (configuración de pluses y descuentos) desde otra hoja de cálculo.

- **Documentación y manuales**:
  - Documentación técnica del funcionamiento de la calculadora.
  - Manual de usuario para que el personal de la empresa pueda usarla y explicarla.

## 5. Tecnologías utilizadas

- **Frontend**
  - HTML5 y CSS3.
  - Tailwind CSS (via CDN) para maquetación rápida y utilitaria.
  - JavaScript (ES6+) para la lógica de cálculo y la interacción con el usuario.
  - Iconos SVG mediante Lucide Icons.

- **Integraciones y librerías**
  - PapaParse para leer ficheros CSV generados desde Google Sheets.
  - EmailJS para el envío de correos sin necesidad de backend propio.
  - (Opcional / Futuro) Integración con el portal inmobiliario principal o CRM.

- **Herramientas de apoyo**
  - Figma para el diseño de la interfaz y prototipos.
  - Git / GitHub para el control de versiones del código.
  - Navegador con herramientas de desarrollador (DevTools) para depuración.

## 6. Relación con el currículo del ciclo DAW

El proyecto permite aplicar y demostrar múltiples resultados de aprendizaje del ciclo de DAW:

- **Programación en lenguajes de marcas y gestión de la información**
  - Uso de HTML5 y estructuras semánticas.
  - Tratamiento de datos externos en formato CSV.

- **Desarrollo web en entorno cliente**
  - Uso intensivo de JavaScript para:
    - Maniobra del DOM.
    - Validaciones de formularios.
    - Cálculos y actualización dinámica de la interfaz.

- **Desarrollo web en entorno servidor y bases de datos** (para la parte de portal inmobiliario/MVC, si se integra):
  - Estructura MVC en PHP.
  - Acceso a datos y configuración de conexiones a base de datos (en el proyecto global).

- **Despliegue y mantenimiento de aplicaciones web**
  - Uso de entornos de desarrollo y producción.
  - Organización del proyecto en carpetas (public, app, docs, etc.).
  - Documentación técnica y manuales de usuario.

## 7. Limitaciones y alcance de la versión entregada

La versión desarrollada durante la FCT se centra principalmente en:

- Tener una calculadora plenamente funcional desde el punto de vista del usuario final.
- Asegurar que la herramienta es **configurable** desde hojas de cálculo (sin tocar código).
- Dejar documentadas las bases para una futura integración más profunda con:
  - El portal inmobiliario completo.
  - Un backend propio y base de datos para registro de leads.

En el apartado de propuestas de mejora se detallan las líneas de evolución recomendadas.
