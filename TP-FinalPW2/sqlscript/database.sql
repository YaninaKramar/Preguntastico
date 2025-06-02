-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-06-2025 a las 05:11:05
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
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `dificultad` enum('facil','media','dificil') NOT NULL DEFAULT 'media',
  `categoria_id` int(11) NOT NULL,
  `creador_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id`, `texto`, `estado`, `correctas`, `intentos`, `dificultad`, `categoria_id`, `creador_id`) VALUES
(1, '¿Cuál es el río más largo del mundo?', 'activa', 5, 7, 'media', 1, 1),
(2, '¿En qué continente se encuentra el desierto del Sahara?', 'activa', 5, 8, 'media', 1, 1),
(3, '¿Cuál es la capital de Australia?', 'activa', 7, 8, 'media', 1, 1),
(4, '¿Qué país tiene más islas en el mundo?', 'activa', 4, 5, 'media', 1, 1),
(5, '¿Quién fue el primer presidente de Estados Unidos?', 'activa', 3, 4, 'media', 2, 1),
(6, '¿En qué año terminó la Segunda Guerra Mundial?', 'activa', 3, 4, 'media', 2, 1),
(7, '¿Qué imperio construyó el Coliseo?', 'activa', 3, 3, 'media', 2, 1),
(8, '¿Quién fue Napoleón Bonaparte?', 'activa', 4, 8, 'media', 2, 1),
(9, '¿Cuál es el elemento químico con símbolo O?', 'activa', 7, 9, 'media', 3, 1),
(10, '¿Qué planeta del sistema solar es el más grande?', 'activa', 3, 4, 'media', 3, 1),
(11, '¿Cómo se llama el proceso por el cual las plantas convierten luz en energía?', 'activa', 4, 6, 'media', 3, 1),
(12, '¿Cuántos huesos tiene el cuerpo humano adulto?', 'activa', 3, 3, 'media', 3, 1),
(13, '¿Cuántos jugadores tiene un equipo de fútbol?', 'activa', 6, 10, 'media', 4, 1),
(14, '¿En qué deporte se utiliza un puck?', 'activa', 6, 8, 'media', 4, 1),
(15, '¿Qué país ha ganado más Copas Mundiales de fútbol?', 'activa', 5, 11, 'media', 4, 1),
(16, '¿Cómo se llama el deporte que combina natación, ciclismo y carrera?', 'activa', 4, 5, 'media', 4, 1),
(17, '¿Cuál es el nombre del mago protagonista de la saga de J.K. Rowling?', 'activa', 5, 8, 'media', 5, 1),
(18, '¿Qué serie presenta a un grupo de científicos liderado por Sheldon Cooper?', 'activa', 2, 2, 'media', 5, 1),
(19, '¿Quién pintó La noche estrellada?', 'activa', 3, 3, 'media', 6, 1),
(20, '¿A qué movimiento artístico pertenecía Salvador Dalí?', 'activa', 6, 8, 'media', 6, 1);

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
(2, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `fecha_nac` date NOT NULL,
  `sexo` varchar(100) NOT NULL,
  `pais` varchar(100) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `foto_perfil` text NOT NULL,
  `nivel` enum('facil','media','dificil') NOT NULL DEFAULT 'media',
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre_completo`, `fecha_nac`, `sexo`, `pais`, `ciudad`, `email`, `contrasena`, `nombre_usuario`, `foto_perfil`, `nivel`, `id_rol`) VALUES
(1, 'Admin Principal', '1990-01-01', 'Otro', 'Argentina', 'Buenos Aires', 'admin@preguntastico.com', 'admin123', 'admin', 'a', '', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `reporte`
--
ALTER TABLE `reporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
