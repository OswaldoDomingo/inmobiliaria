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
- GET /propiedades → listado solo inmuebles activos (activo=1, estado='activo', archivado=0)
- GET /propiedades/ver?id=ID → ficha solo si inmueble está activo

## Validaciones
- ref obligatoria, máx 20, única.
- título obligatorio, máx 255.
- tipo: Piso|Casa|Chalet|Adosado|Duplex|Local|Oficina|Terreno|Otros
- estado: borrador|activo|reservado|vendido|retirado
- operacion: venta|alquiler|vacacional
- flags activo/archivado 0/1
- CP: string, si se valida: 5 dígitos.
- propietario_id obligatorio y existente.

## Base de datos
Tabla: inmuebles (PK id_inmueble, ref UNIQUE).
Relaciones:
- propietario_id → clientes.id_cliente (ON DELETE RESTRICT)
- comercial_id → usuarios.id_usuario (ON DELETE SET NULL)

## Seguridad
- PDO + consultas preparadas.
- Escape con htmlspecialchars en todas las vistas.
- CSRF en todos los POST del admin.

## Listados públicos de inmuebles (09/12/2025)

### Contexto
Se ha implementado la parte pública del módulo de inmuebles para permitir que usuarios no autenticados puedan consultar el catálogo de propiedades disponibles y acceder a fichas detalladas.

### Arquitectura

#### Rutas
- **GET /propiedades**: Listado público de inmuebles con paginación y filtros
- **GET /propiedades/ver?id=ID**: Ficha pública de un inmueble específico

Las rutas son públicas (no requieren autenticación) y están definidas en `public/index.php`.

#### Controlador
Se utiliza `InmueblePublicController` con dos métodos principales:

- **`index()`**:
  - Lee parámetros de filtro desde `$_GET` (localidad, tipo, operación)
  - Llama a `Inmueble::paginatePublic()` con paginación de 10 elementos por página
  - Carga las vistas: header, listado y footer

- **`show()`**:
  - Recibe parámetro `id` desde `$_GET`
  - Valida que el inmueble existe y está activo
  - Verifica condiciones: `activo = 1 AND estado = 'activo' AND archivado = 0`
  - Devuelve 404 si el inmueble no cumple los criterios
  - Carga las vistas: header, ficha y footer

#### Modelo
El método `Inmueble::paginatePublic()`:
- Filtra automáticamente inmuebles con `activo = 1`, `estado = 'activo'` y `archivado = 0`
- Aplica filtros adicionales de búsqueda (localidad, tipo, operación, precio)
- Devuelve datos paginados con total de resultados

#### Vistas
Las vistas se encuentran en `app/views/propiedades/`:

- **`index.php`** (Listado):
  - Formulario de búsqueda con filtros (localidad, tipo, operación)
  - Tarjetas de inmuebles mostrando:
    - Imagen principal (o placeholder si no existe)
    - Título (tipo + operación)
    - Ubicación (localidad, provincia)
    - Precio formateado
    - Referencia
    - Descripción breve (máx 120 caracteres)
    - Características: superficie (m²), habitaciones, baños
    - Botones: "Más información" y "Contactar"
  - Paginación con anterior/siguiente y números de página
  - Responsive con Bootstrap 5

- **`show.php`** (Ficha detalle):
  - Breadcrumb de navegación
  - Imagen principal grande
  - Título y ubicación completa
  - Precio destacado en sidebar sticky
  - Sección de características con iconos (superficie, habitaciones, baños)
  - Descripción completa
  - Lista de detalles (referencia, tipo, operación, dirección, localidad, provincia, CP)
  - Botones de contacto (contactar, llamar, WhatsApp)
  - Botón "Volver al listado"

### Decisiones de diseño

1. **Paginación**: 10 inmuebles por página para optimizar la carga y mejorar la experiencia de usuario
2. **Imágenes**: Se muestra la imagen principal del inmueble desde `/uploads/inmuebles/`, con fallback a placeholder si no existe
3. **Filtros**: Se mantienen los filtros activos en la URL mediante query string para facilitar la navegación
4. **Seguridad**: Solo se muestran inmuebles que cumplan todos los criterios de publicación (activo, no archivado, estado activo)
5. **Enlaces**: Tanto la imagen como el botón "Más información" enlazan a la ficha, mejorando la usabilidad
6. **Contacto**: El botón "Contactar" apunta a `/tasacion` como punto de contacto centralizado

### Integración con el menú
El enlace "Propiedades" en el header principal (`app/views/layouts/header.php`) apunta a `/propiedades`, facilitando el acceso desde cualquier página del sitio.

### Mejoras futuras
- Implementación de galería de imágenes en la ficha
- Búsqueda avanzada con rangos de precio
- Mapa de ubicación con geolocalización
- Compartir en redes sociales
- Inmuebles destacados/recomendados
- Filtro por número de habitaciones y baños

---

## Módulo Inmuebles — Intento de implementación y bloqueo técnico (07/12/2025)

