-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 06, 2025 alle 16:49
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `todolistapp`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `due_datetime` datetime DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT current_timestamp(),
  `section` enum('inbox','today','upcoming') DEFAULT 'inbox',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `completed`, `user_id`, `due_datetime`, `list_id`, `completed_at`, `section`, `created_at`) VALUES
(8, 'Jogging', 'Run fast as fuck', 1, 1, '2025-05-04 07:00:00', NULL, NULL, 'inbox', '2025-05-06 10:49:42'),
(22, 'ascascasc', '', 1, 1, '2025-05-06 00:00:00', NULL, '2025-05-06 10:31:11', 'inbox', '2025-05-06 10:49:42'),
(23, 'qwdqdascawvasva', '', 1, 1, '2025-05-06 00:00:00', NULL, '2025-05-06 10:32:31', 'inbox', '2025-05-06 10:49:42'),
(24, 'ascasczxccasc', '', 1, 1, '2025-05-06 00:00:00', NULL, '2025-05-06 10:33:18', 'inbox', '2025-05-06 10:49:42'),
(25, 'asasdasd', '', 1, 1, '2025-05-06 00:00:00', NULL, '2025-05-06 11:05:16', 'today', '2025-05-06 11:05:16'),
(28, 'Continuare a studiare AJAX', '', 0, 1, '2025-05-08 00:00:00', NULL, '2025-05-06 11:35:36', 'upcoming', '2025-05-06 11:35:36'),
(30, 'asdasd', '', 0, 1, NULL, NULL, '2025-05-06 12:45:07', 'inbox', '2025-05-06 12:45:07'),
(31, 'asdasdasd', '', 0, 1, NULL, NULL, '2025-05-06 12:48:54', 'inbox', '2025-05-06 12:48:54'),
(32, 'sdfsdfsdf', '', 0, 1, '2025-05-06 00:00:00', NULL, '2025-05-06 12:49:02', 'today', '2025-05-06 12:49:02'),
(33, 'Portare fuori il cane', '', 0, 1, '2025-05-17 00:00:00', NULL, '2025-05-06 13:06:10', 'upcoming', '2025-05-06 13:06:10'),
(34, 'ascascasc', '', 0, 1, NULL, NULL, '2025-05-06 13:09:15', 'inbox', '2025-05-06 13:09:15'),
(35, 'aevav', '', 0, 1, '2025-05-14 00:00:00', NULL, '2025-05-06 13:09:42', 'today', '2025-05-06 13:09:42'),
(36, 'aksnvonownv', '', 0, 1, NULL, NULL, '2025-05-06 13:12:10', 'inbox', '2025-05-06 13:12:10'),
(37, 'wcqacawc', '', 0, 1, '2025-05-17 00:00:00', NULL, '2025-05-06 14:08:04', 'upcoming', '2025-05-06 14:08:04'),
(38, 'mjdkjdjvmjfckl', '', 0, 1, '2025-05-17 00:00:00', NULL, '2025-05-06 14:08:35', 'upcoming', '2025-05-06 14:08:35');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `dob`, `email`, `password`) VALUES
(1, 'Ivan Kovic', 'Sion', '2002-07-09', 'ivankovicsibsion@gmail.com', '$2y$10$G7iAbxCwam3MZ68HoQKBgeMdgUbLDFltzdjh7PGm.hehT2KeJFTn.');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`due_datetime`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
