## 🗓️ 2025-11-10
**Tema:** Creación de wireframes iniciales (Login, Listado de Comerciales  y Listado de Propiedades)  
**Tipo de avance:** Diseño estructural (wireframe funcional)  

**Resumen:**  
- Se han creado los **wireframes base en Excalidraw** correspondientes a:
  - Página de **Login** (estructura de campos, CTA, enlace de recuperación).  
  - **Listado de propiedades** (disposición de tarjetas, filtros, navegación).  
- Objetivo: definir la arquitectura visual y jerarquía de información antes de aplicar estilos o componentes de diseño.  
- Se trabajará en versiones para **desktop, tablet y móvil**.  
- Pendiente: revisión del flujo de navegación entre vistas y posterior paso a *mockup visual*.  

**Archivos relacionados:**   
- `/docs/img/wireframe_login_listado_20251110.png`  

**Observaciones:**  
Se considera parte de la **Fase II: Diseño del proyecto** en la documentación oficial del Proyecto DAW (IES Abastos).  

---
## 🗓️ 2025-11-20

**Tema:** Arquitectura MVC Base, Configuración de Servidor y Definición Final de BBDD.
**Tipo de avance:** Backend / DevOps / DB Design.

### 🚀 Resumen del día
Se ha establecido la estructura "esqueleto" definitiva del proyecto, abandonando las carpetas de pruebas anteriores y adoptando una arquitectura **MVC** con **PHP**.

### 🔧 1. Configuración del Entorno y Servidor
* **Virtual Host:** Configurado `inmobiliaria.loc` en Apache apuntando a la carpeta `/public` (Security by Design).
* **Routing (.htaccess):** Implementación de redirección de tráfico al *Front Controller* (`index.php`) para manejo de URLs limpias.
* **Estructura de Directorios:** Limpieza y definición de:
    * `/app`: Núcleo protegido (Controllers, Models, Views, Core).
    * `/public`: Único punto de acceso web (Assets, index.php).
    * `/config`: Variables globales fuera del núcleo.

### 🏗️ 2. Arquitectura Backend (PHP)
* **Front Controller:** Creación del punto de entrada único.
* **Autoloader:** Implementación de carga automática de clases (PSR-4 style) para evitar `require` manuales.
* **Conexión BBDD:** Creación de la clase `Database.php` utilizando **PDO** y el patrón de diseño **Singleton** para optimizar conexiones.
* **Configuración Global:** Centralización de credenciales y rutas en `config/config.php`.

### 🗄️ 3. Base de Datos (Evolución Final)
Se ha modificado el diseño inicial que incluye:
* **Soft Deletes:** Implementación de campos `archivado`/`activo` en lugar de borrado físico.
* **Auditoría:** Tabla específica para registrar acciones críticas (quién hizo qué y cuándo).
* **Integridad Referencial:** Restricciones estrictas (`ON DELETE RESTRICT`) para evitar inconsistencias (ej. no borrar propietarios con inmuebles).
* **Multimedia:** Tabla `medios` separada para soportar galerías de fotos/videos por inmueble.
* **Flexibilidad:** Uso de campos `JSON` en la tabla `demandas` para criterios de búsqueda complejos.

### 📝 Archivos clave creados/modificados
* `public/.htaccess`
* `public/index.php`
* `app/core/Database.php`
* `app/Autoloader.php`
* `config/config.php`
* `docs/base_datos.md` (Documentación técnica del esquema).

---

