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


## 🗓️ 2025-11-28 (Seguridad y Gestión de Bloqueos)

**Tema:** Seguridad en Login y Gestión de Bloqueos
**Tipo de avance:** Seguridad / Backend / UX

### 🚀 Resumen
Se ha reforzado la seguridad del sistema de autenticación implementando protección contra ataques de fuerza bruta y un sistema de gestión manual de bloqueos para administradores.

### 🔧 Cambios Realizados

#### 1. Seguridad en Login (Fuerza Bruta)
*   **Base de Datos:** Nuevas columnas `intentos_fallidos` y `cuenta_bloqueada` en la tabla `usuarios`.
*   **Lógica de Bloqueo:**
    *   Incremento de contador tras fallo.
    *   **Bloqueo automático** al alcanzar 3 intentos fallidos.
    *   Reseteo de contador tras login exitoso.
*   **UX:** Implementación de **Flash Messages** (`$_SESSION['error']`) para mostrar alertas visuales en lugar de páginas en blanco.

#### 2. Gestión Manual (Admin)
*   **Panel de Usuarios:** Nueva funcionalidad para bloquear/desbloquear usuarios manualmente.
*   **Indicadores Visuales:** Botones de estado (Verde/Naranja) según el estado de bloqueo.
*   **Protección:** Restricción para evitar que un administrador se bloquee a sí mismo.

### 📝 Archivos clave creados/modificados
*   `app/Controllers/AuthController.php`
*   `app/Controllers/UserController.php`
*   `app/Models/User.php`
*   `app/views/auth/login.php`
*   `app/views/admin/users/index.php`
*   `public/index.php` (Nuevas rutas)


## 🗓️ 2025-11-29 (Seguridad y Estabilidad)

**Tema:** Hardening de Seguridad y Manejo de Errores Global
**Tipo de avance:** Backend / Seguridad / DevOps

### 🚀 Resumen
Se han aplicado mejoras críticas de seguridad y estabilidad en el núcleo de la aplicación, enfocándose en el manejo robusto de errores y la protección de datos sensibles.

### 🔧 Cambios Realizados

#### 1. Manejo de Errores y Excepciones
*   **Database Core (`App\Core\Database`):** Eliminación de `die()` en fallos de conexión. Ahora lanza `PDOException` para ser capturada por el manejador global.
*   **Global Exception Handler (`public/index.php`):** Implementación de `set_exception_handler` para capturar errores no controlados.
    *   **Producción:** Muestra un mensaje genérico "Error de sistema" (HTTP 500) y registra el detalle en el log del servidor (`error_log`).
    *   **Debug:** Muestra la traza completa si `app.debug` es true.

#### 2. Configuración y Secretos
*   **Configuración Centralizada (`config/config.php`):** Integración de configuración de emails (`emails.agency`, `emails.noreply`) leyendo desde variables de entorno (`.env`).
*   **TasacionController:** Refactorización para usar las nuevas claves de configuración, eliminando direcciones de correo hardcodeadas.

#### 3. Base de Datos (Schema)
*   **Schema Update (`database/schema.sql`):** Actualización del esquema de referencia de la tabla `usuarios` con columnas de auditoría y seguridad:
    *   `intentos_fallidos` (Protección fuerza bruta).
    *   `cuenta_bloqueada` (Bloqueo temporal/permanente).
    *   `archivado` y `fecha_baja` (Soft deletes y auditoría).

### 📝 Archivos clave creados/modificados
*   `app/Core/Database.php`
*   `public/index.php`
*   `config/config.php`
*   `app/Controllers/TasacionController.php`
*   `database/schema.sql`


## 🗓️ 2025-11-29 (Fotos de Perfil)

**Tema:** Implementación de Fotos de Perfil de Usuario
**Tipo de avance:** Frontend / Backend / UX

### 🚀 Resumen
Se ha añadido la capacidad de que los usuarios (Admin, Coordinadores, Comerciales) tengan una foto de perfil asociada a su cuenta.

### 🔧 Cambios Realizados

#### 1. Base de Datos
*   **Nueva Columna:** Se añadió `foto_perfil` (VARCHAR 255) a la tabla `usuarios`.

#### 2. Backend (UserController)
*   **Subida Segura:** He implementado `handleFileUpload` con validación estricta:
    *   **MIME Type:** Solo permito JPG, PNG, WEBP para asegurar que no se suban scripts ejecutables disfrazados.
    *   **Renombrado:** Genero nombres únicos (`uniqid`) para evitar colisiones y ejecución de scripts maliciosos.
    *   **Limpieza:** Borro automáticamente la imagen anterior al actualizar para no saturar el servidor.
*   **Manejo de Errores:** He utilizado bloques `try-catch` para capturar fallos en la subida y notificar al usuario sin romper la ejecución, priorizando la experiencia de usuario y la estabilidad.

#### 3. Frontend (Vistas)
*   **Formularios:** He actualizado `create.php` y `edit.php` con `enctype="multipart/form-data"` y previsualización de imagen.
*   **Listado:** He añadido una columna "Avatar" en `index.php` con miniaturas circulares.

### 📝 Archivos clave creados/modificados
*   `database/migrations/01_add_foto_perfil.sql`
*   `app/Models/User.php`
*   `app/Controllers/UserController.php`
*   `app/views/admin/users/create.php`
*   `app/views/admin/users/edit.php`
*   `app/views/admin/users/index.php`

### 💡 Justificación Técnica para el Tribunal
He decidido implementar la subida de archivos de esta manera manual en lugar de usar librerías externas para demostrar el conocimiento sobre el manejo de streams de archivos, permisos y validación de tipos MIME en PHP nativo. El uso de `uniqid` es una decisión de seguridad deliberada para desacoplar el nombre del archivo original del nombre en el servidor.



## 🗓️ 2025-11-29 (Mejoras UX Dashboard)

**Tema:** Mejora de Experiencia de Usuario en Panel de Control
**Tipo de avance:** Frontend / UX / Backend

### 🚀 Resumen
Se ha mejorado la interfaz del Dashboard y la cabecera para mostrar información contextual del usuario logueado, personalizando la experiencia.

### 🔧 Cambios Realizados

#### 1. Persistencia de Datos de Sesión
*   **AuthController:** Modificado para almacenar `email` y `foto_perfil` en la variable superglobal `$_SESSION` al momento del login, evitando consultas redundantes a la base de datos en cada carga de página.

#### 2. Interfaz de Usuario (UI)
*   **Header:** Ahora muestra la foto de perfil (o un icono por defecto si no existe) y el email del usuario junto a su nombre.
*   **Dashboard:** La sección "Tus Datos" ahora refleja la información real del usuario logueado, incluyendo su avatar.

### 📝 Archivos clave creados/modificados
*   `app/Controllers/AuthController.php`
*   `app/views/layouts/header.php`
*   `app/views/admin/dashboard.php`

## ✅ 2025-11-30 (Clientes: Schema + CRUD + Menús)

**Tema:** Alta de Clientes e Integración en Dashboard  
**Tipo de avance:** Backend / DB / UX  

### ✅ Resumen
- Se creó la migración `database/migrations/03_create_crm_tables.sql` con las tablas `clientes` e `inmuebles` (FK a `usuarios` y `clientes`, índices de filtrado y flags de operación).
- Nuevo módulo CRUD de clientes con control por rol (admin/coordinador ven todo; comercial solo los suyos).
- Enlace directo a clientes desde el header y botones en el dashboard según rol.
- Manejador global de errores muestra mensaje genérico; detalle queda en el log.
- Incidencia resuelta: error “Unknown column usuario_id/telefono” al crear clientes; se corrigió el esquema y se reintentó el alta.

### ✅ Archivos clave creados/modificados
- `database/migrations/03_create_crm_tables.sql`
- `app/Models/Cliente.php`
- `app/Controllers/ClienteController.php`
- `app/Views/admin/clientes/{index.php,create.php,edit.php}`
- `public/index.php` (rutas de clientes)
- `app/Views/layouts/header.php` (menú Clientes)
- `app/Views/admin/dashboard.php` (accesos rápidos por rol)

### ✅ Notas de implementación
- CSRF en todos los formularios de clientes; asignación automática de `usuario_id` al comercial logueado.
- Validación de DNI duplicado antes de insertar/actualizar.
- Borrado protegido: si hay inmuebles, el delete falla y muestra mensaje.

### ✅ Errores y soluciones
- **1054 Unknown column usuario_id/telefono**: la tabla `clientes` no tenía las columnas del nuevo esquema; se corrigió con ALTER y se añadió la migración completa.
- **Error de sistema** al volver/guardar: se resolvió al alinear el esquema y dejar que el manejador global devuelva mensaje genérico y loguee detalle.
- **PHP 8 (tipado estricto):** Se casteó `$_SESSION['user_id']` a int en `ClienteController::index()` para evitar la excepción de tipo en producción.
- **Warnings deprecados:** Se ajustó `error_reporting` eliminando `E_STRICT` y se usó `\PDOException` en el handler global para limpiar avisos de `use` sin efecto.
---

## ✅ 2025-11-30 (Roles y reasignación de clientes)

**Tema:** Permisos para asignar/reasignar clientes a comerciales  
**Tipo de avance:** Backend / CRM / Seguridad de roles

