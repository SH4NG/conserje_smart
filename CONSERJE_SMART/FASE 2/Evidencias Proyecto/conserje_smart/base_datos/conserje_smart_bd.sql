-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2026 a las 00:47:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `conserje_smart_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `id_condominio_fk` int(11) NOT NULL,
  `id_usuario_fk` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` varchar(40) DEFAULT NULL,
  `tipo_lugar` varchar(40) DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `entidad_relacionada` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`entidad_relacionada`) AND json_type(`entidad_relacionada`) = 'ARRAY'),
  `fecha_evento` date DEFAULT NULL,
  `hora_evento` time DEFAULT NULL,
  `etiquetas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`etiquetas`) AND json_type(`etiquetas`) = 'ARRAY'),
  `campos_faltantes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`campos_faltantes`) AND json_type(`campos_faltantes`) = 'ARRAY'),
  `fecha_novedad` date NOT NULL,
  `hora_novedad` time NOT NULL,
  `observaciones` text DEFAULT NULL,
  `requiere_confirmacion` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condominio`
--

CREATE TABLE `condominio` (
  `id_condominio` int(11) NOT NULL,
  `rut_condominio` varchar(20) NOT NULL,
  `nombre_condominio` varchar(100) NOT NULL,
  `direccion_condominio` varchar(200) DEFAULT NULL,
  `correo_condominio` varchar(100) DEFAULT NULL,
  `telefono_condominio` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `condominio`
--

INSERT INTO `condominio` (`id_condominio`, `rut_condominio`, `nombre_condominio`, `direccion_condominio`, `correo_condominio`, `telefono_condominio`) VALUES
(1, '21.767373-7', 'condominio fruna', 'av siempre viva 1313', 'contacto@condominiofruna.cl', '+56947587069'),
(2, '76.543.210-1', 'Condominio Los Aromos', 'Av. Las Palmas 1450, Santiago', 'contacto@losaromos.cl', '+56961234501'),
(3, '77.654.321-2', 'Condominio Vista Norte', 'Calle Los Arrayanes 2380, Concepción', 'contacto@vistanorte.cl', '+56961234502'),
(4, '78.765.432-3', 'Condominio Parque Central', 'Av. Parque Central 890, Viña del Mar', 'contacto@parquecentral.cl', '+56961234503'),
(5, '79.876.543-4', 'Condominio Altos del Valle', 'Camino El Valle 3210, Rancagua', 'contacto@altosdelvalle.cl', '+56961234504'),
(6, '80.987.654-5', 'Condominio Brisas del Mar', 'Av. Costanera 760, La Serena', 'contacto@brisasdelmar.cl', '+56961234505'),
(7, '81.098.765-6', 'Condominio Jardines del Sur', 'Calle Los Cipreses 1125, Puerto Montt', 'contacto@jardinesdelsur.cl', '+56961234506'),
(8, '82.109.876-7', 'Condominio Portal Oriente', 'Av. Los Libertadores 450, Talca', 'contacto@portaloriente.cl', '+56961234507'),
(9, '83.210.987-8', 'Condominio Santa Elena', 'Calle Santa Elena 1875, Temuco', 'contacto@santaelena.cl', '+56961234508');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `rut_usuario` varchar(20) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `apellido_usuario` varchar(100) NOT NULL,
  `telefono_usuario` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `rol_usuario` varchar(50) NOT NULL,
  `codigo_auth_usuario` varchar(255) NOT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `id_condominio_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `rut_usuario`, `nombre_usuario`, `apellido_usuario`, `telefono_usuario`, `correo`, `rol_usuario`, `codigo_auth_usuario`, `contraseña`, `id_condominio_fk`) VALUES
