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

Permisos: admin/coordinador (gestión completa) y comercial (solo inmuebles de su cartera de clientes).

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

## Mejora de navegación: Return Path en edición de inmuebles

### Problema detectado

En la primera versión del módulo, al acceder a la edición de un inmueble desde la ficha de un cliente, los botones **«Volver»** y la redirección tras **«Guardar inmueble»** apuntaban siempre al listado general `/admin/inmuebles`.  
Esto obligaba al usuario a salir del contexto del cliente, localizarlo de nuevo en el CRM y suponía un flujo poco natural para el trabajo diario de la agencia.

### Objetivo funcional

Conseguir que, cuando la edición de un inmueble se inicia desde la ficha de un cliente, todas las acciones de salida (volver, cancelar, guardar con éxito) lleven de vuelta a esa misma ficha, manteniendo:

- Seguridad frente a redirecciones manipuladas.
- Compatibilidad con el flujo clásico cuando la edición se inicia desde el listado de inmuebles.

### Implementación técnica

1. **Parámetro `return_to` en enlaces de edición**
   - En `admin/clientes/edit.php` se genera el enlace a la edición del inmueble con un parámetro `return_to` que contiene la URL actual de la ficha de cliente (`/admin/clientes/editar?id=...`), codificada con `urlencode()`.

2. **Validación de `return_to` en el controlador**
   - En `InmuebleController` se añade un método privado `validateReturnTo(?string $url): ?string` que:
     - Acepta únicamente rutas internas que comienzan por `/admin/`.
     - Rechaza cualquier URL que contenga un esquema externo o doble barra inicial (`http://`, `https://`, `//`).
     - Devuelve `null` si la ruta no es válida.
   - Los métodos `edit()` y `update()` utilizan este helper para obtener un `$returnTo` seguro.

3. **Persistencia en el formulario**
   - La vista `app/Views/admin/inmuebles/form.php` incluye, cuando procede, un campo oculto:
     ```php
     <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
     ```
   - De esta forma, el valor se mantiene entre peticiones POST incluso si hay errores de validación.

4. **Botones de navegación y redirección tras el guardado**
   - Se calcula una URL de retorno:
     ```php
     $returnUrl = $returnTo ?: '/admin/inmuebles';
     ```
   - Los botones **«Volver»** y **«Cancelar»** usan siempre `$returnUrl`.
   - En caso de éxito al actualizar (`update()`), se construye la URL de redirección final con un helper `addQueryParam(string $url, string $key, string $value)`, que añade `msg=updated` respetando la query string existente.

5. **Gestión de errores de validación**
   - Si hay errores, no se redirige:
     - Se vuelve a cargar la vista del formulario con los mensajes de error.
     - Se vuelve a pasar `$returnTo` a la vista para no perder el origen.
   - Esto evita corrupciones de datos y permite al usuario corregir el formulario sin perder el contexto.

### Problemas y cómo se han resuelto

- **Riesgo de open redirect:**  
  Aceptar un `return_to` arbitrario abría la puerta a redirecciones externas.  
  → Se ha mitigado limitando las rutas a `/admin/*` y bloqueando explícitamente cualquier URL con esquema o doble barra inicial.

- **Compatibilidad con flujos existentes:**  
  La edición desde `/admin/inmuebles` no debe depender de `return_to`.  
  → Se ha establecido un *fallback* claro: si no hay `return_to` válido, se usa siempre `/admin/inmuebles`.

- **Conservación de contexto con errores:**  
  Si el formulario daba error y redirigía, se perdían tanto los datos como el origen.  
  → Ahora se re-renderiza el formulario con los errores, manteniendo campos y `return_to`.

### Pruebas realizadas

Se ha verificado el comportamiento con los tres roles de la aplicación:

- **Administrador y Coordinador**
  - Edición de inmuebles desde ficha de cliente → tras guardar o pulsar «Volver» se regresa a la ficha del cliente con el mensaje `msg=updated`.
  - Edición desde el listado general → comportamiento idéntico al original (vuelta al listado).

- **Comercial**
  - Puede editar los inmuebles que le corresponden.
  - Navegación de retorno funciona igual que para admin/coordinador, respetando el contexto de cliente.

- **Pruebas de seguridad**
  - Manipulación manual del parámetro `return_to` con URLs externas o rutas no válidas → el sistema ignora el valor y redirige al listado `/admin/inmuebles`.

Con esta mejora, el módulo de inmuebles ofrece una navegación mucho más coherente con el flujo real de trabajo de la agencia, reduciendo clics innecesarios y manteniendo un nivel adecuado de seguridad en el manejo de URLs de retorno.

Adicionalmente, se ha creado el documento `docs/verificacion_return_path.md` donde se describe de forma detallada el plan de pruebas, los casos ejecutados (incluyendo pruebas específicas de persistencia del `return_to` tras errores de validación) y los resultados obtenidos.  
Durante estas pruebas se confirmó también el comportamiento de seguridad frente a intentos de redirección externa, verificando que el sistema realiza siempre un fallback seguro cuando el `return_to` no supera la validación.

## Control de seguridad por roles en Inmuebles (Actualización 08/12/2025)

En la primera versión del CRUD de inmuebles se detectó un problema grave:  
un comercial podía acceder, mediante URL directa o manipulando el formulario, a inmuebles de otros comerciales e incluso “quedárselos” cambiando el propietario.

Para respetar la regla de negocio **“cada comercial solo gestiona los inmuebles de sus clientes”**, se han aplicado las siguientes medidas:

### 1. Filtrado en el modelo (`Inmueble::paginateAdmin()`)

- Nueva firma: `paginateAdmin(int $userId, string $rol, array $filtros, int $page, int $perPage)`.
- Comportamiento:
  - **Admin / Coordinador** → sin filtro por comercial (ven todos los inmuebles).
  - **Comercial** → se añade JOIN con `clientes` y condición `clientes.usuario_id = :userId`, de forma que solo se devuelven inmuebles de su cartera.

### 2. Validaciones en el controlador (`InmuebleController`)

- `index()`:
  - Obtiene `userId` y `rol` desde la sesión y los pasa al modelo.
- `create()/store()`:
  - Admin/Coordinador → `<select>` de propietarios con todos los clientes.
  - Comercial → `<select>` limitado a **sus** clientes.
  - En servidor se valida que el `cliente_id` enviado pertenece al comercial logueado, incluso si manipula el HTML desde las herramientas de desarrollo.
- `edit()/update()`:
  - Carga el inmueble con el cliente asociado.
  - Solo permite editar si `clientes.usuario_id === userId` (para comerciales).
  - Si se intenta cambiar el propietario a un cliente ajeno, el sistema devuelve error de permiso y no guarda cambios.

### 3. Resultado

- Cada comercial trabaja únicamente con la cartera de inmuebles de sus clientes.
- Se evita que un comercial pueda ver, editar o “robar” inmuebles de otros compañeros.
- Se refuerza el principio de **Security by Design** en uno de los módulos más críticos del panel de administración.
