-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Pon 26. pro 2016, 12:50
-- Verze serveru: 5.7.14
-- Verze PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `komponentysn`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `komponenty`
--

CREATE TABLE `komponenty` (
  `id` int(11) NOT NULL,
  `id_typ` int(11) NOT NULL,
  `datetime_change` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Spojení tabulek skrze id klíče.';

-- --------------------------------------------------------

--
-- Struktura tabulky `seriova_cisla`
--

CREATE TABLE `seriova_cisla` (
  `id` int(11) NOT NULL,
  `id_komponenty` int(11) NOT NULL,
  `seriove_cislo` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `poznamka` text COLLATE utf8_czech_ci NOT NULL,
  `datum` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Sériová čísla komponent, poznámky k výměnám a datum změny.';

-- --------------------------------------------------------

--
-- Struktura tabulky `typy_komponent`
--

CREATE TABLE `typy_komponent` (
  `id` int(11) NOT NULL,
  `nazev` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `sn_regular` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `sn_var_offset` int(11) NOT NULL,
  `sn_start_char` varchar(1) COLLATE utf8_czech_ci NOT NULL,
  `sn_end_char` varchar(1) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka komponent a jejich originálních výrobních čísel.';

--
-- Vypisuji data pro tabulku `typy_komponent`
--

INSERT INTO `typy_komponent` (`id`, `nazev`, `sn_regular`, `sn_var_offset`, `sn_start_char`, `sn_end_char`) VALUES
(1, 'Car - PC', '0150[0-9]{4}', 3, '1', '9'),
(2, 'Car - MDVR', '0250[0-9]{4}', 3, '1', '9'),
(3, 'Car - POE', '0350[0-9]{4}', 3, '1', '9'),
(4, 'Kamera', '2[0-9]{7}[12]', 0, '3', '9'),
(5, 'Monitor', '869[A-C][0-9]{9}', 3, 'Z', 'F');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `komponenty`
--
ALTER TABLE `komponenty`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `seriova_cisla`
--
ALTER TABLE `seriova_cisla`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `typy_komponent`
--
ALTER TABLE `typy_komponent`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `komponenty`
--
ALTER TABLE `komponenty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `seriova_cisla`
--
ALTER TABLE `seriova_cisla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `typy_komponent`
--
ALTER TABLE `typy_komponent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
