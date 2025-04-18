<!-- en el archivo includes/dp.php actualizar de los datos de la base de datos -->

$host = "localhost";
$usuario = "root";
$contraseña = "";  👈👈
$baseDeDatos = "condominio";


<!-- Creación de la base de datos -->

-- base de datos "condominio" referenciada en el código
CREATE DATABASE `condominio` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */

<!-- Creación de las tablas -->

-- 1. Tabla usuarios (base para las relaciones)
CREATE TABLE `usuarios` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
 `numero_casa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `rol` enum('residente','administrador') COLLATE utf8mb4_unicode_ci NOT NULL,
 `correo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
 `contraseña` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

-- 2. Tabla documentos (no tiene dependencias)
CREATE TABLE `documentos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `tipo` enum('obra','trasteo','alquiler_area_social') COLLATE utf8mb4_unicode_ci NOT NULL,
 `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

-- 3. Tabla recibos (depende de usuarios)
CREATE TABLE `recibos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `usuario_id` int(11) NOT NULL,
 `monto` decimal(10,2) NOT NULL,
 `fecha_limite` date NOT NULL,
 `estado` enum('Pendiente','Pagado','Vencido','En acuerdo de pago','En mora') DEFAULT 'Pendiente',
 PRIMARY KEY (`id`),
 KEY `usuario_id` (`usuario_id`),
 CONSTRAINT `recibos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

-- 4. Tabla solicitudes (depende de usuarios)
CREATE TABLE `solicitudes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `usuario_id` int(11) NOT NULL,
 `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `fecha` datetime NOT NULL,
 PRIMARY KEY (`id`),
 KEY `fk_usuario` (`usuario_id`),
 CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
 CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

<!-- Creación del usuario Administrador con contraseña encriptada -->

INSERT INTO usuarios (nombre, numero_casa, rol, correo, contraseña)
VALUES ('Administrador', 'NULL', 'administrador', 'admin@condominio.com', MD5('admin123'));