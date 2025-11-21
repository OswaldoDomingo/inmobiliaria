# Base de datos del proyecto **Inmobiliaria**

## 1. Objetivo de la base de datos

La base de datos `inmobiliaria_db` tiene como finalidad dar soporte a un **CRM inmobiliario** sencillo pero realista, centrado en dos perfiles:

* **Administrador / Coordinador**
* **Comercial**

Y tres entidades de negocio principales:

* **Clientes** (propietarios, demandantes o ambos)
* **Inmuebles**
* **Demandas** (lo que busca un cliente demandante)

Además, se gestiona:

* Los **cruces** (match entre demanda e inmueble).
* Los **medios** (fotos/vídeos de los inmuebles).
* Una **auditoría básica** de acciones relevantes.

La base de datos está diseñada para:

* Respetar reglas de negocio típicas de una inmobiliaria:

  * Un **propietario con inmuebles no se puede borrar**.
  * Un **demandante** puede eliminarse y con él sus cruces.
  * Si se da de baja un comercial, se puede **reasignar cartera**.
* Evitar borrar datos importantes: se utiliza un sistema de **borrado lógico/archivado**.
* Preparar el terreno para una futura implementación MVC en PHP.

---

## 2. Decisiones de diseño importantes

### 2.1. Borrado lógico (archivado) en lugar de borrado físico

En lugar de hacer `DELETE` sobre registros importantes (clientes, inmuebles, demandas), se usan campos:

* `activo TINYINT(1)` – indica si el registro sigue en uso.
* `archivado TINYINT(1)` – indica que se ha dado de baja / archivado.
* `fecha_archivado DATETIME` – cuándo se ha archivado.

**Motivo**: en un contexto real, los datos de clientes, inmuebles y demandas tienen valor histórico, legal y comercial.
En este proyecto académico no se implementa todo el control legal, pero se deja la estructura preparada.

### 2.2. Estados en inmuebles y demandas

En lugar de “borrar” un inmueble, se controla con **estados**:

* Inmueble: `borrador`, `activo`, `reservado`, `vendido`, `retirado`
* Demanda: `activa`, `en_gestion`, `pausada`, `archivada`

**Motivo**: esto refleja el flujo real de trabajo:

* Un inmueble no desaparece: se publica, se reserva, se vende o se retira.
* Una demanda puede estar activa, en seguimiento, pausada o cerrada.

### 2.3. Tipos de cliente

Se usa un único registro en `clientes` con un campo:

```sql
tipo_cliente ENUM('propietario','demandante','ambos')
```

**Motivo**: simplificar el modelo en este proyecto:

* Un cliente puede ser solo propietario.
* Solo demandante.
* O ambas cosas (ej. vende para comprar).

Si el cliente deja de ser demandante, se puede pasar de `ambos` a `propietario` y archivar/eliminar sus demandas y cruces.

### 2.4. Restricciones y ON DELETE / ON UPDATE

* `ON DELETE RESTRICT` en `inmuebles.propietario_id`
  → Impide borrar un propietario que tenga inmuebles: obligas a resolver antes esa relación.

* `ON DELETE CASCADE` en:

  * Demanda → Cruces
  * Inmueble → Cruces
  * Inmueble → Medios
  * Cliente → Demandas

  → Si se elimina una demanda o un inmueble (p. ej. en entorno de pruebas), se limpian automáticamente los datos relacionados.

* `ON DELETE SET NULL` en las referencias a `usuarios` (comerciales):
  → Si por alguna razón se borra físicamente un usuario, el resto de datos no se eliminan, simplemente se queda la referencia vacía.

---

## 3. Estructura general (visión de conjunto)

### 3.1. Tablas principales

* `usuarios` – usuarios del sistema (admin, coordinador, comercial).
* `clientes` – personas físicas o jurídicas que son propietarios, demandantes o ambas cosas.
* `inmuebles` – propiedades en cartera.
* `demandas` – criterios de búsqueda de los clientes demandantes.
* `cruces` – matches entre demandas e inmuebles.
* `medios` – fotos y vídeos asociados a un inmueble.
* `auditoria` – registro básico de acciones importantes.

### 3.2. Relaciones clave

* `usuarios 1–N clientes` (cada cliente lo lleva un comercial).
* `usuarios 1–N inmuebles`.
* `usuarios 1–N demandas`.
* `clientes 1–N inmuebles` (propietario ↔ inmueble).
* `clientes 1–N demandas` (demandante ↔ demanda).
* `demandas N–N inmuebles` vía `cruces`.
* `inmuebles 1–N medios`.

