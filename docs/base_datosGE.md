# Proyecto Inmobiliaria: Diseño y Estructura de la Base de Datos

Este documento detalla la arquitectura de la base de datos diseñada para la aplicación de gestión inmobiliaria, explicando su funcionalidad, el modelo de datos, las reglas de negocio y el código SQL completo para su implementación.

## 1\. Funcionalidad y Propósito

El objetivo de esta base de datos es dar soporte a una aplicación de gestión inmobiliaria completa. Su funcionalidad principal es:

  * **Gestionar la Oferta:** Dar de alta y administrar `Inmuebles` (pisos, locales, etc.) con sus características, precios y estado (Libre, Vendido, etc.).
  * **Gestionar la Demanda:** Registrar las búsquedas de los `Clientes` (qué buscan, dónde y a qué precio).
  * **Gestionar las Personas:** Mantener un registro único de `Clientes` (que pueden ser Propietarios, Demandantes, o ambos) y de `Usuarios` del sistema (Comerciales y Administradores).
  * **Implementar Lógica de Negocio:** Asignar `Inmuebles` y `Clientes` a `Comerciales` (Coordinadores) específicos.
  * **Habilitar "Cruces":** Permitir al sistema encontrar coincidencias entre la Oferta (`Inmuebles`) y la Demanda (`Demandas`).

## 2\. El Modelo de Datos: ¿Por qué se ha hecho así?

El error más común es intentar guardar todo en una sola tabla, lo que genera duplicidad y ambigüedad. El modelo que hemos diseñado se basa en la **separación de conceptos** (un modelo relacional) para garantizar flexibilidad y evitar errores.

Este es el núcleo de nuestra lógica:

1.  **Pilar 1: `Usuarios` (El Gestor)**

      * **Qué es:** Es la tabla de *quién usa la aplicación* (el Comercial, el Administrador).
      * **Por qué:** Separa a tus empleados de tus clientes. Es la tabla maestra de la que dependen las asignaciones (`id_coordinador`).

2.  **Pilar 2: `Clientes` (La Persona)**

      * **Qué es:** Es la ficha de la *persona externa* (Ana García).
      * **Por qué:** Esta es la clave. Un `Cliente` no es "un propietario" o "un demandante". Es una **persona** que puede tener *ambos roles*. Por eso, en lugar de un campo "Tipo", usamos dos *checkboxes* (`rol_propietario`, `rol_demandante`). Ana García existe **una sola vez** en esta tabla.

3.  **Pilar 3: `Inmuebles` (La Oferta)**

      * **Qué es:** Es la ficha del *producto* (Piso en C/Colón).
      * **Por qué:** Es el "qué ofrece" el cliente. Esta ficha se **enlaza** al `Cliente` (`id_cliente`) que es su propietario. Aquí es donde viven los detalles (precio de venta, habitaciones, estado, etc.).

4.  **Pilar 4: `Demandas` (La Búsqueda)**

      * **Qué es:** Es la ficha de la *búsqueda* (Busco ático 3hab).
      * **Por qué:** Es el "qué busca" el cliente. Esta ficha se **enlaza** al `Cliente` (`id_cliente`) que busca. Un mismo `Cliente` puede tener **múltiples demandas** (una de compra y otra de alquiler). Aquí viven los filtros (precio mín/máx, zona).

Este modelo de 4 pilares evita toda ambigüedad, no duplica datos y te permite que un cliente sea propietario y demandante al mismo tiempo sin ningún conflicto.

## 3\. Lógica de Negocio y Reglas Clave

Hemos definido reglas cruciales que se implementan directamente en el SQL:

1.  **Seguridad (Contraseñas):** Las contraseñas en la tabla `Usuarios` **nunca** se guardan en texto plano. Se guardan como un "hash" (cifrado) `bcrypt`, que se genera desde la aplicación (PHP, Python, etc.) antes de enviarlo al `INSERT`.
2.  **Permisos (Admin vs. C/C):**
      * `Administrador`: Ve todas las filas de todas las tablas.
      * `Coordinador`: Solo puede ver las filas donde `id_coordinador` sea igual a su propio ID. (Esto se implementa en las consultas `SELECT` de la aplicación).
3.  **Regla de Borrado (Comercial):** Si se borra un `Usuario` (Comercial), sus `Clientes`, `Inmuebles` y `Demandas` no se borran. En su lugar, el campo `id_coordinador` se pone a `NULL` (Nulo o "sin asignar") para que el Admin pueda reasignarlos.
      * *Implementación SQL:* `ON DELETE SET NULL`.
