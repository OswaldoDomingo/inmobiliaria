-- Solución: Actualizar inmuebles existentes para que aparezcan en el carrusel
-- Este script actualiza los primeros 6 inmuebles que tienen activo=1 pero estado != 'activo'
-- para que cumplan todos los criterios del carrusel

-- ANTES DE EJECUTAR, verifica cuáles serán afectados:
SELECT id_inmueble, ref, tipo, localidad, estado, activo, archivado 
FROM inmuebles 
WHERE activo = 1 
AND (estado != 'activo' OR archivado = 1)
LIMIT 6;

-- Si estás conforme con los inmuebles que se mostrarán arriba, ejecuta este UPDATE:
UPDATE inmuebles 
SET estado = 'activo', archivado = 0
WHERE activo = 1 
AND (estado != 'activo' OR archivado = 1)
LIMIT 6;

-- Verificar el resultado:
SELECT COUNT(*) as total_para_carousel 
FROM inmuebles 
WHERE estado = 'activo' AND activo = 1 AND archivado = 0;

-- Ver los inmuebles que ahora aparecerán en el carrusel:
SELECT id_inmueble, ref, tipo, localidad, provincia, precio
FROM inmuebles 
WHERE estado = 'activo' AND activo = 1 AND archivado = 0
ORDER BY RAND(TO_DAYS(CURDATE()))
LIMIT 6;