---

## 4. Tablas explicadas una a una

### 4.1. `usuarios`

**Función**: gestionar los usuarios internos del sistema (no el “usuario web público”).

Campos importantes:

* `rol ENUM('admin','coordinador','comercial')`
  – Controla qué opciones verá en el panel.

* `activo`, `archivado`, `fecha_baja`
  – Para dar de baja lógica a un usuario sin borrarlo.

* `es_coordinador_general TINYINT(1)`
  – Permite marcar un usuario como el coordinador al que se pueden reasignar inmuebles/clientes si un comercial se va.

### 4.2. `clientes`

**Función**: almacenar propietarios, demandantes y clientes que son ambas cosas.

Campos importantes:

* `tipo_cliente ENUM('propietario','demandante','ambos')`
* `dni` (UNIQUE) – no se permiten dos clientes con el mismo DNI.
* `comercial_id` FK → `usuarios.id_usuario`
  – Indica quién lleva la relación con ese cliente.

**Reglas de negocio que apoya**:

* No se debe permitir borrar (en la lógica PHP) un **propietario con inmuebles**.
* Un **demandante** se puede archivar; sus demandas y cruces se pueden eliminar o archivar.

### 4.3. `inmuebles`

**Función**: representar cada propiedad de la cartera.

Campos importantes:

* `propietario_id` FK → `clientes.id_cliente`
  – Propietario del inmueble.

* `comercial_id` FK → `usuarios.id_usuario`
  – Comercial responsable.

* `ref` – referencia única del inmueble (`UNIQUE`).

* `estado ENUM('borrador','activo','reservado','vendido','retirado')`
  – Estado comercial del inmueble.

**Reglas de negocio que refuerza**:

* `ON DELETE RESTRICT` en `propietario_id` evita borrar un propietario con inmuebles.
* El borrado de inmuebles en entorno real debería ser **archivado**, no `DELETE`.

### 4.4. `demandas`

**Función**: guardar lo que busca un cliente demandante.

Campos importantes:

* `cliente_id` FK → `clientes.id_cliente`
* `comercial_id` FK → `usuarios.id_usuario`
* Campos de filtro: `rango_precio_min/max`, `superficie_min`, `habitaciones_min`, `banos_min`, `zonas`.
* `caracteristicas JSON` – para características especiales (terraza, piscina, garaje…).
* `estado ENUM('activa','en_gestion','pausada','archivada')`

**Relación con borrado**:

* Si se elimina físicamente una demanda, sus cruce(s) se eliminan con `ON DELETE CASCADE`.
* En la práctica de negocio, se tenderá a **archivar la demanda** en lugar de borrar.

### 4.5. `cruces`

**Función**: representa los “matches” entre una demanda y un inmueble.

Campos:

* `demanda_id` FK → `demandas.id_demanda`
* `inmueble_id` FK → `inmuebles.id_inmueble`
* `estado ENUM('nuevo','contactado','interesado','descartado')` – estado del cruce.
* `nota` – observaciones comerciales.

Es una tabla puramente relacional, por eso:

* Tiene `ON DELETE CASCADE` tanto desde demandas como desde inmuebles.

### 4.6. `medios`

**Función**: almacenar fotos y vídeos de cada inmueble.

Campos:

* `tipo ENUM('foto','video')`
* `estancia` – etiqueta de la estancia (salón, cocina, baño…).
* `url` – ruta o URL del archivo.
* `orden` – para ordenar las imágenes.

### 4.7. `auditoria`

**Función**: registro de acciones relevantes, como:

* altas
* modificaciones
* archivados
* borrados
* logins
* reasignaciones

Campos:

* `entidad` – tabla afectada (`usuario`,`cliente`,`inmueble`,`demanda`,`cruce`,`medio`).
* `id_registro` – id del registro afectado.
* `accion` – tipo de acción.
* `usuario_id` – quién lo hizo.
* `descripcion` – detalles opcionales.

---

## 5. Cómo se implementará desde PHP (visión rápida)

Aunque el código PHP llegará más adelante, esta base de datos está pensada para una arquitectura MVC. A nivel muy general:

* **Modelos**:

  * `Usuario`, `Cliente`, `Inmueble`, `Demanda`, `Cruce`, `Medio`.
