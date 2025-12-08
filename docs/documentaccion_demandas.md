# Módulo Demandas

## 1. Objetivo

El módulo de **Demandas** permite registrar las necesidades de búsqueda de inmuebles de cada cliente. No se trata de inmuebles concretos, sino de “lo que está buscando” la persona (rango de precio, zonas, superficie mínima, características deseadas, etc.).

Este módulo:

- Enlaza cada demanda con un cliente del CRM.  
- Asigna automáticamente el comercial responsable de esa demanda.  
- Permite a la agencia saber qué clientes buscan qué tipo de inmueble y priorizar el trabajo comercial.  

Es la base para un futuro **matching automático** entre `demandas` e `inmuebles`.

---

## 2. Relación con otros módulos

- **Clientes**:  
  - Un cliente puede tener **0..N demandas**.  
  - Cada demanda pertenece a un único cliente.
- **Usuarios (Comerciales)**:  
  - El campo `comercial_id` de la demanda se hereda del `usuario_id` del cliente.  
  - Esto asegura que cada demanda está ligada al comercial que lleva al cliente.
- **Inmuebles** (futuro):  
  - La tabla `cruces` permitirá relacionar demandas con inmuebles concretos.  
  - El módulo actual deja preparada esta base.

---

## 3. Rutas del módulo

El módulo Demandas es solo de backoffice (no tiene parte pública).

### Admin / CRM

- **Listado general de demandas**
  - `GET /admin/demandas`
  - Muestra todas las demandas (filtradas según rol).

- **Alta de demanda**
  - `GET /admin/demandas/nueva`
  - `POST /admin/demandas/guardar`

- **Edición de demanda**
  - `GET /admin/demandas/editar?id=ID_DEMANDA`
  - `POST /admin/demandas/actualizar`

- **Borrado de demanda**
  - `POST /admin/demandas/borrar`

Permisos:

- **Admin / Coordinador**: pueden ver, crear, editar y borrar demandas de cualquier cliente.  
- **Comercial**: solo puede ver/crear/editar demandas de los **clientes que tiene asignados**; no puede borrar si no se le concede en el futuro.

---

## 4. Flujo funcional

### 4.1 Alta desde la ficha de cliente

1. El usuario entra en **Clientes** → `Editar` un cliente.
2. En la ficha aparece la sección **“Demandas de este cliente”** y el botón **“➕ Añadir demanda”**.
3. Al pulsarlo:
   - Se abre `/admin/demandas/nueva` con el cliente preseleccionado.
   - El campo cliente se muestra en `readonly` para evitar cambios.
4. El usuario rellena:
   - Tipo de operación (compra/alquiler/vacacional).
   - Rango de precio.
   - Superficie mínima, habitaciones y baños.
   - Zonas de interés.
   - Características extra (garaje, piscina, ascensor, terraza, etc.).
   - Estado de la demanda (activa, en gestión, pausada, archivada).
5. Al guardar:
   - La demanda se crea en BD.
   - El sistema redirige de vuelta a la ficha del cliente (`return_to`), mostrando un mensaje de éxito.
   - La nueva demanda aparece en la tabla de “Demandas de este cliente”.

### 4.2 Alta desde el listado global de Demandas

1. El usuario entra en `/admin/demandas`.
2. Pulsa **“+ Nueva demanda”**.
3. El formulario muestra un `select` de clientes:
   - Admin/Coordinador: ven todos.
   - Comercial: solo los suyos.
4. Tras guardar, se vuelve al listado de demandas.

### 4.3 Edición y borrado

- Desde el listado de demandas, el usuario puede:
  - Editar una demanda (corrigiendo criterios, zonas, etc.).
  - Borrar una demanda (solo admin/coordinador, mediante POST + CSRF).
- Desde la ficha de cliente:
  - En la tabla de “Demandas de este cliente” hay un botón **“Editar”** que lleva al formulario de edición y, al guardar, devuelve a la ficha.

---

## 5. Validaciones

A nivel de servidor se aplican validaciones básicas para asegurar que los datos tienen sentido:

- **Cliente**
  - Debe existir en BD.
  - Para rol `comercial`, el cliente debe tener su `usuario_id` igual al ID del usuario logueado.

- **Tipo de operación**
  - Solo se aceptan los valores previstos: `compra`, `alquiler`, `vacacional`.

- **Rango de precio**
  - `rango_precio_min` y `rango_precio_max` deben ser numéricos.
  - Si ambos están informados, se exige que `min ≤ max`.

- **Campos numéricos**
  - `superficie_min`, `habitaciones_min`, `banos_min` no pueden ser negativos.

- **Características**
  - Los checkboxes se convierten en un array de strings.
  - Se guardan en un campo JSON (`caracteristicas`) para permitir flexibilidad (añadir nuevas características sin tocar el esquema).

Cuando alguna validación falla:

- El formulario se repinta con los errores.
- Los datos introducidos se conservan (`old values`) para no obligar al usuario a reescribir todo.

---

## 6. Base de datos

Tabla principal: `demandas`.

Relaciones:

- `cliente_id` → `clientes.id_cliente` (elimina o protege según se haya definido la FK; el objetivo es que si se borra un cliente, sus demandas también se gestionen de forma coherente).
- `comercial_id` → `usuarios.id_usuario` (permite saber quién es el responsable comercial de esa demanda).

El uso de un campo JSON `caracteristicas` aporta:

- Flexibilidad para guardar una lista de atributos sin crear una tabla auxiliar.
- Facilidad para cambios futuros (se pueden añadir más características sin alterar el diseño global).

---

## 7. Seguridad

- **CSRF:** todos los formularios de creación, edición y borrado incluyen token anti-CSRF.
- **Control por rol:**
  - Admin/Coordinador: acceso completo.
  - Comercial: limitado a sus clientes.
- **Validación de permisos en el controlador:**
  - Antes de crear o editar una demanda, se comprueba que el `cliente_id` pertenece al usuario (si es comercial).
  - Se reutiliza la misma lógica que en módulos como Clientes e Inmuebles para mantener consistencia.

---

## 8. UX y vista de usuario

- Listado de Demandas:
  - Tabla clara con columnas clave para la gestión diaria.
  - Filtros por tipo, estado, comercial y rango de precio para acotar el trabajo.
- Ficha de Cliente:
  - Bloque “Demandas de este cliente” que da contexto inmediato al comercial (qué busca este cliente y en qué estado está cada petición).
  - Botón de acción rápida **“Añadir demanda”**.

Este diseño está pensado para que el módulo sea comprensible para el tribunal (a nivel funcional) y útil en el día a día de una agencia inmobiliaria real.

---

**Última actualización:** 08/12/2025  
**Autor:** Oswaldo Domingo (desarrollo) + soporte de IA para documentación
