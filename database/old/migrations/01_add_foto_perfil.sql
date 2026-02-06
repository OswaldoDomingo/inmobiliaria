-- Añadir columna para foto de perfil
ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255) NULL DEFAULT NULL AFTER rol;
