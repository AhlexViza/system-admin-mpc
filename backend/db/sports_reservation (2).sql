-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 22-12-2024 a las 21:26:52
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sports_reservation`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `courts`
--

DROP TABLE IF EXISTS `courts`;
CREATE TABLE IF NOT EXISTS `courts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `capacity` int NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price_per_hour` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `courts`
--

INSERT INTO `courts` (`id`, `name`, `location`, `capacity`, `phone`, `image`, `created_at`, `price_per_hour`) VALUES
(1, 'Campo de Fútbol 1', 'Sector Norte, Complejo Deportivo', 22, '987654321', 'images/campo_futbol_1.jpg', '2024-12-22 16:16:21', 50.00),
(2, 'Campo de Fútbol 2', 'Sector Sur, Complejo Deportivo', 22, '912345678', 'images/campo_futbol_2.jpg', '2024-12-22 16:16:21', 60.00),
(7, 'holamundo', 'tucasa', 22, '987456321', 'images/campo_futbol_2.jpg', '2024-12-22 17:00:53', 50.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility` varchar(255) NOT NULL,
  `sport` varchar(255) NOT NULL,
  `court` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `payment_method` enum('now','at_court') NOT NULL DEFAULT 'now',
  `court_id` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `court_id` (`court_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`id`, `facility`, `sport`, `court`, `date`, `start_time`, `end_time`, `customer_name`, `customer_email`, `customer_phone`, `payment_method`, `court_id`, `total_price`) VALUES
(1, 'soccer-field-1', 'soccer-11', 'natural', '2024-12-23', '07:00:00', '08:00:00', 'ALEX', 'ALEX@gmail.com', '987546123', 'at_court', 0, 0.00),
(2, 'soccer-field-1', 'soccer-11', 'natural', '2024-12-24', '08:00:00', '10:00:00', 'ALEX', 'ALEX@gmail.com', '987546123', 'now', 0, 0.00),
(3, '', '', '', '2024-12-24', '11:00:00', '12:00:00', 'ALEX', 'ALEX@gmail.com', '987546123', 'now', 1, 0.00),
(4, '', '', '', '2024-12-23', '07:00:00', '08:00:00', 'ALEX', 'ALEX@gmail.com', '987546123', 'now', 7, 0.00),
(5, '', '', '', '2024-12-23', '10:00:00', '11:00:00', 'ALEX', 'ALEX@gmail.com', '987546123', 'now', 7, 50.00),
(6, '', '', '', '2024-12-24', '08:00:00', '10:00:00', 'ALEX', 'ALEX@gmail.com', '987546123', 'now', 7, 100.00),
(7, '', '', '', '2024-12-27', '08:00:00', '09:00:00', 'vbc', 'cbvcvb', 'bcvbc', 'now', 7, 50.00),
(8, '', '', '', '2024-12-26', '08:00:00', '09:00:00', 'ALEX', 'ALEX@gmail.com', '987546123', 'now', 7, 50.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rol` enum('usuario','administrador') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `password`, `avatar`, `created_at`, `updated_at`, `rol`) VALUES
(1, 'miguel', 'juan@gmail.com', '$2y$10$NFdwSU9jVFjQ9qNXvz/45uCfuRp4LHkkbsF87lhUXUNb2I/jy27we', 'fa-user-circle', '2024-12-09 22:39:14', '2024-12-10 00:31:27', 'usuario'),
(6, 'Admin', 'admin@example.com', '$2y$10$Ijk2LGwiagsfEfjNo9/J0eVSyoJvut8R8kzN.CSKMUhJ/K/iSWrIC', NULL, '2024-12-10 00:46:06', '2024-12-10 00:46:06', 'administrador'),
(7, 'alvaroshe', 'alvaro@gmail.com', '$2y$10$BIW5UysEphxs5QsepXyHYu5f.aRTKKOO9NR0c2y/BoMEnlG7jdZqi', NULL, '2024-12-10 00:52:47', '2024-12-10 00:52:47', 'administrador'),
(8, 'alex viza', 'alex@gmail.com', '$2y$10$EOWbrd/5xoyN7Ef4w4ew7e98/owvJv623MN2RjtSrFnMMyL9gwpvy', 'fa-user-alt', '2024-12-10 01:13:28', '2024-12-11 02:45:15', 'usuario');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
