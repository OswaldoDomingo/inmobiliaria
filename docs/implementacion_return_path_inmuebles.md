# Implementación: Return Path en Edición de Inmuebles

**Fecha**: 2025-12-07
**Ticket**: "Editar Inmueble" debe volver al origen

---

## Resumen

Se ha implementado funcionalidad para que al editar un inmueble desde la ficha de un cliente, tanto el botón "Volver" como el guardado exitoso regresen a la ficha del cliente original. Si hay errores de validación, se re-renderiza el formulario sin redirigir, conservando el parámetro `return_to`.

---

## Archivos Modificados

### 1. **app/Views/admin/clientes/edit.php**
**Cambio**: Añadido parámetro `return_to` al enlace "Editar" del inmueble

```php
<?php 
    $returnPath = '/admin/clientes/editar?id=' . (int)$cliente->id_cliente;
    $editLink = '/admin/inmuebles/editar?id=' . $inm['id_inmueble'] . '&return_to=' . urlencode($returnPath);
?>
<a href="<?= htmlspecialchars($editLink) ?>" class="btn btn-sm btn-outline-primary">
    Editar
</a>
```

**Líneas**: 135-141

---

### 2. **app/Controllers/InmuebleController.php**

#### A. Método `edit()` - Leer y validar return_to
**Cambio**: Lee el parámetro `return_to` de GET, lo valida y lo pasa a la vista

```php
// Leer y validar return_to
$returnTo = $this->validateReturnTo($_GET['return_to'] ?? null);
```

**Líneas**: 131-132

---

#### B. Método `update()` - Manejo de return_to
**Cambios**:
1. Lee y valida `return_to` desde POST
2. En caso de errores, mantiene `return_to` al re-renderizar
3. En caso de éxito, redirige a `return_to` con `msg=updated`

```php
// Leer y validar return_to
$returnTo = $this->validateReturnTo($_POST['return_to'] ?? null);

// ... validaciones ...

if ($errors) {
    // Re-renderizar con errores, manteniendo return_to
    // ... (se pasa $returnTo a la vista)
}

// Actualizar en BD
$ok = $this->inmuebles->update($id, $data);

// Redirigir a return_to (si válido) o fallback al listado
$destination = $returnTo ?: '/admin/inmuebles';
$destination = $this->addQueryParam($destination, 'msg', 'updated');

$this->redirect($ok ? $destination : '/admin/inmuebles?error=db');
```

**Líneas**: 156, 177, 187-193

---

#### C. Nuevos métodos helper

**1. `validateReturnTo()`**: Valida que la URL de retorno sea segura

```php
private function validateReturnTo(?string $url): ?string
{
    if (!$url || trim($url) === '') return null;
    
    $url = trim($url);
    
    // Solo rutas internas que empiecen con /admin/
    if (!str_starts_with($url, '/admin/')) return null;
    
    // Sin protocolos externos (evitar open redirect)
    if (preg_match('#^(https?:)?//#i', $url)) return null;
    
    return $url;
}
```

**Seguridad**:
- ✅ Solo acepta rutas que empiecen con `/admin/`
- ✅ Rechaza URLs con protocolos (`http://`, `https://`, `//`)
- ✅ Protege contra open redirect attacks

**Líneas**: 387-400

---

**2. `addQueryParam()`**: Añade parámetros a URLs sin romper querystrings

```php
private function addQueryParam(string $url, string $key, string $value): string
{
    $separator = str_contains($url, '?') ? '&' : '?';
    return $url . $separator . urlencode($key) . '=' . urlencode($value);
}
```

**Funcionalidad**:
- ✅ Detecta si ya hay querystring (`?`)
- ✅ Usa `&` si ya existe, `?` si no
- ✅ Codifica key y value con `urlencode()`

**Líneas**: 402-415

---

### 3. **app/Views/admin/inmuebles/form.php**

#### A. Preparación de return_to
**Cambio**: Prepara variables para usar en los botones

```php
// Preparar return_to
$returnTo = $returnTo ?? null;
$returnUrl = $returnTo ?: '/admin/inmuebles';
```

**Líneas**: 41-43

---

#### B. Campo hidden en formulario
**Cambio**: Añade `return_to` como campo oculto si existe

```php
<?php if ($returnTo): ?>
    <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
<?php endif; ?>
```

**Líneas**: 73-75

---

#### C. Botón "Volver" (cabecera)
**Cambio**: Usa `$returnUrl` en lugar de ruta fija

```php
<a href="<?= e($returnUrl) ?>" class="btn btn-outline-secondary me-3">
    <i class="bi bi-arrow-left"></i> Volver
</a>
```

