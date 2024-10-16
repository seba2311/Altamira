-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         11.5.2-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para altamira
CREATE DATABASE IF NOT EXISTS `altamira` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `altamira`;

-- Volcando estructura para tabla altamira.detalle_guia_entrada
CREATE TABLE IF NOT EXISTS `detalle_guia_entrada` (
  `gdet_id` int(11) NOT NULL AUTO_INCREMENT,
  `gdet_guia_entrada` varchar(50) NOT NULL DEFAULT '0',
  `gdet_producto` varchar(50) NOT NULL DEFAULT '0',
  `gdet_cantidad` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`gdet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.detalle_guia_entrada: ~14 rows (aproximadamente)
INSERT INTO `detalle_guia_entrada` (`gdet_id`, `gdet_guia_entrada`, `gdet_producto`, `gdet_cantidad`) VALUES
	(1, '147852', 'CPU002', 10),
	(2, '15852', 'MB001', 5),
	(3, '123456', 'RAM001', 10),
	(4, '123456', 'RAM002', 5),
	(6, '12547', 'RAM001', 5),
	(7, '12547', 'MON004', 5),
	(8, '12548', 'MOUSE001', 5),
	(9, '158236', 'MOUSE002', 2),
	(10, '25842', 'MOUSE003', 5),
	(11, '25842', 'RAM003', 5),
	(15, '2563584', 'RAM002', 10),
	(16, '85932', 'MOUSE003', 10),
	(17, '21561561', 'CASE004', 12),
	(18, '526189523', 'CASE004', 12);

-- Volcando estructura para tabla altamira.detalle_nota_venta
CREATE TABLE IF NOT EXISTS `detalle_nota_venta` (
  `ndet_id` int(11) NOT NULL AUTO_INCREMENT,
  `ndet_nota_venta` varchar(50) NOT NULL DEFAULT '0',
  `ndet_producto` varchar(50) NOT NULL DEFAULT '0',
  `ndet_cantidad` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ndet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.detalle_nota_venta: ~50 rows (aproximadamente)
INSERT INTO `detalle_nota_venta` (`ndet_id`, `ndet_nota_venta`, `ndet_producto`, `ndet_cantidad`) VALUES
	(1, '1001', 'GPU002', 3),
	(2, '1001', 'RAM006', 9),
	(3, '1001', 'KB004', 6),
	(4, '1002', 'SSD007', 9),
	(5, '1002', 'MON006', 2),
	(6, '1003', 'SSD002', 9),
	(7, '1003', 'MB001', 6),
	(8, '1003', 'RAM003', 9),
	(9, '1004', 'CPU001', 5),
	(10, '1004', 'PSU004', 6),
	(11, '1005', '525', 8),
	(12, '1005', 'PSU006', 8),
	(13, '1005', 'RAM001', 9),
	(14, '1006', 'SSD008', 10),
	(15, '1006', 'OS004', 9),
	(16, '1007', 'HDD005', 7),
	(17, '1007', 'OS005', 10),
	(18, '1008', 'PSU003', 7),
	(19, '1008', 'SSD006', 1),
	(20, '1009', 'CASE003', 10),
	(21, '1009', 'RAM003', 4),
	(22, '1010', 'SSD001', 6),
	(23, '1010', 'HDD002', 4),
	(24, '1010', 'COOL004', 3),
	(25, '1011', 'CPU003', 8),
	(26, '1011', 'SSD005', 7),
	(27, '1011', 'RAM003', 8),
	(28, '1012', 'MOUSE006', 1),
	(29, '1012', 'MON001', 8),
	(30, '1013', 'MON005', 5),
	(31, '1013', 'CPU005', 1),
	(32, '1013', 'HDD003', 1),
	(33, '1014', 'HDD008', 6),
	(34, '1015', 'PSU006', 3),
	(35, '1015', 'AUD002', 7),
	(36, '1016', 'OS005', 6),
	(37, '1016', 'HDD003', 9),
	(38, '1016', 'GPU001', 5),
	(39, '1017', 'CASE002', 1),
	(40, '1017', 'RAM005', 8),
	(41, '1017', 'KB003', 6),
	(42, '1018', 'CASE005', 10),
	(43, '1018', 'AUD002', 5),
	(44, '1018', 'OS005', 3),
	(45, '1019', 'GPU005', 10),
	(46, '1020', 'COOL003', 6),
	(47, '1020', 'RAM005', 3),
	(48, '1020', 'KB006', 4),
	(49, '23615', 'MOUSE001', 5),
	(50, '23615', 'MOUSE002', 5);

-- Volcando estructura para tabla altamira.etiquetas
CREATE TABLE IF NOT EXISTS `etiquetas` (
  `eti_id` int(11) NOT NULL AUTO_INCREMENT,
  `eti_numero` varchar(50) NOT NULL DEFAULT '0',
  `eti_producto` varchar(100) NOT NULL DEFAULT '0',
  `eti_nota_venta` varchar(100) DEFAULT NULL,
  `eti_guia_entrada` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`eti_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.etiquetas: ~44 rows (aproximadamente)
INSERT INTO `etiquetas` (`eti_id`, `eti_numero`, `eti_producto`, `eti_nota_venta`, `eti_guia_entrada`) VALUES
	(50, '100000000000000000000001', 'RAM002', NULL, NULL),
	(51, '100000000000000000000002', 'RAM002', NULL, NULL),
	(52, '100000000000000000000003', 'RAM002', NULL, NULL),
	(53, '100000000000000000000004', 'RAM002', NULL, NULL),
	(54, '100000000000000000000005', 'RAM002', NULL, NULL),
	(55, '100000000000000000000006', 'RAM002', NULL, NULL),
	(56, '100000000000000000000007', 'RAM002', NULL, NULL),
	(57, '100000000000000000000008', 'RAM002', NULL, NULL),
	(58, '100000000000000000000009', 'RAM002', NULL, NULL),
	(59, '100000000000000000000010', 'RAM002', NULL, NULL),
	(60, '100000000000000000000011', 'MOUSE003', NULL, '85932'),
	(61, '100000000000000000000012', 'MOUSE003', NULL, '85932'),
	(62, '100000000000000000000013', 'MOUSE003', NULL, '85932'),
	(63, '100000000000000000000014', 'MOUSE003', NULL, '85932'),
	(64, '100000000000000000000015', 'MOUSE003', NULL, '85932'),
	(65, '100000000000000000000016', 'MOUSE003', NULL, '85932'),
	(66, '100000000000000000000017', 'MOUSE003', NULL, '85932'),
	(67, '100000000000000000000018', 'MOUSE003', NULL, '85932'),
	(68, '100000000000000000000019', 'MOUSE003', NULL, '85932'),
	(69, '100000000000000000000020', 'MOUSE003', NULL, '85932'),
	(70, '100000000000000000000021', 'CASE004', NULL, '21561561'),
	(71, '100000000000000000000022', 'CASE004', NULL, '21561561'),
	(72, '100000000000000000000023', 'CASE004', NULL, '21561561'),
	(73, '100000000000000000000024', 'CASE004', NULL, '21561561'),
	(74, '100000000000000000000025', 'CASE004', NULL, '21561561'),
	(75, '100000000000000000000026', 'CASE004', NULL, '21561561'),
	(76, '100000000000000000000027', 'CASE004', NULL, '21561561'),
	(77, '100000000000000000000028', 'CASE004', NULL, '21561561'),
	(78, '100000000000000000000029', 'CASE004', NULL, '21561561'),
	(79, '100000000000000000000030', 'CASE004', NULL, '21561561'),
	(80, '100000000000000000000031', 'CASE004', NULL, '21561561'),
	(81, '100000000000000000000032', 'CASE004', NULL, '21561561'),
	(82, '100000000000000000000033', 'CASE004', NULL, '526189523'),
	(83, '100000000000000000000034', 'CASE004', NULL, '526189523'),
	(84, '100000000000000000000035', 'CASE004', NULL, '526189523'),
	(85, '100000000000000000000036', 'CASE004', NULL, '526189523'),
	(86, '100000000000000000000037', 'CASE004', NULL, '526189523'),
	(87, '100000000000000000000038', 'CASE004', NULL, '526189523'),
	(88, '100000000000000000000039', 'CASE004', NULL, '526189523'),
	(89, '100000000000000000000040', 'CASE004', NULL, '526189523'),
	(90, '100000000000000000000041', 'CASE004', NULL, '526189523'),
	(91, '100000000000000000000042', 'CASE004', NULL, '526189523'),
	(92, '100000000000000000000043', 'CASE004', NULL, '526189523'),
	(93, '100000000000000000000044', 'CASE004', NULL, '526189523');

-- Volcando estructura para tabla altamira.guia_entrada
CREATE TABLE IF NOT EXISTS `guia_entrada` (
  `guia_id` int(11) NOT NULL AUTO_INCREMENT,
  `guia_folio` varchar(50) NOT NULL DEFAULT '0',
  `guia_fecha` date NOT NULL DEFAULT '0000-00-00',
  `guia_estado` char(3) NOT NULL DEFAULT 'PND',
  `guia_glosa` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`guia_id`),
  UNIQUE KEY `guia_folio` (`guia_folio`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.guia_entrada: ~11 rows (aproximadamente)
INSERT INTO `guia_entrada` (`guia_id`, `guia_folio`, `guia_fecha`, `guia_estado`, `guia_glosa`) VALUES
	(1, '147852', '2024-10-13', 'RCP', 'Prueba de guia de entrada'),
	(2, '15852', '2024-10-13', 'PND', 'crear guía prueba'),
	(3, '123456', '2024-10-13', 'PND', 'prueba múltiples detalles'),
	(5, '12547', '2024-10-13', 'RCP', 'prueba de creación de guia con etiquetas'),
	(6, '12548', '2024-10-13', 'PND', 'prueba de creación de guia con etiquetas 2'),
	(8, '158236', '2024-10-13', 'RCP', 'prueba de creación de guia con etiquetas 3'),
	(10, '25842', '2024-10-13', 'PND', 'prueba de etiquetas multiples'),
	(14, '2563584', '2024-10-13', 'PND', 'crear guía prueba'),
	(15, '85932', '2024-10-13', 'RCP', 'guia de pruebas 52'),
	(16, '21561561', '2024-10-14', 'RCP', 'ajuste de stock por guía '),
	(17, '526189523', '2024-10-14', 'RCP', 'pruebas de stock 5');

-- Volcando estructura para tabla altamira.nota_venta
CREATE TABLE IF NOT EXISTS `nota_venta` (
  `nv_id` int(11) NOT NULL AUTO_INCREMENT,
  `nv_folio` varchar(50) NOT NULL DEFAULT '0',
  `nv_fecha` date NOT NULL DEFAULT '0000-00-00',
  `nv_estado` char(3) NOT NULL DEFAULT 'PND',
  `nv_glosa` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`nv_id`),
  UNIQUE KEY `nv_folio` (`nv_folio`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.nota_venta: ~21 rows (aproximadamente)
INSERT INTO `nota_venta` (`nv_id`, `nv_folio`, `nv_fecha`, `nv_estado`, `nv_glosa`) VALUES
	(1, '1001', '2024-09-23', 'PND', 'Nota de venta de prueba'),
	(2, '1002', '2024-09-12', 'PND', 'Nota de venta de prueba'),
	(3, '1003', '2024-09-10', 'PND', 'Nota de venta de prueba'),
	(4, '1004', '2024-10-04', 'PND', 'Nota de venta de prueba'),
	(5, '1005', '2024-10-09', 'PND', 'Nota de venta de prueba'),
	(6, '1006', '2024-09-26', 'PND', 'Nota de venta de prueba'),
	(7, '1007', '2024-10-03', 'PND', 'Nota de venta de prueba'),
	(8, '1008', '2024-09-15', 'PND', 'Nota de venta de prueba'),
	(9, '1009', '2024-09-30', 'PND', 'Nota de venta de prueba'),
	(10, '1010', '2024-10-03', 'PND', 'Nota de venta de prueba'),
	(11, '1011', '2024-10-06', 'PND', 'Nota de venta de prueba'),
	(12, '1012', '2024-09-12', 'PND', 'Nota de venta de prueba'),
	(13, '1013', '2024-10-02', 'PND', 'Nota de venta de prueba'),
	(14, '1014', '2024-09-24', 'PND', 'Nota de venta de prueba'),
	(15, '1015', '2024-09-16', 'PND', 'Nota de venta de prueba'),
	(16, '1016', '2024-09-27', 'PND', 'Nota de venta de prueba'),
	(17, '1017', '2024-09-17', 'PND', 'Nota de venta de prueba'),
	(18, '1018', '2024-09-23', 'PND', 'Nota de venta de prueba'),
	(19, '1019', '2024-09-27', 'PND', 'Nota de venta de prueba'),
	(20, '1020', '2024-09-24', 'PND', 'Nota de venta de prueba'),
	(21, '23615', '2024-10-14', 'PND', 'nota de ventas creadas por formulario');

-- Volcando estructura para tabla altamira.producto
CREATE TABLE IF NOT EXISTS `producto` (
  `pro_id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_codigo` varchar(200) NOT NULL,
  `pro_nombre` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pro_id`),
  UNIQUE KEY `pro_codigo` (`pro_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=316 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.producto: ~104 rows (aproximadamente)
INSERT INTO `producto` (`pro_id`, `pro_codigo`, `pro_nombre`) VALUES
	(209, 'CPU001', 'Procesador Intel Core i9-11900K'),
	(210, 'CPU002', 'Procesador AMD Ryzen 9 5950X'),
	(211, 'MB001', 'Placa base ASUS ROG Strix Z590-E Gaming'),
	(212, 'MB002', 'Placa base MSI MPG B550 Gaming Edge WiFi'),
	(213, 'RAM001', 'Memoria RAM Corsair Vengeance RGB Pro 32GB'),
	(214, 'RAM002', 'Memoria RAM G.Skill Trident Z Neo 64GB'),
	(215, 'GPU001', 'Tarjeta gráfica NVIDIA GeForce RTX 3080'),
	(216, 'GPU002', 'Tarjeta grafica AMD Radeon RX 6800 XT'),
	(217, 'SSD001', 'SSD Samsung 970 EVO Plus 1TB'),
	(218, 'SSD002', 'SSD Crucial MX500 2TB'),
	(219, 'HDD001', 'Disco duro Seagate Barracuda 4TB'),
	(220, 'HDD002', 'Disco duro Western Digital Blue 6TB'),
	(221, 'PSU001', 'Fuente de alimentación Corsair RM850x'),
	(222, 'PSU002', 'Fuente de alimentación EVGA SuperNOVA 750 G5'),
	(223, 'CASE001', 'Caja NZXT H510i'),
	(224, 'CASE002', 'Caja Fractal Design Meshify C'),
	(225, 'COOL001', 'Refrigeración líquida NZXT Kraken X53'),
	(226, 'COOL002', 'Ventilador Noctua NH-D15'),
	(227, 'MON001', 'Monitor ASUS TUF Gaming VG27AQ'),
	(228, 'MON002', 'Monitor LG UltraFine 5K'),
	(229, 'KB001', 'Teclado mecánico Logitech G915'),
	(230, 'KB002', 'Teclado Razer BlackWidow V3 Pro'),
	(231, 'MOUSE001', 'Ratón Logitech G502 HERO'),
	(232, 'MOUSE002', 'Ratón Razer DeathAdder V2'),
	(233, 'AUD001', 'Auriculares HyperX Cloud II'),
	(234, 'AUD002', 'Auriculares SteelSeries Arctis Pro'),
	(235, 'NET001', 'Tarjeta de red TP-Link Archer TX3000E'),
	(236, 'NET002', 'Adaptador Wi-Fi USB Netgear Nighthawk AC1900'),
	(237, 'OS001', 'Sistema Operativo Windows 10 Pro'),
	(238, 'OS002', 'Sistema Operativo Ubuntu 20.04 LTS'),
	(239, 'CPU003', 'Procesador Intel Core i7-10700K'),
	(240, 'CPU004', 'Procesador AMD Ryzen 7 5800X'),
	(241, 'MB003', 'Placa base Gigabyte X570 AORUS Elite'),
	(242, 'MB004', 'Placa base ASRock B450M PRO4'),
	(243, 'RAM003', 'Memoria RAM Kingston HyperX Fury 16GB'),
	(244, 'RAM004', 'Memoria RAM Crucial Ballistix 32GB'),
	(245, 'GPU003', 'Tarjeta gráfica NVIDIA GeForce RTX 3070'),
	(246, 'GPU004', 'Tarjeta gráfica AMD Radeon RX 6700 XT'),
	(247, 'SSD003', 'SSD Western Digital Black SN750 500GB'),
	(248, 'SSD004', 'SSD Intel 660p 1TB'),
	(249, 'HDD003', 'Disco duro Toshiba P300 3TB'),
	(250, 'HDD004', 'Disco duro HGST Ultrastar 8TB'),
	(251, 'PSU003', 'Fuente de alimentación be quiet! Straight Power 11 750W'),
	(252, 'PSU004', 'Fuente de alimentación Seasonic FOCUS GX-650'),
	(253, 'CASE003', 'Caja Cooler Master MasterBox TD500 Mesh'),
	(254, 'CASE004', 'Caja Phanteks Eclipse P400A'),
	(255, 'COOL003', 'Refrigeración líquida Corsair H100i RGB Platinum'),
	(256, 'COOL004', 'Ventilador be quiet! Dark Rock Pro 4'),
	(257, 'MON003', 'Monitor Dell S2721DGF'),
	(258, 'MON004', 'Monitor BenQ EX2780Q'),
	(259, 'KB003', 'Teclado Corsair K95 RGB Platinum XT'),
	(260, 'KB004', 'Teclado Ducky One 2 Mini'),
	(261, 'MOUSE003', 'Ratón SteelSeries Rival 600'),
	(262, 'MOUSE004', 'Ratón Glorious Model O'),
	(263, 'AUD003', 'Auriculares Sennheiser GSP 600'),
	(264, 'AUD004', 'Auriculares Astro A50 Wireless'),
	(265, 'NET003', 'Tarjeta de red Intel Wi-Fi 6 AX200'),
	(266, 'NET004', 'Switch de red TP-Link TL-SG108'),
	(267, 'OS003', 'Sistema Operativo Windows 11 Home'),
	(268, 'OS004', 'Sistema Operativo Fedora 34 Workstation'),
	(269, 'CPU005', 'Procesador Intel Core i5-11600K'),
	(270, 'CPU006', 'Procesador AMD Ryzen 5 5600X'),
	(271, 'MB005', 'Placa base MSI MAG B560 TOMAHAWK WIFI'),
	(272, 'MB006', 'Placa base ASUS TUF Gaming X570-Plus'),
	(273, 'RAM005', 'Memoria RAM Team T-Force Delta RGB 32GB'),
	(274, 'RAM006', 'Memoria RAM Patriot Viper Steel 64GB'),
	(275, 'GPU005', 'Tarjeta gráfica NVIDIA GeForce RTX 3060 Ti'),
	(276, 'GPU006', 'Tarjeta gráfica AMD Radeon RX 6600 XT'),
	(277, 'SSD005', 'SSD Sabrent Rocket Q 2TB'),
	(278, 'SSD006', 'SSD ADATA XPG SX8200 Pro 1TB'),
	(279, 'HDD005', 'Disco duro Seagate IronWolf 6TB'),
	(280, 'HDD006', 'Disco duro Western Digital Red Plus 8TB'),
	(281, 'PSU005', 'Fuente de alimentación Thermaltake Toughpower GF1 750W'),
	(282, 'PSU006', 'Fuente de alimentación Cooler Master V650 Gold'),
	(283, 'CASE005', 'Caja Lian Li PC-O11 Dynamic'),
	(284, 'CASE006', 'Caja be quiet! Pure Base 500DX'),
	(285, 'COOL005', 'Refrigeración líquida Arctic Freezer II 280'),
	(286, 'COOL006', 'Ventilador Scythe Mugen 5 Rev.B'),
	(287, 'MON005', 'Monitor Gigabyte G27Q'),
	(288, 'MON006', 'Monitor ViewSonic Elite XG270QG'),
	(289, 'KB005', 'Teclado HyperX Alloy Origins Core'),
	(290, 'KB006', 'Teclado SteelSeries Apex Pro'),
	(291, 'MOUSE005', 'Ratón Corsair Dark Core RGB Pro'),
	(292, 'MOUSE006', 'Ratón Zowie EC2'),
	(293, 'AUD005', 'Auriculares Beyerdynamic MMX 300'),
	(294, 'AUD006', 'Auriculares Audeze Mobius'),
	(295, 'NET005', 'Adaptador Bluetooth USB TP-Link UB400'),
	(296, 'NET006', 'Router ASUS RT-AX86U'),
	(297, 'OS005', 'Sistema Operativo macOS Big Sur'),
	(298, 'OS006', 'Sistema Operativo Linux Mint 20.2'),
	(299, 'CPU007', 'Procesador Intel Core i3-10100'),
	(300, 'CPU008', 'Procesador AMD Ryzen 3 3300X'),
	(301, 'MB007', 'Placa base ASRock H510M-HDV'),
	(302, 'MB008', 'Placa base Gigabyte B450M DS3H'),
	(303, 'RAM007', 'Memoria RAM Corsair Dominator Platinum RGB 32GB'),
	(304, 'RAM008', 'Memoria RAM ADATA XPG Spectrix D60G 16GB'),
	(305, 'GPU007', 'Tarjeta gráfica NVIDIA GeForce GTX 1660 Super'),
	(306, 'GPU008', 'Tarjeta gráfica AMD Radeon RX 5600 XT'),
	(307, 'SSD007', 'SSD Corsair MP600 1TB'),
	(308, 'SSD008', 'SSD Kingston A2000 500GB'),
	(309, 'HDD007', 'Disco duro Seagate Exos 10TB'),
	(310, 'HDD008', 'Disco duro Western Digital Purple 4TB'),
	(311, '525', 'prueba de stock'),
	(312, '1512541', 'prueba'),
	(314, '159', 'producto');

-- Volcando estructura para tabla altamira.stock
CREATE TABLE IF NOT EXISTS `stock` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_producto` varchar(50) NOT NULL DEFAULT '0',
  `stock_cantidad` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`stock_id`),
  UNIQUE KEY `stock_producto` (`stock_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.stock: ~98 rows (aproximadamente)
INSERT INTO `stock` (`stock_id`, `stock_producto`, `stock_cantidad`) VALUES
	(1, '15987', 13),
	(2, '15988', 67),
	(3, 'AUD001', 95),
	(4, 'AUD002', 74),
	(5, 'AUD003', 83),
	(6, 'AUD004', 94),
	(7, 'AUD005', 22),
	(8, 'AUD006', 24),
	(9, 'CASE001', 155),
	(10, 'CASE002', 100),
	(11, 'CASE003', 48),
	(12, 'CASE004', 100),
	(13, 'CASE005', 26),
	(14, 'CASE006', 65),
	(15, 'COOL001', 45),
	(16, 'COOL002', 32),
	(17, 'COOL003', 22),
	(18, 'COOL004', 14),
	(19, 'COOL005', 4),
	(20, 'COOL006', 76),
	(21, 'CPU001', 68),
	(22, 'CPU002', 22),
	(23, 'CPU003', 54),
	(24, 'CPU004', 35),
	(25, 'CPU005', 10),
	(26, 'CPU006', 48),
	(27, 'CPU007', 7),
	(28, 'CPU008', 91),
	(29, 'GPU001', 35),
	(30, 'GPU002', 99),
	(31, 'GPU003', 90),
	(32, 'GPU004', 51),
	(33, 'GPU005', 88),
	(34, 'GPU006', 84),
	(35, 'GPU007', 57),
	(36, 'GPU008', 34),
	(37, 'HDD001', 96),
	(38, 'HDD002', 80),
	(39, 'HDD003', 9),
	(40, 'HDD004', 6),
	(41, 'HDD005', 1),
	(42, 'HDD006', 89),
	(43, 'HDD007', 40),
	(44, 'HDD008', 34),
	(45, 'KB001', 47),
	(46, 'KB002', 33),
	(47, 'KB003', 24),
	(48, 'KB004', 20),
	(49, 'KB005', 26),
	(50, 'KB006', 71),
	(51, 'MB001', 500),
	(52, 'MB002', 60),
	(53, 'MB003', 78),
	(54, 'MB004', 8),
	(55, 'MB005', 6),
	(56, 'MB006', 3),
	(57, 'MB007', 100),
	(58, 'MB008', 89),
	(59, 'MON001', 44),
	(60, 'MON002', 54),
	(61, 'MON003', 37),
	(62, 'MON004', 25),
	(63, 'MON005', 92),
	(64, 'MON006', 99),
	(65, 'MOUSE001', 18),
	(66, 'MOUSE002', 95),
	(67, 'MOUSE003', 10),
	(68, 'MOUSE004', 72),
	(69, 'MOUSE005', 29),
	(70, 'MOUSE006', 29),
	(71, 'NET001', 57),
	(72, 'NET002', 100),
	(73, 'NET003', 25),
	(74, 'NET004', 24),
	(75, 'NET005', 48),
	(76, 'NET006', 64),
	(77, 'OS001', 77),
	(78, 'OS002', 91),
	(79, 'OS003', 26),
	(80, 'OS004', 54),
	(81, 'OS005', 94),
	(82, 'OS006', 6),
	(83, 'PSU001', 47),
	(84, 'PSU002', 15),
	(85, 'PSU003', 37),
	(86, 'PSU004', 37),
	(87, 'PSU005', 75),
	(88, 'PSU006', 65),
	(89, 'RAM001', 161),
	(90, 'RAM002', 86),
	(91, 'RAM003', 42),
	(92, 'RAM004', 51),
	(93, 'RAM005', 27),
	(94, 'RAM006', 83),
	(95, 'RAM007', 31),
	(96, 'RAM008', 9),
	(97, 'SSD001', 50),
	(98, 'SSD002', 21),
	(99, 'SSD003', 54),
	(100, 'SSD004', 7),
	(101, 'SSD005', 72),
	(102, 'SSD006', 39),
	(103, 'SSD007', 80),
	(104, 'SSD008', 80);

-- Volcando estructura para tabla altamira.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nom_usuario` varchar(100) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT 'https://png.pngtree.com/png-clipart/20190516/original/pngtree-users-vector-icon-png-image_3725294.jpg',
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla altamira.usuarios: ~4 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `nom_usuario`, `usuario`, `clave`, `foto_perfil`) VALUES
	(1, 'Administrador', 'admin', 'admin', 'https://png.pngtree.com/png-clipart/20190516/original/pngtree-users-vector-icon-png-image_3725294.jpg'),
	(2, 'sebastian', 'seba', 'seba1550', 'uploads/sin_foto.jpg'),
	(3, 'Usuario de  pruebas', 'prueba', '123123', 'https://png.pngtree.com/png-clipart/20190516/original/pngtree-users-vector-icon-png-image_3725294.jpg'),
	(4, 'nuevo usuario', 'usuario', 'usuario123', 'https://png.pngtree.com/png-clipart/20190516/original/pngtree-users-vector-icon-png-image_3725294.jpg');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
