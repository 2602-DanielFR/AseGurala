-- =========================================================================
-- ESTRUCTURA Y PADRÓN DE 41 ALUMNOS INTEGRADOS (ZKTECO MB10-VL)
-- =========================================================================

CREATE TABLE IF NOT EXISTS `alumnos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dni` varchar(20) NOT NULL,
  `codigo` varchar(30) DEFAULT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) NOT NULL DEFAULT '',
  `email` varchar(120) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumnos_dni_unique` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `curso_alumno` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `curso_id` bigint unsigned NOT NULL,
  `alumno_id` bigint unsigned NOT NULL,
  `seccion` varchar(20) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `curso_alumno_unique` (`curso_id`, `alumno_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sesion_clase_id` bigint unsigned NOT NULL,
  `alumno_id` bigint unsigned NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'falta',
  `hora_marcacion` datetime DEFAULT NULL,
  `origen` varchar(30) NOT NULL DEFAULT 'ZKTeco MB10-VL',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asistencias_sesion_alumno_unique` (`sesion_clase_id`, `alumno_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `alumnos` (`dni`, `codigo`, `apellidos`, `nombres`, `estado`) VALUES
('70000001', '1',  'ALVA BERROSPI',         'ANGELA XIMENA', 1),
('70000002', '2',  'ALVARADO CAMPOS',       'BRENDA', 1),
('70000003', '3',  'BARRAGAN CERDAN',       'CESAR ALBERTO', 1),
('70000004', '4',  'BARRUETA MARCELLINI',   'CESAR RAFAEL', 1),
('70000005', '5',  'CAJAS BERNARDO',        'JHAN CRHISTIAN', 1),
('70000006', '6',  'CEFERINO OLORTIN',      'LEONARDO JAREN', 1),
('70000007', '7',  'CUARITE ESTRADA',       'CARLOS MANUEL', 1),
('70000008', '8',  'CUEVA SANTAMARIA',      'LEITO OSCAR', 1),
('70000009', '9',  'DUEÑAS LOYOLA',         'YHOZIRA MILAGROS', 1),
('70000010', '10', 'ESPINOZA CASTAÑEDA',    'ADAMARIS VALENTINA', 1),
('70000011', '11', 'ESPINOZA DELGADO',      'MARCO NICCOLO', 1),
('70000012', '12', 'ESTRADA RIVERA',        'JAMIL', 1),
('70000013', '13', 'FERNANDEZ RAMOS',       'JUAN JOSE', 1),
('70000014', '14', 'FRANCISCO ROJAS',       'DANIEL', 1),
('70000015', '15', 'GODOY CONDEZO',         'ALEJANDRO JEANPIER', 1),
('70000016', '16', 'HUANCAYA RECINES',      'ERICSSON JUNIOR', 1),
('70000017', '17', 'LOZANO LEANDRO',        'TAYLOR GEOFRE', 1),
('70000018', '18', 'MANZANO SOTO',          'FRANK DAYVIS', 1),
('70000019', '19', 'MENDOZA LOPEZ',         'MARCO ANTONIO', 1),
('70000020', '20', 'MENDOZA VILLANUEVA',    'MAYRA MIRIAM', 1),
('70000021', '21', 'NALVARTE MANZANO',      'ORLANDO BRYAM', 1),
('70000022', '22', 'PEÑA RAMÍREZ',          'KAROL ANDREA', 1),
('70000023', '23', 'POMA RECINAS',          'LADY DAYANA', 1),
('70000024', '24', 'POMA SANCHEZ',          'ROBERTO CARLOS', 1),
('70000025', '25', 'QUIROZ LUNA',           'JULIO CESAR', 1),
('70000026', '26', 'RAFAEL RAMON',          'HELI HIDETOSHI', 1),
('70000027', '27', 'RIOS SALDAÑA',          'KLEYVIN ADIEL', 1),
('70000028', '28', 'ROJAS MALDONADO',       'PERCY JOHAN', 1),
('70000029', '29', 'SALAZAR TARAZONA',      'JORGE LUIS', 1),
('70000030', '30', 'SANCHEZ CELIS',         'ALEXIS DEL PIERO', 1),
('70000031', '31', 'SANTIAGO JUAN DE DIOS', 'YEZER', 1),
('70000032', '32', 'SARAVIA LEON',          'WILLIAM ANTONIO', 1),
('70000033', '33', 'SIMON GONZALES',        'RONALDO JHOSEP', 1),
('70000034', '34', 'SORIA BLAS',            'LUSDITH DIANA', 1),
('70000035', '35', 'TORRES PALOMINO',       'CARLOS ADOLFO ANTONIO', 1),
('70000036', '36', 'TRUJILLO AVILA',        'MARCEL ALEXANDER JAVIER', 1),
('70000037', '37', 'VALDIZAN ARCE',         'DIACK KENNEDY', 1),
('70000038', '38', 'VERASTEGUI SANTIAGO',   'SILVANA DANITZA', 1),
('70000039', '39', 'VILCA ROSAS',           'JOEL AUGUSTO', 1),
('70000040', '40', 'ZEGARRA PIMENTEL',      'JHONNY CHRISTIAN', 1),
('70000041', '41', 'ZEVALLOS SALAS',        'DIEGO ARNOLD', 1);
