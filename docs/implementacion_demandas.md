# Implementación Completada: Módulo Demandas (CRM de necesidades de clientes)

## 🎯 Objetivo

Implementar el módulo completo de **Demandas** en el área de administración, permitiendo registrar las necesidades de compra/alquiler de los clientes, con:

- CRUD completo (alta, edición, listado, borrado).  
- Control de permisos por rol (admin, coordinador, comercial).  
- Integración directa en la ficha de cliente.  
- Uso de un campo **JSON** para características adicionales.

---

## ✅ Archivos creados / modificados

### 1. Modelo

- ✅ `app/Models/Demanda.php` (NUEVO)  
  - Conecta con la tabla `demandas` mediante `Database::conectar()`.  
  - Mapea los campos principales:
    - `id_demanda` (PK, autoincremental).  
    - `cliente_id` (FK → `clientes.id_cliente`).  
    - `comercial_id` (FK → `usuarios.id_usuario`).  
    - `tipo_operacion` (`compra`, `alquiler`, `vacacional`).  
    - `rango_precio_min`, `rango_precio_max`.  
    - `superficie_min`, `habitaciones_min`, `banos_min`.  
    - `zonas` (TEXT).  
    - `caracteristicas` (JSON).  
    - `estado` (`activa`, `en_gestion`, `pausada`, `archivada`).  
    - Flags `activo`, `archivado`, `fecha_alta`, `fecha_archivado`.
  - Métodos principales:
    - `paginateAdmin(int $userId, string $rol, array $filtros, int $page, int $perPage)`:  
      - Admin / Coordinador → ven todas las demandas.  
      - Comercial → solo demandas de clientes asignados a ese comercial.
    - `findById(int $id)`: obtiene una demanda concreta.
    - `getByCliente(int $clienteId)`: devuelve todas las demandas de un cliente.
    - `create(array $data)`: inserta una nueva demanda.
    - `update(int $id, array $data)`: actualiza una demanda existente.
    - `delete(int $id)`: realiza borrado físico; las FKs en BD se encargan de la integridad (por ejemplo, borrado en cascada si aplica).
  - Gestión del campo JSON:
    - Al guardar:  
      ```php
      $stmt->bindValue(':caracteristicas', json_encode($data['caracteristicas'] ?? [], JSON_UNESCAPED_UNICODE));
      ```
    - Al leer:  
      ```php
      $row['caracteristicas'] = json_decode($row['caracteristicas'] ?? '[]', true) ?? [];
      ```

### 2. Controlador