### ?? Resumen
Se habilitó que **administradores y coordinadores** puedan asignar o reasignar clientes a cualquier comercial, manteniendo a los comerciales limitados a su propia cartera.

### ✅ Cambios realizados
- **Modelo `User`:** Nuevo `getComercialesActivos()` devuelve id/nombre de comerciales y coordinadores activos (no archivados) ordenados.
- **`ClienteController`:** Carga la lista de comerciales en `create/edit` solo para roles con permiso; en `store/update` fuerza el `usuario_id` según rol (admin/coordinador toma el select, comercial se autoasigna o mantiene el asignado).
- **Vistas `clientes/create` y `clientes/edit`:** Select condicional "Comercial Asignado" visible solo para admin/coordinador; en edición se marca el comercial actual.

### ✅ Archivos clave tocados
- `app/Models/User.php`
- `app/Controllers/ClienteController.php`
- `app/Views/admin/clientes/create.php`
- `app/Views/admin/clientes/edit.php`

### ✅ Notas
- El controlador impide que un comercial manipule el formulario para reasignar clientes ajenos.
- Recomendado: test manual en producción tras limpiar caché de sesiones.

## ✅ 2025-12-02

**Tema:** Cumplimiento legal base (aviso legal, privacidad, cookies y banner RGPD)
**Tipo de avance:** Legal / Frontend / UX

### Resumen
- Se ha creado el modulo legal en MVC con `LegalController` y vistas provisionales (`app/Views/legal/*`) accesibles desde `/legal/aviso-legal`, `/legal/privacidad` y `/legal/cookies`.
- El footer se reorganizo para mostrar enlaces legales visibles y las redes oficiales debajo en formato horizontal.
- Se anadio un banner de cookies fijo inferior con gestion de consentimiento en `localStorage` (aceptar/rechazar) y botones con colores corporativos.

### Archivos clave creados/modificados
- `app/Controllers/LegalController.php`
- `app/Views/legal/{aviso_legal.php,privacidad.php,cookies.php}`
- `public/index.php`
- `app/views/layouts/footer.php`
- `docs/avances.md`, `docs/memoria_proyecto.md`

### Notas
- Los textos legales son provisionales y se sustituiran por los definitivos tras la revision juridica.

---

## ✅ 2025-12-04

**Tema:** Banner principal dinamico y popup estacional controlado por sesion  
**Tipo de avance:** Frontend / UX / MVC

### ✅ Resumen
- Refactor del `HomeController` para centralizar variables de interfaz (hero y popup) respetando la separacion de responsabilidades.
- Creacion de la carpeta `app/Views/partials/` para alojar vistas reutilizables y despliegue del hero.
- Integracion de `Hero Section` con imagen aleatoria de Lorem Picsum y textos configurables desde el controlador.
- Logica de sesion con `$_SESSION['tarjeta_vista']` para evitar que el popup navideno rebote en recargas sucesivas.

## 🗓️ 2025-12-06 (Seguridad y Arquitectura)

**Tema:** Hardening del servidor, Refactorización de Configuración y Limpieza.
**Tipo de avance:** Backend / DevOps / Seguridad

### 🚀 Resumen
Se ha realizado una refactorización integral de la capa de configuración y seguridad del proyecto. El objetivo ha sido eliminar credenciales del código fuente, proteger los archivos sensibles y limpiar la estructura de directorios, centralizando la configuración en la carpeta `config/`.

### 🔧 Cambios Realizados

#### 1. Sistema de Configuración y Entorno
* **Implementación Nativa (`App\Core\Env`):** Se ha desarrollado un cargador de variables de entorno propio (sin dependencias externas) que utiliza funciones nativas de PHP.
* **Centralización:** Se ha movido el archivo `.env` desde la raíz a la carpeta `config/` para mantener el directorio raíz limpio.
* **Refactorización de `config.php`:** Se han eliminado los valores por defecto inseguros (fallbacks como "root"). Ahora el sistema obliga a la lectura del archivo `.env`, cumpliendo con normativas de seguridad OWASP.
* **Bootstrap (`index.php`):** Actualización de la ruta de carga en el punto de entrada para apuntar a `CONFIG . '/.env'`.

#### 2. Seguridad del Servidor (Hardening)
* **Protección Global (`.htaccess` en raíz):** Configuración para bloquear estrictamente el acceso web a archivos ocultos (que empiezan por punto, como `.git` o `.env`), manteniendo la excepción para certificados SSL (`.well-known`).
* **Protección Específica (`.htaccess` en `config/`):** Se ha creado un archivo con la directiva `Deny from all` dentro de la carpeta `config/` para blindar totalmente el acceso a los archivos de configuración.

#### 3. Correcciones Adicionales
* **Enrutamiento:** Ajuste en la expresión regular del Router para permitir URLs con barras y parámetros complejos.
* **Git:** Verificación de que el archivo `.env` está correctamente ignorado en `.gitignore`.

### 📝 Archivos clave modificados
* `app/Core/Env.php` (Nuevo)
* `config/config.php`
* `public/index.php`

## ✅ 07/12/2025 (Módulo Inmuebles: Implementación, Bloqueo y Resolución)

**Tema:** Implementación Completa del Módulo Inmuebles y Core Routing Fix
**Tipo de avance:** Backend / Core / UI / DB

### 🚀 Resumen del día
Se ha desarrollado e integrado con éxito el módulo de **Inmuebles**, cubriendo tanto el panel de administración (CRUD) como la estructura para la parte pública. Aunque inicialmente se detectó un bloqueo técnico severo relacionado con el enrutamiento (Error 404), este fue diagnosticado y resuelto en la misma jornada, permitiendo cerrar el módulo como **funcional**.

### 1. Desarrollo del Módulo (Fase Inicial)
*   **Modelo de Datos (`Inmueble.php`):** Mapeo completo de la tabla `inmuebles` (`ref`, `propietario_id`, `comercial_id`, etc.) con métodos de paginación y filtrado.
*   **Controladores:**
    *   `InmuebleController`: Lógica de administración, validaciones y gestión de permisos.
    *   `InmueblePublicController`: Estructura para el catálogo público.
*   **Vistas Admin:** Listado (`index.php`) y Formulario (`form.php`) maquetado con Bootstrap 5.
*   **Rutas:** Registro de endpoints en `public/index.php`.

