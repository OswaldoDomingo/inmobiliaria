Nombre del alumno: Oswaldo Domingo
Ciclo: Desarrollo de Aplicaciones Web (DAW)
Memoria del Proyecto de DAW
IES Abastos. Curso 2024/25. Grupo 2DAW. 29 de Noviembre de 2025
Tutor individual: [Nombre del Tutor]

---

# ÍNDICE

1. [Identificación, justificación y objetivos del proyecto](#1-identificación-justificación-y-objetivos-del-proyecto)
2. [Diseño del proyecto](#2-diseño-del-proyecto)
3. [Desarrollo del proyecto](#3-desarrollo-del-proyecto)
4. [Evaluación y conclusiones finales](#4-evaluación-y-conclusiones-finales)
5. [Referencias](#5-referencias)

---

# 1. Identificación, justificación y objetivos del proyecto

## 1.1. Identificación
**Título del Proyecto:** Plataforma de Gestión Inmobiliaria y Tasación Online.
**Alumno:** Oswaldo Domingo.
**Ciclo Formativo:** Desarrollo de Aplicaciones Web (DAW).

## 1.2. Justificación
El sector inmobiliario demanda herramientas digitales ágiles que permitan no solo la visualización de propiedades, sino también la captación de leads cualificados y la gestión eficiente de los mismos. Este proyecto nace de la necesidad de modernizar los procesos de una agencia inmobiliaria tradicional, integrando una herramienta de tasación online automatizada y un panel de administración robusto para la gestión de usuarios y propiedades.

## 1.3. Objetivos
*   **Objetivo General:** Desarrollar una aplicación web completa (Full Stack) que permita la gestión integral de una inmobiliaria.
*   **Objetivos Específicos:**
    *   Implementar una arquitectura MVC sólida y segura en PHP sin frameworks pesados.
    *   Desarrollar una herramienta de tasación online que genere valor inmediato al usuario final y capture leads para la agencia.
    *   Crear un sistema de autenticación y autorización basado en roles (Admin, Coordinador, Comercial).
    *   Asegurar la aplicación contra vulnerabilidades comunes (XSS, SQL Injection, CSRF).
    *   Implementar un sistema de despliegue y configuración basado en variables de entorno.

---

# 2. Diseño del proyecto

## 2.1. Arquitectura del Sistema
Se ha optado por una arquitectura **Modelo-Vista-Controlador (MVC)** personalizada, favoreciendo el control total sobre el flujo de la aplicación y el rendimiento.

*   **Frontend:** HTML5, CSS3 (con Tailwind CSS y Bootstrap 5), JavaScript (Vanilla).
*   **Backend:** PHP 8.2+ (Estricto tipado).
*   **Base de Datos:** MySQL / MariaDB.
*   **Servidor Web:** Apache 2.4.

## 2.2. Estructura de Directorios
El proyecto sigue una estructura segura donde solo el directorio `public/` es accesible desde la web:

```
/
├── app/                # Núcleo de la aplicación
│   ├── Controllers/    # Lógica de control
│   ├── Models/         # Acceso a datos
│   ├── Views/          # Plantillas HTML
│   └── Core/           # Router, Database, Config
├── config/             # Configuración del entorno
├── public/             # Entry point (index.php) y assets
└── docs/               # Documentación
```

## 2.3. Diseño de Base de Datos
El esquema relacional se ha diseñado para garantizar la integridad de los datos.
*   **Tabla `usuarios`:** Gestión de acceso y roles. Incluye campos de auditoría (`created_at`, `updated_at`) y seguridad (`intentos_fallidos`, `cuenta_bloqueada`).
*   **Soft Deletes:** Se implementa el borrado lógico mediante columnas `activo` y `archivado` para preservar el histórico de datos.

---

# 3. Desarrollo del proyecto

## 3.1. Configuración del Entorno
Se ha implementado un sistema de carga de variables de entorno (`.env`) para separar la configuración sensible (credenciales de BD, SMTP) del código fuente.

Dado que no se utilizan frameworks ni gestores de dependencias como Composer, he desarrollado una solución propia y nativa:
*   **Clase `App\Core\Env`:** Un parser ligero que lee el archivo `.env` línea por línea, procesa las claves y valores, y los inyecta tanto en `$_ENV` como en el entorno del sistema mediante `putenv()`.
*   **Agnosticismo:** El archivo `config.php` no contiene credenciales "hardcoded", sino que depende exclusivamente de la existencia de estas variables, garantizando que el código sea seguro y portable entre entornos (local/producción).

## 3.2. Núcleo (Core)
*   **Router:** Se desarrolló un enrutador personalizado que despacha las peticiones a los controladores correspondientes basándose en la URI.
*   **Database:** Clase Singleton que gestiona la conexión PDO, configurada para lanzar excepciones (`PDOException`) en caso de error, facilitando el manejo global de fallos.

## 3.3. Módulos Implementados

### 3.3.1. Autenticación y Seguridad
*   **Login Seguro:** Verificación de hash de contraseñas (`password_verify`).
*   **Gestión de Sesiones:** Regeneración de ID de sesión tras login para prevenir fijación de sesión. Cookies con flags `HttpOnly` y `Secure`.
*   **Control de Acceso (RBAC):** Middleware en los constructores de los controladores para restringir el acceso según el rol del usuario.
*   **Seguridad del Servidor (.htaccess):** Implementación de reglas de reescritura en Apache para bloquear estrictamente el acceso público a archivos de configuración y control de versiones (como `.env`, `.git`, `.htaccess`), devolviendo un error 403 Forbidden ante cualquier intento de lectura externa.

### 3.3.2. Herramienta de Tasación
*   Formulario interactivo para la valoración de inmuebles.
*   Envío de correos electrónicos transaccionales (al cliente y a la agencia) utilizando una librería SMTP personalizada (`SimpleSMTP`).
*   Sanitización estricta de todos los datos de entrada para prevenir inyección de código.

### 3.3.3. Gestión de Usuarios (CRUD)
*   **Listado:** Visualización de usuarios con filtros de estado.
*   **Creación/Edición:** Formularios validados en servidor.
*   **Baja Lógica:** Implementación de "Soft Delete" para desactivar usuarios sin perder sus datos históricos.
*   **Protección Anti-Suicidio:** Lógica que impide que un administrador se desactive a sí mismo.
*   **Fotos de Perfil:** Sistema de subida de imágenes seguro con validación de tipo MIME, renombrado aleatorio y limpieza automática de archivos antiguos.
*   **Sistema de Auditoría:** Implementación de logs de seguridad en archivo de texto (Flat-File) para registrar accesos, fallos y bloqueos, con visor integrado en el panel de administración.
*   **Mejora de UX en Dashboard:** Personalización de la interfaz (Header y Dashboard) para mostrar la foto y el email del usuario logueado, mejorando la orientación y el feedback visual.
*   **Fotos de Perfil:** Sistema de subida de imágenes seguro con validación de tipo MIME real, control de tamaño (≤2MB), manejo de errores `UPLOAD_ERR_*` y mensajes amigables en UI (sin exponer rutas internas), con limpieza de archivos antiguos.

### 3.3.4. Gestión de Clientes
*   **Migración y esquema:** Se creó `database/migrations/03_create_crm_tables.sql` con las tablas `clientes` e `inmuebles`, FKs a `usuarios` y `clientes`, flags de operación e índices de filtrado.
*   **CRUD con control de rol:** Admin y coordinador ven todos los clientes; el comercial sólo los asignados (`usuario_id` se asigna automáticamente al crear).
*   **Asignacion y reasignacion por rol:** Admin/coordinador pueden elegir o cambiar el comercial desde el formulario; el controlador fuerza el `usuario_id` correcto para impedir manipulacion por comerciales.
*   **Seguridad y validacion:** CSRF en formularios, sanitizacion basica y control de DNI duplicado antes de insertar/actualizar.
*   **Borrado protegido:** Si existen inmuebles ligados, el delete devuelve error controlado y no elimina.
*   **UI:** Nuevas vistas (`index`, `create`, `edit`) y accesos desde el header y el dashboard segun rol.
*   **Seguridad y validación:** CSRF en formularios, sanitización básica y control de DNI duplicado antes de insertar/actualizar.
*   **Borrado protegido:** Si existen inmuebles ligados, el delete devuelve error controlado y no elimina.
*   **UI:** Nuevas vistas (`index`, `create`, `edit`) y accesos desde el header y el dashboard según rol.

### 3.3.5. Manejo de errores y logs
*   **Excepciones de BD:** `App\Core\Database` lanza `PDOException` y el front controller captura y muestra mensaje genérico “Error de sistema”, registrando detalle en el log del servidor.
*   **Errores detectados y resueltos:** Ajuste de la tabla `clientes` (faltaban `usuario_id`, `telefono`) que provocaba `Unknown column` al crear clientes; se corrigió el esquema y se añadió la migración completa.
*   **Compatibilidad PHP 8:** Se normalizó el tipo de `user_id` (casteo a int en `ClienteController::index()`) para evitar excepciones de tipado en producción y se eliminó `E_STRICT` de `error_reporting`, usando `\PDOException` en el handler global para limpiar warnings.

### 3.3.5.1. Gestión de Inmuebles

Se inició la implementación del módulo **Inmuebles** para cubrir dos áreas:

- **Backoffice (Admin/Coordinador/Comercial):** alta, edición, listado, filtrado y baja lógica de inmuebles.
- **Área pública:** catálogo de inmuebles “publicables” según criterios de visibilidad.

**Regla de negocio clave:** cada inmueble pertenece a un **cliente/propietario**. Dado que cada cliente está asignado a un **comercial**, los inmuebles deben quedar gestionados por dicho comercial. Si un cliente cambia de comercial, los inmuebles vinculados deben pasar automáticamente a ser gestionados por el nuevo comercial y aparecer en su listado.

**Trabajo realizado (a nivel técnico):**  
- Se desarrolló el **modelo `Inmueble`** alineado con el esquema real de la tabla `inmuebles`: `ref`, `propietario_id`, `comercial_id`, `direccion`, `localidad`, `provincia`, `cp`, `tipo`, `operacion`, `precio`, `estado`, `activo`, `archivado`, `fecha_alta`, etc.  
- Se implementaron **controladores** (admin y público), **vistas** (listado y formulario en admin, y estructura del catálogo público) y se registraron las **rutas** correspondientes en `public/index.php`.  
- Desde la ficha pública de inmueble, el usuario dispone de un botón **“Contactar”** (y “Solicitar información”) que abre el formulario `\`/contacto\``, pre-rellenado con la referencia y datos básicos del inmueble. Este formulario está descrito en detalle en el apartado 3.3.11, incluyendo las medidas de seguridad y el sistema de trazabilidad mediante logs.  
- Se añadieron validaciones de servidor y ajustes para compatibilidad con los tipos `ENUM` y las columnas existentes en la base de datos.


**Bloqueo técnico actual:**
A pesar de que el código no presenta errores de sintaxis y el mapeo del modelo está alineado con la base de datos, el acceso a rutas del módulo devuelve **404 Not Found** (por ejemplo `/admin/inmuebles/nuevo`), impidiendo acceder al formulario y completar el flujo de alta.

Se intentó un ajuste del Router para normalizar rutas y gestionar posibles prefijos (basePath), pero no se resolvió el 404. Por control de calidad, se detuvo la integración del módulo hasta aislar el origen exacto del fallo (resolución hacia `public/index.php` por configuración de `DocumentRoot`/rewrite y/o coincidencia exacta de rutas en el Router).

**Pendiente para cierre del módulo:**
- Verificar que todas las URLs resuelven pasando por `public/index.php` (rewrite/DocumentRoot).
- Confirmar normalización de URIs y coincidencia con rutas registradas.
- Añadir accesos de UI (botones/enlaces) desde el panel para entrar al módulo (listado y alta).
- Implementar definitivamente la regla comercial:
  - opción A: el inmueble hereda siempre el comercial del cliente,
  - opción B: asignación manual con sincronización cuando cambie el comercial del cliente.

**Estado actual del proyecto:** La plataforma es funcional en los módulos base (autenticación por roles, tasación online, gestión de usuarios, clientes y logs). El módulo **Inmuebles** se encuentra en fase de integración, actualmente **bloqueado** por un problema de **routing (404 en rutas como `/admin/inmuebles/nuevo`)**. Por control de calidad, estos cambios no se han subido a GitHub hasta recuperar el flujo básico (acceso al formulario, alta y listado).

### 3.3.5.2. Criterios de publicación y catálogo público (definición funcional)

El portal incluye una zona pública orientada a usuario final con un **catálogo de inmuebles**. Para garantizar que la web solo muestre propiedades válidas y controladas internamente, se definieron **criterios explícitos de publicación** basados en el estado y flags de la tabla `inmuebles`.

#### Definición de “Inmueble publicable”
Un inmueble se considera **publicable** cuando cumple simultáneamente:

- `estado = 'activo'`  
- `activo = 1`  
- `archivado = 0`

Este criterio permite separar claramente:
- **Estado comercial** (`estado`): borrador/activo/reservado/vendido/retirado.
- **Visibilidad operativa** (`activo`): habilita o deshabilita el registro para su uso normal.
- **Ciclo de vida / histórico** (`archivado`): permite retirar el inmueble del flujo activo sin perder datos.

#### Objetivo de la regla
Con esta combinación se consigue:
- Evitar la publicación accidental de inmuebles en **borrador**, **vendidos** o **retirados**.
- Mantener un histórico consultable sin eliminar registros (borrado físico).
- Controlar la visibilidad pública con una condición simple, consistente y auditable.

#### Búsqueda y navegación pública previstas
El catálogo público está planteado para permitir:
- Listado paginado de inmuebles publicables.
- Filtros típicos de búsqueda (p. ej. localidad, operación, tipo, rango de precio).
- Acceso a ficha individual por referencia (`ref`) únicamente si cumple el criterio de publicación.

#### Estado de integración
Aunque estos criterios y estructura pública están definidos e implementados a nivel de modelo/controlador, la publicación efectiva del módulo está actualmente condicionada a resolver el bloqueo de routing descrito en el apartado **3.3.5.1** (404 en rutas internas del módulo).
Este enfoque reduce riesgos de publicación no autorizada y simplifica la auditoría funcional del catálogo.

### 3.3.5.3. Resolución del bloqueo técnico y cierre de integración (Actualización 07/12/2025)

Tras el análisis detallado del error 404 en las rutas de administración, se procedió a la corrección y finalización del módulo:

*   **Corrección del Router:** Se identificó que la normalización de la URI en entornos Windows (donde `SCRIPT_NAME` usa backslash `\`) fallaba al intentar eliminar el prefijo del script, haciendo que la ruta final no coincidiera con las registradas. Se aplicó una normalización de separadores (`str_replace('\\', '/', ...)`) antes del procesamiento, solucionando el problema de ruteo.
*   **Ajuste de Sesiones:** Se unificó el uso de las claves de sesión (`user_id` vs `id_usuario`) en los controladores `InmuebleController` y `DemandaController` para alinearlos con el `AuthController`, evitando redirecciones erróneas al login.
*   **Gestión de Roles extendida:** Se habilitó el acceso al módulo para el rol `comercial` (además de admin y coordinador), implementando una lógica de negocio específica: los comerciales pueden crear inmuebles pero se auto-asignan como responsables, sin posibilidad de asignar a otros compañeros (campo de solo lectura).
*   **Integración UI:** Se completó la interfaz añadiendo accesos directos desde la ficha del cliente ("Añadir inmueble"), facilitando el flujo de trabajo natural del operador.
### 3.3.5.4. Mejora de navegación en la creación y edición de inmuebles (Return Path)

Durante las pruebas de uso del módulo de inmuebles se detectó un problema de usabilidad importante:  
cuando un usuario accedía a la edición de un inmueble desde la ficha de un cliente, al pulsar el botón **«Volver»** o al guardar correctamente el formulario, el sistema redirigía siempre al listado general `/admin/inmuebles`. Esto obligaba al usuario a localizar de nuevo al cliente y rompía el flujo natural de trabajo orientado a “ficha de cliente → inmuebles de ese cliente”.

Para resolverlo, se ha implementado un mecanismo de **return path** basado en un parámetro seguro `return_to`:

- La vista de edición de clientes genera el enlace de **Editar inmueble** (y también **Añadir inmueble**) incluyendo un `return_to` con la URL completa de la ficha de cliente.
- El controlador `InmuebleController` lee este parámetro tanto en `edit()`/`update()` como en `create()`/`store()` y lo valida mediante un método privado `validateReturnTo()`, que solo acepta rutas internas que comienzan por `/admin/` y descarta cualquier intento de URL externa o potencial *open redirect*.
- La vista del formulario de inmuebles (`form.php`) recibe el return path y lo mantiene en un campo oculto `<input type="hidden" name="return_to">`.
- Los botones **«Volver»** y **«Cancelar»** utilizan este `return_to` cuando está presente, y realizan un *fallback* controlado al listado `/admin/inmuebles` cuando no existe (por ejemplo, si el usuario llega desde el propio listado de inmuebles).
- Tras una actualización o creación correcta, el controlador redirige al `return_to` validado y añade el parámetro `msg=updated` o `msg=created` mediante un helper `addQueryParam()`, que construye la query string sin romper parámetros previos.

En caso de errores de validación en el formulario, el flujo se mantiene deliberadamente en la vista de edición/alta:  
no se realiza ninguna redirección, se muestran los mensajes de error y se conserva tanto el contenido del formulario como el `return_to`, permitiendo corregir los datos sin perder el contexto de origen.

Esta mejora se ha probado con los tres perfiles definidos en la aplicación (administrador, coordinador y comercial):

- Desde la ficha de un cliente, al **editar** o **dar de alta** un inmueble y pulsar **«Volver»** o guardar correctamente, se regresa siempre a la ficha de ese cliente, mostrando el mensaje de éxito correspondiente.
- Desde el listado general de inmuebles, el comportamiento clásico se mantiene: la navegación vuelve al propio listado.
- Manipulaciones manuales del parámetro `return_to` con URLs externas o rutas no válidas son neutralizadas por `validateReturnTo()`, que fuerza el uso del *fallback* seguro.

Con este cambio se mejora de forma significativa la experiencia de usuario, se respeta el flujo de trabajo real de una agencia inmobiliaria (operar siempre “dentro” de la ficha de cliente) y se mantiene al mismo tiempo un nivel adecuado de seguridad frente a redirecciones abiertas y manipulación de URLs.


### 3.3.5.4. Soporte de imagen principal de inmuebles (subida segura)

Una vez resuelto el bloqueo de routing y estabilizado el CRUD del módulo **Inmuebles**, se decidió incorporar una mejora funcional y visual clave: permitir que cada inmueble tenga una **imagen principal** opcional, gestionada desde el backoffice y visible en el listado y en la ficha.

#### Diseño de la solución

El objetivo era cumplir dos requisitos:

1. **No romper inmuebles existentes** (compatibilidad hacia atrás).
2. **Aplicar buenas prácticas de seguridad en subida de ficheros**, alineadas con el resto de la arquitectura del proyecto.

Las decisiones principales fueron:

* Añadir una columna opcional `imagen` a la tabla `inmuebles` mediante la migración `04_add_imagen_to_inmuebles.sql`, almacenando únicamente el **nombre del archivo** (no la ruta completa).  
* Considerar la imagen como un campo **no obligatorio**: si un inmueble no tiene imagen, la interfaz utiliza un **placeholder** (`no-image.png`) y la base de datos mantiene `imagen = NULL`.  
* Reutilizar el patrón ya empleado en las fotos de usuario (`foto_perfil`), pero adaptado a un directorio específico para inmuebles: `/public/uploads/inmuebles`.

#### Lógica de subida y validación

Para evitar vulnerabilidades típicas en sistemas de subida de archivos (ejecución de scripts, abuso de tamaño, imágenes malformadas, etc.), se implementó un método privado `handleImageUpload()` en el controlador de inmuebles. Este método:

* **Valida el origen del archivo** con `move_uploaded_file()`, asegurando que proviene de una petición `POST` multipart/form-data.
* **Comprueba el tamaño máximo** (2 MB) para evitar consumos excesivos de disco y de memoria.
* **Detecta el tipo MIME real** con `finfo_file()` y no con los datos proporcionados por el navegador (cabeceras manipulables). Solo se admiten `image/jpeg`, `image/png`, `image/webp` y `image/gif`.
* **Verifica que el archivo es una imagen** usando `getimagesize()`, que además proporciona las dimensiones.
* **Impone un límite de dimensiones** (máximo 1920x1920 píxeles) como compromiso entre calidad visual y rendimiento.
* **Genera un nombre único y seguro** (`inmueble_<uniqid>.ext`) para evitar colisiones y ataques de path traversal.
* **Crea el directorio de subida si no existe** (`/public/uploads/inmuebles`) y añade un archivo `.htaccess` que:
  * desactiva la ejecución del motor PHP dentro de ese directorio,
  * deshabilita el listado de directorios.

### 3.3.6. Módulo de Tasación Online (Fase 1 - Email)

Implementado para captar leads y ofrecer valor añadido a visitantes web.

#### Funcionalidad
1.  **Formulario Público**: `/tasacion`. Recoge datos del inmueble (ubicación, características) y del contacto.
2.  **Algoritmo de Valoración**: Calcula un rango de precio estimado aplicando incrementos porcentuales (ascensor, exterior, estado) sobre un precio base por zona.
3.  **Comunicación**: Envía dos correos electrónicos mediante SMTP seguro (`PHPMailer`):
    *   **Cliente**: Recibe la valoración estimada y confirmación de recepción.
    *   **Agencia**: Recibe los datos del lead para seguimiento comercial.

#### Retos Técnicos y Soluciones
*   **Envío de Correos**: Se sustituyó la clase básica `SimpleSMTP` por `PHPMailer` para soportar autenticación segura (SSL/TLS) requerida por servidores modernos como cPanel y Gmail.
*   **Plantillas HTML**: Se implementó un sistema de templates (separando lógica de vista) para enviar correos corporativos con diseño responsive.
*   **Seguridad**: Credenciales SMTP alojadas en variables de entorno (`.env`), fuera del código fuente.

En caso de fallo en cualquiera de estas validaciones, el método devuelve `null` y el controlador muestra un mensaje genérico de error en la imagen, manteniendo el resto de datos del formulario para no penalizar la experiencia del usuario.

#### Integración en el ciclo de vida del inmueble

La columna `imagen` se integra en las operaciones principales del módulo:

* **Alta (`store`)**  
  Si el usuario adjunta una imagen válida, se procesa y se guarda el nombre del archivo en `$data['imagen']` antes de invocar al modelo. Si no se adjunta nada, el campo se deja en `NULL` y el inmueble se considera “sin foto principal”.

* **Edición (`update`)**  
  * Si se sube una nueva imagen válida, se guarda el nuevo archivo, se actualiza la columna `imagen` y se elimina del disco la imagen anterior asociada al inmueble, evitando archivos huérfanos.
  * Si no se adjunta nueva imagen, se mantiene el valor existente de `imagen`, de forma transparente para el usuario.

* **Borrado (`delete`)**  
  Antes de eliminar el registro de la tabla `inmuebles`, el controlador recupera el nombre de la imagen asociada (si existe) y borra el archivo físico del directorio de uploads. De este modo, la base de datos y el sistema de ficheros se mantienen coherentes.

#### Cambios en las vistas y experiencia de usuario

En el formulario de inmuebles del área de administración se han aplicado los siguientes cambios:

* Se ha añadido `enctype="multipart/form-data"` al formulario para permitir el envío de archivos.
* Se incorpora un campo de tipo `file` para seleccionar la imagen, junto con ayuda textual sobre formatos permitidos, tamaño máximo y dimensiones recomendadas.
* En modo edición, se muestra una **miniatura de la imagen actual** y un mensaje informativo indicando que subir una nueva imagen reemplazará a la existente.

En el listado de inmuebles se añade una columna **Imagen** que muestra:

* Una miniatura de 60x60 píxeles (con `object-fit: cover`) cuando el inmueble tiene imagen principal.
* Un icono genérico (`no-image.png`) con menor opacidad cuando no hay imagen asociada, para mantener la coherencia visual sin obligar al usuario a subir fotos en todas las altas.

Esta mejora acerca el módulo a un caso de uso real de una inmobiliaria, donde la imagen de la propiedad es un elemento clave tanto para el trabajo del equipo comercial como para la web pública.

#### Pruebas y lecciones aprendidas

Se han realizado pruebas manuales con diferentes escenarios:

* Alta y edición de inmuebles con y sin imagen.
* Sustitución de imagen y verificación del borrado del archivo anterior.
* Intento de subida de ficheros no válidos (formatos incorrectos, tamaño excesivo, imágenes demasiado grandes).
* Borrado de inmuebles con imagen para comprobar la limpieza del sistema de ficheros.

Durante estas pruebas apareció una advertencia deprecada en PHP 8.5 relacionada con `finfo_close()`, que se solucionó eliminando la llamada explícita, ya que los objetos `finfo` se liberan automáticamente en versiones recientes del intérprete. Esta incidencia ha servido para ajustar el código a las nuevas versiones de PHP y mantener la compatibilidad futura.

### 3.3.5.5 Refuerzo de seguridad en el módulo de Inmuebles (control por rol)

Durante las pruebas del módulo de Inmuebles se detectó un fallo crítico de seguridad:  
un usuario con rol **Comercial** podía acceder a inmuebles de otros comerciales e incluso cambiar el propietario desde el formulario, “apropiándose” de la propiedad.

Este comportamiento vulneraba directamente la regla de negocio definida para la agencia:  
**cada comercial solo puede gestionar la cartera de inmuebles de sus propios clientes**.

Para corregirlo se han introducido varios mecanismos de protección:

- **Filtrado en el modelo (`Inmueble::paginateAdmin`)**  
  El listado de inmuebles ahora recibe el `user_id` y el `rol` del usuario:
  - Admin y coordinador ven todos los inmuebles.
  - El rol comercial solo recibe inmuebles cuyo propietario (`clientes.usuario_id`) coincide con su `user_id`.

- **Validaciones en controladores (`InmuebleController`)**  
  Se han reforzado los métodos `create`, `store`, `edit` y `update`:
  - En alta, un comercial solo puede seleccionar clientes de su cartera (y el servidor lo valida).
  - En edición, únicamente puede abrir inmuebles de sus clientes.
  - Si intenta cambiar el propietario a un cliente de otro comercial, la operación se bloquea y se devuelve un error de permisos.

- **Consistency con la gestión de clientes**  
  El módulo de Inmuebles queda alineado con la lógica de asignación de clientes (apartado 3.3.4), garantizando que:
  - Si un cliente se reasigna a otro comercial, sus inmuebles pasan a formar parte de la nueva cartera.
  - Nadie puede “robar” inmuebles manipulando formularios o URLs.

Con este refuerzo, el módulo de Inmuebles deja de ser solo un CRUD técnico y pasa a comportarse como una herramienta de trabajo segura y coherente con la operativa real de una agencia inmobiliaria.

### 3.3.5.6. Carrusel de propiedades destacadas en la landing (Home)

Con el objetivo de mejorar la experiencia de usuario (UX) en la página de inicio y mostrar una imagen más dinámica de la cartera, se implementó un carrusel de propiedades destacadas.

**Motivación y Diseño:**
Se buscaba que la landing page no fuera estática, sino que invitara a la navegación mostrando inmuebles reales. Para ello, se decidió reutilizar el criterio de **"inmueble publicable"** (`activo=1`) definido para el catálogo, garantizando consistencia: lo que sale en el carrusel es navegable y está disponible.

**Decisión Técnica (Gestión de Riesgos):**
En esta fase final del proyecto, se decidió **no modificar la estructura de la base de datos** (evitando añadir columnas como `destacado` o `orden_home`) para minimizar riesgos de regresión. En su lugar, se optó por una estrategia de selección algorítmica:
*   **Selección Aleatoria Estable:** Se utiliza `ORDER BY RAND(TO_DAYS(CURDATE()))`. Esto selecciona propiedades al azar, pero mantiene la misma selección durante todo el día (24h). Esto evita el efecto de "sitio roto" o inestable donde el contenido cambia con cada refresco de página (F5).

**Implementación:**
*   **Backend:** Método `Inmueble::getHomeCarousel()` con un límite de seguridad (clamped 1-12).
*   **Frontend:** Implementación "Vanilla" (sin librerías externas) usando CSS `scroll-snap` para el desplazamiento horizontal y JavaScript nativo para la gestión de botones (ocultar anterior/siguiente según scroll).

**Retos y Soluciones:**
Durante la integración surgió un error crítico (`Fatal Error: Cannot use object of type stdClass as array`) en el renderizado de las tarjetas. Esto se debió a que el método de acceso a datos devolvía objetos genéricos mientras la vista esperaba arrays asociativos. Se solucionó forzando el modo de recuperación `PDO::FETCH_ASSOC` en el modelo, lo que además estandarizó el formato de datos con el resto de la aplicación.

**Resultado Final:**
El carrusel es funcional, responsive (soporta swipe en móviles) y mejora la percepción de calidad del sitio sin haber comprometido la estabilidad de la base de datos en la entrega final.

### 3.3.6. Cumplimiento normativo (RGPD y cookies)
*   Paginas legales provisionales (aviso legal, privacidad, cookies) publicadas bajo `/legal/*` con controlador dedicado y vistas en `app/Views/legal/`, marcando que el contenido es temporal hasta validacion juridica.
*   Footer reorganizado con enlaces legales visibles y las redes sociales oficiales en formato horizontal, debajo del bloque legal.
*   Banner de cookies fijo en la parte inferior con botones de aceptar/rechazar; la preferencia se guarda en `localStorage` (`cookie_consent`) y se oculta el aviso tras la decision del usuario.

### 3.3.7. Gestion de Interfaz y UX
Se ha optado por una arquitectura modular basada en vistas parciales ubicadas en `app/Views/partials/` para encapsular componentes reutilizables (por ejemplo, el banner principal). La visibilidad de cada elemento de interfaz se controla de forma estricta desde `HomeController` mediante banderas (`$showHero`, `$mostrar_tarjeta` y las variables asociadas al hero), manteniendo la separacion de responsabilidades propia del MVC: el controlador decide y la vista se limita a representar el contenido recibido.
### 3.3.8. Módulo de Demandas (gestión de necesidades de compra/alquiler)

El módulo de **Demandas** completa el ciclo CRM del backoffice, permitiendo registrar y gestionar las necesidades de búsqueda de inmuebles de cada cliente: tipo de operación (compra, alquiler, vacacional), rango de precios, superficie mínima, número de habitaciones y baños, zonas de interés y características adicionales (garaje, piscina, ascensor, terraza, etc.).

A nivel de modelo, se implementa la clase `Demanda` alineada con la tabla `demandas` definida en la base de datos real. Esta tabla incluye, entre otros, los siguientes campos:

- `cliente_id`: referencia al cliente que solicita la demanda.  
- `comercial_id`: usuario responsable de gestionar la demanda.  
- `tipo_operacion`: compra | alquiler | vacacional.  
- `rango_precio_min` / `rango_precio_max`: banda de precio objetivo.  
- `superficie_min`, `habitaciones_min`, `banos_min`: criterios mínimos.  
- `zonas`: texto libre con barrios o zonas preferidas.  
- `caracteristicas` (JSON): lista de etiquetas (`["garaje","piscina","ascensor", ...]`).  
- `estado`: `activa`, `en_gestion`, `pausada`, `archivada`.  
- Flags `activo`/`archivado` y campos de fecha (`fecha_alta`, `fecha_archivado`) para el ciclo de vida.

El modelo se conecta a la base de datos mediante la clase `Database` del core y expone métodos para:

- **Paginación por rol** (`paginateAdmin`):  
  - Admin / Coordinador: ven todas las demandas.  
  - Comercial: solo ve demandas de los clientes que tiene asignados.
- **CRUD completo** (`create`, `findById`, `update`, `delete`) con conversión automática del campo JSON `caracteristicas` a arrays PHP (y viceversa).
- **Integración con Clientes** (`getByCliente`): listado de demandas asociadas a un cliente concreto para mostrarlas en su ficha.

Desde el punto de vista de negocio, se mantiene la coherencia con el módulo de Clientes:

- Cada cliente puede tener **0..N demandas**.  
- El `comercial_id` de la demanda **se hereda automáticamente** del cliente (`usuario_id`), evitando que un comercial pueda “colarse” asignándose clientes que no le corresponden.
- Admin y coordinador pueden trabajar con el conjunto completo de datos, mientras que el comercial solo opera sobre su cartera.

La interfaz se integra en dos niveles:

1. **Listado global de Demandas** (`/admin/demandas`):  
   - Tabla con filtros por tipo de operación, estado, comercial y rango de precio.  
   - Paginación reutilizando el patrón existente en otros módulos.

2. **Ficha de cliente** (`/admin/clientes/editar`):  
   - Nueva sección “Demandas de este cliente” que muestra un resumen de sus demandas (tipo, rango de precios, superficie mínima, estado, fecha, etc.).  
   - Botón “Añadir demanda” que abre el formulario pre-rellenando el cliente y, tras guardar, retorna de nuevo a la ficha mediante el parámetro `return_to`.

En términos de seguridad y robustez:

- Todos los formularios de alta/edición usan **tokens CSRF** y validación básica del lado servidor.  
- Se validan reglas como:
  - El cliente seleccionado debe existir y ser accesible para el usuario logueado.  
  - El precio mínimo no puede ser mayor que el máximo.  
  - Los campos numéricos (superficie, habitaciones, baños) no admiten valores negativos.
- El campo `caracteristicas` se serializa como JSON con `json_encode` y se deserializa con `json_decode`, normalizando siempre a un array vacío cuando no hay datos para evitar errores en PHP.

Este módulo prepara el terreno para un futuro sistema de **matching automático** entre `demandas` e `inmuebles` (por precio, zona y características), que permitiría sugerir inmuebles a los comerciales en función de las necesidades registradas.

### 3.3.9. Módulo de Demandas (peticiones de compra/alquiler)

En una inmobiliaria real no basta con saber qué inmuebles hay disponibles; es igual de importante registrar **qué está buscando cada cliente**.  
Para cubrir esta necesidad se ha desarrollado el módulo de **Demandas**, que permite asociar a cada cliente una o varias “peticiones” de compra o alquiler con sus criterios de búsqueda.

#### Modelo de datos y relaciones

El módulo se apoya en la tabla `demandas`, que está relacionada de forma directa con clientes y usuarios (comerciales):

- `cliente_id` → `clientes.id_cliente` (**ON DELETE CASCADE**):  
  Si se elimina un cliente, todas sus demandas se borran automáticamente.
- `comercial_id` → `usuarios.id_usuario` (**ON DELETE SET NULL**):  
  Si se elimina un usuario/comercial, las demandas siguen existiendo pero quedan sin comercial asignado.

Además de las claves externas, la tabla almacena:

- Información comercial:
  - `tipo_operacion` (`compra`, `alquiler`, `vacacional`).
  - `rango_precio_min` y `rango_precio_max` (DECIMAL).
  - `superficie_min`, `habitaciones_min`, `banos_min`.
  - `zonas` (texto libre para barrios o zonas concretas).
- Estado y ciclo de vida:
  - `estado` (`activa`, `en_gestion`, `pausada`, `archivada`).
  - Flags `activo` y `archivado`.
  - `fecha_alta`, `fecha_archivado`.

Para las características se ha optado por un campo **JSON**:

- `caracteristicas` (JSON): array de cadenas con etiquetas como `"garaje"`, `"ascensor"`, `"terraza"`, etc.
- A nivel de código, el modelo `Demanda` se encarga de:
  - Codificar el array a JSON al guardar (`json_encode`).
  - Decodificarlo siempre a **array** al leer (`json_decode(..., true) ?? []`), evitando valores `NULL`.

Esta decisión facilita la **evolución futura**: se pueden añadir nuevas características sin alterar el esquema de la base de datos.

#### Implementación en MVC

Se ha creado el modelo `App\Models\Demanda`, que reutiliza la clase `Database` existente (`Database::conectar()`) y proporciona los métodos habituales de acceso:

- `paginateAdmin($userId, $rol, $filtros, $page, $perPage)`: listado paginado con filtros y control por rol.
- `findById($id)`: obtención de una demanda concreta.
- `create(array $data)`, `update(int $id, array $data)`, `delete(int $id)`.
- `getByCliente(int $clienteId)`: helper para obtener todas las demandas asociadas a un cliente.

En el controlador `DemandaController` se ha implementado el CRUD completo:

- `index()`: listado con filtros (tipo de operación, estado, comercial, rango de precio).
- `create()` / `store()`: alta de nuevas demandas.
- `edit()` / `update()`: edición de demandas existentes.
- `delete()`: borrado (solo disponible para admin y coordinador).

El controlador reutiliza los mismos **helpers de autenticación y seguridad** que el módulo de inmuebles:

- `requireAuth()`, `requireRole()`, `currentUserId()`, `currentUserRole()`.
- `csrfToken()`, `csrfValidate()` para protección frente a CSRF.
- `validateReturnTo()` y `addQueryParam()` para gestionar el patrón de navegación contextual (`return_to`).

#### Reglas de negocio por rol

El módulo está alineado con la organización real de la inmobiliaria:

- **Administrador / Coordinador**:
  - Pueden ver y filtrar **todas las demandas**.
  - Pueden crear, editar y borrar demandas de cualquier cliente.
  - En los formularios de alta/edición pueden seleccionar cualquier cliente.

- **Comercial**:
  - Solo ve demandas de **sus clientes** (se filtra por `clientes.usuario_id = user_id`).
  - Solo puede crear demandas para clientes que le están asignados.
  - Si intenta manipular el `cliente_id` para apuntar a otro comercial, el controlador bloquea la operación y muestra un error de permisos.

Para mantener la coherencia, el `comercial_id` de la demanda se hereda siempre del cliente:

- En perfiles admin/coordinador: se toma `cliente->usuario_id`.
- En perfiles comercial: se fuerza al `user_id` de la sesión, aunque se manipule el formulario.

#### Flujo de trabajo e integración con Clientes

La integración con la ficha de cliente es clave para que el módulo tenga sentido en el día a día de la oficina:

- En `ClienteController` se inyecta el modelo `Demanda` y, en el método `edit()`, se obtienen las demandas del cliente mediante `getByCliente($id)`.
- En la vista `app/Views/admin/clientes/edit.php` se ha añadido una sección específica “Demandas de este cliente”:
  - Muestra una tabla con tipo de operación, rango de precio, superficie mínima, estado y fecha.
  - Cada fila incluye un botón **Editar** que respeta el patrón `return_to` (tras guardar vuelve a la ficha del cliente).
- Sobre la propia ficha del cliente se ha añadido el botón “➕ Añadir demanda”:
  - Abre el formulario de alta de demandas con el `cliente_id` ya fijado.
  - El campo de cliente se muestra en modo **solo lectura** para evitar inconsistencias.

Paralelamente, existe un listado global en `/admin/demandas` accesible desde el dashboard, que permite filtrar y revisar todas las demandas existentes.

#### Validación de datos y coherencia funcional

En el servidor se aplican una serie de validaciones para garantizar la calidad del dato:

- `cliente_id` debe existir y ser accesible según el rol del usuario.
- `tipo_operacion` debe estar dentro de los valores permitidos.
- `rango_precio_min` y `rango_precio_max`:
  - Se convierten a enteros (truncando posibles decimales).
  - Se valida que el mínimo no sea mayor que el máximo.
- `superficie_min`, `habitaciones_min`, `banos_min` deben ser valores numéricos positivos o cero.

En caso de error, el controlador no redirige: vuelve a renderizar el formulario conservando los datos introducidos y mostrando mensajes de validación campo a campo.

Gracias a las claves externas definidas en la base de datos, se garantiza además que:

- Al borrar un cliente, se eliminan automáticamente sus demandas asociadas (`ON DELETE CASCADE`).
- Si se elimina un comercial, las demandas siguen existiendo pero `comercial_id` pasa a `NULL`, lo que facilita reasignarlas posteriormente.

En conjunto, el módulo de Demandas convierte la aplicación en una herramienta más cercana a un CRM real, permitiendo cruzar de forma estructurada **lo que el cliente busca** con **lo que la agencia tiene en cartera** (funcionalidad de cruces prevista para desarrollos posteriores).


### 3.3.10. Front público de propiedades

Una vez estabilizado el módulo de inmuebles en el backoffice, se implementó la parte pública del catálogo de propiedades, accesible desde el menú principal del sitio.

El objetivo de este apartado es permitir que cualquier usuario pueda consultar los inmuebles activos sin necesidad de registrarse, pero apoyándose en la misma base de datos y reglas de negocio que se utilizan internamente en la agencia.

#### Rutas públicas y arquitectura

Se definieron dos rutas principales:

- `GET /propiedades` → listado público de inmuebles.
- `GET /propiedades/ver?id=ID` → ficha de detalle de un inmueble.

Estas rutas se atienden desde el controlador `InmueblePublicController`, que actúa como capa de orquestación entre el Router, el modelo `Inmueble` y las vistas específicas de la parte pública.

En el modelo se reutilizan las mismas columnas y flags que en el backoffice (`activo`, `archivado`, `estado`), de forma que solo se muestran inmuebles que la agencia considera “publicables” (por ejemplo, activos y no retirados). Esta decisión evita mantener dos lógicas distintas para decidir qué se publica en la web.

#### Listado con paginación

Para el listado de propiedades se optó por una paginación de **10 elementos por página**, una cifra que ofrece una buena experiencia en escritorio y en móvil y reduce los tiempos de carga. Cada card del listado muestra:

- Imagen principal o un placeholder si no existe aún.
- Precio destacado.
- Localidad/provincia.
- Superficie en m², número de habitaciones y baños (si están informados).
- Un extracto de la descripción.
- Botones de acción (“Más información” y “Contactar”).

En el pie del listado se incluye un paginador clásico (Anterior/Siguiente y números de página), implementado en PHP sin dependencias externas.

#### Ficha detallada

La ficha de detalle (`/propiedades/ver?id=ID`) amplía la información mostrada en el listado:

- Imagen grande del inmueble.
- Título y ubicación completa.
- Todos los datos públicos relevantes (tipo, operación, superficie, habitaciones, baños, estado, referencia interna, etc.).
- Descripción extendida.
- Zona/localidad y código postal.
- En una columna lateral, el precio y botones de contacto (email/teléfono) que derivan al formulario de tasación/contacto.

Si el inmueble no existe o no cumple las condiciones de publicación (no está activo, está archivado o se encuentra en un estado no publicable), el sistema devuelve un error 404 en lugar de mostrar la ficha.

#### Decisiones técnicas y de diseño

- Se ha mantenido la arquitectura MVC: Router → controlador público → modelo → vistas específicas bajo `app/views/propiedades/`.
- Se ha reutilizado la lógica de visibilidad del backoffice para evitar inconsistencias.
- Se ha priorizado un diseño responsive y orientado al usuario final, separando claramente lo que ve el cliente de la información interna que solo utiliza la agencia.

Con este módulo, la plataforma pasa de ser una herramienta exclusivamente interna a ofrecer también un front público funcional, alineado con el trabajo real de una inmobiliaria.



## 3.4. Manejo de Errores
He implementado un manejador global de excepciones (`set_exception_handler`) en el punto de entrada. Esto asegura que, en producción, los errores técnicos (como fallos de BD) se registren en el log del servidor pero se muestre un mensaje genérico y amigable al usuario final, evitando la fuga de información sensible.

## 3.5. Justificación de Decisiones Técnicas
*   **¿Por qué PDO?** He elegido PDO sobre MySQLi porque me permite trabajar con una capa de abstracción de base de datos, facilitando una futura migración a otro motor si fuera necesario, y por su soporte nativo para sentencias preparadas, cruciales para evitar inyecciones SQL.
*   **¿Por qué `password_hash`?** Utilizo el algoritmo `PASSWORD_DEFAULT` (actualmente Bcrypt) porque es el estándar de la industria para el hashing seguro, incorporando "salt" automáticamente y haciendo computacionalmente costosos los ataques de fuerza bruta.
*   **¿Por qué `uniqid` en archivos?** Para evitar colisiones de nombres y prevenir ataques donde un usuario malicioso intenta sobrescribir archivos del sistema subiendo ficheros con nombres conocidos (ej. `index.php`).
*   **¿Por qué `try-catch` en subidas?** La manipulación de archivos es propensa a errores (permisos, disco lleno). He encapsulado esta lógica para garantizar que un fallo en el sistema de archivos no detenga la ejecución del script ni muestre errores fatales al usuario, mejorando la robustez.

---
**Estado actual del proyecto:** La plataforma es funcional en sus módulos base (autenticación por roles, tasación online, gestión de usuarios, clientes y logs). Se inició la implementación del módulo **Inmuebles**, alineándolo con el esquema real de base de datos (modelo/controladores/vistas y rutas), pero su integración quedó **temporalmente bloqueada** por un problema de **routing (404 en rutas como `/admin/inmuebles/nuevo`)**, pendiente de verificación de la resolución hacia `public/index.php` (rewrite/DocumentRoot) y del match/normalización de URIs en el Router. Por control de calidad, estos cambios no se han subido aún a GitHub hasta recuperar el flujo básico de alta/listado.

**Actualización (07/12/2025):** El bloqueo técnico ha sido **finalmente resuelto** con éxito.
*   **Corrección del Router:** Se normalizaron los separadores de directorio (`\`) a (`/`) en `Router::dispatch` para garantizar un `str_replace` correcto al procesar `SCRIPT_NAME` en Windows, solucionando los errores 404.
*   **Gestión de Sesiones:** Se corrigió la inconsistencia de claves (`user_id` vs `id_usuario`) en los controladores, alineando la verificación de autenticación.
El módulo de Inmuebles está ahora plenamente operativo y permite la gestión integral por parte de administradores, coordinadores y comerciales.



---

# 4. Evaluación y conclusiones finales

## 4.1. Grado de cumplimiento de objetivos
Se han alcanzado los objetivos principales del proyecto en arquitectura, seguridad y funcionamiento de los módulos base: autenticación por roles, gestión de usuarios, gestión de clientes, herramienta de tasación y sistema de logs. La aplicación es estable y escalable, quedando **pendiente de cierre** la integración del módulo **Inmuebles**, actualmente bloqueado por un problema de routing documentado en el apartado **3.3.5.1**.

**Nota final:** Dicha integración se ha completado satisfactoriamente antes de la entrega final, cumpliendo con todos los requisitos funcionales previstos.




## 4.2. Dificultades encontradas
* **Configuración de Entornos:** La diferencia de sensibilidad a mayúsculas/minúsculas entre Windows (desarrollo) y Linux (producción) requirió ajustes en el Autoloader y estandarización de rutas.
* **Seguridad en Correos:** La configuración de SPF/DKIM para evitar que los correos de tasación cayeran en SPAM fue un reto de configuración de DNS y SMTP.
* **Routing y despliegue (RESUELTO):** Se detectó un bloqueo en el acceso a rutas del módulo Inmuebles (404) debido a la gestión de separadores de directorio en Windows dentro del Router. El problema fue diagnosticado y corregido normalizando las rutas antes del dispatch, permitiendo completar la integración del módulo.

## 4.3. Conclusiones
El desarrollo de este proyecto ha permitido consolidar conocimientos avanzados de PHP y arquitectura web. La implementación de medidas de seguridad "Security by Design" desde el inicio ha resultado en un producto robusto y preparado para un entorno productivo real.

---

# 5. Referencias
*   **PHP Documentation:** https://www.php.net/docs.php
*   **PSR Standards (PHP-FIG):** https://www.php-fig.org/psr/
*   **OWASP Top 10:** https://owasp.org/www-project-top-ten/
*   **Bootstrap 5 Docs:** https://getbootstrap.com/docs/5.0/getting-started/introduction/

### 3.3.7. Front público de propiedades (09/12/2025)

Una vez consolidado el backoffice del módulo de inmuebles, se procedió a implementar la parte pública: un **catálogo de propiedades** accesible para cualquier usuario (sin autenticación) donde se muestran únicamente los inmuebles disponibles que cumplen criterios de visibilidad.

#### Rutas públicas

Se han definido dos rutas principales fuera del área /admin:

- **GET /propiedades**: List ado paginado con filtros de búsqueda (localidad, tipo de inmueble, operación).
- **GET /propiedades/ver?id=ID**: Ficha detallada de un inmueble concreto.

Ambas rutas están accesibles desde el menú principal del sitio (enlace "Propiedades" en el header).

#### Arquitectura de la implementación

**Controlador dedicado (InmueblePublicController)**

Se creó un controlador separado del administrativo para garantizar la separación de responsabilidades:

- `index()`: gestiona la visualización del listado aplicando filtros de búsqueda y **paginación de 10 inmuebles por página**, respetando el criterio de "publicable" definido en el apartado **3.3.5.2**.
- `show()`: muestra la ficha de un inmueble concreto solo si cumple con todos los requisitos de visibilidad.

**Modelo y filtrado automático**

El modelo `Inmueble` ya disponía del método `paginatePublic()`, implementado con anterioridad, que aplica de forma **automática** el filtrado de:

- stado = 'activo'
- ctivo = 1
- rchivado = 0

Esto asegura que nunca se expongan públicamente inmuebles en borrador, vendidos, retirados o archivados, eliminando cualquier riesgo de visualización no deseada.

**Cambio de parámetro en ficha pública**

Inicialmente, se planteó el acceso a la ficha con ?ref=REF (referencia alfanumérica del inmueble). Para homogeneizar el funcionamiento con el resto de la aplicación, se modificó a ?id=ID (clave primaria numérica), simplificando la lógica del controlador y evitando lookups innecesarios.

#### Vistas y experiencia de usuari o

Las vistas se encuentran en `app/views/propiedades/` para separar claramente la parte pública del backoffice:

**Listado (index.php)**

- Formulario de búsqueda con tres selectores (localidad, tipo, operación) y botón de búsqueda.
- Tarjetas de inmuebles con diseño responsive (Bootstrap 5):
  - Imagen principal del inmueble (con fallback a placeholder si no existe).
  - Título (tipo + operación), ubicación (localidad, provincia), referencia.
  - Precio destacado en formato legible.
  - Descripción breve (máximo 120 caracteres).
  - Iconos con superficie (m²), habitaciones y baños.
  - Botones "Más información" (enlaza a ficha) y "Contactar" (enlaza a /tasacion).
- Paginador con números de página, botones "Anterior/Siguiente" y contador de resultados.

**Ficha de detalle (show.php)**

- Breadcrumb de navegación (Inicio \u003e Propiedades \u003e immueble actual).
- Imagen principal a tamaño completo.
- Título, ubicación y precio destacado.
- Sección de características con iconografía (superficie, habitaciones, baños).
- Descripción completa del inmueble.
- Lista de detalles (dirección, localidad, provincia, CP, tipo, operación, referencia).
- Botón "Volver al listado"  (regresa a /propiedades).
- Sidebar sticky con precio, botones de contacto (Contactar, Llamar, WhatsApp) e información de la agencia.

#### Decisiones de  diseño y usabilidad

- **Paginación ajustada a 10 items** en lugar de 12: decisión tomada para mejorar el tiempo de carga percibido y la experiencia en dispositivos móviles.
- **Imagen con gestión de errores (onerror)**: si la imagen principal no existe o falla al cargar, se muestra automáticamente un placeholder genérico, evitando imágenes rotas.
- **Enlaces duplicados (imagen + botón)**: tanto la imagen como el botón "Más información" enlazan a la ficha, siguiendo el patrón de usabilidad de los principales portales inmobiliarios (Idealista, Fotocasa).
- **Puntos de contacto centralizados**: la aplicación diferencia entre el formulario de tasación (/tasacion, pensado como lead magnet para posibles vendedores) y el formulario de contacto general (/contacto), accesible desde la navegación y desde las fichas de inmueble. Ambos reutilizan la misma infraestructura de correo y las mismas medidas de seguridad (validación en servidor, CSRF, sanitización y logs).
- **Breadcrumb y navegación contextual**: facilita la  orientación del usuario y permite volver al listado sin perder el contexto de búsqueda (filtros activos se mantienen en la URL mediante query string).

#### Seguridad y coherencia

A nivel de seguridad, la implementación reutiliza los mecanismos del núcleo:

- Escape de todas las salidas HTML mediante función helper () (equivalente a htmlspecialchars).
- Parámetros de URL ya validados en el controlador (id numérico entero positivo).
- Verificación estricta  de visibilidad en show(): si el inmueble no está activo, se devuelve **404** (no se expone información de inmuebles no publicables).

Por otra parte, la separación de vistas públicas/admin en directorios distintos (/propiedades vs /admin/inmuebles) refuerza la modularidad y facilita el mantenimiento.

#### Integración final

Con la implementación de este módulo público, el proyecto ofrece **valor inmediato** tanto para la agencia (catálogo profesional) como para el usuario final (consulta de propiedades sin necesidad de registro ni intermediarios).

Al mismo tiempo, el enlace desde el menú principal garantiza que el catálogo sea **descubrible**, cumpliendo con uno de los objetivos iniciales: posicionar la web como punto de captación de leads cualificados.

### 3.3.11. Formulario de Contacto y Sistema de Trazabilidad (Logs)

Se ha implementado un sistema de contacto centralizado accesible a través de la ruta `/contacto`. Este módulo permite a los usuarios enviar consultas generales o solicitar información específica sobre un inmueble directamente desde su ficha pública.

#### Funcionalidad y Flujo
El formulario es contextual: si se accede desde un inmueble, pre-carga la referencia y datos básicos de la propiedad. Al enviar, el sistema dispara un flujo de notificaciones triple:
1.  **A la Agencia:** Aviso inmediato con todos los datos del lead.
2.  **Al Comercial Asignado:** Si el inmueble tiene un agente responsable, recibe una copia (CC).
3.  **Al Cliente:** Email de auto-respuesta confirmando la recepción (`contacto_cliente`).

#### Seguridad y Trazabilidad
El formulario de contacto público no solo valida y sanitiza los datos en servidor, sino que además implementa un sistema de trazabilidad mediante logs.

**Medidas de seguridad implementadas:**
*   **Validación estricta:** Todos los campos (nombre, email, teléfono, mensaje) se validan en el servidor.
*   **Honeypot:** Campo oculto para detectar y bloquear bots automáticos.
*   **Rate Limiting:** Control de frecuencia que bloquea envíos sucesivos desde la misma sesión (30 segundos de espera) para evitar spam/flooding.
*   **Protección CSRF:** Uso de tokens para garantizar que la petición proviene del sitio legítimo.

**Sistema de Logs (`storage/logs/contacto.log`):**
Cada envío genera una entrada en `storage/logs/contacto.log` donde se registra la fecha y hora, la IP, el estado del intento (`FORM_OK`, `VALIDATION_ERROR`, `EMAIL_SENT`, `AUTO_REPLY_SENT`, `SMTP_ERROR`, etc.) y datos básicos de contexto (como email, teléfono o la referencia del inmueble asociado).

Esto permite auditar el funcionamiento del formulario, detectar patrones de uso o errores y analizar posibles intentos de abuso, sin necesidad de una tabla específica en base de datos en esta primera versión del proyecto.

### 3.3.12. Estabilización y Correcciones (Hotfixes)

Durante la fase de integración final se detectaron y resolvieron incidencias críticas para garantizar la estabilidad del entorno de producción:

*   **Tipado Estricto (stdClass vs Array):** Se corrigió la incompatibilidad en la gestión de objetos devueltos por `PDO::fetchObject` vs arrays asociativos en el controlador de contacto.
*   **Renderizado de Plantillas de Email:** Ajuste en el búfer de salida (`ob_start` / `ob_get_clean`) en `MailService` para evitar la impresión de código PHP crudo en los correos y asegurar la correcta interpretación de HTML.
*   **Sanitización en Vistas:** Estandarización del uso de `htmlspecialchars()` en lugar de helpers no definidos (`e()`), previniendo errores fatales en tiempo de ejecución.


