-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 06-02-2026 a las 16:41:21
-- Versión del servidor: 8.2.0
-- Versión de PHP: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inmobiliaria_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id_audit` int UNSIGNED NOT NULL,
  `entidad` enum('usuario','cliente','inmueble','demanda','cruce','medio') NOT NULL,
  `id_registro` int UNSIGNED NOT NULL,
  `accion` enum('alta','modificacion','archivado','borrado','login','reasignacion') NOT NULL,
  `usuario_id` int UNSIGNED DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `tipo_cliente` enum('propietario','demandante','ambos') NOT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `notas` text,
  `usuario_id` int UNSIGNED DEFAULT NULL,
  `telefono1` varchar(30) DEFAULT NULL,
  `telefono2` varchar(30) DEFAULT NULL,
  `estado` enum('activo','inactivo','archivado') NOT NULL DEFAULT 'activo',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `archivado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_archivado` datetime DEFAULT NULL,
  `comercial_id` int UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `apellidos`, `tipo_cliente`, `dni`, `email`, `telefono`, `direccion`, `notas`, `usuario_id`, `telefono1`, `telefono2`, `estado`, `activo`, `archivado`, `fecha_alta`, `fecha_archivado`, `comercial_id`, `created_at`, `updated_at`) VALUES
(1, 'Cliente 1', 'cliente 1', 'propietario', '25376777A', 'oswaldo.domingo@gmail.com', '644403640', 'Cliente 1', 'Un nuevo cliente Valencia', 4, NULL, NULL, 'activo', 1, 0, '2025-11-30 01:52:26', NULL, NULL, '2025-11-30 01:52:26', '2025-12-08 11:09:46'),
(2, 'Ccliente 2', 'cliente 2', 'propietario', '19358969F', 'oswaldo.domingo@gmail.com', '644403640', 'Calle Ccliente 2', 'Mas clientes', 15, NULL, NULL, 'activo', 1, 0, '2025-11-30 03:17:03', NULL, NULL, '2025-11-30 03:17:03', '2026-01-04 17:03:22'),
(3, 'Oswaldo 3', 'Domingo', 'propietario', '22551024F', 'oswaldo.domingo@gmail.com', '644403640', 'Carrer de Vicente Parra Actor 4 -1', 'Otro', 2, NULL, NULL, 'activo', 1, 0, '2025-11-30 09:49:08', NULL, NULL, '2025-11-30 09:49:08', '2025-11-30 10:33:51'),
(4, 'Cliente 4', 'cliente 4', 'propietario', '123456789A', 'oswaldo.domingo@gmail.com', '644403640', 'Dirección 1', 'Otro más que aguantar', 4, NULL, NULL, 'activo', 1, 0, '2025-12-06 20:24:46', NULL, NULL, '2025-12-06 20:24:46', '2025-12-08 11:09:37'),
(5, 'Cliente 5', 'cliente 5', 'propietario', '123435qwe', 'correo5@clientes.es', '1123456789', 'Calle cliente 5', 'Notas cliente 5', 3, NULL, NULL, 'activo', 1, 0, '2025-12-07 20:14:10', NULL, NULL, '2025-12-07 20:14:10', '2025-12-07 20:14:10'),
(6, 'Cliente 6', 'cliente 6', 'propietario', '123456789P', 'cliente6@inmobiliaria.loc', '123456789', 'Calle cliente 6', 'Notas del cliente 6', 3, NULL, NULL, 'activo', 1, 0, '2025-12-08 11:12:17', NULL, NULL, '2025-12-08 11:12:17', '2025-12-08 11:12:17'),
(10, 'cliente10', 'Apellidos 10', 'propietario', '90000010A', 'cliente10@correo.es', '600000010', 'Calle Ejemplo 10', 'Cliente de prueba 10', 10, '600000010', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 10, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(11, 'cliente11', 'Apellidos 11', 'propietario', '90000011A', 'cliente11@correo.es', '600000011', 'Calle Ejemplo 11', 'Cliente de prueba 11', 10, '600000011', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 10, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(12, 'cliente12', 'Apellidos 12', 'propietario', '90000012A', 'cliente12@correo.es', '600000012', 'Calle Ejemplo 12', 'Cliente de prueba 12', 10, '600000012', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 10, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(13, 'cliente13', 'Apellidos 13', 'propietario', '90000013A', 'cliente13@correo.es', '600000013', 'Calle Ejemplo 13', 'Cliente de prueba 13', 10, '600000013', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 10, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(14, 'cliente14', 'Apellidos 14', 'propietario', '90000014A', 'cliente14@correo.es', '600000014', 'Calle Ejemplo 14', 'Cliente de prueba 14', 11, '600000014', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 11, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(15, 'cliente15', 'Apellidos 15', 'propietario', '90000015A', 'cliente15@correo.es', '600000015', 'Calle Ejemplo 15', 'Cliente de prueba 15', 11, '600000015', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 11, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(16, 'cliente16', 'Apellidos 16', 'propietario', '90000016A', 'cliente16@correo.es', '600000016', 'Calle Ejemplo 16', 'Cliente de prueba 16', 11, '600000016', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 11, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(17, 'cliente17', 'Apellidos 17', 'propietario', '90000017A', 'cliente17@correo.es', '600000017', 'Calle Ejemplo 17', 'Cliente de prueba 17', 11, '600000017', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 11, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(18, 'cliente18', 'Apellidos 18', 'propietario', '90000018A', 'cliente18@correo.es', '600000018', 'Calle Ejemplo 18', 'Cliente de prueba 18', 12, '600000018', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 12, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(19, 'cliente19', 'Apellidos 19', 'propietario', '90000019A', 'cliente19@correo.es', '600000019', 'Calle Ejemplo 19', 'Cliente de prueba 19', 12, '600000019', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 12, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(20, 'cliente20', 'Apellidos 20', 'propietario', '90000020A', 'cliente20@correo.es', '600000020', 'Calle Ejemplo 20', 'Cliente de prueba 20', 12, '600000020', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 12, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(21, 'cliente21', 'Apellidos 21', 'propietario', '90000021A', 'cliente21@correo.es', '600000021', 'Calle Ejemplo 21', 'Cliente de prueba 21', 12, '600000021', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 12, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(22, 'cliente22', 'Apellidos 22', 'propietario', '90000022A', 'cliente22@correo.es', '600000022', 'Calle Ejemplo 22', 'Cliente de prueba 22', 13, '600000022', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 13, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(23, 'cliente23', 'Apellidos 23', 'propietario', '90000023A', 'cliente23@correo.es', '600000023', 'Calle Ejemplo 23', 'Cliente de prueba 23', 13, '600000023', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 13, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(24, 'cliente24', 'Apellidos 24', 'propietario', '90000024A', 'cliente24@correo.es', '600000024', 'Calle Ejemplo 24', 'Cliente de prueba 24', 13, '600000024', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 13, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(25, 'cliente25', 'Apellidos 25', 'propietario', '90000025A', 'cliente25@correo.es', '600000025', 'Calle Ejemplo 25', 'Cliente de prueba 25', 13, '600000025', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 13, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(26, 'cliente26', 'Apellidos 26', 'propietario', '90000026A', 'cliente26@correo.es', '600000026', 'Calle Ejemplo 26', 'Cliente de prueba 26', 14, '600000026', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 14, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(27, 'cliente27', 'Apellidos 27', 'propietario', '90000027A', 'cliente27@correo.es', '600000027', 'Calle Ejemplo 27', 'Cliente de prueba 27', 14, '600000027', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 14, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(28, 'cliente28', 'Apellidos 28', 'propietario', '90000028A', 'cliente28@correo.es', '600000028', 'Calle Ejemplo 28', 'Cliente de prueba 28', 14, '600000028', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 14, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(29, 'cliente29', 'Apellidos 29', 'propietario', '90000029A', 'cliente29@correo.es', '600000029', 'Calle Ejemplo 29', 'Cliente de prueba 29', 14, '600000029', NULL, 'activo', 1, 0, '2025-12-09 19:00:15', NULL, 14, '2025-12-09 19:00:15', '2025-12-09 19:00:15'),
(30, 'Cliente Comercial 18', 'Cliente 18', 'propietario', '123456789H', 'cliente18@cliente18.es', '123456789', 'Call del cliente 18', 'Es el cliente 18', 15, NULL, NULL, 'activo', 1, 0, '2026-01-04 16:51:13', NULL, NULL, '2026-01-04 16:51:13', '2026-01-04 16:51:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruces`
--