- ✅ `app/Controllers/DemandaController.php` (REESCRITO)

  - **Dependencias inyectadas:**
    - `Demanda` (módulo actual).
    - `Cliente` (para comprobar permisos y heredar comercial).
    - `User` (listado de comerciales para filtros y selects).

  - **Helpers reutilizados (patrón `InmuebleController`):**
    - `currentUserId()`, `currentUserRole()`, `isAdminOrCoordinador()`.
    - `requireAuth()`, `requireRole()`, `ensurePost()`.
    - `csrfToken()`, `csrfValidate()`.
    - `validateReturnTo()`, `addQueryParam()`.
    - `getClientesDelComercial(int $comercialId)`:
      - Devuelve solo los clientes cuyo `usuario_id` coincide con el comercial logueado.

  - **Acciones implementadas:**

    - `index()`
      - Aplica filtros desde `$_GET`:
        - `tipo_operacion`, `estado`, `comercial_id`, `precio_min`, `precio_max`.
      - Llama a `Demanda::paginateAdmin()` para obtener el listado paginado.
      - Pasa a la vista:
        - Lista de demandas.
        - Datos de paginación.
        - Filtros activos.
        - Listado de comerciales (solo para admin/coordinador).

    - `create()`
      - Lee `cliente_id` y `return_to` de `$_GET`.
      - Si se recibe `cliente_id`, carga el cliente y comprueba permisos:
        - Admin/Coordinador → acceso total.
        - Comercial → solo si `cliente.usuario_id` coincide con el usuario logueado.
      - Prepara:
        - Cliente (si aplica).
        - Listado de clientes:
          - Admin/Coordinador → todos.
          - Comercial → solo sus clientes (`getClientesDelComercial()`).
      - Genera token CSRF y pasa a la vista `admin/demandas/form.php`.

    - `store()`
      - `ensurePost()` + `csrfValidate()`.
      - Recupera `cliente_id` y carga el cliente asociado.
      - **Control de permisos:**
        - Si rol = `comercial` → debe coincidir `cliente.usuario_id === currentUserId()`.
      - Validaciones:
        - `tipo_operacion` ∈ {compra, alquiler, vacacional}.
        - `rango_precio_min` ≤ `rango_precio_max` (cuando ambos existen).
        - Campos numéricos no negativos (`superficie_min`, `habitaciones_min`, `banos_min`).
      - Construye el array `caracteristicas` desde checkboxes.
      - Asigna `comercial_id` heredándolo del cliente:
        ```php
        $data['comercial_id'] = $cliente->usuario_id ?? null;
        ```
      - Si hay errores:
        - Re-renderiza `form.php` con `$errors` y `$old`.
      - Si todo es correcto:
        - Llama a `$this->demandaModel->create($data)`.
        - Redirige a:
          - `return_to` (si es válido) **o**
          - `/admin/demandas?msg=created`.

    - `edit(int $id)`
      - Carga la demanda (`findById`).
      - Carga el cliente asociado.
      - Comprueba permisos iguales a `store()`:
        - Comercial solo si el cliente es suyo.
      - Prepara `return_to`, listas de clientes y comerciales y renderiza el formulario en modo edición.

    - `update(int $id)`
      - Mismo patrón que `store()`:
        - POST + CSRF.
        - Carga demanda + cliente.
        - Validación de permisos.
        - Misma lógica de validaciones y de cálculo de `caracteristicas`.
        - `comercial_id` heredado del cliente.
      - Si OK → `update()` en el modelo y redirección:
        - Preferentemente a `return_to` (ficha de cliente) con `msg=updated`.

    - `delete(int $id)`
      - Solo accesible vía POST.
      - Solo para roles `admin` y `coordinador`.
      - Aplica CSRF, llama a `Demanda::delete($id)` y redirige a `/admin/demandas?msg=deleted`.

### 3. Vistas

- ✅ `app/Views/admin/demandas/index.php` (NUEVO)

  - Cabecera con título **“Demandas”** y botón **“+ Nueva demanda”**.
  - Formulario de filtros (método GET):
    - Tipo de operación.
    - Estado.
    - Comercial (solo para admin/coordinador).
    - Precio mínimo y máximo.
  - Tabla con columnas:
    - Cliente, Comercial, Tipo operación, Precio min–max, Superficie mínima, Habitaciones mínimas, Estado, Fecha alta, Acciones.
  - Acciones por fila:
    - Editar.
    - Borrar (formulario POST con CSRF, solo admin/coordinador).
  - Paginación reutilizando el componente existente, preservando filtros en los enlaces.

- ✅ `app/Views/admin/demandas/form.php` (NUEVO)

  - Soporta modo **alta** y **edición**.
  - Dos escenarios de cliente:
    - Desde ficha de cliente:
      - Campo visible en `readonly` con nombre del cliente.
      - `cliente_id` oculto.
    - Desde listado de demandas:
      - `select` con clientes:
        - Admin/Coordinador → todos.
        - Comercial → solo los suyos.
  - Campos:
    - Tipo de operación: `select` o radios (`compra`, `alquiler`, `vacacional`).
    - Rango de precio:
      - `<input type="number" step="1" min="0" name="rango_precio_min">`
      - `<input type="number" step="1" min="0" name="rango_precio_max">`
    - `superficie_min`, `habitaciones_min`, `banos_min`.
    - `zonas` (textarea).
    - Checkboxes de características (mapeados a un array para el JSON):
      - `garaje`, `piscina`, `ascensor`, `terraza`, `amueblado`, `trastero`, `jardin`, etc.
    - Estado: `select` (`activa`, `en_gestion`, `pausada`, `archivada`).
  - Campos ocultos:
    - `csrf_token`
    - `return_to`
    - `id` (en edición)
  - Muestra mensajes de error junto a cada campo y recupera los valores anteriores (`$old`) en caso de validación fallida.

