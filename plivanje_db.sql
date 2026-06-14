-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 14, 2026 at 12:10 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plivanje_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `instruktori`
--

DROP TABLE IF EXISTS `instruktori`;
CREATE TABLE IF NOT EXISTS `instruktori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ime` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prezime` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specijalnost` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biografija` text COLLATE utf8mb4_unicode_ci,
  `godine_iskustva` int DEFAULT NULL,
  `sertifikati_opis` text COLLATE utf8mb4_unicode_ci,
  `obrazovanje` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instruktori`
--

INSERT INTO `instruktori` (`id`, `ime`, `prezime`, `telefon`, `email`, `specijalnost`, `biografija`, `godine_iskustva`, `sertifikati_opis`, `obrazovanje`) VALUES
(1, 'Sale', 'Salinjo', '0611231234', 'salesaki@gmail.com', 'kraul 400m', 'Sale Salinjo je instruktor plivanja specijalizovan za kraul, razvoj kondicije i pripremu plivača za srednje i duge deonice. U radu sa polaznicima poseban akcenat stavlja na pravilnu tehniku disanja, položaj tela u vodi, ekonomičnost pokreta i postepeno povećavanje izdržljivosti. Iskustvo je sticao kroz individualne i grupne treninge sa početnicima, rekreativcima i takmičarima. Njegov pristup je disciplinovan, ali prilagođen sposobnostima i ciljevima svakog polaznika.', 5, 'Licencirani instruktor plivanja. Završena stručna obuka za rad sa početnicima i neplivačima. Sertifikat iz oblasti bezbednosti na vodi i pružanja prve pomoći. Dodatna edukacija iz planiranja kondicionih treninga za plivače i usavršavanja tehnike kraula. Iskustvo u pripremi plivača za discipline 200 m i 400 m slobodnim stilom.', 'Fakultet sporta i fizickog vaspitanja'),
(2, 'Ivan', 'Ivanovic', '0611234569', 'ivanchad@example.com', 'prsno 50m', 'Ivan je instruktor plivanja sa sedam godina iskustva u radu sa početnicima, rekreativcima i naprednim plivačima. Specijalizovan je za prsno plivanje na 50 metara, pravilno disanje i preživljavanje nakon prejakog starta. Poznat je po tome što strogo vodi trening, ali se povremeno neprimetno iskrade iz bazena i priključi grupi za vodeni aerobik. Tvrdi da to radi isključivo zbog stručnog usavršavanja i bolje pokretljivosti kukova.', 69, 'Licencirani instruktor plivanja\r\nSertifikat za spasavanje na vodi', 'Fakultet sporta i fizičkog vaspitanja, smer plivanje i vodeni sportovi');

-- --------------------------------------------------------

--
-- Table structure for table `nagrade`
--

