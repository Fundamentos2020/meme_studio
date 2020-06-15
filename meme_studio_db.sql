-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-06-2020 a las 20:41:17
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `meme_studio_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `comentario_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `meme_id` int(11) NOT NULL,
  `contenido` varchar(512) NOT NULL,
  `fecha_comentario` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`comentario_id`, `usuario_id`, `meme_id`, `contenido`, `fecha_comentario`) VALUES
(8, 19, 12, 'No entiendo porqué le dan dislike :(', '2020-06-14 13:06:00'),
(9, 19, 21, 'Me la rifé 8v', '2020-06-15 03:43:00'),
(10, 20, 22, 'Gracias por los likes!', '2020-06-15 13:03:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `memes`
--

CREATE TABLE `memes` (
  `meme_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `dislikes` int(11) NOT NULL DEFAULT 0,
  `estado_meme` enum('PRIVADO','PENDIENTE','RECHAZADO','ACEPTADO') NOT NULL DEFAULT 'PRIVADO',
  `ruta_imagen_meme` varchar(1024) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `texto_superior` varchar(40) DEFAULT NULL,
  `texto_inferior` varchar(40) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_publicacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `memes`
--

INSERT INTO `memes` (`meme_id`, `usuario_id`, `likes`, `dislikes`, `estado_meme`, `ruta_imagen_meme`, `titulo`, `texto_superior`, `texto_inferior`, `fecha_creacion`, `fecha_publicacion`) VALUES
(12, 19, 2, 1, 'ACEPTADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme2.jpg', 'Prueba Momazo', 'Esto es', 'una prueba', '2020-06-14 12:31:00', '2020-06-14 12:32:00'),
(13, 19, 0, 1, 'ACEPTADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme2.jpg', 'Otra prueba', 'esto es una', 'prueba massssss', '2020-06-14 13:37:00', '2020-06-14 13:37:00'),
(14, 19, 0, 0, 'ACEPTADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme4.jpg', 'Memingo', 'xdxdxd', 'xdxdxd', '2020-06-14 13:52:00', '2020-06-14 13:52:00'),
(15, 19, 0, 0, 'PENDIENTE', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme1.jpg', 'Muertazo', 'Aquí esperando', 'a terminar el proyecto', '2020-06-15 00:09:00', '2020-06-15 00:09:00'),
(16, 19, 0, 0, 'PENDIENTE', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme5.jpg', 'Victoria', 'Como cuando', 'acabas tu proyecto', '2020-06-15 00:14:00', '2020-06-15 00:14:00'),
(17, 19, 0, 0, 'PENDIENTE', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme1.jpg', 'Prueba', 'Prueba', 'Prueba', '2020-06-15 00:15:00', '2020-06-15 00:15:00'),
(18, 19, 0, 0, 'RECHAZADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme1.jpg', 'Prueba', 'Prueba', 'Prueba', '2020-06-15 00:22:00', '2020-06-15 00:22:00'),
(19, 19, 0, 0, 'PRIVADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme2.jpg', 'Terminar', 'Como cuando terminas', 'tu proyecto a tiempo', '2020-06-15 01:03:00', '2020-06-15 01:03:00'),
(20, 19, 1, 0, 'ACEPTADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme3.jpg', 'Kiko', 'uuuuuuy', 'así que chiste', '2020-06-15 01:18:00', '2020-06-15 01:18:00'),
(21, 19, 5, 0, 'ACEPTADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme4.jpg', 'Cambios', 'Como cuando', 'sigues haciendo cambios al proyecto', '2020-06-15 03:23:00', '2020-06-15 03:24:00'),
(22, 20, 15, 2, 'ACEPTADO', 'http://localhost/ProyectoFundamentosWeb/imagenes/plantillas/meme2.jpg', 'Finalizado', 'Como cuando mis alumnos', 'terminan el proyecto', '2020-06-15 12:57:00', '2020-06-15 13:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `memes_tags`
--

CREATE TABLE `memes_tags` (
  `meme_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `memes_tags`
--

INSERT INTO `memes_tags` (`meme_id`, `tag_id`) VALUES
(12, 5),
(12, 6),
(12, 7),
(13, 7),
(13, 8),
(14, 9),
(15, 10),
(15, 11),
(16, 12),
(16, 13),
(17, 14),
(18, 5),
(18, 6),
(18, 7),
(19, 15),
(19, 16),
(19, 17),
(19, 18),
(20, 19),
(20, 20),
(21, 16),
(21, 21),
(21, 22),
(22, 10),
(22, 23),
(22, 24);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moderaciones`
--

CREATE TABLE `moderaciones` (
  `moderacion_id` int(11) NOT NULL,
  `meme_id` int(11) NOT NULL,
  `estatus_moderacion` enum('PENDIENTE','ACEPTADO','RECHAZADO') NOT NULL DEFAULT 'PENDIENTE',
  `retroalimentacion` varchar(250) DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `moderaciones`
--

INSERT INTO `moderaciones` (`moderacion_id`, `meme_id`, `estatus_moderacion`, `retroalimentacion`, `fecha_solicitud`) VALUES
(2, 12, 'ACEPTADO', 'Buen meme, pero no tiene texto jajaja >:V', '2020-06-14 12:31:00'),
(3, 13, 'ACEPTADO', 'y el texto?', '2020-06-14 13:37:00'),
(4, 14, 'ACEPTADO', 'xsd', '2020-06-14 13:52:00'),
(5, 18, 'RECHAZADO', 'Pésimo', '2020-06-15 00:22:00'),
(6, 20, 'ACEPTADO', 'Está más o menos', '2020-06-15 01:18:00'),
(7, 21, 'ACEPTADO', 'Excelente momazo', '2020-06-15 03:23:00'),
(8, 22, 'ACEPTADO', 'Excelente meme, me encantó', '2020-06-15 12:57:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `sesion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `token_acceso` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `caducidad_token_acceso` datetime NOT NULL,
  `token_actualizacion` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `caducidad_token_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sesiones`
--

INSERT INTO `sesiones` (`sesion_id`, `usuario_id`, `token_acceso`, `caducidad_token_acceso`, `token_actualizacion`, `caducidad_token_actualizacion`) VALUES
(10, 19, 'NzY0OWE0MzBiMGUzNDcyNThjZjRjMDZjYjczY2ZjOTkwOWU0ZDg2MjA3Mzk1NjEyMTU5MTk0NzU1Mg==', '2020-06-12 02:59:12', 'Mjg2ZWQ2ZGRlNjQ3MzM2YjQ0MmQyODM1N2NmOGE4Zjg5ODliYTRiOTkwNWYwNGYxMTU5MTk0NzU1Mg==', '2020-06-27 02:39:12'),
(11, 19, 'ZjY2Yzc2MDU2OWZjYzEzNjZhNTMyYzJmMmQxNWU5NDdjNTEzNmRmMmMxMzYxNTFkMTU5MTk4Mzc1NQ==', '2020-06-12 13:02:35', 'YzRhNmRkMzNlYTQ5NmNlMmI3YWM4ODlhZDE5Y2IzOTkyMGE4Y2IyZGVmNThlNjNkMTU5MTk4Mzc1NQ==', '2020-06-27 12:42:35'),
(12, 19, 'ZDMzMWZkM2I5NWNmZTgwYTY3MDQzZTAzNzk1ZDQxY2E4MDM5MWE5ZGUxODI1NmFmMTU5MTk4NDg4MQ==', '2020-06-12 13:21:21', 'ZTJkY2VjN2ZjMjE5NDM3OTM2YmI3OTEyMDM1N2MyODc4NzdjNDI0YTg0YTY5M2UzMTU5MTk4NDg4MQ==', '2020-06-27 13:01:21'),
(13, 19, 'ZWMxZDhlMDY1MWI5ZjEwMWEwZDEyZTMwNjBhYTM4ZDUyNzY3ODYyNjM2ZjFlOWUxMzEzNTM5MzIzMTM5MzczNjMzMzc=', '2020-06-15 00:27:17', 'ZjA5YTdlMTU5ZWE4MWMyNWIxZTZhODdlZmMzN2E3ZGQ4ZWI0ZjJmYjFiYmVhNDE2MzEzNTM5MzIzMTM5MzczNjMzMzc=', '2020-06-30 00:07:17'),
(14, 20, 'MTZiYzEwNTVmNmJmNGQ2N2E2NzgyMzkzN2EzYzk3MTdlMjIzYTc2MzQyZjhkZTFkMTU5MjI0MzQ5NA==', '2020-06-15 13:11:34', 'NGNiOWIwMDZmNGZkZDk4ZGFlZDQwYjRjYWE4YjQ3YWIyZWFjYWFmMDJkZDRhODE2MTU5MjI0MzQ5NA==', '2020-06-30 12:51:34'),
(15, 19, 'YjQwMTk4YWM4Njk5MDllN2IzOTk2YTdiMzBiNDFjZDUyMTE5MTFmZWFlODMzNDUyMTU5MjI0MzkxNw==', '2020-06-15 13:18:37', 'Njk3YjQwOTU3NDQ1NmYxMjk2Mjc5Yzc3ZGUwYmFkMTkzMDRjNzA0ZDAxNGM5OTZjMTU5MjI0MzkxNw==', '2020-06-30 12:58:37'),
(16, 20, 'NjkzOWU4OGE4MGYyNTQ0MDQ5MjA2ZmY2NzBlOTAzNzlkOWEwOWRhODlhZDZkNzBmMTU5MjI0NDA2Nw==', '2020-06-15 13:21:07', 'MGExYjE1ODNmZDVjNjRiZDM2YzA5NjFlOTliYmJhYzdkNDZlZTFlMmM1ZDM2NTZkMTU5MjI0NDA2Nw==', '2020-06-30 13:01:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `nombre_tag` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tags`
--

INSERT INTO `tags` (`tag_id`, `nombre_tag`) VALUES
(1, 'Bob esponja'),
(22, 'Cambios'),
(8, 'Cat'),
(19, 'Chiste'),
(23, 'final'),
(20, 'Kiko'),
(11, 'Muerto'),
(2, 'perros'),
(10, 'Proyecto'),
(14, 'Prueba'),
(18, 'salvado'),
(15, 'Stark'),
(7, 'Tag1'),
(6, 'Tag2'),
(5, 'Tag3'),
(9, 'Taggg'),
(13, 'Terminado'),
(17, 'terminar'),
(3, 'Test tag'),
(4, 'Test tag 2'),
(24, 'Tienen 10'),
(16, 'Tony'),
(12, 'Victoria'),
(21, 'wey ya');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `rol` enum('USUARIO','MODERADOR') NOT NULL DEFAULT 'USUARIO',
  `nombre_completo` varchar(100) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(320) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `ruta_imagen_perfil` varchar(1024) NOT NULL DEFAULT 'imagenes/avatars/avatar.png',
  `descripcion` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `rol`, `nombre_completo`, `nombre_usuario`, `email`, `contrasena`, `ruta_imagen_perfil`, `descripcion`) VALUES
(1, 'USUARIO', 'Alejandro Rodríguez', 'Aleaxes', 'asd@gmail.com', '$2y$10$iDJ7v6beE7XG0N.oaezOpe2QMk6MOB7IDBgDxYZ3WBHK6DOU1.4i.', 'imagenes/avatars/avatar.png', 'Aqui rifandola'),
(2, 'USUARIO', 'Monica López García', 'MLG', 'correo@gmail.com', '$2y$10$iDJ7v6beE7XG0N.oaezOpe2QMk6MOB7IDBgDxYZ3WBHK6DOU1.4i.', 'imagenes/avatars/avatar.png', NULL),
(19, 'MODERADOR', 'erick eduardo galindo chavez', 'engineererick', 'engineererick@hotmail.com', '$2y$10$gXkgQSBk7Pgt7L1.NSJ2TOYlUip0Y33Fto6swdaD9i/9PoWIjbf2a', 'http://localhost/ProyectoFundamentosWeb/imagenes/avatars/avatar3.jpg', 'Networking (Defensive and Offensive), social engineering'),
(20, 'USUARIO', 'Josué Sánchez Olvera', 'josue', 'josue_sanchez@gmail.com', '$2y$10$x5zaKE3qoMu5QQL57u7fL.75rRLiBYrOIrDmjU66.iivATwclcaqa', 'http://localhost/ProyectoFundamentosWeb/imagenes/avatars/avatar1.jpg', 'Profesor de la materia Fundamentos de Desarrollo Web');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`comentario_id`),
  ADD KEY `comentarios_usuarios_FK` (`usuario_id`),
  ADD KEY `comentarios_memes_FK` (`meme_id`);

--
-- Indices de la tabla `memes`
--
ALTER TABLE `memes`
  ADD PRIMARY KEY (`meme_id`),
  ADD KEY `memes_usuarios_FK` (`usuario_id`);

--
-- Indices de la tabla `memes_tags`
--
ALTER TABLE `memes_tags`
  ADD UNIQUE KEY `idx_meme_tag` (`meme_id`,`tag_id`),
  ADD KEY `memestags_tags_FK` (`tag_id`);

--
-- Indices de la tabla `moderaciones`
--
ALTER TABLE `moderaciones`
  ADD PRIMARY KEY (`moderacion_id`),
  ADD KEY `moderaciones_memes_FK` (`meme_id`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`sesion_id`),
  ADD UNIQUE KEY `token_acceso` (`token_acceso`),
  ADD UNIQUE KEY `token_actualizacion` (`token_actualizacion`),
  ADD KEY `sesiones_usuarios_FK` (`usuario_id`);

--
-- Indices de la tabla `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `nombre_tag` (`nombre_tag`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `comentario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `memes`
--
ALTER TABLE `memes`
  MODIFY `meme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `moderaciones`
--
ALTER TABLE `moderaciones`
  MODIFY `moderacion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `sesion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_memes_FK` FOREIGN KEY (`meme_id`) REFERENCES `memes` (`meme_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comentarios_usuarios_FK` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `memes`
--
ALTER TABLE `memes`
  ADD CONSTRAINT `memes_usuarios_FK` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `memes_tags`
--
ALTER TABLE `memes_tags`
  ADD CONSTRAINT `memestags_memes_FK` FOREIGN KEY (`meme_id`) REFERENCES `memes` (`meme_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `memestags_tags_FK` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `moderaciones`
--
ALTER TABLE `moderaciones`
  ADD CONSTRAINT `moderaciones_memes_FK` FOREIGN KEY (`meme_id`) REFERENCES `memes` (`meme_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_usuarios_FK` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