CREATE TABLE `cruces` (
  `id_cruce` int UNSIGNED NOT NULL,
  `demanda_id` int UNSIGNED NOT NULL,
  `inmueble_id` int UNSIGNED NOT NULL,
  `estado` enum('nuevo','contactado','interesado','descartado') NOT NULL DEFAULT 'nuevo',
  `nota` text,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_estado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `demandas`
--

CREATE TABLE `demandas` (
  `id_demanda` int UNSIGNED NOT NULL,
  `cliente_id` int UNSIGNED NOT NULL,
  `comercial_id` int UNSIGNED DEFAULT NULL,
  `tipo_operacion` enum('compra','alquiler','vacacional') NOT NULL,
  `rango_precio_min` decimal(12,2) DEFAULT NULL,
  `rango_precio_max` decimal(12,2) DEFAULT NULL,
  `superficie_min` int UNSIGNED DEFAULT NULL,
  `habitaciones_min` tinyint UNSIGNED DEFAULT NULL,
  `banos_min` tinyint UNSIGNED DEFAULT NULL,
  `zonas` text,
  `caracteristicas` json DEFAULT NULL,
  `estado` enum('activa','en_gestion','pausada','archivada') NOT NULL DEFAULT 'activa',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `archivado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_archivado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `demandas`
--

INSERT INTO `demandas` (`id_demanda`, `cliente_id`, `comercial_id`, `tipo_operacion`, `rango_precio_min`, `rango_precio_max`, `superficie_min`, `habitaciones_min`, `banos_min`, `zonas`, `caracteristicas`, `estado`, `activo`, `archivado`, `fecha_alta`, `fecha_archivado`) VALUES
(1, 5, 3, 'compra', 1000000.00, 20000000.00, 90, 3, 2, NULL, '[\"garaje\"]', 'activa', 0, 0, '2025-12-08 00:36:19', NULL),
(2, 5, 3, 'compra', 345999.00, 459996.00, 200, 4, 2, 'centro', '[\"garaje\"]', 'activa', 0, 0, '2025-12-08 00:50:01', NULL),
(3, 6, 3, 'alquiler', 160.00, 400.00, 50, 1, 2, 'De vacaciones', '[\"garaje\", \"piscina\", \"terraza\"]', 'activa', 0, 0, '2025-12-08 11:15:00', NULL),
(4, 1, 4, 'compra', 1234.00, 2345.00, 45, 1, 1, NULL, '[\"garaje\"]', 'en_gestion', 0, 0, '2025-12-08 17:03:32', NULL),
(5, 4, 4, 'vacacional', 234.00, 300.00, 100, 5, 3, NULL, '[\"garaje\", \"piscina\", \"terraza\", \"amueblado\"]', 'activa', 0, 0, '2025-12-08 22:32:20', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inmuebles`
--

CREATE TABLE `inmuebles` (
  `id_inmueble` int UNSIGNED NOT NULL,
  `ref` varchar(30) NOT NULL,
  `propietario_id` int UNSIGNED NOT NULL,
  `comercial_id` int UNSIGNED DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `tipo` enum('piso','casa','chalet','adosado','duplex','local','oficina','terreno','otros') NOT NULL DEFAULT 'piso',
  `operacion` enum('venta','alquiler','vacacional') NOT NULL DEFAULT 'venta',
  `precio` decimal(12,2) NOT NULL DEFAULT '0.00',
  `superficie` int UNSIGNED DEFAULT NULL,
  `habitaciones` tinyint UNSIGNED DEFAULT NULL,
  `banos` tinyint UNSIGNED DEFAULT NULL,
  `estado` enum('borrador','activo','reservado','vendido','retirado') NOT NULL DEFAULT 'borrador',
  `descripcion` text,
  `imagen` varchar(255) DEFAULT NULL COMMENT 'Nombre del archivo de la imagen principal del inmueble',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `archivado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_archivado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `inmuebles`
--

INSERT INTO `inmuebles` (`id_inmueble`, `ref`, `propietario_id`, `comercial_id`, `direccion`, `localidad`, `provincia`, `cp`, `tipo`, `operacion`, `precio`, `superficie`, `habitaciones`, `banos`, `estado`, `descripcion`, `imagen`, `activo`, `archivado`, `fecha_alta`, `fecha_archivado`) VALUES
(2, 'REF-07-12-2025-00002', 4, 3, 'Carrer de Vicente Parra Actor 4 -1', 'VALENCIA', 'Valencia', '46017', 'piso', 'venta', 234567.00, 180, 4, 2, 'activo', 'Lorem ipsum es', 'inmueble_6935a797c1b4a1.40074334.jpg', 1, 0, '2025-12-07 11:16:22', NULL),
(3, 'REF-07-12-2025-00003', 1, 3, 'Calle Test 1', 'Test City', 'Test Prov', NULL, 'piso', 'venta', 100001.00, NULL, NULL, NULL, 'activo', NULL, NULL, 1, 0, '2025-12-07 15:38:54', NULL),
(4, 'REF-07-12-2025-00001', 4, 3, 'Poligono 9', 'Montserrat', 'Valencia', '46192', 'chalet', 'venta', 500000.00, 11000, 4, 3, 'activo', 'Chalet en zona rústica', NULL, 1, 0, '2025-12-07 17:16:59', NULL),
(5, 'REF-07-12-2025-00005', 5, 3, 'Calle Cliente 5', 'Cliente 5', 'Cliente 5', '00005', 'duplex', 'venta', 200000.00, 90, 4, 1, 'activo', 'Inmueble cliente 5', 'inmueble_6936954921ca58.37816660.jpg', 1, 0, '2025-12-07 20:15:30', NULL),
(6, 'REF-07-12-2025-00051', 5, 3, 'Cliente 5', 'Inmueble 2', 'Cliente 5', '12345', 'local', 'vacacional', 234.00, 23, 1, 1, 'reservado', 'Casa vacacional', 'inmueble_693612f9b163b7.54540601.jpg', 1, 0, '2025-12-07 20:31:46', NULL),
(7, 'REF-07-12-2025-00006', 6, 3, 'Calle de venta del cliente 6', 'VALENCIA', 'VALENCIA', '46017', 'piso', 'alquiler', 234000.00, 200, 3, 2, 'vendido', 'Para entrar a vivir', 'inmueble_6936a4f260de00.60614344.webp', 1, 0, '2025-12-08 11:14:10', NULL),
(8, 'VENTA001', 10, 10, 'Calle Venta 1', 'Valencia', 'Valencia', '46001', 'piso', 'venta', 120000.00, 80, 2, 1, 'activo', 'Inmueble de ejemplo en venta 1', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(9, 'VENTA002', 11, 10, 'Calle Venta 2', 'Valencia', 'Valencia', '46001', 'piso', 'venta', 125000.00, 85, 3, 2, 'activo', 'Inmueble de ejemplo en venta 2', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(10, 'VENTA003', 12, 10, 'Calle Venta 3', 'Valencia', 'Valencia', '46001', 'piso', 'venta', 130000.00, 90, 3, 2, 'activo', 'Inmueble de ejemplo en venta 3', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(11, 'VENTA004', 13, 10, 'Calle Venta 4', 'Valencia', 'Valencia', '46001', 'piso', 'venta', 135000.00, 95, 4, 2, 'activo', 'Inmueble de ejemplo en venta 4', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(12, 'VENTA005', 14, 11, 'Calle Venta 5', 'Valencia', 'Valencia', '46001', 'casa', 'venta', 140000.00, 100, 3, 2, 'activo', 'Inmueble de ejemplo en venta 5', 'inmueble_69387723ab9d74.38413219.png', 1, 0, '2025-12-09 19:00:16', NULL),
(13, 'VENTA006', 15, 11, 'Calle Venta 6', 'Valencia', 'Valencia', '46001', 'casa', 'venta', 145000.00, 110, 4, 2, 'activo', 'Inmueble de ejemplo en venta 6', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(14, 'VENTA007', 16, 11, 'Calle Venta 7', 'Valencia', 'Valencia', '46001', 'chalet', 'venta', 150000.00, 120, 4, 2, 'activo', 'Inmueble de ejemplo en venta 7', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(15, 'VENTA008', 17, 11, 'Calle Venta 8', 'Valencia', 'Valencia', '46001', 'chalet', 'venta', 155000.00, 90, 3, 2, 'activo', 'Inmueble de ejemplo en venta 8', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(16, 'VENTA009', 18, 12, 'Calle Venta 9', 'Valencia', 'Valencia', '46001', 'duplex', 'venta', 160000.00, 75, 2, 1, 'activo', 'Inmueble de ejemplo en venta 9', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(17, 'VENTA010', 19, 12, 'Calle Venta 10', 'Valencia', 'Valencia', '46001', 'duplex', 'venta', 165000.00, 85, 3, 2, 'activo', 'Inmueble de ejemplo en venta 10', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(18, 'ALQ001', 20, 12, 'Calle Alquiler 1', 'Valencia', 'Valencia', '46002', 'piso', 'alquiler', 750.00, 70, 2, 1, 'activo', 'Inmueble de ejemplo en alquiler 1', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(19, 'ALQ002', 21, 12, 'Calle Alquiler 2', 'Valencia', 'Valencia', '46002', 'piso', 'alquiler', 800.00, 80, 3, 2, 'activo', 'Inmueble de ejemplo en alquiler 2', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(20, 'ALQ003', 22, 13, 'Calle Alquiler 3', 'Valencia', 'Valencia', '46002', 'piso', 'alquiler', 700.00, 65, 2, 1, 'activo', 'Inmueble de ejemplo en alquiler 3', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(21, 'ALQ004', 23, 13, 'Calle Alquiler 4', 'Valencia', 'Valencia', '46002', 'piso', 'alquiler', 825.00, 75, 3, 1, 'activo', 'Inmueble de ejemplo en alquiler 4', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(22, 'ALQ005', 24, 13, 'Calle Alquiler 5', 'Valencia', 'Valencia', '46002', 'piso', 'alquiler', 900.00, 90, 4, 2, 'activo', 'Inmueble de ejemplo en alquiler 5', NULL, 1, 0, '2025-12-09 19:00:16', NULL),
(23, 'VAC001', 25, 13, 'Calle Vacacional 1', 'Valencia', 'Valencia', '46003', 'piso', 'vacacional', 80.00, 60, 2, 1, 'activo', 'Inmueble de ejemplo vacacional 1', 'inmueble_69387e44dafba5.48887807.png', 1, 0, '2025-12-09 19:00:16', NULL),
(24, 'VAC002', 26, 14, 'Calle Vacacional 2', 'Valencia', 'Valencia', '46003', 'piso', 'vacacional', 90.00, 65, 2, 1, 'activo', 'Inmueble de ejemplo vacacional 2', 'inmueble_69387e298ffe76.37458193.png', 1, 0, '2025-12-09 19:00:16', NULL),
(25, 'VAC003', 27, 14, 'Calle Vacacional 3', 'Valencia', 'Valencia', '46003', 'casa', 'vacacional', 100.00, 70, 3, 2, 'activo', 'Inmueble de ejemplo vacacional 3', 'inmueble_69387e1b47a4a5.63667571.png', 1, 0, '2025-12-09 19:00:16', NULL),
(26, 'VAC004', 28, 14, 'Calle Vacacional 4', 'Valencia', 'Valencia', '46003', 'casa', 'vacacional', 110.00, 75, 3, 2, 'activo', 'Inmueble de ejemplo vacacional 4', 'inmueble_69387e0be595e1.33907192.png', 1, 0, '2025-12-09 19:00:16', NULL),
(27, 'VAC005', 29, 14, 'Calle Vacacional 5', 'Valencia', 'Valencia', '46003', 'duplex', 'vacacional', 120.00, 80, 4, 2, 'activo', 'Inmueble de ejemplo vacacional 5', 'inmueble_69387dc2a01df7.97020476.png', 1, 0, '2025-12-09 19:00:16', NULL),
(28, 'REF-04-01-2026-00052', 2, 15, 'Calle del Cliente 2', 'VALENCIA', 'VALENCIA', '46017', 'piso', 'venta', 234000.00, 200, 3, 2, 'activo', 'Casa en piso', 'inmueble_695a8bcdd07643.19646405.png', 1, 0, '2026-01-04 16:48:29', NULL),
(29, 'REF-07-12-2025-00052', 2, 15, 'Carrer de Vicente Parra Actor 4 -1', 'VALENCIA', 'VALENCIA', '46017', 'piso', 'venta', 234.00, 344, NULL, NULL, 'activo', 'Casa en piso 2', 'inmueble_695a8c11bb40c8.71313925.png', 1, 0, '2026-01-04 16:49:37', NULL),
(30, 'REF-04-01-2026-00053', 30, 15, 'Carrer de Vicente Parra Actor 4 -1', 'VALENCIA', 'VALENCIA', '46017', 'piso', 'venta', 2345.00, 145, 4, 2, 'activo', 'Casa del cliente 18', 'inmueble_695a8cd6d76157.33016147.png', 1, 0, '2026-01-04 16:52:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medios`
--

CREATE TABLE `medios` (
  `id_medio` int UNSIGNED NOT NULL,
  `inmueble_id` int UNSIGNED NOT NULL,
  `tipo` enum('foto','video') NOT NULL DEFAULT 'foto',
  `estancia` varchar(100) DEFAULT NULL,
  `url` text NOT NULL,
  `orden` smallint UNSIGNED DEFAULT '1',
  `fecha_subida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','coordinador','comercial') NOT NULL DEFAULT 'comercial',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `archivado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_baja` datetime DEFAULT NULL,
  `es_coordinador_general` tinyint(1) NOT NULL DEFAULT '0',
  `intentos_fallidos` int DEFAULT '0',
  `cuenta_bloqueada` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `telefono`, `password_hash`, `rol`, `foto_perfil`, `activo`, `archivado`, `fecha_alta`, `fecha_baja`, `es_coordinador_general`, `intentos_fallidos`, `cuenta_bloqueada`) VALUES
(1, 'Oswaldo Admin', 'admin@inmobiliaria.loc', '644403640', '$2y$12$9WteibV3LnMU5xE5TuX6Ge28RvnKYhVyFqv2tOUMnFPyjkcACYwn.', 'admin', 'profile_692b0fdc54b742.47933595.png', 1, 0, '2025-11-20 16:55:33', NULL, 1, 0, 0),
(2, 'Oswaldo', 'coordinador@inmobiliaria.loc', NULL, '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'coordinador', 'profile_692aba07d04880.16797404.png', 1, 0, '2025-11-28 23:57:39', NULL, 0, 0, 0),
(3, 'Comercial 1', 'comercial1@inmobiliaria.loc', NULL, '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', 'profile_692b91a52bb3d6.64271830.png', 1, 0, '2025-11-30 01:36:53', NULL, 0, 0, 0),
(4, 'Comercial 2', 'comercial2@inmobiliaria.loc', '+34 644 403 640', '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', NULL, 1, 0, '2025-12-08 11:09:03', NULL, 0, 0, 0),
(10, 'comercial10', 'comercial10@correo.es', NULL, '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', NULL, 1, 0, '2025-12-09 19:00:14', NULL, 0, 0, 0),
(11, 'comercial11', 'comercial11@correo.es', NULL, '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', NULL, 1, 0, '2025-12-09 19:00:14', NULL, 0, 0, 0),
(12, 'comercial12', 'comercial12@correo.es', NULL, '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', NULL, 1, 0, '2025-12-09 19:00:14', NULL, 0, 0, 0),
(13, 'comercial13', 'comercial13@correo.es', NULL, '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', NULL, 1, 0, '2025-12-09 19:00:14', NULL, 0, 0, 0),
(14, 'comercial14', 'oswaldomingo@gmail.com', '123456789', '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', NULL, 1, 0, '2025-12-09 19:00:14', NULL, 0, 0, 0),
(15, 'Comercial 18', 'comercial18@correo.es', '1123456789', '$2y$12$7NyEt2W6nUMfZMnpH1ooo.6X4lUh2YIMU6TeIzMy3behDjbRTgEYK', 'comercial', 'profile_693b0af31e8c41.25512992.jpg', 1, 0, '2025-12-11 19:18:27', NULL, 0, 0, 0),
(16, 'Comercial 19', 'comercial19@ccorreo.es', '123456789', '$2y$12$gTMuxmnQZD2sSxT.IKp2nOe.NCir8ZOeftTVnpxLO9rjoTHkxjMSC', 'comercial', NULL, 1, 0, '2026-01-04 18:06:09', NULL, 0, 0, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id_audit`),
  ADD KEY `fk_auditoria_usuario` (`usuario_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `uq_clientes_dni` (`dni`),
  ADD KEY `fk_clientes_comercial` (`comercial_id`),
  ADD KEY `idx_clientes_telefono` (`telefono`),
  ADD KEY `idx_clientes_usuario` (`usuario_id`);

--
-- Indices de la tabla `cruces`
--
ALTER TABLE `cruces`
  ADD PRIMARY KEY (`id_cruce`),
  ADD KEY `fk_cruces_demanda` (`demanda_id`),
  ADD KEY `fk_cruces_inmueble` (`inmueble_id`);

--
-- Indices de la tabla `demandas`
--
ALTER TABLE `demandas`
  ADD PRIMARY KEY (`id_demanda`),
  ADD KEY `fk_demandas_cliente` (`cliente_id`),
  ADD KEY `fk_demandas_comercial` (`comercial_id`);

--
-- Indices de la tabla `inmuebles`
--
ALTER TABLE `inmuebles`
  ADD PRIMARY KEY (`id_inmueble`),
  ADD UNIQUE KEY `uq_inmuebles_ref` (`ref`),
  ADD KEY `fk_inmuebles_propietario` (`propietario_id`),
  ADD KEY `fk_inmuebles_comercial` (`comercial_id`);

--
-- Indices de la tabla `medios`
--
ALTER TABLE `medios`
  ADD PRIMARY KEY (`id_medio`),
  ADD KEY `fk_medios_inmueble` (`inmueble_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuarios_email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_audit` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `cruces`
--
ALTER TABLE `cruces`
  MODIFY `id_cruce` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `demandas`
--
ALTER TABLE `demandas`
  MODIFY `id_demanda` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inmuebles`
--
ALTER TABLE `inmuebles`
  MODIFY `id_inmueble` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `medios`
--
ALTER TABLE `medios`
  MODIFY `id_medio` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_comercial` FOREIGN KEY (`comercial_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_clientes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cruces`
--
ALTER TABLE `cruces`
  ADD CONSTRAINT `fk_cruces_demanda` FOREIGN KEY (`demanda_id`) REFERENCES `demandas` (`id_demanda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cruces_inmueble` FOREIGN KEY (`inmueble_id`) REFERENCES `inmuebles` (`id_inmueble`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `demandas`
--
ALTER TABLE `demandas`
  ADD CONSTRAINT `fk_demandas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_demandas_comercial` FOREIGN KEY (`comercial_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `inmuebles`
--
ALTER TABLE `inmuebles`
  ADD CONSTRAINT `fk_inmuebles_comercial` FOREIGN KEY (`comercial_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inmuebles_propietario` FOREIGN KEY (`propietario_id`) REFERENCES `clientes` (`id_cliente`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `medios`
--
ALTER TABLE `medios`
  ADD CONSTRAINT `fk_medios_inmueble` FOREIGN KEY (`inmueble_id`) REFERENCES `inmuebles` (`id_inmueble`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