### 4. Integración con Clientes

- ✅ `app/Controllers/ClienteController.php` (MODIFICADO)
  - Propiedad nueva:
    ```php
    private Demanda $demandaModel;
    ```
  - En el constructor se instancia el modelo `Demanda`.
  - En el método `edit()`:
    ```php
    $demandasCliente = $this->demandaModel->getByCliente($id);
    ```
  - Se pasa `$demandasCliente` a la vista.

- ✅ `app/Views/admin/clientes/edit.php` (MODIFICADO)

  - Botón “➕ Añadir demanda” ajustado para incluir `cliente_id` + `return_to`:
    - `/admin/demandas/nueva?cliente_id={id_cliente}&return_to=/admin/clientes/editar?id={id_cliente}`.
  - Nueva sección:
    - Título “Demandas de este cliente”.
    - Tabla con:
      - Tipo, Rango de precios, Superficie mínima, Estado, Fecha, Acción “Editar”.
    - El botón “Editar” también conserva `return_to` para volver a la ficha del cliente tras guardar.

### 5. Rutas y navegación

- ✅ `public/index.php` (MODIFICADO)
  - Registro de rutas:
    ```php
    $router->get('/admin/demandas', [DemandaController::class, 'index']);
    $router->get('/admin/demandas/nueva', [DemandaController::class, 'create']);
    $router->post('/admin/demandas/guardar', [DemandaController::class, 'store']);
    $router->get('/admin/demandas/editar', [DemandaController::class, 'edit']);
    $router->post('/admin/demandas/actualizar', [DemandaController::class, 'update']);
    $router->post('/admin/demandas/borrar', [DemandaController::class, 'delete']);
    ```

- ✅ `app/Views/admin/dashboard.php` (MODIFICADO)
  - Añadido acceso directo al módulo Demandas junto al resto de módulos del panel.

---

## 🧪 Plan de Pruebas (resumen)

1. **Alta de demanda desde ficha de cliente (Admin)**
   - Ir a `/admin/clientes`, editar un cliente.
   - Click en “➕ Añadir demanda”.
   - Rellenar datos y guardar.
   - **Esperado:** vuelve a la ficha del cliente con mensaje de éxito y la demanda aparece en la tabla.

2. **Alta desde listado global con error de validación**
   - Ir a `/admin/demandas`, pulsar “+ Nueva demanda”.
   - Seleccionar cliente, poner `precio_min > precio_max`.
   - **Esperado:** no redirige, muestra error y mantiene los datos en el formulario.

3. **Control por rol (Comercial)**
   - Login como comercial.
   - En `/admin/demandas` solo se ven demandas de clientes asignados a ese comercial.
   - Intentar acceder a `/admin/demandas/editar?id=X` de un cliente ajeno.  
   - **Esperado:** error/denegación de acceso.

4. **Integración con ficha de cliente**
   - Con un cliente que tenga varias demandas, entrar a `/admin/clientes/editar`.
   - Ver la tabla de “Demandas de este cliente”.
   - Editar una demanda, cambiar algún dato y guardar.
   - **Esperado:** vuelve a la ficha del cliente y refleja los cambios.

5. **Campo JSON `caracteristicas`**
   - Crear una demanda marcando varias características (ej: garaje + piscina).
   - Verificar en BD que se guarda un JSON tipo `["garaje","piscina"]`.
   - Editar la demanda, desmarcar/añadir otra característica.
   - **Esperado:** JSON actualizado correctamente y checkboxes coherentes en el formulario.

---

## 📊 Resumen técnico

- Módulo alineado con la arquitectura MVC del proyecto.  
- Control de roles coherente con el resto del CRM.  
- JSON utilizado para extender características sin alterar el esquema de forma rígida.  
- Integración directa con la ficha de cliente y navegación con `return_to`.  

**Implementación completada el:** 2025-12-08   
**Versión del proyecto:** Inmobiliaria v1.0
