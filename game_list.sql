-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Erstellungszeit: 25. Mai 2021 um 16:33
-- Server-Version: 10.5.10-MariaDB-1:10.5.10+maria~focal
-- PHP-Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `switch_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_list`
--

CREATE TABLE `game_list` (
  `id` int(6) UNSIGNED NOT NULL,
  `game_name` varchar(129) NOT NULL,
  `game_id` varchar(50) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_ending` varchar(20) DEFAULT NULL,
  `path` varchar(1055) DEFAULT NULL,
  `file_size` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `game_list`
--
ALTER TABLE `game_list`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `game_list`
--
ALTER TABLE `game_list`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
