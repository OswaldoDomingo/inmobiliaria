-- database/migrations/03_create_crm_tables.sql

-- Tabla clientes
CREATE TABLE IF NOT EXISTS clientes (
  id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(150) NOT NULL,
  dni VARCHAR(20) DEFAULT NULL,
  email VARCHAR(190) DEFAULT NULL,
  telefono VARCHAR(25) DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL,
  notas TEXT,
  usuario_id INT UNSIGNED NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT uq_clientes_dni UNIQUE (dni),
  CONSTRAINT fk_clientes_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id_usuario)
    ON DELETE SET NULL,
  KEY idx_clientes_email (email),
  KEY idx_clientes_telefono (telefono),
  KEY idx_clientes_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla inmuebles
CREATE TABLE IF NOT EXISTS inmuebles (
  id_inmueble INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(20) NOT NULL,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT,
  direccion VARCHAR(255),
  ciudad VARCHAR(100),
  cp VARCHAR(10),
  superficie INT UNSIGNED DEFAULT NULL,
  habitaciones TINYINT UNSIGNED DEFAULT NULL,
  banos TINYINT UNSIGNED DEFAULT NULL,
  tipo ENUM('Piso','Casa','Local','Terreno','Nave','Otro') NOT NULL DEFAULT 'Piso',
  estado ENUM('Captacion','Publicado','Reservado','Vendido','Retirado') NOT NULL DEFAULT 'Captacion',
  en_venta TINYINT(1) NOT NULL DEFAULT 0,
  en_alquiler TINYINT(1) NOT NULL DEFAULT 0,
  en_vacacional TINYINT(1) NOT NULL DEFAULT 0,
  precio_venta DECIMAL(12,2) DEFAULT NULL,
  precio_alquiler DECIMAL(12,2) DEFAULT NULL,
  precio_vacacional DECIMAL(12,2) DEFAULT NULL,
  propietario_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT uq_inmuebles_ref UNIQUE (ref),
  CONSTRAINT fk_inmuebles_propietario FOREIGN KEY (propietario_id)
    REFERENCES clientes(id_cliente)
    ON DELETE RESTRICT,
  CONSTRAINT fk_inmuebles_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id_usuario)
    ON DELETE SET NULL,
  KEY idx_inmuebles_propietario (propietario_id),
  KEY idx_inmuebles_usuario (usuario_id),
  KEY idx_inmuebles_tipo (tipo),
  KEY idx_inmuebles_estado (estado),
  KEY idx_inmuebles_cp (cp),
  KEY idx_inmuebles_venta (en_venta),
  KEY idx_inmuebles_alquiler (en_alquiler),
  KEY idx_inmuebles_vacacional (en_vacacional)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
