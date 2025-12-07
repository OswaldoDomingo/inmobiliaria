# INSTRUCCIONES: Ejecutar Migración de BD para Soporte de Imagen

## ⚠️ IMPORTANTE: Ejecutar ANTES de usar la funcionalidad

Antes de poder subir imágenes a inmuebles, debes ejecutar el siguiente script SQL en tu base de datos.

---

## 📋 Script SQL a Ejecutar

```sql
-- Migración: Añadir soporte de imagen principal a inmuebles
-- Fecha: 2025-12-07
-- Propósito: Permitir subir una imagen principal por cada inmueble

ALTER TABLE inmuebles
ADD COLUMN imagen VARCHAR(255) NULL DEFAULT NULL
COMMENT 'Nombre del archivo de la imagen principal del inmueble (ej: inmueble_abc123.jpg)'
AFTER descripcion;
```

---

## 🔧 Cómo Ejecutar el Script

### Opción 1: phpMyAdmin (Recomendado)
1. Abre **phpMyAdmin** en tu navegador
2. Selecciona la base de datos: **`p261985_inmobiliaria`**
3. Ve a la pestaña **"SQL"**
4. Copia y pega el script de arriba
5. Haz clic en **"Continuar"** o **"Ejecutar"**
6. Verifica que aparezca el mensaje: "1 fila afectada" o similar

### Opción 2: Línea de Comandos MySQL
```bash
mysql -u root -p p261985_inmobiliaria < database/migrations/04_add_imagen_to_inmuebles.sql
```

### Opción 3: Directamente desde archivo
El script ya está guardado en:
```
database/migrations/04_add_imagen_to_inmuebles.sql
```

Puedes ejecutarlo desde phpMyAdmin usando "Importar" y seleccionando ese archivo.

---

## ✅ Verificar que Funcionó

Después de ejecutar, verifica la migración con esta consulta:

```sql
DESCRIBE inmuebles;
```

Deberías ver la nueva columna `imagen` en la lista, con estas características:
- **Field**: `imagen`
- **Type**: `varchar(255)`
- **Null**: `YES`
- **Default**: `NULL`

---

## 🔄 Rollback (si necesitas revertir)

Si por alguna razón necesitas deshacer el cambio:

```sql
ALTER TABLE inmuebles DROP COLUMN imagen;
```

⚠️ **Advertencia**: Esto eliminará la columna y TODOS los nombres de archivo almacenados, pero NO borrará las imágenes físicas del servidor.

---

## 🎯 Una vez ejecutado...

Una vez ejecutes el script SQL, ya podrás:
- ✅ Subir imágenes al crear inmuebles
- ✅ Ver miniaturas en el listado
- ✅ Editar y reemplazar imágenes
- ✅ Los inmuebles antiguos mostrarán imagen placeholder automáticamente

---

**¿Dudas?** Consulta `docs/implementacion_imagen_inmuebles.md` para más detalles.