(1, '12-33333333-4', 'Juan', 'Pérez', '+56987654321', 'juan.pe@admin.conserje.smart.cl', 'admin', 'auth_1', 'Admin2026', 1),
(2, '13-44444444-5', 'Maria', 'Gonzalez', '+56911223344', 'maria.gon@conserje.smart.cl', 'conserje', 'auth_2', 'Conserje2026', 1),
(3, '20.492.799-5', 'joaquin', 'orellana', '+56912345678', 'joaquinor@admin.conserje.smart.cl', 'admin', 'a12a512cc0c673db23a59b7cfa2b2986c61e0cd2f8568cef082b15fc2cc8edcd', 'Wawa1234', 1),
(4, '90.000.001-4', 'ana', 'perez', NULL, 'anape@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-01', 'Admin2026!', 1),
(5, '90.000.002-7', 'benito', 'soto', NULL, 'beniso@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-02', 'Admin2026!', 2),
(6, '90.000.003-5', 'carla', 'diaz', NULL, 'cardi@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-03', 'Admin2026!', 3),
(7, '90.000.004-3', 'daniela', 'flores', NULL, 'danifl@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-04', 'Admin2026!', 4),
(8, '90.000.005-1', 'elena', 'rojas', NULL, 'elero@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-05', 'Admin2026!', 5),
(9, '90.000.007-8', 'fernando', 'silva', NULL, 'fersi@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-06', 'Admin2026!', 6),
(10, '90.000.008-6', 'gabriela', 'mena', NULL, 'gabme@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-07', 'Admin2026!', 7),
(11, '90.000.009-4', 'hector', 'riquelme', NULL, 'hecri@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-08', 'Admin2026!', 8),
(12, '90.000.010-3', 'isabel', 'carrasco', NULL, 'isaca@admin.conserje.smart.cl', 'admin', 'AUTH-ADMIN-COND-09', 'Admin2026!', 9);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `idx_bitacora_condominio_fecha` (`id_condominio_fk`,`fecha_novedad`,`hora_novedad`),
  ADD KEY `idx_bitacora_usuario_fecha` (`id_usuario_fk`,`fecha_novedad`,`hora_novedad`);