* **Capa de acceso a datos**:

  * Clases tipo `ClienteRepository` o `ClienteModel` con métodos:

    * `findById($id)`
    * `findAll($filtros)`
    * `create($datos)`
    * `update($id, $datos)`
    * `archive($id)` → en lugar de `DELETE`, marcan `archivado=1`.
* **Lógica de negocio**:

  * Antes de archivar/borrar un cliente, se comprueba si:

    * Es propietario y tiene inmuebles.
    * Es demandante y tiene demandas/cruces.
  * Antes de borrar un inmueble, se revisan estados y se decide si se permite o se pasa a `retirado`.

La BD ya incorpora las restricciones mínimas (FOREIGN KEY, ON DELETE RESTRICT/CASCADE),
y la lógica de negocio se completará en PHP para mostrar mensajes claros al usuario.

---

## 6. SQL completo para crear la base de datos

Este script se puede usar **tanto en phpMyAdmin** como en **MySQL Workbench**:

* En phpMyAdmin:

  1. Seleccionar servidor (no bases de datos).
  2. Pestaña **SQL**.
  3. Pegar este script y ejecutar.

* En Workbench:

  1. Abrir conexión.
  2. Crear pestaña de query.
  3. Pegar el script.
  4. Ejecutar.

> ⚠️ El script borra la base de datos `inmobiliaria_db` si ya existe, para dejar todo limpio.

```sql
DROP DATABASE IF EXISTS `inmobiliaria_db`;

CREATE DATABASE `inmobiliaria_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE `inmobiliaria_db`;

-- =========================
-- 1) USUARIOS
-- =========================
CREATE TABLE usuarios (
  id_usuario             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre                 VARCHAR(100) NOT NULL,
  email                  VARCHAR(120) NOT NULL,
  password_hash          VARCHAR(255) NOT NULL,
  rol                    ENUM('admin','coordinador','comercial') NOT NULL DEFAULT 'comercial',
  activo                 TINYINT(1) NOT NULL DEFAULT 1,
  archivado              TINYINT(1) NOT NULL DEFAULT 0,
  fecha_alta             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_baja             DATETIME DEFAULT NULL,
  es_coordinador_general TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB;

-- =========================
-- 2) CLIENTES
-- =========================
CREATE TABLE clientes (
  id_cliente      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre          VARCHAR(100) NOT NULL,
  apellidos       VARCHAR(150) NOT NULL,
  tipo_cliente    ENUM('propietario','demandante','ambos') NOT NULL,
  dni             VARCHAR(20) DEFAULT NULL,
  email           VARCHAR(120) DEFAULT NULL,
  telefono1       VARCHAR(30) DEFAULT NULL,
  telefono2       VARCHAR(30) DEFAULT NULL,
  estado          ENUM('activo','inactivo','archivado') NOT NULL DEFAULT 'activo',
  activo          TINYINT(1) NOT NULL DEFAULT 1,
  archivado       TINYINT(1) NOT NULL DEFAULT 0,
  fecha_alta      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_archivado DATETIME DEFAULT NULL,
  comercial_id    INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id_cliente),
  UNIQUE KEY uq_clientes_dni (dni),
  KEY fk_clientes_comercial (comercial_id),
  CONSTRAINT fk_clientes_comercial
    FOREIGN KEY (comercial_id)
    REFERENCES usuarios (id_usuario)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 3) INMUEBLES
-- =========================
CREATE TABLE inmuebles (
  id_inmueble     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ref             VARCHAR(30) NOT NULL,
  propietario_id  INT UNSIGNED NOT NULL,
  comercial_id    INT UNSIGNED DEFAULT NULL,
  direccion       VARCHAR(255) NOT NULL,
  localidad       VARCHAR(100) NOT NULL,
  provincia       VARCHAR(100) NOT NULL,
  cp              VARCHAR(10) DEFAULT NULL,
  tipo            ENUM('piso','casa','chalet','adosado','duplex','local','oficina','terreno','otros')
                    NOT NULL DEFAULT 'piso',
  operacion       ENUM('venta','alquiler','vacacional')
                    NOT NULL DEFAULT 'venta',
  precio          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  superficie      INT UNSIGNED DEFAULT NULL,
  habitaciones    TINYINT UNSIGNED DEFAULT NULL,
  banos           TINYINT UNSIGNED DEFAULT NULL,
  estado          ENUM('borrador','activo','reservado','vendido','retirado')
                    NOT NULL DEFAULT 'borrador',
  descripcion     TEXT,
  activo          TINYINT(1) NOT NULL DEFAULT 1,
  archivado       TINYINT(1) NOT NULL DEFAULT 0,
  fecha_alta      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_archivado DATETIME DEFAULT NULL,
  PRIMARY KEY (id_inmueble),
  UNIQUE KEY uq_inmuebles_ref (ref),
  KEY fk_inmuebles_propietario (propietario_id),
  KEY fk_inmuebles_comercial (comercial_id),
  CONSTRAINT fk_inmuebles_propietario
    FOREIGN KEY (propietario_id)
    REFERENCES clientes (id_cliente)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_inmuebles_comercial
    FOREIGN KEY (comercial_id)
    REFERENCES usuarios (id_usuario)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 4) DEMANDAS
