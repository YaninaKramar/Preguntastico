-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-07-2025 a las 23:34:27
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `preguntastico`
--
CREATE DATABASE IF NOT EXISTS `preguntastico` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `preguntastico`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `color` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `color`) VALUES
(1, 'Geografía', '#1E90FF'),
(2, 'Historia', '#FFD700'),
(3, 'Ciencia', '#228B22'),
(4, 'Deportes', '#FFA500'),
(5, 'Entretenimiento', '#800080'),
(6, 'Arte', '#8B0000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida`
--

CREATE TABLE `partida` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `puntaje_final` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partida`
--

INSERT INTO `partida` (`id`, `fecha`, `puntaje_final`, `usuario_id`, `fecha_creacion`) VALUES
(1, '2025-07-01', 60, 6, '2025-07-06 20:58:13'),
(2, '2025-06-18', 85, 4, '2025-07-06 20:58:13'),
(3, '2025-07-01', 30, 8, '2025-07-06 20:58:13'),
(4, '2025-06-30', 45, 5, '2025-07-06 20:58:13'),
(5, '2025-06-21', 70, 9, '2025-07-06 20:58:13'),
(6, '2025-06-22', 25, 3, '2025-07-06 20:58:13'),
(7, '2025-06-23', 90, 7, '2025-07-06 20:58:13'),
(8, '2025-06-22', 55, 8, '2025-07-06 20:58:13'),
(9, '2025-06-19', 40, 9, '2025-07-06 20:58:13'),
(10, '2025-06-24', 100, 5, '2025-07-06 20:58:13'),
(11, '2025-06-28', 60, 4, '2025-07-06 20:58:13'),
(12, '2025-06-18', 85, 7, '2025-07-06 20:58:13'),
(13, '2025-06-08', 30, 3, '2025-07-06 20:58:13'),
(14, '2025-06-19', 45, 4, '2025-07-06 20:58:13'),
(15, '2025-06-21', 70, 9, '2025-07-06 20:58:13'),
(16, '2025-06-22', 25, 3, '2025-07-06 20:58:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida_pregunta`
--

CREATE TABLE `partida_pregunta` (
  `partida_id` int(11) NOT NULL,
  `pregunta_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `respondida_bien` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partida_pregunta`
--

INSERT INTO `partida_pregunta` (`partida_id`, `pregunta_id`, `numero`, `respondida_bien`) VALUES
(1, 1, 1, 1),
(1, 2, 2, 1),
(1, 3, 3, 0),
(2, 1, 1, 1),
(2, 2, 2, 0),
(2, 3, 3, 0),
(3, 1, 1, 1),
(3, 2, 2, 1),
(3, 3, 3, 1),
(4, 1, 1, 1),
(4, 3, 3, 0),
(5, 1, 1, 1),
(5, 2, 2, 0),
(5, 3, 3, 0),
(6, 2, 2, 1),
(6, 3, 3, 1),
(7, 1, 1, 1),
(7, 3, 3, 1),
(8, 3, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
  `id` int(11) NOT NULL,
  `texto` text NOT NULL,
  `estado` varchar(30) NOT NULL,
  `correctas` int(11) NOT NULL DEFAULT 0,
  `intentos` int(11) NOT NULL DEFAULT 0,
  `dificultad` enum('facil','media','dificil') DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `creador_id` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id`, `texto`, `estado`, `correctas`, `intentos`, `dificultad`, `categoria_id`, `creador_id`, `fecha_creacion`) VALUES
(1, '¿Cuál es el río más largo del mundo?', 'activa', 1, 2, '', 1, 1, '2025-07-06 20:58:13'),
(2, '¿En qué continente se encuentra el desierto del Sahara?', 'activa', 1, 1, '', 1, 1, '2025-07-06 20:58:13'),
(3, '¿Cuál es la capital de Australia?', 'activa', 1, 1, '', 1, 1, '2025-07-06 20:58:13'),
(4, '¿Qué país tiene más islas en el mundo?', 'activa', 0, 1, '', 1, 1, '2025-07-06 20:58:13'),
(5, '¿Quién fue el primer presidente de Estados Unidos?', 'activa', 1, 1, '', 2, 1, '2025-07-06 20:58:13'),
(6, '¿En qué año terminó la Segunda Guerra Mundial?', 'activa', 1, 1, '', 2, 1, '2025-07-06 20:58:13'),
(7, '¿Qué imperio construyó el Coliseo?', 'activa', 1, 1, '', 2, 1, '2025-07-06 20:58:13'),
(8, '¿Quién fue Napoleón Bonaparte?', 'activa', 0, 1, '', 2, 1, '2025-07-06 20:58:13'),
(9, '¿Cuál es el elemento químico con símbolo O?', 'activa', 1, 1, '', 3, 1, '2025-07-06 20:58:13'),
(10, '¿Qué planeta del sistema solar es el más grande?', 'activa', 1, 1, '', 3, 1, '2025-07-06 20:58:13'),
(11, '¿Cómo se llama el proceso por el cual las plantas convierten luz en energía?', 'activa', 1, 1, '', 3, 1, '2025-07-06 20:58:13'),
(12, '¿Cuántos huesos tiene el cuerpo humano adulto?', 'activa', 1, 1, '', 3, 1, '2025-07-06 20:58:13'),
(13, '¿Cuántos jugadores tiene un equipo de fútbol?', 'activa', 1, 1, '', 4, 1, '2025-07-06 20:58:13'),
(14, '¿En qué deporte se utiliza un puck?', 'activa', 1, 1, '', 4, 1, '2025-07-06 20:58:13'),
(15, '¿Qué país ha ganado más Copas Mundiales de fútbol?', 'activa', 1, 1, '', 4, 1, '2025-07-06 20:58:13'),
(16, '¿Cómo se llama el deporte que combina natación, ciclismo y carrera?', 'activa', 1, 1, '', 4, 1, '2025-07-06 20:58:13'),
(17, '¿Cuál es el nombre del mago protagonista de la saga de J.K. Rowling?', 'activa', 1, 2, '', 5, 1, '2025-07-06 20:58:13'),
(18, '¿Qué serie presenta a un grupo de científicos liderado por Sheldon Cooper?', 'activa', 1, 2, '', 5, 1, '2025-07-06 20:58:13'),
(19, '¿Quién pintó La noche estrellada?', 'activa', 1, 1, '', 6, 1, '2025-07-06 20:58:13'),
(20, '¿A qué movimiento artístico pertenecía Salvador Dalí?', 'activa', 1, 1, '', 6, 1, '2025-07-06 20:58:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

CREATE TABLE `reporte` (
  `id` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pregunta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta`
--

CREATE TABLE `respuesta` (
  `pregunta_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `texto` text NOT NULL,
  `es_correcta` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta`
--

INSERT INTO `respuesta` (`pregunta_id`, `numero`, `texto`, `es_correcta`) VALUES
(1, 1, 'Amazonas', 1),
(1, 2, 'Nilo', 0),
(1, 3, 'Yangtsé', 0),
(1, 4, 'Misisipi', 0),
(2, 1, 'África', 1),
(2, 2, 'Asia', 0),
(2, 3, 'América del Sur', 0),
(2, 4, 'Europa', 0),
(3, 1, 'Canberra', 1),
(3, 2, 'Sídney', 0),
(3, 3, 'Melbourne', 0),
(3, 4, 'Perth', 0),
(4, 1, 'Suecia', 1),
(4, 2, 'Japón', 0),
(4, 3, 'Canadá', 0),
(4, 4, 'Brasil', 0),
(5, 1, 'George Washington', 1),
(5, 2, 'Abraham Lincoln', 0),
(5, 3, 'Thomas Jefferson', 0),
(5, 4, 'John Adams', 0),
(6, 1, '1945', 1),
(6, 2, '1939', 0),
(6, 3, '1918', 0),
(6, 4, '1950', 0),
(7, 1, 'Imperio Romano', 1),
(7, 2, 'Imperio Griego', 0),
(7, 3, 'Imperio Bizantino', 0),
(7, 4, 'Imperio Persa', 0),
(8, 1, 'Militar y emperador francés', 1),
(8, 2, 'Rey de Inglaterra', 0),
(8, 3, 'Papa católico', 0),
(8, 4, 'Explorador español', 0),
(9, 1, 'Oxígeno', 1),
(9, 2, 'Oro', 0),
(9, 3, 'Osmio', 0),
(9, 4, 'Ozono', 0),
(10, 1, 'Júpiter', 1),
(10, 2, 'Saturno', 0),
(10, 3, 'Neptuno', 0),
(10, 4, 'Tierra', 0),
(11, 1, 'Fotosíntesis', 1),
(11, 2, 'Fermentación', 0),
(11, 3, 'Evaporación', 0),
(11, 4, 'Digestión', 0),
(12, 1, '206', 1),
(12, 2, '198', 0),
(12, 3, '201', 0),
(12, 4, '212', 0),
(13, 1, '11', 1),
(13, 2, '10', 0),
(13, 3, '12', 0),
(13, 4, '9', 0),
(14, 1, 'Hockey sobre hielo', 1),
(14, 2, 'Béisbol', 0),
(14, 3, 'Tenis', 0),
(14, 4, 'Fútbol americano', 0),
(15, 1, 'Brasil', 1),
(15, 2, 'Alemania', 0),
(15, 3, 'Italia', 0),
(15, 4, 'Argentina', 0),
(16, 1, 'Triatlón', 1),
(16, 2, 'Pentatlón', 0),
(16, 3, 'Atletismo', 0),
(16, 4, 'Ironman', 0),
(17, 1, 'Harry Potter', 1),
(17, 2, 'Frodo Baggins', 0),
(17, 3, 'Percy Jackson', 0),
(17, 4, 'Artemis Fowl', 0),
(18, 1, 'The Big Bang Theory', 1),
(18, 2, 'Friends', 0),
(18, 3, 'How I Met Your Mother', 0),
(18, 4, 'Stranger Things', 0),
(19, 1, 'Vincent van Gogh', 1),
(19, 2, 'Claude Monet', 0),
(19, 3, 'Leonardo da Vinci', 0),
(19, 4, 'Pablo Picasso', 0),
(20, 1, 'Surrealismo', 1),
(20, 2, 'Impresionismo', 0),
(20, 3, 'Cubismo', 0),
(20, 4, 'Barroco', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Editor'),
(3, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `fecha_nac` date NOT NULL,
  `sexo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `foto_perfil` text NOT NULL,
  `nivel` enum('facil','media','dificil') DEFAULT NULL,
  `status` enum('inactivo','activo') NOT NULL DEFAULT 'inactivo',
  `token` varchar(64) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre_completo`, `fecha_nac`, `sexo`, `email`, `contrasena`, `nombre_usuario`, `foto_perfil`, `nivel`, `status`, `token`, `id_rol`, `latitud`, `longitud`, `pais`, `fecha_registro`) VALUES
(1, 'Admin Principal', '1990-01-01', 'Otro', 'admin@preguntastico.com', 'admin', 'admin', 'default.jpg', '', 'activo', '', 1, -34.67022400, -58.56363400, 'Argentina', '2020-09-03 00:00:00'),
(2, 'Editor', '1990-01-01', 'Otro', 'editor@preguntastico.com', 'editor', 'editor', 'default.jpg', '', 'activo', '', 2, -34.67022400, -58.56363400, 'Argentina', '2020-09-03 00:00:00'),
(3, 'Usuario', '1990-01-01', 'Otro', 'usuario@preguntastico.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'usuario', 'default.jpg', '', 'activo', '', 3, -34.67022400, -58.56363400, 'Uruguay', '2020-09-03 00:00:00'),
(4, 'Juan Pérez', '1981-01-03', 'Femenino', 'user4@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user4', 'default.jpg', 'facil', 'activo', '', 3, -40.80259300, -57.59370900, 'Uruguay', '2024-09-03 00:00:00'),
(5, 'Ana García', '1946-02-13', 'Femenino', 'user5@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user5', 'default.jpg', 'facil', 'activo', '', 3, -27.32179700, -63.85808200, 'México', '2025-06-07 00:00:00'),
(6, 'Carlos Díaz', '1960-01-24', 'Masculino', 'user6@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user6', 'default.jpg', 'facil', 'activo', '', 3, -31.93420800, -68.31987000, 'Chile', '2025-06-21 00:00:00'),
(7, 'María López', '2007-03-28', 'Otro', 'user7@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user7', 'default.jpg', 'media', 'activo', '', 3, -35.10431300, -56.00846000, 'Argentina', '2024-08-05 00:00:00'),
(8, 'Pedro Fernández', '1998-09-20', 'Masculino', 'user8@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user8', 'default.jpg', 'facil', 'activo', '', 3, -30.04126300, -54.49828100, 'Uruguay', '2024-10-29 00:00:00'),
(9, 'Lucía Gómez', '2010-11-02', 'Otro', 'user9@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user9', 'default.jpg', 'facil', 'activo', '', 3, -52.90127600, -57.99914600, 'Chile', '2025-06-13 00:00:00'),
(10, 'Jorge Rodríguez', '2003-02-15', 'Masculino', 'user10@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user10', 'default.jpg', 'facil', 'activo', '', 3, -21.11870800, -65.33775300, 'México', '2024-07-05 00:00:00'),
(11, 'Sofía Martínez', '2009-09-28', 'Femenino', 'user11@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user11', 'default.jpg', 'facil', 'activo', '', 3, -20.93268400, -74.44992100, 'Argentina', '2025-01-01 00:00:00'),
(12, 'Martín Romero', '1957-10-06', 'Otro', 'user12@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user12', 'default.jpg', 'media', 'activo', '', 3, -48.42316100, -63.26944800, 'Chile', '2025-04-11 00:00:00'),
(13, 'Paula Torres', '1991-07-10', 'Masculino', 'user13@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user13', 'default.jpg', 'facil', 'activo', '', 3, -33.08492200, -68.96414300, 'Argentina', '2025-06-04 00:00:00'),
(14, 'Tomás Navarro', '1999-06-16', 'Femenino', 'user14@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user14', 'default.jpg', 'facil', 'activo', '', 3, -19.82807700, -55.62531700, 'Chile', '2025-07-01 00:00:00'),
(15, 'Camila Silva', '1975-08-19', 'Femenino', 'user15@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user15', 'default.jpg', 'facil', 'activo', '', 3, -54.45691300, -62.50201700, 'Uruguay', '2025-06-29 00:00:00'),
(16, 'Federico Méndez', '2012-07-07', 'Masculino', 'user16@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user16', 'default.jpg', 'facil', 'activo', '', 3, -49.95011200, -73.29376400, 'México', '2025-06-30 00:00:00'),
(17, 'Valentina Castro', '1986-06-30', 'Otro', 'user17@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user17', 'default.jpg', 'media', 'activo', '', 3, -33.36902500, -61.12230600, 'Argentina', '2024-12-18 00:00:00'),
(18, 'Nicolás Aguirre', '2013-04-21', 'Masculino', 'user18@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user18', 'default.jpg', 'facil', 'activo', '', 3, -36.50965200, -59.20451800, 'Uruguay', '2025-03-10 00:00:00'),
(19, 'Florencia Domínguez', '2006-11-12', 'Femenino', 'user19@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user19', 'default.jpg', 'media', 'activo', '', 3, -42.13362800, -60.90317300, 'Chile', '2024-07-28 00:00:00'),
(20, 'Matías Rivas', '1950-05-04', 'Masculino', 'user20@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user20', 'default.jpg', 'media', 'activo', '', 3, -32.27249300, -69.04009800, 'México', '2024-08-30 00:00:00'),
(21, 'Julieta Acosta', '2008-01-01', 'Femenino', 'user21@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user21', 'default.jpg', 'facil', 'activo', '', 3, -36.18510900, -65.25596700, 'Argentina', '2025-06-20 00:00:00'),
(22, 'Agustín Herrera', '1994-10-23', 'Masculino', 'user22@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user22', 'default.jpg', 'facil', 'activo', '', 3, -35.20647000, -70.49276800, 'Uruguay', '2025-02-14 00:00:00'),
(23, 'Brenda Morales', '2002-06-25', 'Femenino', 'user23@mail.com', '$2y$10$TSQFKjjfwLTxm4psvizumOZbmpWw132xa2K/caNJXd5X7T5H.qc8W', 'user23', 'default.jpg', 'facil', 'activo', '', 3, -53.61669300, -64.80947100, 'Chile', '2024-06-25 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_pregunta`
--

CREATE TABLE `usuario_pregunta` (
  `usuario_id` int(11) NOT NULL,
  `pregunta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partida`
--
ALTER TABLE `partida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `partida_pregunta`
--
ALTER TABLE `partida_pregunta`
  ADD PRIMARY KEY (`partida_id`,`pregunta_id`),
  ADD KEY `pregunta_id` (`pregunta_id`);

--
-- Indices de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `creador_id` (`creador_id`);

--
-- Indices de la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `pregunta_id` (`pregunta_id`);

--
-- Indices de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD UNIQUE KEY `pregunta_id` (`pregunta_id`,`numero`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_rol` (`id_rol`);

--
-- Indices de la tabla `usuario_pregunta`
--
ALTER TABLE `usuario_pregunta`
  ADD PRIMARY KEY (`usuario_id`,`pregunta_id`),
  ADD KEY `pregunta_id` (`pregunta_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `partida`
--
ALTER TABLE `partida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `reporte`
--
ALTER TABLE `reporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `partida`
--
ALTER TABLE `partida`
  ADD CONSTRAINT `partida_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `partida_pregunta`
--
ALTER TABLE `partida_pregunta`
  ADD CONSTRAINT `partida_pregunta_ibfk_1` FOREIGN KEY (`partida_id`) REFERENCES `partida` (`id`),
  ADD CONSTRAINT `partida_pregunta_ibfk_2` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id`);

--
-- Filtros para la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD CONSTRAINT `pregunta_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `pregunta_ibfk_2` FOREIGN KEY (`creador_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD CONSTRAINT `reporte_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `reporte_ibfk_2` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id`);

--
-- Filtros para la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD CONSTRAINT `respuesta_ibfk_1` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_id_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id`);

--
-- Filtros para la tabla `usuario_pregunta`
--
ALTER TABLE `usuario_pregunta`
  ADD CONSTRAINT `usuario_pregunta_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `usuario_pregunta_ibfk_2` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