4.  **Regla de Borrado (Cliente-Inmueble):** ¡La regla más importante\! **No se puede borrar** un `Cliente` si tiene `Inmuebles` asociados. Es un bloqueo fuerte para proteger la integridad de tus activos.
      * *Implementación SQL:* `ON DELETE RESTRICT`.
5.  **Regla de Borrado (Cliente-Demanda):** Las `Demandas` son un "servicio", no un activo. Si se borra un `Cliente`, todas sus `Demandas` (búsquedas) asociadas se borran automáticamente con él.
      * *Implementación SQL:* `ON DELETE CASCADE`.
6.  **Borrado Lógico (Historial):** Los `Inmuebles` que tengan un `Estado` de 'Vendido' o 'Alquilado' **no se deben borrar físicamente** (Hard Delete). Se deben "archivar" (Soft Delete) para mantener el historial de transacciones. Esto es una regla de la aplicación (el botón "Borrar" se convierte en "Archivar").

## 4\. El Código SQL Completo (Para phpMyAdmin / Workbench)

Aquí tienes el script SQL completo. Puedes copiar y pegar esto directamente en la pestaña "SQL" de phpMyAdmin o en un *query editor* de Workbench para crear toda la estructura de una sola vez.

El orden es importante (creamos las tablas maestras primero).