-- =========================
CREATE TABLE demandas (
  id_demanda       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_id       INT UNSIGNED NOT NULL,
  comercial_id     INT UNSIGNED DEFAULT NULL,
  tipo_operacion   ENUM('compra','alquiler','vacacional') NOT NULL,
  rango_precio_min DECIMAL(12,2) DEFAULT NULL,
  rango_precio_max DECIMAL(12,2) DEFAULT NULL,
  superficie_min   INT UNSIGNED DEFAULT NULL,
  habitaciones_min TINYINT UNSIGNED DEFAULT NULL,
  banos_min        TINYINT UNSIGNED DEFAULT NULL,
  zonas            TEXT,
  caracteristicas  JSON DEFAULT NULL,
  estado           ENUM('activa','en_gestion','pausada','archivada')
                     NOT NULL DEFAULT 'activa',
  activo           TINYINT(1) NOT NULL DEFAULT 1,
  archivado        TINYINT(1) NOT NULL DEFAULT 0,
  fecha_alta       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_archivado  DATETIME DEFAULT NULL,
  PRIMARY KEY (id_demanda),
  KEY fk_demandas_cliente (cliente_id),
  KEY fk_demandas_comercial (comercial_id),
  CONSTRAINT fk_demandas_cliente
    FOREIGN KEY (cliente_id)
    REFERENCES clientes (id_cliente)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_demandas_comercial
    FOREIGN KEY (comercial_id)
    REFERENCES usuarios (id_usuario)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 5) CRUCES
-- =========================
CREATE TABLE cruces (
  id_cruce       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  demanda_id     INT UNSIGNED NOT NULL,
  inmueble_id    INT UNSIGNED NOT NULL,
  estado         ENUM('nuevo','contactado','interesado','descartado')
                   NOT NULL DEFAULT 'nuevo',
  nota           TEXT,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_estado   DATETIME DEFAULT NULL,
  PRIMARY KEY (id_cruce),
  KEY fk_cruces_demanda (demanda_id),
  KEY fk_cruces_inmueble (inmueble_id),
  CONSTRAINT fk_cruces_demanda
    FOREIGN KEY (demanda_id)
    REFERENCES demandas (id_demanda)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_cruces_inmueble
    FOREIGN KEY (inmueble_id)
    REFERENCES inmuebles (id_inmueble)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 6) MEDIOS (fotos / vídeos)
-- =========================
CREATE TABLE medios (
  id_medio      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  inmueble_id   INT UNSIGNED NOT NULL,
  tipo          ENUM('foto','video') NOT NULL DEFAULT 'foto',
  estancia      VARCHAR(100) DEFAULT NULL,
  url           TEXT NOT NULL,
  orden         SMALLINT UNSIGNED DEFAULT 1,
  fecha_subida  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_medio),
  KEY fk_medios_inmueble (inmueble_id),
  CONSTRAINT fk_medios_inmueble
    FOREIGN KEY (inmueble_id)
    REFERENCES inmuebles (id_inmueble)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 7) AUDITORIA
-- =========================
CREATE TABLE auditoria (
  id_audit     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  entidad      ENUM('usuario','cliente','inmueble','demanda','cruce','medio') NOT NULL,
  id_registro  INT UNSIGNED NOT NULL,
  accion       ENUM('alta','modificacion','archivado','borrado','login','reasignacion') NOT NULL,
  usuario_id   INT UNSIGNED DEFAULT NULL,
  fecha        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  descripcion  TEXT,
  PRIMARY KEY (id_audit),
  KEY fk_auditoria_usuario (usuario_id),
  CONSTRAINT fk_auditoria_usuario
    FOREIGN KEY (usuario_id)
    REFERENCES usuarios (id_usuario)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;
```