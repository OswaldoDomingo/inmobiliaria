-- =====================================================
-- SQL PARA EJECUTAR EN PRODUCCIÓN
-- Añade campo telefono a tabla usuarios
-- =====================================================

ALTER TABLE usuarios 
ADD COLUMN telefono VARCHAR(25) DEFAULT NULL 
AFTER email;

-- =====================================================
-- OPCIONAL: Actualizar usuarios con números de teléfono
-- Ejecutar solo después de añadir el campo
-- =====================================================

-- Ejemplo: Actualizar coordinador general
-- UPDATE usuarios SET telefono = '+34 XXX XXX XXX' WHERE es_coordinador_general = 1;

-- Ejemplo: Actualizar comerciales
-- UPDATE usuarios SET telefono = '+34 XXX XXX XXX' WHERE id_usuario = 3;
-- UPDATE usuarios SET telefono = '+34 XXX XXX XXX' WHERE id_usuario = 4;
