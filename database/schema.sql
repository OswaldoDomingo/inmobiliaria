-- Esquema actual de referencia (local)
-- Ajusta tipos/longitudes a tu motor y collation preferida.

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol VARCHAR(50) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  archivado TINYINT(1) NOT NULL DEFAULT 0,
  intentos_fallidos INT UNSIGNED NOT NULL DEFAULT 0,
  cuenta_bloqueada TINYINT(1) NOT NULL DEFAULT 0,
  fecha_baja DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_usuarios_activo (activo),
  INDEX idx_usuarios_bloqueo (cuenta_bloqueada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