DROP TABLE IF EXISTS `nagrade`;
CREATE TABLE IF NOT EXISTS `nagrade` (
  `id` int NOT NULL AUTO_INCREMENT,
  `polaznik_id` int NOT NULL,
  `naziv` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opis` text COLLATE utf8mb4_unicode_ci,
  `datum` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_nagrada_polaznik` (`polaznik_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nivoi`
--

DROP TABLE IF EXISTS `nivoi`;
CREATE TABLE IF NOT EXISTS `nivoi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `naziv` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opis` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nivoi`
--

INSERT INTO `nivoi` (`id`, `naziv`, `opis`) VALUES
(1, 'Početni', 'Osnovno održavanje na vodi i upoznavanje sa osnovnim tehnikama plivanja.'),
(2, 'Srednji', 'Polaznik poznaje osnovne stilove i radi na tehnici i izdržljivosti.'),
(3, 'Napredni', 'Polaznik usavršava stilove, brzinu i kondiciju.'),
(4, 'Takmičarski', 'Polaznik trenira za takmičenja i napredne plivačke discipline.');

-- --------------------------------------------------------

--
-- Table structure for table `polaznici`
--

DROP TABLE IF EXISTS `polaznici`;
CREATE TABLE IF NOT EXISTS `polaznici` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ime` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prezime` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum_rodjenja` date DEFAULT NULL,
  `telefon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivo_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nivo_id` (`nivo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `polaznici`
--

INSERT INTO `polaznici` (`id`, `ime`, `prezime`, `datum_rodjenja`, `telefon`, `email`, `nivo_id`) VALUES
(4, 'Janko', 'Jankovic', '2001-01-02', '0611231233', 'jankojankovic@example.com', 2),
(5, 'Milorad', 'Miloradovic', '2001-02-02', '0611231235', 'mikiii@gmail.com', 3);

-- --------------------------------------------------------

--
-- Table structure for table `rezervacije`
--

DROP TABLE IF EXISTS `rezervacije`;
CREATE TABLE IF NOT EXISTS `rezervacije` (
  `id` int NOT NULL AUTO_INCREMENT,
  `termin_id` int NOT NULL,
  `polaznik_id` int NOT NULL,
  `status` enum('rezervisano','otkazano') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rezervisano',
  `datum_rezervacije` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `termin_id` (`termin_id`,`polaznik_id`),
  KEY `polaznik_id` (`polaznik_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rezervacije`
--

INSERT INTO `rezervacije` (`id`, `termin_id`, `polaznik_id`, `status`, `datum_rezervacije`) VALUES
(1, 4, 4, 'rezervisano', '2026-06-13 21:50:37'),
(2, 3, 4, 'rezervisano', '2026-06-13 21:53:35'),
(3, 2, 4, 'rezervisano', '2026-06-13 22:03:57');

-- --------------------------------------------------------

--
-- Table structure for table `sertifikati`
--

DROP TABLE IF EXISTS `sertifikati`;
CREATE TABLE IF NOT EXISTS `sertifikati` (
  `id` int NOT NULL AUTO_INCREMENT,
  `polaznik_id` int NOT NULL,
  `naziv` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum_izdavanja` date DEFAULT NULL,
  `opis` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `polaznik_id` (`polaznik_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `termini`
--

DROP TABLE IF EXISTS `termini`;
CREATE TABLE IF NOT EXISTS `termini` (
  `id` int NOT NULL AUTO_INCREMENT,
  `instruktor_id` int NOT NULL,
  `datum` date NOT NULL,
  `vreme` time NOT NULL,
  `trajanje_minuta` int NOT NULL,
  `bazen` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tip_treninga` enum('rekreativni','takmicarski','individualni') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rekreativni',
  `opis` text COLLATE utf8mb4_unicode_ci,
  `kapacitet` int NOT NULL DEFAULT '10',
  `rezervacija_dostupna` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `instruktor_id` (`instruktor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `termini`
--

INSERT INTO `termini` (`id`, `instruktor_id`, `datum`, `vreme`, `trajanje_minuta`, `bazen`, `tip_treninga`, `opis`, `kapacitet`, `rezervacija_dostupna`) VALUES
(2, 1, '2026-06-26', '16:32:00', 60, 'Veliki bazen', 'rekreativni', 'Trening sposobnosti i finalni test pred takmicenje u 400m slobodnom stilu', 5, 1),
(3, 1, '2026-06-21', '13:00:00', 60, 'Veliki bazen', 'rekreativni', 'Vodeni Aerobik', 10, 1),
(4, 2, '2026-06-23', '14:01:00', 60, 'Veliki bazen', 'rekreativni', 'Intenzivni treninzi prsnog plivanja uz dodatan trening fleksibilnosti kukova na vodenom aerobiku', 10, 1),
(7, 1, '2026-06-23', '15:02:00', 60, 'Veliki bazen', 'rekreativni', 'Brziiii', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','zaposleni') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zaposleni',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'Viktor123', 'viktorv@gmail.com', '$2y$10$ysg856jj0.SvG3Ihm5aS5.Me5.ygFC6Nqlu8Cj7BaAnXAyF1HVrUS', '2026-06-13 18:04:41', 'zaposleni');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
