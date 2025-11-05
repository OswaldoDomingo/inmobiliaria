# INMOBILIARIA

## 1. Introducción y Contexto
-   En el sector inmobiliario actual, muchas agencias pequeñas se están desvinculando de las grandes firmas porque estas limitan sus zonas de actuación.
-   Estas agencias independientes necesitan expandirse a nuevos lugares para captar propiedades, ya que sus mercados locales pueden estar saturados.
-   Actualmente, carecen de herramientas digitales propias y asequibles que les den visibilidad y control total sobre su cartera de inmuebles.

## 2. Objetivos y Solución Propuesta

-   **Objetivo General:** Desarrollar el portal "Inmobiliaria", una plataforma web que ofrece una solución sencilla para la consulta y gestión de propiedades en venta o alquiler.
-   **Objetivos Específicos:**
    -   Proveer una interfaz pública intuitiva para que los visitantes busquen y filtren propiedades.
    -   Implementar un panel de control privado para que el personal de la empresa gestione el inventario de inmuebles.
    -   (Añadir cualquier otro objetivo clave)

## 3. Alcance y Requisitos Principales
-   El sistema se compondrá de dos interfaces principales:
    -   **Interfaz Pública (Frontend):** Accesible para visitantes. Incluirá listado de propiedades, buscador con filtros y formularios de contacto.
    -   **Interfaz Privada (Backend):** Protegida por contraseña, para gestores y administradores. Permitirá crear, editar y eliminar propiedades (CRUD).

## 4. Planificación del Proyecto (Fases)
-   **Fase I:** Diseño y definición del proyecto (Investigación, UX/UI, Arquitectura).
-   **Fase II:** Base de datos y estructura MVC (Diseño del modelo de datos e implementación de la estructura base).
-   **Fase III:** Desarrollo de funcionalidades principales (Backend y Frontend).
-   **Fase IV:** Pruebas, seguridad y documentación (Testing, QA, redacción de memoria).
-   **Fase V:** Defensa ante el tribunal.

## 5. Entregables
-   Descripción funcional del proyecto (Memoria).
-   Diagrama Entidad-Relación (Diseño de la BD).
-   Script SQL (Código de la BD).
-   Carpeta de código MVC (La aplicación).
-   Memoria final (PDF).

## 6. Requisitos Funcionales
### 6.1. Interfaz Pública (Visitantes)

El visitante, sin necesidad de registro, debe poder:

-   **RF 1.1 (Navegación):** Acceder a páginas estáticas (Inicio, Quiénes Somos, Contacto).
-   **RF 1.2 (Ver Listado):** Ver un listado o galería de todas las propiedades disponibles.
-   **RF 1.3 (Ver Detalles):** Hacer clic en una propiedad para ver su ficha de detalle (incluyendo fotos, descripción completa, características, precio, mapa de ubicación).
-   **RF 1.4 (Búsqueda Básica):** Realizar una búsqueda simple de propiedades (ej. por ciudad o tipo).
-   **RF 1.5 (Filtros Avanzados):** Filtrar el listado de propiedades por múltiples criterios:
    -   Tipo de operación (Venta / Alquiler).
    -   Tipo de propiedad (Piso, Casa, Local).
    -   Rango de precios ($ min - $ max).
    -   Número de habitaciones.
    -   (Cualquier otro que consideres: m², ascensor, etc.).
-   **RF 1.6 (Contacto):** Enviar un formulario de contacto general a la inmobiliaria.
-   **RF 1.7 (Contacto por Propiedad):** Enviar un formulario de contacto específico solicitando información sobre una propiedad en concreto.

### 6.2. Interfaz Privada (Gestores y Administradores)

El personal de la empresa, tras iniciar sesión, debe poder:

-   **RF 2.1 (Autenticación):** Iniciar y cerrar sesión de forma segura.
-   **RF 2.2 (Dashboard):** Ver un panel de control resumen (ej. últimas propiedades añadidas, mensajes recibidos).
-   **RF 2.3 (Gestión de Propiedades - CRUD):**
    -   **Crear:** Añadir una nueva propiedad al sistema (subir fotos, rellenar todos sus datos).
    -   **Leer:** Ver el listado de todas las propiedades (publicadas y no publicadas).
    -   **Actualizar:** Editar la información de una propiedad existente.
    -   **Eliminar:** Borrar una propiedad.
-   **RF 2.4 (Marcar Estado):** Cambiar el estado de una propiedad (ej. de "Disponible" a "Reservada" o "Vendida").
-   **RF 2.5 (Gestión de Mensajes):** Consultar los mensajes recibidos a través de los formularios de contacto.
-   **RF 2.6 (Gestión de Usuarios - *Solo Admin*):** Poder crear, editar o eliminar cuentas de otros gestores (Este requisito es opcional, si planeas tener diferentes roles).