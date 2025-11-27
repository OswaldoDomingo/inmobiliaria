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


# 📅 Avances — 21/11/2025

## 🧩 Reestructuración general del proyecto
Se realizó una reorganización completa de la estructura del proyecto con el objetivo de dejar una arquitectura limpia, modular y segura. Se eliminaron directorios duplicados, configuraciones obsoletas y archivos heredados de pruebas previas.  
El proyecto queda estructurado sobre un patrón MVC básico: `app/`, `config/`, `public/`, `storage/`, `docs/`.

Esta reestructuración permite un desarrollo más ordenado y coherente para las fases siguientes del proyecto.

---

## 🔐 FASE 1 — Seguridad básica y configuración

### ✔ Configuración de archivo `.env`
- Se creó el archivo `.env` en la raíz del proyecto.
- Se añadieron las credenciales de la base de datos.
- Se añadió `.env` a `.gitignore` para evitar exposición de credenciales.

### ✔ Carpeta `config/`
Se estableció la estructura definitiva de configuración con los archivos:
- `env.php` → carga de variables de entorno.
- `paths.php` → rutas absolutas del proyecto.
- `database.php` → configuración central de conexión a MySQL.

### ✔ Actualización de `Database.php`
- Adaptación completa de la clase `Database` al sistema de configuración basado en `.env`.
- Uso de PDO con opciones avanzadas (errores por excepción, fetch por objetos, prepares seguros).
- Integración con `getDatabaseConfig()` para evitar constantes hardcodeadas.

### ✔ Punto de entrada `public/index.php`
- Carga automática de `env.php`, `paths.php` y el autoloader.
- Prueba de inicialización del sistema.
- Test real de conexión a la base de datos.

### ✔ Verificación con consulta real
La consulta al usuario administrador en la tabla `usuarios` devolvió datos correctos, confirmando:
- Conexión funcionando
- Base de datos accesible
- Entorno correctamente configurado

---

## 📌 Estado final
| Elemento | Estado |
|---------|--------|
| Estructura MVC establecida | ✔ |
| Variables de entorno funcionando | ✔ |
| Configuración centralizada | ✔ |
| Conexión a BD validada | ✔ |
| Preparado para fase 2 (Router) | ✔ |

---

## 🗓️ 2025-11-27

**Tema:** Implementación del Router Core
**Tipo de avance:** Backend / Arquitectura

### 🚀 Resumen del día
Se ha implementado el componente `Router` para gestionar las peticiones HTTP de forma explícita, eliminando la carga directa de vistas desde el punto de entrada.

### 🔧 Cambios Realizados
*   **Nuevo Componente `App\Core\Router`**:
    *   Soporte para métodos GET y POST.
    *   Despacho de rutas basado en `$_SERVER['REQUEST_URI']`.
    *   Limpieza de parámetros GET (query strings).
    *   Manejo básico de errores 404.

*   **Actualización de `public/index.php`**:
    *   Integración del Router.
    *   Definición de ruta raíz `/` (carga `landing.php`).
    *   Definición de ruta de prueba `/prueba`.

### 📝 Archivos clave creados/modificados
*   `app/Core/Router.php`
*   `public/index.php`



## 🗓️ 2025-11-27 (Continuación)

**Tema:** Migración Herramienta de Tasación y Consolidación de Arquitectura
**Tipo de avance:** Frontend / Backend / Refactorización

### 🚀 Resumen
Se ha completado la migración de la herramienta de tasación independiente a la arquitectura MVC y se ha consolidado la estructura de vistas del proyecto.

### 🔧 Cambios Realizados

#### 1. Migración Herramienta de Tasación
*   **Controlador (`TasacionController`):** Gestiona la vista del formulario y el envío de correos mediante AJAX.
*   **Librería (`SimpleSMTP`):** Refactorizada e integrada en `App\Lib` para el envío de correos.
*   **Vista (`formulario.php`):** Adaptación del HTML original a una vista PHP limpia.
*   **Assets:** Migración de estilos a `public/assets/css/tasacion.css`.

#### 2. Consolidación de Arquitectura
*   **Layouts Compartidos:** Creación de `header.php` y `footer.php` en `app/views/layouts/` para unificar el diseño.
*   **HomeController:** Nuevo controlador para la página de inicio.
*   **Server Config:** Creación de `.htaccess` en `public/` para asegurar que todas las peticiones pasen por el `Router`.
*   **Router:** Actualización de rutas para usar los nuevos controladores (`/` -> `HomeController`, `/tasacion` -> `TasacionController`).

### 📝 Archivos clave creados/modificados
*   `app/Controllers/TasacionController.php`
*   `app/Controllers/HomeController.php`
*   `app/Lib/SimpleSMTP.php`
*   `app/views/layouts/header.php`
*   `app/views/layouts/footer.php`
*   `app/views/tasacion/formulario.php`
*   `public/.htaccess`

#### 3. Correcciones de Estilo (Hotfixes)
*   **Conflicto Bootstrap vs Tailwind:** Se desactivó el `preflight` de Tailwind y se forzó la visibilidad de la clase `.collapse` en el controlador para recuperar el menú de navegación.
*   **Checkboxes:** Se añadieron reglas `!important` en `tasacion.css` para asegurar la visualización de los estados seleccionados.