### 2. Bloqueo Técnico Detectado (Routing)
Durante las pruebas, se identificó que las rutas de subdirectorios (ej. `/admin/inmuebles/nuevo`) devolvían **404 Not Found** en el entorno de desarrollo Windows, impidiendo el acceso al formulario de creación.
*   **Causa:** La normalización de rutas en `Router.php` fallaba al procesar `SCRIPT_NAME` con separadores de directorio inversos (`\`), típicos de Windows.

### 3. Resolución y Cierre (Fix & Polish)
*   **Corrección del Core:** Se aplicó una normalización de separadores (`str_replace('\\', '/', ...)`) en `Router::dispatch`, solucionando el error 404.
*   **Ajuste de Sesiones:** Unificación de claves de sesión (`user_id` vs `id_usuario`) en controladores para evitar redirecciones erróneas ("bucle de login").
*   **Consulta de Comerciales:** Corrección en `InmuebleController::getComerciales()` para listar correctamente usuarios activos sin depender de columnas obsoletas.

### 4. Lógica de Negocio y Mejoras UI
*   **Roles y Permisos:**
    *   **Comercial:** Acceso habilitado. Al crear inmuebles, se **auto-asigan** como responsables (campo read-only).
    *   **Admin/Coordinador:** Control total para asignar inmuebles a cualquier usuario.
*   **Integración CRM:** Sección "Inmuebles de este cliente" añadida en la ficha de cliente (`admin/clientes/edit.php`) con botón de creación directa.

### 📝 Archivos clave modificados
*   `app/Core/Router.php` (Fix Routing)
*   `app/Models/Inmueble.php`
*   `app/Controllers/InmuebleController.php`
*   `app/Views/admin/inmuebles/form.php`
*   `app/Views/admin/clientes/edit.php`

### ✅ Estado Final
El módulo Inmuebles está **DESBLOQUEADO y 100% OPERATIVO**, cumpliendo los requisitos de seguridad y gestión de roles.

## 2025-12-07 – Mejora de navegación en Edición de Inmuebles (Return Path)

**Contexto**  
Hasta ahora, al editar un inmueble desde la ficha de un cliente, el botón **«Volver»** y la redirección tras **«Guardar Inmueble»** llevaban siempre al listado general `/admin/inmuebles`. Esto hacía perder el contexto de trabajo (ficha del cliente) y obligaba a varios clics extra para volver a la vista original.

**Qué se ha implementado**  
- Se ha añadido un sistema de **return path** mediante un parámetro `return_to`:
  - La ficha de cliente (`admin/clientes/edit`) genera el enlace de **Editar inmueble** incluyendo `return_to` con la URL actual de la ficha.
  - El `InmuebleController` lee y valida ese `return_to` y lo pasa a la vista.
  - El formulario de inmuebles incluye un `<input type="hidden" name="return_to">`.
  - Los botones **«Volver»** y **«Cancelar»** usan `return_to` si existe; si no, hacen fallback al listado `/admin/inmuebles`.
  - Tras guardar correctamente, el método `update()` redirige al `return_to` válido y añade `msg=updated`.

**Cómo se ha hecho (detalle técnico)**  
- Se ha creado un método privado `validateReturnTo()` en el controlador de inmuebles para:
  - Aceptar solo rutas internas que comiencen por `/admin/`.
  - Bloquear URIs externas (`http://`, `https://`, `//`) y posibles intentos de open redirect.
- Se ha creado un helper `addQueryParam()` para añadir `msg=updated` sin romper la query string existente.
- En caso de errores de validación del formulario:
  - No se redirige.
  - Se vuelve a pintar la vista `form.php` con los errores.
  - Se conserva el valor de `return_to` para que, una vez corregido, se pueda volver al origen correcto.

**Problemas detectados y solución**  
- **Riesgo de open redirect** al aceptar un `return_to` sin filtrar → se soluciona con `validateReturnTo()`, que solo admite rutas internas seguras.
- **Compatibilidad hacia atrás**: había que mantener el comportamiento antiguo cuando se edita desde `/admin/inmuebles` → si no hay `return_to`, los botones siguen yendo al listado como antes.
- **Gestión de query string**: al añadir `msg=updated` se podía romper la URL → se ha centralizado en `addQueryParam()` para construir la URL correctamente.

**Pruebas realizadas**  
- Edición de inmueble desde ficha de cliente:
  - **Admin**, **Coordinador** y **Comercial**: tras **«Volver»** o guardar con éxito, se vuelve a la ficha del cliente.
- Edición desde listado de inmuebles:
  - Todos los roles autorizados vuelven al listado, igual que antes.
- Validación con errores:
  - Se muestran los mensajes en el formulario.
  - No se pierde ni el estado del formulario ni el `return_to`.
- Intentos de manipular manualmente el `return_to` con URLs externas:
  - El sistema ignora esas rutas y hace fallback a `/admin/inmuebles`.

Además, se ha ejecutado un conjunto de pruebas formales recogidas en `docs/verificacion_return_path.md`, donde se han validado:
- El comportamiento de los botones «Volver» y «Guardar» desde la ficha de cliente (Pruebas 1 y 2).
- La recarga del formulario con errores sin perder el `return_to` ni el contexto (Prueba 3).
- La protección frente a intentos de open redirect utilizando `?return_to=http://google.com`, confirmando que el sistema realiza fallback seguro al listado `/admin/inmuebles` (Prueba 8).

Todas las pruebas han sido superadas y el entorno ha quedado limpio tras eliminar el script temporal de ayuda (`setup_tests.php`).
#### Navegación contextual en el alta de inmuebles (`return_to`)

- Implementado patrón de navegación contextual en el **alta de inmuebles**:
  - Si el alta se inicia desde la **ficha de un cliente** (`/admin/clientes/editar?id=X`), tras crear el inmueble la aplicación regresa automáticamente a esa ficha añadiendo `msg=created` a la URL.
  - Si el alta se inicia desde el **listado global de inmuebles** (`/admin/inmuebles`), se mantiene el flujo clásico: tras guardar se vuelve al listado con `msg=created`.
  - Se reutilizan los helpers existentes `validateReturnTo()` y `addQueryParam()` para:
    - Validar que `return_to` es siempre una ruta interna segura (evitando redirecciones abiertas).
    - Construir la query string sin romper parámetros previos, manteniendo URLs limpias y consistentes.

#### 5. Imagen principal de inmuebles (subida segura de archivos)

* **Nueva columna `imagen` en `inmuebles`:**  
  Se crea la migración `04_add_imagen_to_inmuebles.sql` para añadir la columna opcional `imagen VARCHAR(255) NULL` que almacena el nombre del archivo de la foto principal del inmueble.

* **Lógica de subida y validación en `InmuebleController`:**  
  * Se implementa el método privado `handleImageUpload()` para gestionar la subida de la imagen de forma segura.
  * Validaciones aplicadas:
    * Tamaño máximo: **2 MB**.
    * Tipo MIME real comprobado con `finfo_file()` (no se confía en `$_FILES['type']`).
    * Solo se aceptan: `image/jpeg`, `image/png`, `image/webp`, `image/gif`.
    * Verificación adicional con `getimagesize()` para asegurarse de que el archivo es una imagen válida.
    * Límite de dimensiones: **1920x1920 px** (se rechazan imágenes enormes tipo 4K).
  * Se genera un nombre de archivo único con el patrón: `inmueble_<uniqid>.ext`.
  * Se guarda el fichero en `/public/uploads/inmuebles`, creando el directorio si no existe y añadiendo un `.htaccess` que:
    * Desactiva la ejecución de PHP.
    * Deshabilita el listado de directorio.

* **Integración en alta, edición y borrado:**
  * En `store()`:
    * Si se sube una imagen válida, se procesa con `handleImageUpload()` y se guarda el nombre en `$data['imagen']`.
    * Si no se sube imagen, el campo queda en `NULL` (inmueble sin foto principal).
  * En `update()`:
    * Si se sube una nueva imagen válida, se guarda el nuevo archivo y se **borra del disco** la imagen anterior asociada al inmueble.
    * Si no se sube nueva imagen, se mantiene el valor actual de `imagen`.
  * En `delete()`:
    * Antes de eliminar el registro se comprueba si existe `imagen` y, en caso afirmativo, se elimina el archivo correspondiente del directorio `/public/uploads/inmuebles`.

* **Cambios en vistas del backoffice:**
  * `app/Views/admin/inmuebles/form.php`:
    * Se añade `enctype="multipart/form-data"` al `<form>`.
    * Se incorpora un campo `input type="file" name="imagen"` con texto de ayuda sobre formatos, tamaño y dimensiones.
    * En modo edición, se muestra una **miniatura de la imagen actual** y se informa de que subir una nueva la reemplazará.
    * Se integran los mensajes de error del campo `imagen` en el sistema de validación ya existente.
  * `app/Views/admin/inmuebles/index.php`:
    * Se añade una columna **“Imagen”** al listado.
    * Si el inmueble tiene imagen, se muestra una miniatura de **60x60px** con `object-fit: cover`.
    * Si no tiene imagen, se muestra un placeholder (`/assets/img/no-image.png`) con opacidad reducida.

* **Pruebas manuales realizadas:**
  * Alta de inmueble **con imagen válida** → miniatura visible en listado, archivo guardado en `/public/uploads/inmuebles` y nombre registrado en BD.
  * Alta de inmueble **sin imagen** → alta correcta, miniatura sustituida por placeholder y columna `imagen` en `NULL`.
  * Edición sin cambiar imagen → solo se actualizan los campos editados, se mantiene el mismo archivo.
  * Edición cambiando imagen → la nueva imagen se guarda y la anterior se borra del disco.
  * Borrado de inmueble con imagen → se elimina el registro y también el archivo físico.
  * Casos de error (tipo no permitido, tamaño > 2 MB o dimensiones excesivas) → el formulario muestra un mensaje de “Error al procesar la imagen” y no se crea/actualiza el inmueble.

* **Incidencia menor (PHP 8.5):**
  * Durante las pruebas apareció un aviso `Deprecated: Function finfo_close() is deprecated since 8.5`.  
    Se ajustó el código eliminando la llamada explícita a `finfo_close()`, ya que los objetos `finfo` se liberan automáticamente en versiones recientes de PHP.
## ✅ 2025-12-08 (Módulo Demandas: implementación completa y control por rol)

**Tema:** Módulo de Demandas (búsquedas de compra/alquiler por cliente)  
**Tipo de avance:** Backend / UI / Reglas de negocio

### 🚀 Resumen del día

He implementado el módulo completo de **Demandas**, que permite registrar qué busca cada cliente (tipo de operación, presupuesto, superficie, zonas y características deseadas).  
El módulo está completamente integrado con **Clientes**, respeta los **roles** (admin, coordinador, comercial) y funciona tanto en local (`inmobiliaria.loc`) como en producción (`inmobiliaria.oswaldo.dev`) usando rutas relativas.

---

### 1. Modelo de datos (`app/Models/Demanda.php`)

- Nuevo modelo `Demanda` mapeado a la tabla `demandas`:
  - `id_demanda`, `cliente_id`, `comercial_id`
  - `tipo_operacion` (compra, alquiler, vacacional)
  - `rango_precio_min`, `rango_precio_max`
  - `superficie_min`, `habitaciones_min`, `banos_min`
  - `zonas` (texto libre)
  - `caracteristicas` (JSON)
  - `estado` (activa, en_gestion, pausada, archivada)
  - `activo`, `archivado`, `fecha_alta`, `fecha_archivado`

- Conexión a BD reutilizando `Database::conectar()` para mantener consistencia con el resto de modelos.
- Métodos principales:
  - `paginateAdmin($userId, $rol, $filtros, $page, $perPage)`
  - `findById($id)`
  - `create($data)`
  - `update($id, $data)`
  - `delete($id)`
  - `getByCliente($clienteId)`

- Campo `caracteristicas`:
  - Al guardar: `json_encode($data['caracteristicas'] ?? [])`.
  - Al leer: siempre se decodifica a **array**, nunca `null`, para simplificar la lógica de las vistas.

---

### 2. Controlador (`app/Controllers/DemandaController.php`)

- Reescritura completa del controlador para implementar el CRUD:
  - `index()`: listado de demandas con filtros por tipo, estado, comercial y precio.
  - `create()`: muestra el formulario de alta (desde listado o desde ficha de cliente).
  - `store()`: valida y guarda una nueva demanda.
  - `edit()`: carga una demanda existente para edición.
  - `update()`: valida cambios y actualiza.
  - `delete()`: borrado de demandas (solo admin/coordinador).

- Reutilización de helpers ya existentes (copiados del controlador de Inmuebles):
  - `requireAuth()`, `requireRole()`
  - `currentUserId()`, `currentUserRole()`, `isAdminOrCoordinador()`
  - `csrfToken()`, `csrfValidate()`
  - `validateReturnTo()`, `addQueryParam()`
  - `ensurePost()` (para asegurar que ciertas acciones vayan siempre por POST).

- **Reglas de negocio por rol:**
  - Admin/Coordinador:
    - Ven **todas** las demandas.
    - Pueden filtrar por comercial.
    - Pueden crear/editar/borrar demandas de cualquier cliente.
  - Comercial:
    - Solo ve demandas de **sus clientes** (tabla `clientes.usuario_id`).
    - Solo puede crear/editar demandas asociadas a sus clientes.
    - No puede crear demandas para clientes de otros comerciales (se valida en servidor).

- Asignación de `comercial_id`:
  - Admin/Coordinador: se hereda del cliente (`cliente->usuario_id`).
  - Comercial: se fuerza siempre al `user_id` actual de sesión.

---

### 3. Vistas del módulo Demandas

#### `app/Views/admin/demandas/index.php`

- Listado paginado con columnas:
  - Cliente, Comercial, Tipo de operación
  - Rango de precio (mín–máx)
  - Superficie mínima, habitaciones, baños
  - Estado, fecha de alta
- Filtros en la parte superior:
  - Tipo de operación (compra / alquiler / vacacional)
  - Estado
  - Comercial (solo visible para admin/coordinador)
  - Rango de precio mínimo/máximo
- Botones de acción:
  - **Editar** demanda.
  - **Eliminar** demanda (solo admin/coordinador, vía POST + CSRF).

#### `app/Views/admin/demandas/form.php`

- Formulario reutilizable para crear y editar demandas:
  - Cliente:
    - Si se entra desde la ficha de cliente → campo de texto **readonly** + `cliente_id` oculto.
    - Si se entra desde el listado → `<select>` con clientes (filtrado según rol).
  - Tipo de operación (`select`).
  - Rango de precio (`rango_precio_min`, `rango_precio_max`):
    - `input type="number" step="1" min="0"`.
    - Cualquier decimal queda truncado a entero en servidor.
  - Superficie mínima, habitaciones mínimas, baños mínimos.
  - Zonas: textarea libre.
  - Características: checkboxes que se guardan en el JSON (`garaje`, `piscina`, `ascensor`, `terraza`, etc.).
  - Estado de la demanda (activa / en_gestion / pausada / archivada).
  - Campos ocultos: `csrf_token` y `return_to`.

- Manejo de errores:
  - Los errores de validación se muestran junto a cada campo (array `$errors`).
  - El formulario repinta los valores anteriores con `$old` para no perder el trabajo del usuario.

---

### 4. Integración con Clientes

#### `app/Controllers/ClienteController.php`

- Nueva propiedad `$demandaModel` inyectando `App\Models\Demanda`.
- En el método `edit()`:
  - Además de los inmuebles del cliente, se cargan sus demandas:  
    ` $demandasCliente = $this->demandaModel->getByCliente($id);`
  - Se pasan a la vista para mostrarlas en una tabla.

#### `app/Views/admin/clientes/edit.php`

- Botón "➕ Añadir demanda" corregido para incluir `return_to` y `cliente_id`.
- Nueva sección “Demandas de este cliente” con tabla:
  - Tipo, precio min–máx, superficie, estado, fecha, acciones.
  - Botón **Editar** que respeta el patrón `return_to` (se vuelve a la ficha de cliente tras guardar).

---

### 5. Rutas y navegación

- Rutas registradas en `public/index.php`:

  - `GET  /admin/demandas` → `DemandaController@index`
  - `GET  /admin/demandas/nueva` → `DemandaController@create`
  - `POST /admin/demandas/guardar` → `DemandaController@store`
  - `GET  /admin/demandas/editar` → `DemandaController@edit`
  - `POST /admin/demandas/actualizar` → `DemandaController@update`
  - `POST /admin/demandas/borrar` → `DemandaController@delete`

- En el `dashboard` se ha añadido un acceso directo al módulo de Demandas.

---

### 6. Estado final del módulo Demandas

- ✅ CRUD completo (alta, edición, listado, borrado).
- ✅ Control de acceso por rol (admin, coordinador, comercial).
- ✅ Integración con la ficha de cliente (tabla de demandas + botón “Añadir demanda”).
- ✅ Validación de seguridad (CSRF, permisos por cliente, limpieza de datos).
- ✅ Campo JSON `caracteristicas` gestionado de forma transparente.
- ✅ Funciona tanto en local (`inmobiliaria.loc`) como en producción (`inmobiliaria.oswaldo.dev`).

## ✅ 08/12/2025 (Módulo Demandas: CRM de necesidades de clientes)

**Tema:** Implementación completa del módulo de Demandas (peticiones de compra/alquiler) con control de roles e integración en la ficha de cliente.  
**Tipo de avance:** Backend / CRM / UX

### 🚀 Resumen del día

Se ha desarrollado el módulo **Demandas**, que permite registrar y gestionar las necesidades de búsqueda de inmuebles de cada cliente (tipo de operación, rango de precio, superficie mínima, habitaciones, zonas y características extra como garaje o piscina).  

El módulo respeta la arquitectura MVC existente, se integra con la ficha de cliente y aplica control estricto por rol:  
- **Admin / Coordinador:** ven y gestionan todas las demandas.  
- **Comercial:** solo puede ver y crear demandas de los **clientes que tiene asignados**.

Además, se ha unificado el flujo de navegación con el patrón `return_to`, permitiendo ir y volver de la ficha del cliente sin “perderse” por el backoffice.

### 🔧 Cambios realizados

#### 1. Modelo `Demanda` (app/Models/Demanda.php)

- Nuevo modelo que mapea la tabla `demandas` (16 campos principales: `cliente_id`, `comercial_id`, `tipo_operacion`, `rango_precio_min`, `rango_precio_max`, `superficie_min`, `habitaciones_min`, `banos_min`, `zonas`, `caracteristicas`, `estado`, flags `activo/archivado`, fechas, etc.).
- Conexión a BD centralizada vía `Database::conectar()`.
- Métodos implementados:
  - `paginateAdmin(int $userId, string $rol, array $filtros, int $page, int $perPage)`: listado con paginación y filtro por rol (comercial solo ve demandas de sus clientes).
  - `findById(int $id)`: obtención de una demanda concreta.
  - `getByCliente(int $clienteId)`: listado de demandas asociadas a un cliente.
  - `create(array $data)`: alta de demanda.
  - `update(int $id, array $data)`: actualización de demanda.
  - `delete(int $id)`: borrado definitivo, respetando las FKs `ON DELETE CASCADE`.
- Campo JSON `caracteristicas`:
  - **Al guardar:** se serializa como `json_encode(array)` (nunca `NULL`).
  - **Al leer:** se deserializa siempre a `array` (`[]` por defecto), evitando avisos en PHP.

#### 2. Controlador `DemandaController` (app/Controllers/DemandaController.php)

- Reescritura completa del controlador con los métodos:
  - `index()`: listado con filtros (tipo de operación, estado, comercial, rango de precio) y paginación.
  - `create()`: muestra formulario de alta, soportando `cliente_id` + `return_to` al venir desde la ficha de cliente.
  - `store()`: alta con validación de datos, permisos por rol y protección CSRF.
  - `edit($id)`: carga de demanda existente, controlando que el comercial solo edite demandas de sus clientes.
  - `update($id)`: actualización con las mismas reglas de validación y permisos que `store()`.
  - `delete($id)`: borrado disponible solo para admin/coordinador (POST + CSRF).
- Helpers reutilizados/replicados siguiendo el patrón de `InmuebleController`:
  - `currentUserId()`, `currentUserRole()`, `isAdminOrCoordinador()`
  - `requireAuth()`, `requireRole()`, `ensurePost()`
  - `csrfToken()`, `csrfValidate()`
  - `validateReturnTo()`, `addQueryParam()`
- Lógica de negocio clave:
  - El `comercial_id` de la demanda **siempre se hereda del cliente** (`cliente.usuario_id`).
  - Un comercial **no puede** crear ni editar demandas de clientes que no le pertenecen.

#### 3. Vistas admin de Demandas (app/Views/admin/demandas)

- `index.php` (NUEVA):
  - Tabla con columnas: Cliente, Comercial, Tipo, Precio min–max, Superficie mín., Habitaciones mín., Estado, Fecha alta y Acciones.
  - Filtros por GET: tipo de operación, estado, comercial (solo visible para admin/coordinador) y rango de precio.
  - Botón **“+ Nueva demanda”** que lleva a `/admin/demandas/nueva`.
  - Paginación manteniendo filtros activos.
  - Mensajes de estado (`?msg=created|updated|deleted`).

- `form.php` (NUEVA, sustituyendo placeholder):
  - Soporta dos flujos:
    - Alta desde ficha de cliente: cliente en `readonly` + `cliente_id` oculto.
    - Alta desde listado global: `select` de cliente (filtrado por comercial si rol = comercial).
  - Campos:
    - Tipo de operación: `compra | alquiler | vacacional`.
    - Rango de precio: `rango_precio_min` / `rango_precio_max` (`step="1"`).
    - Criterios mínimos: `superficie_min`, `habitaciones_min`, `banos_min`.
    - Zonas: `textarea`.
    - Características (checkboxes → JSON): garaje, piscina, ascensor, terraza, amueblado, trastero, jardín, etc.
    - Estado: `activa | en_gestion | pausada | archivada`.
  - Campos ocultos:
    - `csrf_token`
    - `return_to`
    - `id` (en edición).
  - Gestión de errores y `old()` para repintar el formulario cuando hay validaciones fallidas.

#### 4. Integración con Clientes

- `app/Controllers/ClienteController.php`:
  - Se inyecta el modelo `Demanda` (`$this->demandaModel = new Demanda();`).
  - En `edit()` se cargan las demandas del cliente: `$demandasCliente = $this->demandaModel->getByCliente($id);`.

- `app/Views/admin/clientes/edit.php`:
  - Sección nueva **“Demandas de este cliente”** con tabla resumen.
  - Botón **“➕ Añadir demanda”** que pasa `cliente_id` y `return_to=/admin/clientes/editar?id=...`.
  - En la tabla cada fila incluye enlace “Editar” que respeta `return_to` para volver a la ficha del cliente tras guardar.

#### 5. Rutas y navegación

- `public/index.php`:
  - Registro de rutas del módulo:
    - GET  `/admin/demandas`
    - GET  `/admin/demandas/nueva`
    - POST `/admin/demandas/guardar`
    - GET  `/admin/demandas/editar`
    - POST `/admin/demandas/actualizar`
    - POST `/admin/demandas/borrar`
- `app/Views/admin/dashboard.php`:
  - Añadido acceso directo a **Demandas** junto a otros módulos del backoffice.

### 📝 Archivos clave creados/modificados

- **Modelos**
  - `app/Models/Demanda.php` (NUEVO)

- **Controladores**
  - `app/Controllers/DemandaController.php` (REESCRITO)
  - `app/Controllers/ClienteController.php` (MODIFICADO – integración de demandas)

- **Vistas**
  - `app/Views/admin/demandas/index.php` (NUEVA)
  - `app/Views/admin/demandas/form.php` (NUEVA)
  - `app/Views/admin/clientes/edit.php` (MODIFICADA – sección de demandas + botón de alta)

- **Core / Routing / Navegación**
  - `public/index.php` (MODIFICADO – rutas de demandas)
  - `app/Views/admin/dashboard.php` (MODIFICADO – acceso desde panel)

### ✅ Estado Final

El módulo **Demandas** queda **operativo y alineado con el resto del CRM**:

- Control de permisos coherente con el rol del usuario.
- Flujo natural desde la ficha del cliente.
- Datos estructurados y consistentes (incluyendo características en JSON).
- Preparado para futuros cruces automáticos `demandas ↔ inmuebles`.

## 🗓️ 2025-12-08 (Seguridad por roles en el módulo de Inmuebles)

**Tema:** Hardening de permisos en el CRUD de inmuebles  
**Tipo de avance:** Backend / Seguridad de roles

### 🚀 Resumen

Se ha corregido un fallo crítico de seguridad: un comercial podía ver y editar inmuebles de otros comerciales e incluso “quedárselos” cambiando el propietario desde el formulario o manipulando la URL.  
Ahora cada comercial solo puede trabajar con los inmuebles de **su propia cartera de clientes**.

### 🔧 Cambios realizados

- **Modelo `Inmueble`**
  - `paginateAdmin()` ahora recibe también `userId` y `rol`.
  - Para roles `admin`/`coordinador` devuelve todos los inmuebles.
  - Para rol `comercial` añade un JOIN con `clientes` y filtra por `clientes.usuario_id = :userId`, de forma que solo se paginan inmuebles de sus clientes.

- **`InmuebleController`**
  - `index()` pasa al modelo el `userId` y el `rol` actual para que la paginación ya venga filtrada.
  - `create()/store()`:
    - Admin/Coordinador pueden seleccionar cualquier propietario.
    - El comercial solo ve en el `<select>` clientes de su cartera.
    - En servidor se valida que el `cliente_id` pertenece al comercial; si no, se devuelve error de permisos.
  - `edit()/update()`:
    - Solo permite editar inmuebles cuyo propietario (`clientes.usuario_id`) coincide con el `userId` del comercial.
    - Si intenta cambiar el propietario a un cliente de otro comercial, se cancela la operación (403 / mensaje de error).

### ✅ Archivos clave tocados

- `app/Models/Inmueble.php`
- `app/Controllers/InmuebleController.php`

### 🧪 Pruebas realizadas

- Como **comercial**:
  - Listado `/admin/inmuebles` solo muestra inmuebles de sus clientes.
  - Acceso directo por URL a un inmueble de otro comercial → bloqueado.
  - Intento de crear/editar inmueble para cliente ajeno → error de permisos.
- Como **admin/coordinador**:
  - Sigue viendo y gestionando todos los inmuebles sin restricciones.
### 7. Hotfix de visibilidad en listado de Demandas (08/12/2025)

- Se detectó que los usuarios con rol **comercial** no veían ninguna demanda en `/admin/demandas`, incluso teniendo clientes con demandas creadas.
- **Causa técnica:** en `Demanda::paginateAdmin()` el filtro del JOIN usaba la columna `c.comercial_id`, que no corresponde con el esquema actual, en lugar de `c.usuario_id` (FK real que enlaza clientes con su comercial).
- **Solución aplicada:** se actualizó el JOIN para filtrar por `c.usuario_id = :userId` cuando el rol es `comercial`, manteniendo el comportamiento esperado:
  - **Admin / Coordinador:** siguen viendo todas las demandas.
  - **Comercial:** ve únicamente las demandas de los clientes que tiene asignados.
- **Impacto:** corrección puntual y acotada al modelo `Demanda`; no se han tocado controladores ni vistas. Se valida que el control de roles descrito en esta sección se cumple también en el listado global de demandas.

Tema: Visibilidad de botones y permisos de borrado para comerciales
Tipo de avance: Frontend / Backend / Permisos

🐛 Problemas detectados
UI: En el listado /admin/demandas, los botones de "Editar" y "Borrar" mostraban solo iconos sin texto visible, dificultando su reconocimiento.
Permisos: Los comerciales no podían eliminar demandas de sus propios clientes, aunque sí podían editarlas. Esta restricción era innecesariamente estricta y no había riesgo de conflicto con la estructura de BD (la FK cliente_id con ON DELETE CASCADE gestiona la integridad correctamente).
🔧 Solución aplicada
1. Vista app/Views/admin/demandas/index.php
Añadido texto visible a los botones de acción:
Antes: <i class="bi bi-pencil"></i> (solo icono)
Ahora: <i class="bi bi-pencil"></i> Editar (icono + texto)
Antes: <i class="bi bi-trash"></i> (solo icono)
Ahora: <i class="bi bi-trash"></i> Borrar (icono + texto)
Eliminada restricción de rol para mostrar el botón de borrado (ahora visible para todos los roles, pero validado en servidor).
2. Controlador app/Controllers/DemandaController.php (método delete())
Antes: Solo admin y coordinador podían borrar (requireRole).
Ahora: Comerciales pueden borrar demandas de sus propios clientes con validación: php // Se carga la demanda y se verifica el propietario if (!$this->isAdminOrCoordinador($rol)) {     $cliente = $this->clientes->findById((int)$demanda->cliente_id);     if (!$cliente || (int)$cliente->usuario_id !== $userId) {         $this->redirect('/admin/demandas?error=forbidden');     } } 
✅ Resultado
Todos los roles: Ven claramente los textos "Editar" y "Borrar" en los botones de acción.
Admin/Coordinador: Pueden borrar cualquier demanda (sin cambios).
Comercial: Ahora pueden borrar demandas de sus clientes asignados, pero no de clientes ajenos (validación en servidor).
📝 Archivos modificados
app/Views/admin/demandas/index.php
app/Controllers/DemandaController.php

**Tema:** Unificación visual y mejoras de navegación
**Tipo de avance:** Frontend / UX

### 🐛 Problemas detectados
- La vista principal de inmuebles (`/admin/inmuebles`) carecía de estilos CSS del proyecto, mostrándose como una tabla HTML básica sin estructura.
- Faltaban opciones de navegación claras para retornar al Panel de Control (Dashboard).

### 🔧 Solución aplicada
- **Rediseño completo de `admin/inmuebles/index.php`**:
  - Implementación de estructura Bootstrap (Container, Cards, Badges).
  - Integración con el layout principal (`header.php` / `footer.php`).
  - Estilización de filtros y tabla de datos.
- **Navegación mejorada**:
  - Añadido botón "Mi Panel" en la cabecera del listado.
  - Mejorada la disposición de los botones de acción (Nuevo, Filtrar, Limpiar).

### ✅ Resultado
- El módulo de inmuebles ahora mantiene la coherencia visual con el resto de la aplicación (Clientes, Demandas, etc.).
- Navegación más fluida entre el listado y el dashboard.

### 📝 Archivos modificados
- `app/Views/admin/inmuebles/index.php`

## ✅ 2025-12-09 (FASE 1: Sistema de Envío de Correos Electrónicos)

**Tema:** Implementación de Sistema de Correos con PHPMailer y Templates HTML  
**Tipo de avance:** Backend / Email / Templates / Seguridad

### 🚀 Resumen del día

Se ha implementado completamente el sistema de envío de correos electrónicos para el módulo de tasaciones, migrando de una implementación básica (`SimpleSMTP`) a una solución robusta basada en **PHPMailer** con plantillas HTML reutilizables.

---

### 1. Análisis y Decisión Técnica

- **Revisión de código existente:**
  - Análisis de `SimpleSMTP.php` (implementación custom sin soporte TLS/SSL).
  - Análisis de `TasacionController.php` (HTML hardcodeado en controlador).
  - Identificación de 7 problemas principales (falta TLS, manejo limitado de errores, sin soporte adjuntos, etc.).

- **Decisión técnica:**
  - **Opción seleccionada:** Migrar a PHPMailer sin usar Composer.
  - **Razón:** Balance óptimo entre seguridad, robustez y facilidad de integración manual.
  - Documento técnico completo generado: `decision_tecnica_email.md`.

---

### 2. Integración Manual de PHPMailer

- **Descarga e instalación:**
  - PHPMailer v6.9.2 descargado desde GitHub oficial.
  - Archivos copiados a `app/Lib/PHPMailer/`:
    - `PHPMailer.php` (clase principal)
    - `SMTP.php` (cliente SMTP)
    - `Exception.php` (excepciones)
    - 4 archivos adicionales (DSNConfigurator, OAuth, OAuthTokenProvider, POP3)

- **Integración manual (sin Composer):**
  - Uso de `require_once` directo en `MailService`.
  - No se añadió `composer.json` ni `vendor/`.
  - Mantiene compatibilidad con arquitectura existente.

---

### 3. Creación de MailService

**Archivo:** `app/Services/MailService.php`

- **Funcionalidad principal:**
  - Servicio centralizado para envío de correos usando PHPMailer.
  - Método estático `send(, , )`.
  - Soporte para:
    - HTML directo (`body`)
    - Plantillas desde `app/Views/emails/` (`template` + `data`)
    - Adjuntos (`attachments`)
    - Reply-To personalizado
    - Remitente configurable

- **Características implementadas:**
  - Configuración SMTP desde `Config::get('smtp')`.
  - Soporte TLS/SSL nativo vía PHPMailer.
  - Logging automático en `logs/mail.log`.
  - Manejo robusto de errores con excepciones.
  - Renderizado de plantillas con `ob_start/ob_get_clean`.
  - Aplicación automática de layout (`emails/layout.php`).
  - Debug mode para desarrollo (`SMTPDebug = 2` si `app.debug = true`).

---

### 4. Plantillas de Email HTML

**Ubicación:** `app/Views/emails/`

#### `layout.php` (Plantilla base)
  - Header con logo y branding corporativo.
  - Footer con datos de contacto.
  - Estilos inline para compatibilidad con clientes de correo.
  - Diseño responsive (mobile-first).
  - Variables: `` (contenido), `` (asunto).

#### `tasacion_cliente.php` (Email al cliente)
  - Saludo personalizado.
  - Rango de valoración destacado visualmente (verde).
  - Detalles del inmueble (ubicación, superficie, características).
  - Próximos pasos y expectativas.
  - Variables: `precio_min`, `precio_max`, `barrio`, `zona`, `cp`, `superficie`, `caracteristicas`.

#### `tasacion_agencia.php` (Email interno para agencia)
  - Alerta visual de nuevo lead.
  - Timestamp de recepción.
  - Datos completos del cliente (email + teléfono con enlaces click-to-action).
  - Datos del inmueble.
  - Valoración estimada destacada.
  - Call-to-action para contactar rápidamente.
  - Variables: todas las anteriores + `fecha`, `email_cliente`, `telefono`.

---

### 5. Refactorización de TasacionController

**Archivo:** `app/Controllers/TasacionController.php`

- **Cambios realizados:**
  - **Eliminado:** 113 líneas de HTML hardcodeado.
  - **Eliminado:** Import de `SimpleSMTP`.
  - **Añadido:** Import de `MailService`.
  - **Simplificado:** Método `enviar()`:
    - De ~250 líneas a ~200 líneas.
    - HTML movido a plantillas separadas.
    - Headers manuales eliminados (PHPMailer los gestiona).

- **Código antes vs después:**

  Antes:
  `php
   = new SimpleSMTP(System.Management.Automation.Internal.Host.InternalHost, , , );
   = "<html>... (50 líneas de HTML) ...</html>";
   = "MIME-Version: 1.0\r\nContent-type:text/html...";
  ->send(, , , );
  `

  Después:
  `php
  MailService::send(, , [
      'template' => 'tasacion_cliente',
      'data' => 
  ]);
  `

---

### 6. Configuración y Variables de Entorno

**Archivos modificados:**
  - `config/config.php`: Añadida variable `smtp.secure` (tls/ssl/none).
  - `config/config.php`: Añadida variable `app.name` para nombre del remitente.

**Nuevo archivo:** `.env.example`
  - Plantilla completa de configuración SMTP.
  - Ejemplos para Gmail, Outlook, cPanel, SendGrid.
  - Comentarios detallados de ayuda.
  - Notas de seguridad y mejores prácticas.
  - Variables: `SMTP_HOST`, `SMTP_PORT`, `SMTP_SECURE`, `SMTP_USER`, `SMTP_PASS`, `LEAD_AGENCY_EMAIL`, `NOREPLY_EMAIL`.

---

### 7. Herramienta de Testing

**Archivo:** `public/test/email.php`

  - Interfaz web para pruebas de envío.
  - Formulario simple con input de email destino.
  - Envía email de prueba usando plantilla `tasacion_cliente`.
  - Muestra éxito/error visualmente.
  - **Acceso:** `/test/email.php` (solo desarrollo).

---

### 8. Mejoras de Seguridad

- **Configuración SMTP segura:**
  - Soporte nativo TLS/SSL.
  - Credenciales desde `.env` (nunca hardcodeadas).
  - Validación de tipos MIME en PHPMailer.
  - Sanitización automática de headers.

- **Logging:**
  - Registro de todos los envíos en `logs/mail.log`.
  - Timestamps precisos.
  - Niveles de log (info/error/debug).
  - No se logean credenciales sensibles.

---

### 9. Separación de Responsabilidades (Clean Code)

Antes:
  - **1 archivo** (TasacionController) con TODO el código de emails.
  - HTML, lógica de envío y configuración SMTP mezclados.

Después:
  - **MailService:** Lógica de envío y configuración.
  - **Templates:** Presentación HTML (layout + 2 plantillas específicas).
  - **Controlador:** Solo orquestación y paso de datos.
  - **Config:** Variables de entorno separadas.

Beneficios:
  - ✅ Reutilización de plantillas en otros módulos.
  - ✅ Testing más fácil (MailService aislado).
  - ✅ Mantenimiento simplificado.
  - ✅ Escalabilidad (nuevas plantillas sin tocar controlador).

---

### 📝 Archivos clave creados

- `app/Lib/PHPMailer/*` (7 archivos de librería)
- `app/Services/MailService.php`
- `app/Views/emails/layout.php`
- `app/Views/emails/tasacion_cliente.php`
- `app/Views/emails/tasacion_agencia.php`
- `.env.example`
- `public/test/email.php`

### �� Archivos clave modificados

- `app/Controllers/TasacionController.php` (-113 líneas)
- `config/config.php` (+4 líneas)

---

### ✅ Estado final

| Componente | Estado |
|-----------|--------|
| PHPMailer integrado (manual) | ✅ |
| MailService funcionando | ✅ |
| Plantillas HTML creadas | ✅ |
| TasacionController refactorizado | ✅ |
| Configuración SMTP flexible | ✅ |
| Logging implementado | ✅ |
| Herramienta de testing | ✅ |
| Documentación técnica | ✅ |

---

### 🎯 Próximos pasos (fuera de FASE 1)

- FASE 2: Sistema de cruces (matching demandas-inmuebles).
- Envío manual de inmuebles a clientes desde backoffice.
- Configuración de SPF/DKIM/DMARC en dominio de producción.
- Sistema de colas para envíos masivos (newsletters).


## [2025-12-09] Fase 1: Implementación de Sistema de Correos para Tasaciones (WIP)

### Objetivo
Sustituir la clase heredada SimpleSMTP (sin soporte SSL/TLS seguro) por una solución robusta (PHPMailer) para gestionar el envío de correos tras una tasación online.

### Tareas Realizadas
1.  **Análisis Técnico**: Se evaluó SimpleSMTP vs PHPMailer. Se decidió usar PHPMailer por seguridad, soporte de comunidad y manejo de layouts HTML.
2.  **Integración Manual**: Se integró PHPMailer v6.9.2 descargando y copiando los archivos fuente a pp/Lib/PHPMailer/ (evitando Composer por requerimiento del cliente).
3.  **MailService**: Se creó App\Services\MailService encargado de:
    *   Configurar conexión SMTP segura (TLS/SSL).
    *   Renderizar plantillas HTML (layout.php, 	asacion_cliente.php, 	asacion_agencia.php).
    *   Manejar excepciones y logging (logs/mail.log).
4.  **Refactorización de TasacionController**:
    *   Se eliminó código HTML hardcodeado.
    *   Se reemplazó la lógica de envío antigua por MailService::send().
5.  **Plantillas HTML**:
    *   Diseño profesional y responsive.
    *   Separación de lógica (controlador) y vista (templates).
6.  **Configuración**:
    *   Se implementó carga de variables de entorno desde config/.env.
    *   Soporte para cPanel y Gmail con App Passwords.

### Estado Actual (WIP)
- La funcionalidad está implementada a nivel de código.
- Se ha validado la activación de OpenSSL en el servidor local.
- Se ha validado la configuración SMTP contra cPanel (mail.oswaldo.dev).
- **Pendiente**: Resolución final de problemas de entregabilidad (los correos se envían según el log, pero no llegan a la bandeja de entrada, posible filtrado SPAM o configuración DNS). Se deja aparcado temporalmente para verificar en entorno de producción real o continuar más adelante.

### [2025-12-09] Corrección Bug Backend Tasación (Mail)
*   **Error detectado:** TypeError: strip_tags(): Argument #1 () must be of type string en TasacionController.
*   **Causa:** El payload JSON enviado por JavaScript contenía valores numéricos (int) para campos como superficie o 	elefono, y strip_tags requiere strings.
*   **Solución:** Se implementó casting explícito a (string) en todas las variables de entrada ($data[...]) antes de la sanitización.


### [2025-12-09] Nota Importante: Despliegue en cPanel
*   **Incidencia:** El editor de archivos 'moderno' de cPanel corrompe caracteres UTF-8 (como la 'ñ' de la contraseña SMTP) al guardar, convirtiéndolos en ''.
*   **Solución:** Utilizar siempre el **Legacy Editor** de cPanel o subir el archivo .env vía FTP para preservar la codificación correcta.



---

## 09/12/2025 - Implementación del listado público de propiedades

### Cambios realizados

1. **Rutas públicas**:
   - Cambiadas las rutas de `/inmuebles` a `/propiedades`
   - GET /propiedades → listado público con paginación
   - GET /propiedades/ver?id=ID → ficha pública del inmueble

2. **Controlador públic**:
   - Actualizado `InmueblePublicController`
   - Paginación ajustada a **10 inmuebles por página**
   - Método `show()` cambiado para usar parámetro `id` en lugar de `ref`
   - Validación de inmuebles activos (activo=1, estado='activo', archivado=0)

3. **Vistas públicas**:
   - Creado `app/views/propiedades/index.php`
     - Diseño de tarjetas con imagen, título, precio, ubicación
     - Características visibles: superficie (m²), habitaciones, baños
     - Filtros de búsqueda por localidad, tipo y operación
     - Paginación con anterior/siguiente y números de página
   - Creado `app/views/propiedades/show.php`
     - Ficha detallada con imagen principal
     - Información completa del inmueble
     - Sidebar sticky con precio y botones de contacto
     - Breadcrumb y botón 'Volver al listado'

4. **Navegación**:
   - Actualizado enlace 'Propiedades' en header para apuntar a `/propiedades`
   - Enlaces desde imagen y botón 'Más información' a ficha del inmueble
   - Botón 'Contactar' apuntando a `/tasacion`

### Archivos modificados
- `public/index.php` (rutas)
- `app/views/layouts/header.php` (menú)
- `app/Controllers/InmueblePublicController.php` (lógica y paginación)

### Archivos creados
- `app/views/propiedades/index.php` (listado público)
- `app/views/propiedades/show.php` (ficha pública)

### Resultado
Los usuarios pueden navegar públicamente al catálogo de propiedades desde el menú principal, filtrar inmuebles y acceder a fichas detalladas. Solo se muestran inmuebles activos y no archivados.

## 🗓️ 2025-12-09 (Front público de propiedades)

**Tema:** Catálogo público de inmuebles  
**Tipo de avance:** Frontend / Backend / UX

### 🚀 Resumen

Se ha implementado el **listado público de propiedades** y la **ficha de detalle** accesibles desde el menú principal, mostrando únicamente inmuebles activos y publicables. Con esto, la parte pública de la web ya ofrece un catálogo real de inmuebles basado en los datos del CRM.

### 🔧 Cambios realizados

1. **Rutas públicas**
   - Se han registrado las rutas:
     - `GET /propiedades` → listado de inmuebles.
     - `GET /propiedades/ver?id=ID` → ficha de inmueble.
   - El acceso es público (sin autenticación), pero respetando las reglas de visibilidad (`activo`, `archivado`, `estado`).

2. **Controlador**
   - Se ha creado/ajustado `InmueblePublicController` con:
     - `index()` → obtiene filtros, llama a `Inmueble::paginatePublic(...)` y pinta el listado.
     - `show()` → recupera el inmueble por `id_inmueble` y muestra la ficha si es publicable, o 404 en caso contrario.
   - Paginación configurada a **10 inmuebles por página**.

3. **Vistas**
   - `app/views/propiedades/index.php`:
     - Tarjetas con imagen, precio, superficie, habitaciones, baños y descripción corta.
     - Botones “Más información” (ficha) y “Contactar” (formularios de tasación/contacto).
     - Paginador con navegación entre páginas.
   - `app/views/propiedades/show.php`:
     - Ficha detallada con imagen grande, descripción completa y todos los datos públicos clave.
     - Sidebar con precio y botones de contacto.
     - Botón para volver al listado.

4. **Integración con el menú**
   - El enlace “Propiedades” del header ahora apunta a `/propiedades`, conectando la navegación principal con el catálogo real.

### 📝 Archivos clave creados/modificados

- `public/index.php` (rutas públicas `/propiedades` y `/propiedades/ver`)
- `app/Controllers/InmueblePublicController.php`
- `app/views/layouts/header.php` (enlace del menú a `/propiedades`)
- `app/views/propiedades/index.php`
- `app/views/propiedades/show.php`
- `docs/documentacion_inmuebles.md` (sección de front público actualizada)

### 💡 Justificación técnica para el tribunal

Se ha decidido concentrar la lógica de visibilidad (inmuebles activos/publicables) en el modelo y reutilizarla tanto para el backoffice como para el front público, evitando duplicar reglas de negocio.  
La paginación a 10 elementos por página y el diseño en tarjetas buscan un equilibrio entre rendimiento, legibilidad y experiencia de usuario, alineado con los portales inmobiliarios reales.


## ✅ 2025-12-11 (Campo teléfono en usuarios y vista pública de propiedades)

**Tema:** Implementación de campo teléfono en usuarios y visualización de contacto comercial/coordinador en vista pública de propiedades  
**Tipo de avance:** Backend / DB / Frontend / UX / Lógica de negocio

### 🚀 Resumen del día

Se ha implementado un sistema completo para gestionar números de teléfono de usuarios (admin, coordinador, comercial) y mostrar información de contacto (nombre, email, teléfono) en la vista pública de propiedades.

La lógica incluye un sistema de **fallback** inteligente: si el comercial asignado al inmueble no tiene teléfono, se utiliza el teléfono del coordinador general. Si el inmueble no tiene comercial asignado, se muestran todos los datos del coordinador.

### 1. Base de Datos - Migración

- **Archivo:** database/migrations/add_telefono_usuarios.sql
- **Cambio:** Se añadió la columna telefono VARCHAR(25) DEFAULT NULL a la tabla usuarios
- Campo opcional, no afecta a registros existentes

### 2. Backend - Modelos actualizados

- **Inmueble.php:** Métodos findById() y findByRef() incluyen ahora comercial_email y comercial_telefono
- **User.php:** Nuevo método getCoordinadorGeneral() para fallback, métodos create() y update() actualizados

### 3. Backend - Controladores

- **UserController.php:** Métodos store() y update() sanitizan y procesan el campo telefono
- **InmueblePublicController.php:** Implementada lógica de fallback:
  - Sin comercial → usa datos del coordinador
  - Con comercial sin teléfono → usa nombre y email del comercial, teléfono del coordinador
  - Con comercial con teléfono → usa todos los datos del comercial

### 4. Frontend - Formularios

- **create.php y edit.php:** Campo telefono añadido con tipo tel, placeholder y ayuda
- Campo opcional, se pre-rellena en modo edición

### 5. Frontend - Vista Pública

- **propiedades/show.php:** Sección de información adicional actualizada
- Muestra dinámicamente: nombre, email (mailto), teléfono (tel)
- Campos se ocultan si están vacíos

### 6. Decisiones de Implementación

- **Sin validación de formato:** Permite flexibilidad internacional
- **Campo opcional:** Sistema funciona sin teléfonos
- **Fallback inteligente:** Siempre hay contacto visible para visitantes

### 7. Archivos modificados

- database/migrations/add_telefono_usuarios.sql
- app/Models/Inmueble.php
- app/Models/User.php
- app/Controllers/UserController.php
- app/Controllers/InmueblePublicController.php
- app/views/admin/users/create.php
- app/views/admin/users/edit.php
- app/views/propiedades/show.php

### 8. Testing

✅ Migración ejecutada  
✅ Formularios probados  
✅ Vista pública verificada en todos los escenarios  
✅ Enlaces mailto/tel funcionando  

## ✅ 2025-12-11 (Formulario de Contacto Público y Automatización de Emails)

**Tema:** Implementación de formulario de contacto con validación, seguridad antispam y flujo de correos automatizado.
**Tipo de avance:** Backend / Frontend / Security / Email

### 🚀 Resumen del día

Se ha desarrollado e integrado el **Formulario de Contacto Público**, accesible globalmente (`/contacto`) y desde las fichas de inmuebles. El sistema gestiona consultas generales y solicitudes específicas de propiedades, garantizando la entrega de información a la agencia, al comercial responsable y una confirmación inmediata al cliente.

### 🔧 Características Implementadas

#### 1. Backend (`ContactController`)
- **Validación robusta:** Verificación en servidor de todos los campos (nombre, email, telefono, mensaje, privacidad).
- **Seguridad Antispam:**
  - **Honeypot:** Campo oculto para detectar bots.
  - **Rate Limiting:** Bloqueo de envíos múltiples desde la misma sesión (cooldown de 30s).
  - **CSRF:** Protección contra falsificación de solicitudes.
- **Logging:** Registro detallado de actividad en `storage/logs/contacto.log` (intentos de spam, errores SMTP, envíos exitosos).

#### 2. Sistema de Emails (`MailService`)
- **Flujo de 3 vías:**
  1. **Agencia:** Recibe aviso inmediato del lead (`contacto_agencia`).
  2. **Comercial:** Recibe copia (CC) si el inmueble tiene asignado un comercial.
  3. **Cliente:** Recibe auto-respuesta de confirmación (`contacto_cliente`).
- **Reutilización:** Se aprovechó la infraestructura de `MailService` existente, corrigiendo plantillas para evitar renderizado de código PHP crudo.

#### 3. Frontend y UX
- **Vistas:**
  - `contacto/form.php`: Formulario con feedback visual de errores y pre-rellenado de datos si viene de un inmueble.
  - `contacto/exito.php`: Página de agradecimiento con navegación de retorno.
- **Integración:** Botones "Contactar" en ficha de inmueble ahora redirigen a `/contacto?id_inmueble=XXX`.

### 🐛 Solución de Bugs (Hotfixes)
Durante la implementación se detectaron y resolvieron 3 incidencias críticas:
1. **Fatal Error `stdClass`:** El modelo devolvía objetos pero el controlador esperaba arrays. Se aplicó casting explícito `(array)`.
2. **Función indefinida `e()`:** Se sustituyó el helper `e()` (no existente en el core) por `htmlspecialchars()` nativo en las vistas.
3. **Renderizado de Email:** Se corrigieron las plantillas de email que imprimían código PHP (`require ...`) debido a tags de cierre incorrectos.

### 📝 Archivos clave creados/modificados
- `app/Controllers/ContactController.php`
- `app/Views/contacto/form.php`
- `app/Views/contacto/exito.php`
- `app/Views/emails/contacto_agencia.php`
- `app/Views/emails/contacto_cliente.php`
- `public/index.php` (Rutas)

### 📝 Verificación de Logs
- Verificado el archivo `storage/logs/contacto.log`.
- El log registra:
  - Timestamp + IP del usuario.
  - Estados: FORM_OK, VALIDATION_ERROR, EMAIL_SENT, AUTO_REPLY_SENT, SMTP_ERROR (si ocurre).
  - Datos básicos de contexto (email, teléfono, id_inmueble si aplica).
- No se han realizado cambios en la lógica del formulario, solo comprobación de trazabilidad.

### TODO futuro
- Revisar lógica del teléfono mostrado en ficha pública:
  - Actualmente, si el comercial no tiene teléfono se muestra el del coordinador.
  - Pendiente decidir si:
    - ocultar el teléfono en ese caso, o
    - mostrar un texto tipo "Llámanos al teléfono de oficina" en lugar de un móvil personal.
### 2025-12-11 – Sincronización BBDD local → producción
- Se detectó un comportamiento diferente entre local y servidor en el teléfono del coordinador.
- En lugar de parchear campo a campo, se borró la BBDD de producción y se volcó una copia completa de la BBDD local.
- Resultado: estructura y datos totalmente alineados; el fallback de teléfono (coordinador) funciona correctamente.

---

## ✅ 2025-12-14

**Tema:** Carrusel de Propiedades Destacadas en Landing (Home)
**Tipo de avance:** Frontend + Backend (UX)

### 🚀 Resumen
Se ha implementado un carrusel de "Propiedades Destacadas" en la página principal (`/`) para mejorar la UX y mostrar una selección dinámica de la cartera de inmuebles. El objetivo era lograr esto sin modificar la estructura de la base de datos (sin columna "destacado"), utilizando una lógica de selección pseudo-aleatoria consistente.

### 🔧 Cambios Realizados

#### 1. Lógica de Selección (Backend)
- **Método `Inmueble::getHomeCarousel()`:** Recupera hasta 6 inmuebles que cumplen:
  - `activo = 1` (único criterio de publicación tras simplificación).
- **Aleatoriedad Estable:** Se utiliza `ORDER BY RAND(TO_DAYS(CURDATE()))` para que la selección de inmuebles varíe cada día pero se mantenga estable durante las 24 horas, evitando que el slider cambie en cada recarga de página (sensación de sitio más sólido).
- **Límite Seguro:** Parámetro `$limit` restringido internamente entre 1 y 12.

#### 2. Implementación Frontend (Vanilla)
- **CSS Moderno:** Uso de `display: flex`, `overflow-x: auto` y `scroll-snap-type: x mandatory` para un carrusel nativo, ligero y responsive sin dependencias JS pesadas.
- **JavaScript UI:** Script vanilla para gestionar la visibilidad de los botones "Anterior/Siguiente" (ocultarlos si no hay scroll) y permitir navegación por clic además del swipe táctil nativo.
- **Navegación Intuitiva:** Se han incorporado flechas visuales (`bi-arrow-left/right`) acordes al estilo del sitio, con lógica de auto-ocultado (smart auto-hide) cuando se alcanza el inicio o el final del carrusel.
- **Card Reutilizable:** Creación de `partials/inmueble_card.php` para estandarizar la visualización de tarjetas de inmueble en toda la web (home, listados, relacionados).

### 🐛 Problemas Encontrados y Resolución

1.  **Fatal Error `stdClass`:** El método `fetchObject()` devolvía objetos `stdClass`, pero la vista iteraba esperando arrays.
    *   **Solución:** Se forzó `fetchAll(\PDO::FETCH_ASSOC)` en el modelo para garantizar consistencia de tipos.

2.  **Criterios de Publicación Confusos:** Inicialmente se requerían 3 flags (`estado='activo'`, `activo=1`, `archivado=0`), lo que dejaba el carrusel vacío porque pocos inmuebles cumplían todo.
    *   **Solución:** Se simplificó la lógica de negocio pública para depender **únicamente** de `activo = 1`, alineando el comportamiento con la expectativa del usuario gestor.

3.  **Layout CSS Colapsado:** Los items del carrusel se montaban o no respetaban el ancho.
    *   **Solución:** Se aplicó una estrategia CSS robusta con `white-space: nowrap` en el contenedor y `display: inline-block` en los items, asegurando la visualización horizontal correcta en todos los navegadores.

### 📝 Archivos clave creados/modificados
- `app/Models/Inmueble.php` (Método `getHomeCarousel`)
- `app/Controllers/HomeController.php`
- `app/Views/home.php`
- `app/Views/partials/inmueble_card.php` (Nuevo partial)

### 🔮 Roadmap
- Futuro: añadir columna real `destacado` en BBDD para selección manual desde admin.
- Futuro: permitir ordenar manualmente las destacadas.

---

## ✅ 2025-12-15 (Hotfix – Subida de Foto de Perfil >2MB)

**Tema:** Corrección de error fatal al subir imágenes demasiado pesadas en edición de usuario.  
**Tipo de avance:** Backend / Seguridad / UX

### 🐛 Problema detectado
Al subir una imagen de perfil por encima del límite (≈2MB), se producía una excepción no controlada que terminaba en **fatal error**, mostrando **rutas internas** del servidor en pantalla en lugar de un mensaje amigable.

### ✅ Solución aplicada
- Se robusteció el flujo de subida de archivos para **capturar correctamente errores de PHP** (`UPLOAD_ERR_INI_SIZE`, `UPLOAD_ERR_FORM_SIZE`, etc.).
- Se ajustó el manejo de excepciones para que el error se muestre **inline en el formulario** (alert Bootstrap), sin redirecciones que pierdan el contexto.
- Se asegura la **preservación de datos** del formulario (nombre/email/teléfono/rol) cuando hay error.
- Se mantiene la validación de seguridad: **tamaño máximo** y **MIME real** (JPG/PNG/WEBP).

### 🧪 Pruebas realizadas (manual)
✅ Subida de imagen >2MB → muestra “La imagen es demasiado pesada. Máximo 2MB.”  
✅ Formatos no permitidos → error controlado en UI  
✅ Subida válida → actualiza correctamente  
✅ Sin fuga de paths / sin fatal error

### 📝 Archivos modificados
- `app/Controllers/UserController.php`

