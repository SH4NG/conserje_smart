-- Migración aditiva del módulo de visitas para una base ya existente.
-- Ejecutar una sola vez en `conserje_smart_bd`.
-- Solo crea y llena las tablas `departamento` y `visita`.
-- No modifica ni reemplaza las tablas `bitacora`, `condominio` o `usuario`.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `id_condominio_fk` int(11) NOT NULL,
  `numero_departamento` varchar(10) NOT NULL,
  `torre_departamento` varchar(20) NOT NULL DEFAULT '',
  `piso_departamento` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_departamento`),
  KEY `idx_departamento_condominio` (`id_condominio_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `fecha_hora_salida` datetime DEFAULT NULL,
  PRIMARY KEY (`id_visita`),
  KEY `idx_visita_condominio_ingreso` (`id_condominio_fk`,`fecha_hora_ingreso`),
  KEY `idx_visita_departamento_ingreso` (`id_departamento_fk`,`fecha_hora_ingreso`),
  KEY `fk_visita_usuario` (`id_usuario_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `visita` (`id_visita`, `id_condominio_fk`, `id_usuario_fk`, `id_departamento_fk`, `nombre_visitante`, `rut_visitante`, `motivo_visita`, `observaciones_visita`, `fecha_hora_ingreso`, `fecha_hora_salida`) VALUES
(3, 1, 2, 1, 'Nicolás Saavedra', '21.049.584-3', 'Visita', 'Joven viene con pasamontañas, le indique bajarse el pasamontañas pero no quiso.', '2026-10-20 19:09:15', NULL),
(4, 1, 2, 64, 'Jimena Fuentes', '10.000.013-K', 'Visita a un amigo', NULL, '2026-10-21 16:12:13', NULL),
(5, 1, 2, 6, '123123131231313', '11.111.111-1', '213131231312', 'Encomienda de Blue-Express', '2026-10-21 16:16:54', NULL),
(6, 1, 2, 6, 'Eduardo Barrios', '19.343.567-K', 'Entrega de Paquete', NULL, '2026-10-22 09:20:19', NULL);

ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72,
  ADD CONSTRAINT `fk_departamento_condominio` FOREIGN KEY (`id_condominio_fk`) REFERENCES `condominio` (`id_condominio`) ON UPDATE CASCADE;

ALTER TABLE `visita`
  MODIFY `id_visita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7,
  ADD CONSTRAINT `fk_visita_condominio` FOREIGN KEY (`id_condominio_fk`) REFERENCES `condominio` (`id_condominio`),
  ADD CONSTRAINT `fk_visita_departamento` FOREIGN KEY (`id_departamento_fk`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `fk_visita_usuario` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`);