### Contexto y finalidad
Se inició la implementación del módulo **Inmuebles** dentro del portal inmobiliario. El objetivo del módulo es:
- Permitir la **gestión interna (CRUD)** de inmuebles desde el área privada.
- Publicar un **catálogo público** de inmuebles "publicables" (según criterios de estado/visibilidad).

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

3. **Cerrar la regla de negocio "comercial"**
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
un comercial podía acceder, mediante URL directa o manipulando el formulario, a inmuebles de otros comerciales e incluso "quedárselos" cambiando el propietario.

Para respetar la regla de negocio **"cada comercial solo gestiona los inmuebles de sus clientes"**, se han aplicado las siguientes medidas:

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
- Se evita que un comercial pueda ver, editar o "robar" inmuebles de otros compañeros.
- Se refuerza el principio de **Security by Design** en uno de los módulos más críticos del panel de administración.

### Público

- GET  /propiedades  
  Listado público de inmuebles activos.  
  Solo se muestran inmuebles con:
  - `activo = 1`
  - `archivado = 0`
  - `estado` en un conjunto “publicable” (por ejemplo `activo`, `reservado`), nunca `vendido` ni `retirado`.

- GET  /propiedades/ver?id=ID  
  Ficha pública de un inmueble concreto.  
  Solo es accesible si el inmueble cumple las condiciones anteriores (activo/no archivado/publicable).  
  Si el inmueble no existe o no es publicable, se devuelve un 404.
## Front público de propiedades (09/12/2025)

### Objetivo

Exponer en la parte pública de la web un catálogo de inmuebles “publicables”, con paginación y acceso a una ficha detallada, reutilizando el modelo `Inmueble` y respetando las reglas de visibilidad definidas en el backoffice.

### Arquitectura

- **Rutas públicas**
  - `GET /propiedades` → listado público con paginación.
  - `GET /propiedades/ver?id=ID` → ficha de inmueble.

- **Controlador**
  - `InmueblePublicController`:
    - `index()`:
      - Lee parámetros de filtro básicos (localidad, tipo, operación, página).
      - Llama a un método del modelo (ej. `Inmueble::paginatePublic(...)`) que:
        - filtra por `activo = 1`, `archivado = 0` y estados publicables,
        - aplica filtros de búsqueda,
        - devuelve los resultados paginados (10 por página).
    - `show(int $id)`:
      - Busca el inmueble por `id_inmueble`.
      - Verifica que es publicable (`activo = 1`, `archivado = 0`, estado permitido).
      - Si no cumple, responde con 404.

- **Vistas**
  - `app/views/propiedades/index.php`:
    - Muestra un listado de tarjetas de inmuebles (máx. 10 por página).
    - Cada tarjeta incluye:
      - Imagen principal (o placeholder).
      - Precio destacado.
      - Localidad / provincia.
      - Superficie (m²), habitaciones y baños (si existen).
      - Extracto de la descripción.
      - Botón **“Más información”** que enlaza a la ficha (`/propiedades/ver?id=...`).
      - Botón **“Contactar”** que enlaza al formulario de tasación/contacto (`/tasacion`).
    - Incluye paginador (Anterior / Siguiente y número de página).

  - `app/views/propiedades/show.php`:
    - Muestra la ficha completa del inmueble:
      - Imagen principal en grande.
      - Título con dirección/localidad.
      - Precio destacado.
      - Detalles: tipo, operación, superficie, habitaciones, baños, estado, referencia interna, etc.
      - Descripción completa.
      - Zona/localidad/código postal.
    - Columna lateral con:
      - Precio.
      - Datos de contacto de la agencia (en esta fase, genérico).
      - Botones de acción (“Contactar”, “Volver al listado”).
    - Si el inmueble no es publicable, devuelve 404.

### Reglas de negocio aplicadas

- Solo se muestran inmuebles:
  - `activo = 1`
  - `archivado = 0`
  - `estado` distinto de `vendido` o `retirado`.
- El front público **no** muestra información interna sensible (propietario, comercial, notas, etc.), solo datos pensados para el usuario final.
- La cantidad por página se fija en **10 inmuebles**, buscando un equilibrio entre rendimiento y experiencia de usuario, especialmente en móvil.

### Seguridad y buenas prácticas

- Todas las salidas en las vistas se escapan con `htmlspecialchars`.
- El parámetro `id` de la ficha se valida como entero; si no es válido o no existe, se devuelve 404.
- La parte pública no requiere autenticación, pero se apoya en la misma capa de modelo/DAO que el backoffice, evitando duplicar lógica de acceso a datos.

### Relación con el backoffice

- El estado de publicación se controla únicamente desde el panel de administración:
  - Un administrador/coordinador decide qué inmuebles están “activos” y no archivados.
  - El comercial gestiona la ficha y el contenido; la publicación en web es consecuencia de esos flags.
- La estructura sigue el patrón MVC del proyecto:
  - Router → `InmueblePublicController` → `Inmueble` → vistas en `app/views/propiedades`.