#### 4. Correcciones en Producción (Hotfixes)
*   **Autoloader Case-Sensitivity:** Se actualizó `App\Autoloader` para soportar directorios en minúsculas (fallback), solucionando el error `Class not found` en entornos Linux (producción).
*   **Renombrado de Directorios:** Se renombró `app/controllers` a `app/Controllers` para cumplir estrictamente con PSR-4.

---

## 🗓️ 2025-11-27 (Sesión Nocturna)

**Tema:** Implementación de Autenticación (Login) y Dashboard
**Tipo de avance:** Backend / Seguridad / UI

### 🚀 Resumen
Se ha implementado el sistema completo de autenticación de usuarios, incluyendo login seguro, protección de rutas y un panel de control (Dashboard) con vistas diferenciadas por rol.

### 🔧 Cambios Realizados

#### 1. Arquitectura y Seguridad
*   **Modelo de Usuario (`App\Models\User`):** Implementación de acceso a datos para verificación de credenciales.
*   **Controlador de Autenticación (`AuthController`):** Gestión de inicio de sesión (`login`), autenticación (`authenticate`) y cierre de sesión (`logout`).
*   **Seguridad:**
    *   Uso de `password_hash` y `password_verify` para almacenamiento seguro de contraseñas.
    *   Gestión de sesiones PHP (`session_start`, `session_regenerate_id`).
    *   Protección de rutas: El Dashboard redirige al login si no hay sesión activa.

#### 2. Interfaz de Usuario (UI)
*   **Vista Login:** Formulario de acceso integrado con el layout principal.
*   **Vista Dashboard:** Panel de bienvenida que adapta el mensaje según el rol del usuario (`admin`, `coordinador`, `comercial`).
*   **Header Dinámico:** El menú de navegación ahora muestra "Acceso Profesionales" para visitantes y "Mi Panel" para usuarios logueados.

#### 3. Enrutamiento
*   **Router:** Registro de nuevas rutas:
    *   `GET /login`, `POST /login`
    *   `GET /logout`
    *   `GET /dashboard` (Protegida)

### 📝 Archivos clave creados/modificados
*   `app/Models/User.php`
*   `app/Controllers/AuthController.php`
*   `app/views/auth/login.php`
*   `app/views/admin/dashboard.php`
*   `app/views/layouts/header.php`
*   `public/index.php`


## 🗓️ 2025-11-27 (Gestión de Usuarios y Seguridad)

**Tema:** CRUD de Usuarios y Refactorización de Seguridad
**Tipo de avance:** Backend / Seguridad

### 🚀 Resumen
Se ha implementado el sistema de gestión de usuarios (CRUD) con un enfoque estricto en la seguridad, y se ha refactorizado el controlador de tasación para blindar la entrada de datos.

### 🔧 Cambios Realizados

#### 1. Gestión de Usuarios (CRUD)
*   **Controlador (`UserController`):** Implementación de métodos para listar (`index`), crear (`create`) y guardar (`store`) usuarios.
    *   **Acceso Restringido:** Solo los administradores pueden acceder a estas rutas.
*   **Modelo (`User`):** Nuevos métodos `getAll()` y `create()` utilizando sentencias preparadas PDO.
*   **Vistas:**
    *   `admin/users/index.php`: Listado de usuarios con indicadores de estado y rol.
    *   `admin/users/create.php`: Formulario de alta con validación visual de errores.

#### 2. Seguridad y Validación (Política de Tolerancia Cero)
*   **Sanitización Universal:** Aplicación de `trim()` y `strip_tags()` a todas las entradas de usuario.
*   **Validación Estricta:**
    *   Verificación de email único en BD.
    *   Validación de formato de email (`filter_var`).
    *   Longitud mínima de contraseña.
    *   Validación de tipos de datos (numéricos, longitud mínima) en el tasador.
*   **Refactorización `TasacionController`:** Reescribimos el método `enviar()` para asegurar que ningún dato malicioso llegue al sistema de correos, utilizando `htmlspecialchars` en la construcción del mensaje HTML.

### 📝 Archivos clave creados/modificados
*   `app/Controllers/UserController.php`
*   `app/Controllers/TasacionController.php` (Refactorizado)
*   `app/views/admin/users/index.php`
*   `app/views/admin/users/create.php`


#### 3. Gestión de Usuarios (Parte 2: Ciclo de Vida)
*   **Edición de Usuarios:** Implementación de la vista y lógica para modificar datos de usuarios existentes.
    *   Validación de unicidad de email (excluyendo al propio usuario).
    *   Gestión opcional de cambio de contraseña.
*   **Baja de Usuarios (Soft Delete):**
    *   Implementación de borrado lógico (`activo = 0`, `archivado = 1`).
    *   **Protección Anti-Suicidio:** Bloqueo de intentos de auto-desactivación por parte del usuario logueado.
*   **Login Reforzado:** Actualización del `AuthController` para impedir el acceso a usuarios inactivos o archivados.
*   **Dashboard:** Añadido botón de acceso rápido a "Gestionar Usuarios" para administradores.

### 📝 Archivos clave creados/modificados
*   `app/Controllers/UserController.php` (Métodos `edit`, `update`, `delete`)
*   `app/Controllers/AuthController.php` (Check de estado)
*   `app/views/admin/users/edit.php`
*   `app/views/admin/users/index.php`
*   `app/views/admin/dashboard.php`