--
-- Indices de la tabla `condominio`
--
ALTER TABLE `condominio`
  ADD PRIMARY KEY (`id_condominio`),
  ADD UNIQUE KEY `rut_condominio` (`rut_condominio`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `rut_usuario` (`rut_usuario`),
  ADD KEY `fk_usuario_condominio` (`id_condominio_fk`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `condominio`
--
ALTER TABLE `condominio`
  MODIFY `id_condominio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `fk_bitacora_condominio` FOREIGN KEY (`id_condominio_fk`) REFERENCES `condominio` (`id_condominio`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bitacora_usuario` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_condominio` FOREIGN KEY (`id_condominio_fk`) REFERENCES `condominio` (`id_condominio`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `departamento`
-- Agregada para el módulo de visitas.
--

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `id_condominio_fk` int(11) NOT NULL,
  `numero_departamento` varchar(10) NOT NULL,
  `torre_departamento` varchar(20) NOT NULL DEFAULT '',
  `piso_departamento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos de ejemplo para la tabla `departamento`
--

INSERT INTO `departamento` (`id_departamento`, `id_condominio_fk`, `numero_departamento`, `torre_departamento`, `piso_departamento`) VALUES
(1, 1, '101', '', 1),
(2, 1, '102', '', 1),
(3, 1, '201', '', 2),
(4, 1, '202', '', 2),
(5, 1, '301', '', 3),
(6, 1, '302', '', 3),
(7, 2, '101', '', 1),
(8, 2, '102', '', 1),
(9, 2, '201', '', 2),
(10, 2, '202', '', 2),
(11, 2, '301', '', 3),
(12, 2, '302', '', 3),
(13, 3, '101', '', 1),
(14, 3, '102', '', 1),
(15, 3, '201', '', 2),
(16, 3, '202', '', 2),
(17, 3, '301', '', 3),
(18, 3, '302', '', 3),
(19, 4, '101', '', 1),
(20, 4, '102', '', 1),
(21, 4, '201', '', 2),
(22, 4, '202', '', 2),
(23, 4, '301', '', 3),
(24, 4, '302', '', 3),
(25, 5, '101', '', 1),
(26, 5, '102', '', 1),
(27, 5, '201', '', 2),
(28, 5, '202', '', 2),
(29, 5, '301', '', 3),
(30, 5, '302', '', 3),
(31, 6, '101', '', 1),
(32, 6, '102', '', 1),
(33, 6, '201', '', 2),
(34, 6, '202', '', 2),
(35, 6, '301', '', 3),
(36, 6, '302', '', 3),
(37, 7, '101', '', 1),
(38, 7, '102', '', 1),
(39, 7, '201', '', 2),
(40, 7, '202', '', 2),
(41, 7, '301', '', 3),
(42, 7, '302', '', 3),
(43, 8, '101', '', 1),
(44, 8, '102', '', 1),
(45, 8, '201', '', 2),
(46, 8, '202', '', 2),
(47, 8, '301', '', 3),
(48, 8, '302', '', 3),
(49, 9, '101', '', 1),
(50, 9, '102', '', 1),
(51, 9, '201', '', 2),
(52, 9, '202', '', 2),
(53, 9, '301', '', 3),
(54, 9, '302', '', 3),
(64, 1, '101', 'A', 1),
(65, 1, '102', 'A', 1),
(66, 1, '201', 'A', 2),
(67, 1, '202', 'A', 2),
(68, 1, '301', 'A', 3),
(69, 1, '302', 'A', 3),
(70, 1, '101', 'B', 1),
(71, 1, '201', 'B', 2);

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `visita`
-- Agregada para el módulo de visitas.
--

CREATE TABLE `visita` (
  `id_visita` int(11) NOT NULL,
  `id_condominio_fk` int(11) NOT NULL,
  `id_usuario_fk` int(11) NOT NULL,
  `id_departamento_fk` int(11) NOT NULL,
  `nombre_visitante` varchar(120) NOT NULL,
  `rut_visitante` varchar(12) NOT NULL,
  `motivo_visita` varchar(180) NOT NULL,
  `observaciones_visita` text DEFAULT NULL,
  `fecha_hora_ingreso` datetime NOT NULL,
  `fecha_hora_salida` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Datos de ejemplo para la tabla `visita`
--

INSERT INTO `visita` (`id_visita`, `id_condominio_fk`, `id_usuario_fk`, `id_departamento_fk`, `nombre_visitante`, `rut_visitante`, `motivo_visita`, `observaciones_visita`, `fecha_hora_ingreso`, `fecha_hora_salida`) VALUES
(3, 1, 2, 1, 'Nicolás Saavedra', '21.049.584-3', 'Visita', 'Joven viene con pasamontañas, le indique bajarse el pasamontañas pero no quiso.', '2026-10-20 19:09:15', NULL),
(4, 1, 2, 64, 'Jimena Fuentes', '10.000.013-K', 'Visita a un amigo', NULL, '2026-10-21 16:12:13', NULL),
(5, 1, 2, 6, '123123131231313', '11.111.111-1', '213131231312', 'Encomienda de Blue-Express', '2026-10-21 16:16:54', NULL),
(6, 1, 2, 6, 'Eduardo Barrios', '19.343.567-K', 'Entrega de Paquete', NULL, '2026-10-22 09:20:19', NULL);

--
-- Índices y autoincremento de las tablas del módulo de visitas
--

ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id_departamento`),
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

ALTER TABLE `visita`
  ADD PRIMARY KEY (`id_visita`),
  ADD KEY `idx_visita_condominio_ingreso` (`id_condominio_fk`,`fecha_hora_ingreso`),
  ADD KEY `idx_visita_departamento_ingreso` (`id_departamento_fk`,`fecha_hora_ingreso`),
  ADD KEY `fk_visita_usuario` (`id_usuario_fk`),
  MODIFY `id_visita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Relaciones de las tablas del módulo de visitas con tablas existentes
--

ALTER TABLE `departamento`
  ADD CONSTRAINT `fk_departamento_condominio` FOREIGN KEY (`id_condominio_fk`) REFERENCES `condominio` (`id_condominio`) ON UPDATE CASCADE;

ALTER TABLE `visita`
  ADD CONSTRAINT `fk_visita_condominio` FOREIGN KEY (`id_condominio_fk`) REFERENCES `condominio` (`id_condominio`),
  ADD CONSTRAINT `fk_visita_departamento` FOREIGN KEY (`id_departamento_fk`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `fk_visita_usuario` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
