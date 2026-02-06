-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 07-12-2025 a las 09:52:19
-- Versión del servidor: 8.0.37
-- Versión de PHP: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `p261985_inmobiliaria`
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
(1, 'Oswaldo', 'Domingo', 'propietario', '25376777A', 'oswaldo.domingo@gmail.com', '644403640', 'Carrer de Vicente Parra Actor 4 -1', 'Un nuevo cliente nuevo', 4, NULL, NULL, 'activo', 1, 0, '2025-11-30 01:52:26', NULL, NULL, '2025-11-30 01:52:26', '2025-11-30 13:08:24'),
(2, 'Manuel', 'Domingo', 'propietario', '19358969F', 'oswaldo.domingo@gmail.com', '644403640', 'Carrer de Vicente Parra Actor 4 -1', 'Mi nuevo cliente', 1, NULL, NULL, 'activo', 1, 0, '2025-11-30 09:40:31', NULL, NULL, '2025-11-30 09:40:31', '2025-11-30 09:40:31'),
(3, 'Chon', 'Quiles', 'propietario', '22551024F', 'chon@quiles.es', '123456789', 'Dr Ddommagk 3-44', 'Otro más', 4, NULL, NULL, 'activo', 1, 0, '2025-11-30 09:41:35', NULL, NULL, '2025-11-30 09:41:35', '2025-11-30 21:17:39'),
(4, 'Oswaldo 3', 'Domingo 3', 'propietario', '123456789A', 'oswaldo.domingo@gmail.com', '644403640', 'Carrer de Vicente Parra Actor 4 -1', 'Para borrar', 3, NULL, NULL, 'activo', 1, 0, '2025-11-30 21:17:00', NULL, NULL, '2025-11-30 21:17:00', '2025-11-30 21:17:32');

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
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `archivado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_archivado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password_hash`, `rol`, `foto_perfil`, `activo`, `archivado`, `fecha_alta`, `fecha_baja`, `es_coordinador_general`, `intentos_fallidos`, `cuenta_bloqueada`) VALUES
(1, 'Oswaldo Admin', 'admin@inmobiliaria.loc', '$2y$12$9WteibV3LnMU5xE5TuX6Ge28RvnKYhVyFqv2tOUMnFPyjkcACYwn.', 'admin', 'profile_692c8e02827e94.95399840.png', 1, 0, '2025-11-20 16:55:33', NULL, 1, 0, 0),
(2, 'Oswaldo', 'coordinador@inmobiliaria.loc', '$2y$12$4OOiTK5EURnoQaVN4V1qLOsYQvKICP.5WU3OBp/DnTmWMOkhCn.KC', 'coordinador', 'profile_692c8dd71e2a20.00637288.png', 1, 0, '2025-11-28 23:57:39', NULL, 0, 0, 0),
(3, 'Comercial 1', 'comercial1@inmobiliaria.loc', '$2y$12$4ZeMQ.jVp.DZYTeJlCntwunWVvEmADaSc9fAbf2fg81yQcXxcujZq', 'comercial', 'profile_692c8dc8cfa686.90820673.png', 1, 0, '2025-11-30 01:36:53', NULL, 0, 0, 0),
(4, 'Comercial 2', 'comercial2@inmobiliaria.loc', '$2y$12$Jlc5TsWzz1qpy7j7cB98g.D1iQPkr4T31As0JlopHA3q8odowRCaq', 'comercial', 'profile_692c8dbb64fc01.28745059.png', 1, 0, '2025-11-30 09:52:03', NULL, 0, 0, 0);

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
  MODIFY `id_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cruces`
--
ALTER TABLE `cruces`
  MODIFY `id_cruce` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `demandas`
--
ALTER TABLE `demandas`
  MODIFY `id_demanda` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inmuebles`
--
ALTER TABLE `inmuebles`
  MODIFY `id_inmueble` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `medios`
--
ALTER TABLE `medios`
  MODIFY `id_medio` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
