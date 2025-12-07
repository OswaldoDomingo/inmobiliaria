# Módulo Inmuebles

## Objetivo
Gestión completa de inmuebles en backoffice (CRUD) y publicación en parte pública (listado + ficha), aplicando seguridad básica (PDO + CSRF + escape).

## Rutas

### Admin
- GET  /admin/inmuebles → listado + filtros + paginación
- GET  /admin/inmuebles/nuevo → formulario alta
- POST /admin/inmuebles/guardar → alta (CSRF)
- GET  /admin/inmuebles/editar?id=ID → formulario edición
- POST /admin/inmuebles/actualizar → edición (CSRF)
- POST /admin/inmuebles/borrar → borrado (CSRF)

Permisos: admin/coordinador.

### Público
- GET /inmuebles → listado solo estado='Publicado'
- GET /inmuebles/ver?ref=REF → ficha solo si estado='Publicado'

## Validaciones
- ref obligatoria, máx 20, única.
- título obligatorio, máx 255.
- tipo: Piso|Casa|Local|Terreno|Nave|Otro
- estado: Captacion|Publicado|Reservado|Vendido|Retirado
- flags venta/alquiler/vacacional 0/1 + coherencia con precios.
- CP: string, si se valida: 5 dígitos.
- propietario_id obligatorio y existente.

## Base de datos
Tabla: inmuebles (PK id_inmueble, ref UNIQUE).
Relaciones:
- propietario_id → clientes.id_cliente (ON DELETE RESTRICT)
- usuario_id → usuarios.id_usuario (ON DELETE SET NULL)

## Seguridad
- PDO + consultas preparadas.
- Escape con htmlspecialchars en todas las vistas.
- CSRF en todos los POST del admin.

## Módulo Inmuebles — Intento de implementación y bloqueo técnico (07/12/2025)

### Contexto y finalidad
Se inició la implementación del módulo **Inmuebles** dentro del portal inmobiliario. El objetivo del módulo es:
- Permitir la **gestión interna (CRUD)** de inmuebles desde el área privada.
- Publicar un **catálogo público** de inmuebles “publicables” (según criterios de estado/visibilidad).

Además, se definió una regla de negocio clave: **cada inmueble pertenece a un cliente/propietario** y cada cliente está asignado a un **comercial**. Por tanto, si un cliente cambia de comercial, los inmuebles asociados deben pasar a ser gestionados por el nuevo comercial y reflejarse en su listado de trabajo.

### Implementación desarrollada
Se construyó una primera versión del módulo alineada con el esquema real de base de datos, incorporando:

- **Modelo `Inmueble`**
  - Mapeo de columnas reales (ej.: `ref`, `propietario_id`, `comercial_id`, `direccion`, `localidad`, `provincia`, `cp`, `tipo`, `operacion`, `precio`, `estado`, `activo`, `archivado`, `fecha_alta`, etc.).
  - Métodos principales: CRUD, búsqueda por `ref` y paginación (admin/público).

- **Controladores**
  - Controlador **administrativo**: listado, alta, edición y borrado, con validaciones.
  - Controlador **público**: listado y ficha por referencia (`ref`).

- **Vistas**
  - Vistas del área admin para listado y formulario.
  - Preparación de vistas públicas (listado/ficha) según el filtro de publicación.

- **Rutas**
  - Registro de rutas del módulo en el front controller (`public/index.php`) para el área admin y pública.

### Dificultad principal encontrada (bloqueante)
El bloqueo actual **no** está en base de datos ni en sintaxis PHP (se verificó el esquema y los archivos no presentan errores), sino en **routing/acceso a rutas**:

- La ruta de creación de inmuebles (`/admin/inmuebles/nuevo`) devuelve **404 Not Found**, impidiendo acceder al formulario y completar el flujo de alta.
- Se intentó ajustar el Router para normalizar rutas y gestionar posibles prefijos/basePath, pero el problema **persistió**.
- Se decidió **no seguir avanzando** con cambios estructurales hasta aislar el origen exacto del fallo, para no comprometer la estabilidad del proyecto.

### Lecciones aprendidas y criterios aplicados
- Se priorizó la coherencia con el **esquema real** de la base de datos y el cumplimiento de buenas prácticas (validación, seguridad y estructura MVC).
- Se evitó subir cambios **no funcionales** al repositorio, manteniendo un control de calidad mínimo antes de integrar.
- Se documentó el estado actual y los siguientes pasos para retomar el trabajo con contexto claro, siguiendo un enfoque similar a un entorno profesional.

### Trabajo pendiente
1. **Resolver el 404 de routing**
   - Verificar que la URL llega al front controller (`public/index.php`) mediante configuración del servidor (DocumentRoot y reglas rewrite).
   - Confirmar que el Router recibe la ruta normalizada y que existe match con las rutas registradas.

2. **Añadir accesos visibles en interfaz**
   - Incluir enlaces/botones en el panel de administración para acceder al listado y a la creación de inmuebles (por ejemplo desde dashboard o desde clientes).

3. **Cerrar la regla de negocio “comercial”**
   - Definir e implementar el comportamiento final:
     - Opción A: el inmueble hereda el comercial del cliente de forma automática (dato derivado).
     - Opción B: el inmueble permite asignación manual y se sincroniza cuando cambia el comercial del cliente.

### Desbloqueo y Avances (07/12/2025)

#### Resolución del Bloqueo Técnico
Se ha resuelto el problema de routing que impedía el acceso a `/admin/inmuebles/nuevo`.
- **Causa:** La normalización de rutas en `Router.php` no gestionaba correctamente los separadores de directorio en entorno Windows (`\`) al calcular el `scriptDir`, lo que provocaba que el prefijo del directorio no se eliminara correctamente de la URI, fallando el matching de rutas.
- **Solución:** Se normalizaron los separadores a `/` antes de procesar la URI en `Router::dispatch`.
- **Corrección adicional:** Se solucionó un problema de redirección incorrecta causado por el uso de claves de sesión inconsistentes (`user_id` vs `id_usuario`) en `InmuebleController` y `DemandaController`.

#### Nuevas Funcionalidades Implementadas

1.  **Integración en Ficha de Cliente**
    - Se ha añadido una sección "Inmuebles de este cliente" en la vista de edición de clientes (`admin/clientes/edit.php`).
    - Permite ver rápidamente el inventario de un propietario y acceder a la edición de sus inmuebles.
    - Botón directo "➕ Añadir inmueble" que pre-selecciona al propietario en el formulario de alta.

2.  **Formulario de Inmuebles (Mejoras)**
    - **Maquetación:** Rediseño completo con Bootstrap 5 (Grid, Cards, Feedback visual de validación).
    - **Lógica de Comercial:**
        - **Admin/Coordinador:** Pueden asignar el inmueble a cualquier usuario activo (Admin, Coordinador, Comercial).
        - **Comercial:** Al crear/editar, el campo "Comercial" aparece bloqueado (read-only) y se auto-asigna a sí mismo.
    - **Correcciones:** Solucionados errores de "Undefined variable" y acceso a objetos `stdClass` como arrays.

3.  **Gestión de Permisos**
    - Se ha habilitado el acceso a los controladores `InmuebleController` y `DemandaController` para el rol `comercial` (antes restringido a admin/coordinador).

### Estado final
El módulo de inmuebles está **DESBLOQUEADO y FUNCIONAL**. Se permite el flujo completo de alta, edición y listado, integrando la lógica de negocio para la asignación de comerciales y la visualización desde la ficha de cliente.

