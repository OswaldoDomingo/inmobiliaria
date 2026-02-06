-- Migración: Añadir soporte de imagen principal a inmuebles
-- Fecha: 2025-12-07
-- Propósito: Permitir subir una imagen principal por cada inmueble

ALTER TABLE inmuebles
ADD COLUMN imagen VARCHAR(255) NULL DEFAULT NULL
COMMENT 'Nombre del archivo de la imagen principal del inmueble (ej: inmueble_abc123.jpg)'
AFTER descripcion;