```sql
-- ----------------------------------------
-- 1. TABLA DE USUARIOS (COMERCIALES Y ADMINS)
-- ----------------------------------------
CREATE TABLE Usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    dni VARCHAR(20) UNIQUE,
    telf1 VARCHAR(20),
    telf2 VARCHAR(20),
    correo VARCHAR(255) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('Administrador', 'Coordinador') NOT NULL,
    imagen_url VARCHAR(255) DEFAULT '/imagenes/avatar_default.png'
) ENGINE=InnoDB;

-- ----------------------------------------
-- 2. TABLA DE CLIENTES (PROPIETARIOS Y DEMANDANTES)
-- ----------------------------------------
CREATE TABLE Clientes (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    dni VARCHAR(20) NOT NULL UNIQUE,
    telf1 VARCHAR(20) NOT NULL,
    telf2 VARCHAR(20),
    correo VARCHAR(255) NOT NULL UNIQUE,
    horario_contacto VARCHAR(255),
    rol_propietario BOOLEAN DEFAULT FALSE,
    rol_demandante BOOLEAN DEFAULT FALSE,
    imagen_url VARCHAR(255),
    id_coordinador INT,

    FOREIGN KEY (id_coordinador) 
        REFERENCES Usuarios(id_usuario) 
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------
-- 3. TABLA DE INMUEBLES (LA OFERTA)
-- ----------------------------------------
CREATE TABLE Inmuebles (
    id_inmueble INT PRIMARY KEY AUTO_INCREMENT,
    
    -- Los Enlaces (Claves Ajenas)
    id_cliente INT NOT NULL,
    id_coordinador INT,

    -- Datos de Localización
    ref_propia VARCHAR(50) UNIQUE,
    localidad VARCHAR(100) NOT NULL,
    provincia VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    calle_via VARCHAR(255),
    
    -- Estado y Descripción
    estado ENUM('Destacado', 'Libre', 'Reservado', 'Vendido', 'Alquilado') NOT NULL DEFAULT 'Libre',
    descripcion TEXT,

    -- Características (Datos del inmueble)
    num_habitaciones INT DEFAULT 0,
    num_banos INT DEFAULT 0,
    superficie INT,
    tiene_garaje BOOLEAN DEFAULT FALSE,
    tiene_balcon BOOLEAN DEFAULT FALSE,
    tiene_terraza BOOLEAN DEFAULT FALSE,

    -- Operaciones (La Oferta)
    op_venta BOOLEAN DEFAULT FALSE,
    precio_venta DECIMAL(12, 2),
    op_alquiler_larga BOOLEAN DEFAULT FALSE,
    precio_alquiler_larga DECIMAL(10, 2),
    op_alquiler_corta BOOLEAN DEFAULT FALSE,
    precio_alquiler_corta DECIMAL(10, 2),
    op_alquiler_vacacional BOOLEAN DEFAULT FALSE,
    precio_alquiler_vacacional DECIMAL(10, 2),

    -- Definición de los Enlaces
    FOREIGN KEY (id_cliente) 
        REFERENCES Clientes(id_cliente) 
        ON DELETE RESTRICT,
    
    FOREIGN KEY (id_coordinador) 
        REFERENCES Usuarios(id_usuario) 
        ON DELETE SET NULL

) ENGINE=InnoDB;

-- ----------------------------------------
-- 4. TABLA DE IMAGENES (GALERÍA DEL INMUEBLE)
-- ----------------------------------------
CREATE TABLE Inmueble_Imagenes (
    id_imagen INT PRIMARY KEY AUTO_INCREMENT,
    id_inmueble INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    orden INT DEFAULT 0,

    FOREIGN KEY (id_inmueble) 
        REFERENCES Inmuebles(id_inmueble) 
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------
-- 5. TABLAS DE CATÁLOGO (PARA DEMANDAS)
-- ----------------------------------------
CREATE TABLE Tipos_Vivienda (
    id_tipo INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE Zonas (
    id_zona INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ----------------------------------------
-- 6. TABLA DE DEMANDAS (LA BÚSQUEDA)
-- ----------------------------------------
CREATE TABLE Demandas (
    id_demanda INT PRIMARY KEY AUTO_INCREMENT,
    
    -- Los Enlaces (Claves Ajenas)
    id_cliente INT NOT NULL,
    id_coordinador INT,

    -- Criterios Principales (Operación y Precio)
    tipo_operacion ENUM('Compra', 'Alquiler Larga', 'Alquiler Corta', 'Vacacional') NOT NULL,
    precio_minimo DECIMAL(12, 2),
    precio_maximo DECIMAL(12, 2),

    -- Criterios de Características
    num_habitaciones_min INT DEFAULT 0,
    num_banos_min INT DEFAULT 0,
    superficie_minima INT,
    superficie_maxima INT,
    quiere_garaje BOOLEAN DEFAULT FALSE,
    
    -- Estado de la Demanda
    activa BOOLEAN DEFAULT TRUE,
    
    -- Definición de los Enlaces
    FOREIGN KEY (id_cliente) 
        REFERENCES Clientes(id_cliente) 
        ON DELETE CASCADE,
    
    FOREIGN KEY (id_coordinador) 
        REFERENCES Usuarios(id_usuario) 
        ON DELETE SET NULL

) ENGINE=InnoDB;

-- ----------------------------------------
-- 7. TABLAS PUENTE (MUCHOS-A-MUCHOS PARA DEMANDAS)
-- ----------------------------------------
CREATE TABLE Demanda_Tipos (
    id_demanda INT NOT NULL,
    id_tipo INT NOT NULL,
    PRIMARY KEY (id_demanda, id_tipo),
    
    FOREIGN KEY (id_demanda) REFERENCES Demandas(id_demanda) ON DELETE CASCADE,
    FOREIGN KEY (id_tipo) REFERENCES Tipos_Vivienda(id_tipo) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Demanda_Zonas (
    id_demanda INT NOT NULL,
    id_zona INT NOT NULL,
    PRIMARY KEY (id_demanda, id_zona),

    FOREIGN KEY (id_demanda) REFERENCES Demandas(id_demanda) ON DELETE CASCADE,
    FOREIGN KEY (id_zona) REFERENCES Zonas(id_zona) ON DELETE CASCADE
) ENGINE=InnoDB;
```

## 5\. Desglose del Código (Paso a Paso)

Aquí te explicamos qué hace cada bloque de ese script:

### `CREATE TABLE Usuarios`

  * **Qué hace:** Crea la tabla para tus Comerciales y Administradores.
  * **Campos clave:**
      * `id_usuario INT PRIMARY KEY AUTO_INCREMENT`: La `REF*` (Clave Primaria) automática.
      * `correo VARCHAR(255) NOT NULL UNIQUE`: El email para el login. `NOT NULL` (obligatorio) y `UNIQUE` (no puede repetirse).
      * `contrasena VARCHAR(255) NOT NULL`: Dónde se guarda el hash cifrado.
      * `rol ENUM('Administrador', 'Coordinador')`: El desplegable de permisos. `ENUM` fuerza a que el valor sea uno de esos dos.
      * `imagen_url ... DEFAULT '...'`: Tu regla de la imagen por defecto.
      * `ENGINE=InnoDB`: Esencial. Es el motor que permite crear relaciones (`FOREIGN KEY`).

