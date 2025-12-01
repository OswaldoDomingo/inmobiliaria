# Documentación Técnica del Proyecto: Calculadora de Tasación Inmobiliaria

## 1. Introducción y Objetivos
El objetivo principal de este proyecto ha sido desarrollar una herramienta web de **Tasación Inmobiliaria Online** que permita a los usuarios obtener una valoración estimada de su vivienda en tiempo real. La herramienta busca captar "leads" (clientes potenciales) ofreciendo un valor añadido inmediato (la tasación) a cambio de sus datos de contacto.

## 2. Contexto y Limitaciones del Proyecto
Para entender las decisiones tecnológicas tomadas, es fundamental conocer el entorno de despliegue y las restricciones del cliente:

*   **Entorno de Alojamiento (Inmovilla):** La web del cliente está alojada en un CRM inmobiliario llamado **Inmovilla**. Este entorno es cerrado y **no permite la ejecución de código de servidor** (como PHP, Node.js o Python) ni el acceso a bases de datos tradicionales (MySQL). Solo permite la inyección de HTML, CSS y JavaScript en el frontend.
*   **Ausencia de Servidor Propio:** El cliente no dispone de una infraestructura de servidor dedicada ni conocimientos para mantenerla.
*   **Perfil del Cliente:** El cliente final y su equipo administrativo tienen un perfil **no técnico**. La solución debía ser extremadamente sencilla de gestionar sin requerir intervención de desarrolladores para tareas cotidianas como actualizar precios.

## 3. Justificación de la Solución Tecnológica
Dadas las limitaciones expuestas, se optó por una arquitectura **Serverless (sin servidor)** basada enteramente en el navegador (Client-Side), utilizando servicios externos para suplir la falta de backend.

### 3.1. Stack Tecnológico
*   **Frontend:** HTML5, CSS3 (TailwindCSS vía CDN) y **JavaScript (Vanilla)**. Se eligió JavaScript puro para evitar procesos de compilación complejos (como Webpack o Vite) que dificultarían la integración en el CRM Inmovilla.
*   **Base de Datos (Google Sheets):** Se utiliza una hoja de cálculo de Google como base de datos.
    *   *Por qué:* Es una herramienta que el cliente ya sabe usar. Permite editar precios y zonas en una interfaz familiar (Excel) y los cambios se reflejan en la web automáticamente al publicarse como CSV.
*   **Backend de Correo (EmailJS):** Servicio API para envío de emails directamente desde JavaScript.
    *   *Por qué:* Permite enviar los informes de tasación y las notificaciones a la agencia sin necesidad de un servidor SMTP propio ni código backend (PHP/Node).

## 4. Arquitectura y Flujo de Datos

1.  **Carga de Datos:** Al cargar la página, el JavaScript realiza una petición `fetch` al archivo CSV generado por Google Sheets. Esto carga en memoria los barrios, zonas y precios actualizados.
2.  **Interacción:** El usuario selecciona su ubicación y características. La lógica de negocio (reglas de tasación) se ejecuta en el navegador del cliente.
3.  **Cálculo:** El algoritmo aplica los coeficientes correctores (definidos también en Google Sheets) sobre el precio base.
4.  **Captación y Envío:**
    *   El usuario introduce sus datos para ver el resultado.
    *   **EmailJS** procesa la petición y envía dos correos: uno al cliente con el informe y otro a la agencia con el lead.

## 5. Conclusiones
Esta solución cumple con todos los requisitos funcionales sin infringir las restricciones técnicas del CRM Inmovilla. Se ha logrado entregar un producto profesional, reactivo y fácil de mantener, empoderando al cliente para gestionar su propia herramienta sin dependencia técnica continua.