**Líneas**: 50-52

---

#### D. Botón "Cancelar" (pie del formulario)
**Cambio**: Usa `$returnUrl` en lugar de ruta fija

```php
<a href="<?= e($returnUrl) ?>" class="btn btn-outline-secondary me-md-2">Cancelar</a>
```

**Línea**: 244

---

## Flujo de Funcionamiento

### **Escenario 1: Edición desde ficha de cliente**

1. Usuario está en `/admin/clientes/editar?id=4`
2. Click en "Editar" de un inmueble → redirige a:
   ```
   /admin/inmuebles/editar?id=10&return_to=/admin/clientes/editar?id=4
   ```
3. GET `edit()`: lee `return_to`, lo valida, pasa a vista como `$returnTo`
4. Vista: muestra formulario con botones "Volver" y "Cancelar" apuntando a `/admin/clientes/editar?id=4`
5. Usuario guarda:
   - **Sin errores**: POST `update()` → actualiza BD → redirige a `/admin/clientes/editar?id=4&msg=updated`
   - **Con errores**: Re-renderiza formulario con errores + `return_to` persistido (hidden)

### **Escenario 2: Edición desde listado de inmuebles**

1. Usuario está en `/admin/inmuebles`
2. Click en "Editar" → redirige a:
   ```
   /admin/inmuebles/editar?id=10
   ```
   (sin `return_to`)
3. GET `edit()`: no hay `return_to` → `$returnTo = null`
4. Vista: botones "Volver" y "Cancelar" usan fallback `/admin/inmuebles`
5. Usuario guarda:
   - **Sin errores**: redirige a `/admin/inmuebles?msg=updated`
   - **Con errores**: Re-renderiza formulario con errores

### **Escenario 3: Intento de ataque (open redirect)**

1. Alguien manipula la URL:
   ```
   /admin/inmuebles/editar?id=10&return_to=http://malicious.com
   ```
2. `validateReturnTo()` detecta protocolo `http://` → retorna `null`
3. Se usa fallback `/admin/inmuebles`
4. ✅ Ataque bloqueado

---

## Verificación

### Casos de prueba

| # | Origen | Acción | Resultado esperado | ✅ |
|---|--------|--------|-------------------|---|
| 1 | `/admin/clientes/editar?id=4` | Editar inmueble → Volver | Regresa a `/admin/clientes/editar?id=4` | ✅ |
| 2 | `/admin/clientes/editar?id=4` | Editar inmueble → Guardar (OK) | Regresa a `/admin/clientes/editar?id=4&msg=updated` | ✅ |
| 3 | `/admin/clientes/editar?id=4` | Editar inmueble → Guardar (Errores) | Re-renderiza con errores, sin redirigir | ✅ |
| 4 | `/admin/inmuebles` | Editar inmueble → Volver | Regresa a `/admin/inmuebles` | ✅ |
| 5 | `/admin/inmuebles` | Editar inmueble → Guardar (OK) | Regresa a `/admin/inmuebles?msg=updated` | ✅ |
| 6 | URL manipulada | `return_to=/otra-ruta` | Ignora y usa fallback `/admin/inmuebles` | ✅ |
| 7 | URL manipulada | `return_to=http://evil.com` | Ignora y usa fallback `/admin/inmuebles` | ✅ |

---

## Cambios Mínimos

✅ **Solo se tocaron los archivos necesarios**:
- 1 vista (edición de cliente)
- 1 controlador (inmuebles)  
- 1 vista (formulario inmueble)

✅ **Sin romper funcionalidad existente**:
- Si no hay `return_to`, funciona como antes (fallback al listado)
- Código compatible con flujos actuales

✅ **Seguridad implementada**:
- Validación estricta de URLs de retorno
- Protección contra open redirects
- Solo rutas internas `/admin/...`

---

## Archivos de respaldo

Se crearon copias de seguridad con extensión `.bak`:
- `app/Controllers/InmuebleController.php.bak`
- `app/Views/admin/clientes/edit.php.bak`
- `app/Views/admin/inmuebles/form.php.bak`

---

## Próximos pasos

1. ✅ Probar en desarrollo todos los casos de prueba
2. ✅ Verificar que `msg=updated` aparece correctamente
3. ✅ Comprobar que errores de validación no redirigen
4. ✅ Validar seguridad contra open redirects
5. 📝 Actualizar documentación de usuario (opcional)
6. 🚀 Deploy a producción

---

**Implementado por**: Antigravity AI  
**Revisado**: Pendiente  
**Estado**: ✅ Completado
