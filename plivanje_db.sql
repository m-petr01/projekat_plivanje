-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 15, 2026 at 12:54 PM
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instruktori`
--

INSERT INTO `instruktori` (`id`, `ime`, `prezime`, `telefon`, `email`, `specijalnost`, `biografija`, `godine_iskustva`, `sertifikati_opis`, `obrazovanje`) VALUES
(1, 'Sale', 'Salinjo', '0611231234', 'salesaki@gmail.com', 'kraul 400m', 'Sale Salinjo je instruktor plivanja specijalizovan za kraul, razvoj kondicije i pripremu plivača za srednje i duge deonice. U radu sa polaznicima poseban akcenat stavlja na pravilnu tehniku disanja, položaj tela u vodi, ekonomičnost pokreta i postepeno povećavanje izdržljivosti. Iskustvo je sticao kroz individualne i grupne treninge sa početnicima, rekreativcima i takmičarima. Njegov pristup je disciplinovan, ali prilagođen sposobnostima i ciljevima svakog polaznika.', 5, 'Licencirani instruktor plivanja. Završena stručna obuka za rad sa početnicima i neplivačima. Sertifikat iz oblasti bezbednosti na vodi i pružanja prve pomoći. Dodatna edukacija iz planiranja kondicionih treninga za plivače i usavršavanja tehnike kraula. Iskustvo u pripremi plivača za discipline 200 m i 400 m slobodnim stilom.', 'Fakultet sporta i fizickog vaspitanja'),
(2, 'Ivan', 'Ivanovic', '0611234569', 'ivanchad@example.com', 'prsno 50m', 'Ivan je instruktor plivanja sa sedam godina iskustva u radu sa početnicima, rekreativcima i naprednim plivačima. Specijalizovan je za prsno plivanje na 50 metara, pravilno disanje i preživljavanje nakon prejakog starta. Poznat je po tome što strogo vodi trening, ali se povremeno neprimetno iskrade iz bazena i priključi grupi za vodeni aerobik. Tvrdi da to radi isključivo zbog stručnog usavršavanja i bolje pokretljivosti kukova.', 67, 'Licencirani instruktor plivanja\r\nSertifikat za spasavanje na vodi', 'Fakultet sporta i fizičkog vaspitanja, smer plivanje i vodeni sportovi'),
(3, 'Filip', 'Markovic', '0602131234', 'filipm494@gmail.com', 'freestyle 200m', 'Bivši takmičarski plivač sa fokusom na slobodni stil i discipline srednjih distanci. Više godina iskustva u obuci početnika, rekreativaca i naprednih plivača. Poseban akcenat stavlja na pravilnu tehniku zaveslaja, disanje i ekonomičnost pokreta u vodi.', 12, NULL, 'Fakultet sporta i fizičkog vaspitanja, smer plivanje i vodeni sportovi');

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nagrade`
--

INSERT INTO `nagrade` (`id`, `polaznik_id`, `naziv`, `opis`, `datum`) VALUES
(1, 8, 'Prvo mesto- 100m kraul', 'Osvojeno prvo mesto na gradskom takmicenju u plivanju u disciplini 100 metara kraul, sa vremenom 58.42 sekunde.', '2026-02-26');

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `polaznici`
--

INSERT INTO `polaznici` (`id`, `ime`, `prezime`, `datum_rodjenja`, `telefon`, `email`, `nivo_id`) VALUES
(4, 'Janko', 'Jankovic', '2001-01-02', '0611231233', 'jankojankovic@example.com', 1),
(5, 'Milorad', 'Miloradovic', '2001-02-02', '0611231235', 'mikiii@gmail.com', 3),
(7, 'Stefan', 'Stefanovic', '1993-03-03', '0611231245', 'sstefan@outlook.com', 4),
(8, 'Luka', 'Petrovic', '2001-05-01', '0611231432', 'luleee@example.com', 3);

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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rezervacije`
--

INSERT INTO `rezervacije` (`id`, `termin_id`, `polaznik_id`, `status`, `datum_rezervacije`) VALUES
(1, 4, 4, 'rezervisano', '2026-06-13 21:50:37'),
(2, 3, 4, 'rezervisano', '2026-06-13 21:53:35'),
(3, 2, 4, 'rezervisano', '2026-06-13 22:03:57'),
(4, 2, 5, 'rezervisano', '2026-06-14 01:01:51'),
(5, 9, 4, 'rezervisano', '2026-06-14 01:13:04'),
(6, 10, 4, 'rezervisano', '2026-06-14 01:14:54'),
(7, 11, 4, 'rezervisano', '2026-06-14 01:17:06'),
(8, 12, 4, 'rezervisano', '2026-06-14 01:19:52'),
(9, 9, 8, 'rezervisano', '2026-06-14 10:59:39'),
(10, 14, 5, 'rezervisano', '2026-06-14 11:46:54'),
(11, 7, 5, 'rezervisano', '2026-06-15 12:08:26');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sertifikati`
--

INSERT INTO `sertifikati` (`id`, `polaznik_id`, `naziv`, `datum_izdavanja`, `opis`) VALUES
(1, 8, 'Napredna plivačka tehnika', '2026-06-14', NULL),
(2, 7, 'Takmičarska osposobljenost', '2026-06-14', NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `termini`
--

INSERT INTO `termini` (`id`, `instruktor_id`, `datum`, `vreme`, `trajanje_minuta`, `bazen`, `tip_treninga`, `opis`, `kapacitet`, `rezervacija_dostupna`) VALUES
(13, 2, '2026-06-26', '19:00:00', 60, 'Otvoreni bazen', 'takmicarski', 'takmicenje', 10, 1),
(14, 1, '2026-06-14', '18:00:00', 60, 'Otvoreni bazen', 'individualni', NULL, 1, 1),
(3, 1, '2026-06-21', '16:00:00', 60, 'Otvoreni bazen', 'rekreativni', 'Vodeni Aerobik', 10, 1),
(4, 2, '2026-06-23', '14:01:00', 60, 'Veliki bazen', 'rekreativni', 'Intenzivni treninzi prsnog plivanja uz dodatan trening fleksibilnosti kukova na vodenom aerobiku', 10, 1),
(7, 1, '2026-06-23', '19:00:00', 60, 'Otvoreni bazen', 'individualni', 'Trening usavrsavanja tehnike na kraul stilu uz konstantno pracenje trenera', 1, 1),
(9, 2, '2026-06-14', '16:00:00', 60, 'Otvoreni bazen', 'rekreativni', NULL, 10, 1);

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'Viktor123', 'viktorv@gmail.com', '$2y$10$ysg856jj0.SvG3Ihm5aS5.Me5.ygFC6Nqlu8Cj7BaAnXAyF1HVrUS', '2026-06-13 18:04:41', 'zaposleni'),
(2, 'Zoki123', 'zokicar@hotmail.com', '$2y$10$HJIA1sJmZt7soQZLh6PKAeretr27Gvfs7HwrqdUqEcYm8EscsrACe', '2026-06-14 11:58:22', 'zaposleni');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
