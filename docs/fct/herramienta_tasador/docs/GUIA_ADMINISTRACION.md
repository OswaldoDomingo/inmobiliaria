# Guía de Administración: Calculadora de Tasación

Esta guía está dirigida al administrador del sistema y explica cómo gestionar los datos de precios, zonas y configuración de la calculadora.

## 1. Gestión de Datos (Precios y Zonas)

La calculadora obtiene sus datos de una hoja de cálculo de Google Sheets. Para facilitar la gestión, hemos incluido un archivo Excel maestro en la carpeta `docs/` llamado **`Tasador.xlsx`**.

### Flujo de Trabajo para Actualizar Datos:

1.  **Edite la hoja ded drive:**
    *   Abra el archivo.
    *   En la pestaña de **Datos**, encontrará las columnas: `CP`, `Barrio`, `Zona`, `Precio_m2`.
    *   Añada nuevas filas para nuevos barrios o modifique el `Precio_m2` de los existentes.
    *   Guarde los cambios, Goolgle Sheets guarada los cambios en la hoja de cálculo automaticamente.

2.  **Subir a Google Sheets:**
    *   Vaya a su cuenta de Google Drive y abra la hoja de cálculo que está conectada a la web (el técnico le habrá proporcionado el enlace).
    *   Copie los datos de su Excel local y péguelos en la hoja de Google Sheets, reemplazando los antiguos.
    *   **Importante:** No cambie el nombre de las columnas (cabeceras).

3.  **Publicación:**
    *   Si la hoja de Google Sheets ya estaba publicada, los cambios se reflejarán en la web automáticamente (puede tardar unos minutos o requerir borrar caché del navegador).
    *   Si es una hoja nueva, debe ir a `Archivo > Compartir > Publicar en la web` y seleccionar el formato **CSV**.

## 2. Configuración de Reglas de Cálculo

En la misma hoja de cálculo (o en una pestaña separada llamada "Configuracion"), se definen las reglas que aumentan o disminuyen el valor de la tasación.

Las columnas suelen ser: `Clave`, `Valor`, `Tipo_Operacion`.

*   **Clave:** Identificador interno (ej: `atico`, `bajo`, `interior`). **No modificar**.
*   **Valor:** La cantidad a sumar o restar.
    *   Ejemplo: `15` (suma un 15%).
    *   Ejemplo: `-10` (resta un 10%).
*   **Tipo_Operacion:** Generalmente es `porcentaje`.

### Ejemplo de Ajuste:
Si desea que los áticos se valoren más alto, cambie el valor de la clave `atico` de `15` a `20`. Esto incrementará el precio base en un 20% en lugar de un 15%.

## 3. Configuración de Correos (EmailJS)

El sistema de envío de correos utiliza un servicio externo llamado **EmailJS**.

*   No requiere servidor propio.
*   Si necesita cambiar la dirección de correo a la que llegan los avisos de nuevos leads, deberá acceder al panel de control de EmailJS (o solicitarlo al soporte técnico).
*   Las plantillas de correo (lo que dice el email) también se editan desde el panel de EmailJS.

---

## Soporte Técnico

Si tiene problemas con la carga de datos o la web no responde, verifique:
1.  Que la hoja de Google Sheets está publicada correctamente como CSV.
2.  Que no ha dejado filas vacías entre datos en la hoja de cálculo.
3.  Que los códigos postales tienen el formato correcto (5 dígitos).