### `CREATE TABLE Clientes`

  * **Qué hace:** Crea la ficha de la "Persona" (el cliente).
  * **Campos clave:**
      * `rol_propietario BOOLEAN` y `rol_demandante BOOLEAN`: Tus *checkboxes* (Sí/No) para los roles. `BOOLEAN` es el tipo de dato para `TRUE`/`FALSE`.
      * `id_coordinador INT`: El campo que guardará el número (ID) del comercial que lo gestiona.
      * `FOREIGN KEY (id_coordinador) REFERENCES Usuarios(id_usuario)`: Este es el **enlace**. Conecta esta tabla con la de `Usuarios`.
      * `ON DELETE SET NULL`: Tu regla de negocio. Si el `Usuario` se borra, este campo se pone a `NULL` (vacío).

### `CREATE TABLE Inmuebles`

  * **Qué hace:** El corazón de la BBDD, la ficha del piso.
  * **Campos clave:**
      * `id_cliente INT NOT NULL`: El enlace obligatorio al propietario (Cliente).
      * `estado ENUM(...)`: Tu desplegable de estado, esencial para la lógica de "Borrado Lógico".
      * `descripcion TEXT`: `TEXT` permite guardar descripciones muy largas.
      * `op_venta BOOLEAN` y `precio_venta DECIMAL(12, 2)`: El *checkbox* `[ ] Venta` y su campo de precio. `DECIMAL` es el tipo de dato correcto para dinero.
      * `FOREIGN KEY (id_cliente) ... ON DELETE RESTRICT`: Tu regla de negocio más importante. **Restringe** (bloquea) el borrado de un `Cliente` si tiene este inmueble enlazado.

### `CREATE TABLE Inmueble_Imagenes`

  * **Qué hace:** Resuelve el problema de las "múltiples imágenes" (el carrusel).
  * **Campos clave:**
      * `id_inmueble INT NOT NULL`: El enlace que dice "esta foto pertenece a este piso".
      * `url VARCHAR(255)`: Dónde está guardada la foto.
      * `FOREIGN KEY (...) ON DELETE CASCADE`: Si borras el `Inmueble`, todas sus fotos se borran automáticamente.

### `CREATE TABLE Tipos_Vivienda` y `Zonas`

  * **Qué hace:** Son "tablas de catálogo" o "maestras". Solo guardan las opciones de tus desplegables (`Ático`, `Aldaia`...).
  * **Por qué:** Te da flexibilidad. Si mañana quieres añadir "Chalet" o una zona nueva, solo añades una fila aquí, no tienes que modificar la estructura de la BBDD.

### `CREATE TABLE Demandas`

  * **Qué hace:** La ficha de la búsqueda.
  * **Campos clave:**
      * `id_cliente INT NOT NULL`: Enlace al `Cliente` que busca.
      * `tipo_operacion ENUM(...)`: Obliga al usuario a elegir *una* operación por ficha de demanda (Compra O Alquiler...).
      * `precio_minimo`, `precio_maximo`: El rango de precios.
      * `FOREIGN KEY (id_cliente) ... ON DELETE CASCADE`: Tu regla de negocio. Si el `Cliente` se borra, sus `Demandas` (búsquedas) se borran con él.

### `CREATE TABLE Demanda_Tipos` y `Demanda_Zonas`

  * **Qué hace:** Son "tablas puente". Resuelven el problema de "muchos-a-muchos".
  * **Por qué:** Una `Demanda` (ej: ID 10) puede buscar *múltiples* `Tipos` ('Piso' y 'Ático'). Esta tabla guarda esos enlaces:
      * Fila 1: `id_demanda = 10`, `id_tipo = 1` ('Piso')
      * Fila 2: `id_demanda = 10`, `id_tipo = 2` ('Ático')

## 6\. Primeros Pasos: Crear tu Administrador

Una vez ejecutado el script, tu base de datos estará vacía. Lo primero que debes hacer es insertar tu usuario `Administrador`.

```sql
INSERT INTO Usuarios (
    nombre,
    apellido,
    dni,
    telf1,
    correo,
    contrasena,
    rol
) VALUES (
    'Admin',
    'General',
    'A00000001',
    '999999999',
    'admin@tuinmobiliaria.com',
    '$2y$10$mR/OY1R0v.vj.12.vP6.uODpWwIS.c7.LpW8.fK.1', -- (Esto es un HASH de ejemplo para "admin123")
    'Administrador'
);
```

> **Recordatorio de Seguridad:** El campo `contrasena` debe ser un hash `bcrypt`. El de arriba es un ejemplo. Deberás generar el tuyo propio desde tu aplicación o un generador online para tus pruebas.