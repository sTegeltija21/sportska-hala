-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 09:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sportska_hala`
--

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `dnevni_izvestaj` (IN `datum_izvestaja` DATE)   SELECT
    broj_zahteva,
    podnosilac,
    sport,
    vreme_od,
    vreme_do,
    broj_ucesnika
FROM pregled_zahteva
WHERE datum_koriscenja = datum_izvestaja
  AND status = 'odobren'
ORDER BY vreme_od$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `dnevna_zauzetost`
-- (See below for the actual view)
--
CREATE TABLE `dnevna_zauzetost` (
`datum_koriscenja` date
,`broj_termina` bigint(21)
,`rezervisano_minuta` decimal(42,4)
,`ukupno_ucesnika` decimal(42,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(11) NOT NULL,
  `korisnicko_ime` varchar(50) NOT NULL,
  `lozinka_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `korisnicko_ime`, `lozinka_hash`) VALUES
(1, 'admin', '$2y$10$CAwSMCJElIYinivRCuo5Wu0zaDSSCh5/0DmfczDl7SDFGENLmeAG.');

-- --------------------------------------------------------

--
-- Stand-in structure for view `pregled_zahteva`
-- (See below for the actual view)
--
CREATE TABLE `pregled_zahteva` (
`id` int(11)
,`broj_zahteva` varchar(30)
,`datum_koriscenja` date
,`vreme_od` time
,`vreme_do` time
,`podnosilac` varchar(100)
,`status` enum('na_cekanju','odobren','odbijen')
,`sport` varchar(50)
,`broj_ucesnika` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `sportovi`
--

CREATE TABLE `sportovi` (
  `id` int(11) NOT NULL,
  `naziv` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sportovi`
--

INSERT INTO `sportovi` (`id`, `naziv`) VALUES
(3, 'Futsal'),
(1, 'Košarka'),
(2, 'Odbojka'),
(4, 'Rukomet');

-- --------------------------------------------------------

--
-- Table structure for table `ucesnici`
--

CREATE TABLE `ucesnici` (
  `id` int(11) NOT NULL,
  `zahtev_id` int(11) NOT NULL,
  `ime_prezime` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ucesnici`
--

INSERT INTO `ucesnici` (`id`, `zahtev_id`, `ime_prezime`) VALUES
(7, 2, 'Luka Ivetic'),
(8, 2, 'Joan Pennaroya'),
(9, 2, 'Kevin Punter'),
(13, 3, 'Luka Ivetic'),
(14, 3, 'Marko Markovic'),
(15, 3, 'Aleksa Avramovic'),
(16, 4, 'Bojan Merca'),
(17, 4, 'Merca Bojan'),
(24, 5, 'Sima simic'),
(25, 5, 'Jared Butler');

-- --------------------------------------------------------

--
-- Table structure for table `zahtevi`
--

CREATE TABLE `zahtevi` (
  `id` int(11) NOT NULL,
  `broj_zahteva` varchar(30) NOT NULL,
  `datum_podnosenja` date NOT NULL,
  `podnosilac` varchar(100) NOT NULL,
  `kontakt` varchar(100) NOT NULL,
  `datum_koriscenja` date NOT NULL,
  `vreme_od` time NOT NULL,
  `vreme_do` time NOT NULL,
  `sport_id` int(11) NOT NULL,
  `status` enum('na_cekanju','odobren','odbijen') NOT NULL DEFAULT 'na_cekanju'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zahtevi`
--

INSERT INTO `zahtevi` (`id`, `broj_zahteva`, `datum_podnosenja`, `podnosilac`, `kontakt`, `datum_koriscenja`, `vreme_od`, `vreme_do`, `sport_id`, `status`) VALUES
(2, 'Z-001/2026', '2026-09-23', 'Carlik Jones', 'carliktatica@gmail.com', '2026-09-30', '13:03:00', '13:56:00', 3, 'na_cekanju'),
(3, 'Z-002/2026', '2026-09-23', 'Marko Markovic', 'markomarkovic@gmail.com', '2026-10-06', '20:30:00', '22:32:00', 1, 'odobren'),
(4, 'Z-003/2026', '2026-09-23', 'Pavle Pavlovic', 'pavle@gmail.com', '2026-10-06', '20:30:00', '22:32:00', 1, 'na_cekanju'),
(5, 'Z-004/2026', '2026-09-23', 'Sima Simonovic', 'simeone67@gmail.com', '2026-09-26', '20:48:00', '21:50:00', 2, 'odobren');

-- --------------------------------------------------------

--
-- Structure for view `pregled_zahteva`
--
DROP TABLE IF EXISTS `pregled_zahteva`;

CREATE ALGORITHM=UNDEFINED VIEW `pregled_zahteva`  AS SELECT `z`.`id` AS `id`, `z`.`broj_zahteva` AS `broj_zahteva`, `z`.`datum_koriscenja` AS `datum_koriscenja`, `z`.`vreme_od` AS `vreme_od`, `z`.`vreme_do` AS `vreme_do`, `z`.`podnosilac` AS `podnosilac`, `z`.`status` AS `status`, `s`.`naziv` AS `sport`, count(`u`.`id`) AS `broj_ucesnika` FROM ((`zahtevi` `z` join `sportovi` `s` on(`s`.`id` = `z`.`sport_id`)) left join `ucesnici` `u` on(`u`.`zahtev_id` = `z`.`id`)) GROUP BY `z`.`id`, `z`.`broj_zahteva`, `z`.`datum_koriscenja`, `z`.`vreme_od`, `z`.`vreme_do`, `z`.`podnosilac`, `z`.`status`, `s`.`naziv` ;

--
-- Structure for view `dnevna_zauzetost`
--
DROP TABLE IF EXISTS `dnevna_zauzetost`;

CREATE ALGORITHM=UNDEFINED VIEW `dnevna_zauzetost`  AS SELECT `pregled_zahteva`.`datum_koriscenja` AS `datum_koriscenja`, count(0) AS `broj_termina`, sum(time_to_sec(timediff(`pregled_zahteva`.`vreme_do`,`pregled_zahteva`.`vreme_od`)) / 60) AS `rezervisano_minuta`, sum(`pregled_zahteva`.`broj_ucesnika`) AS `ukupno_ucesnika` FROM `pregled_zahteva` WHERE `pregled_zahteva`.`status` = 'odobren' GROUP BY `pregled_zahteva`.`datum_koriscenja` ;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`);

--
-- Indexes for table `sportovi`
--
ALTER TABLE `sportovi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `naziv` (`naziv`);

--
-- Indexes for table `ucesnici`
--
ALTER TABLE `ucesnici`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zahtev_id` (`zahtev_id`);

--
-- Indexes for table `zahtevi`
--
ALTER TABLE `zahtevi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `broj_zahteva` (`broj_zahteva`),
  ADD KEY `sport_id` (`sport_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sportovi`
--
ALTER TABLE `sportovi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ucesnici`
--
ALTER TABLE `ucesnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `zahtevi`
--
ALTER TABLE `zahtevi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ucesnici`
--
ALTER TABLE `ucesnici`
  ADD CONSTRAINT `ucesnici_ibfk_1` FOREIGN KEY (`zahtev_id`) REFERENCES `zahtevi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zahtevi`
--
ALTER TABLE `zahtevi`
  ADD CONSTRAINT `zahtevi_ibfk_1` FOREIGN KEY (`sport_id`) REFERENCES `sportovi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
