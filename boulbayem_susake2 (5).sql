-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-boulbayem.alwaysdata.net
-- Generation Time: Mar 18, 2025 at 10:59 AM
-- Server version: 10.11.8-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boulbayem_susake2`
--

-- --------------------------------------------------------

--
-- Table structure for table `Addition`
--

CREATE TABLE `Addition` (
  `addition_id` int(11) NOT NULL,
  `commande_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` enum('Non Payée','Payée') DEFAULT 'Non Payée',
  `date_addition` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Boissons`
--

CREATE TABLE `Boissons` (
  `boisson_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` longblob DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Commandes`
--

CREATE TABLE `Commandes` (
  `commande_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `date_commande` timestamp NULL DEFAULT current_timestamp(),
  `statut` enum('Non Payée','Payée') NOT NULL DEFAULT 'Non Payée',
  `archivee` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Desserts`
--

CREATE TABLE `Desserts` (
  `dessert_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` longblob DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Plats`
--

CREATE TABLE `Plats` (
  `plat_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` longblob DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tables`
--

CREATE TABLE `Tables` (
  `table_id` int(11) NOT NULL,
  `numero_table` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Vue_Commande_Table`
--

CREATE TABLE `Vue_Commande_Table` (
  `vue_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `plat_nom` varchar(255) DEFAULT NULL,
  `boisson_nom` varchar(255) DEFAULT NULL,
  `dessert_nom` varchar(255) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Addition`
--
ALTER TABLE `Addition`
  ADD PRIMARY KEY (`addition_id`),
  ADD UNIQUE KEY `commande_id` (`commande_id`);

--
-- Indexes for table `Boissons`
--
ALTER TABLE `Boissons`
  ADD PRIMARY KEY (`boisson_id`);

--
-- Indexes for table `Commandes`
--
ALTER TABLE `Commandes`
  ADD PRIMARY KEY (`commande_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `Desserts`
--
ALTER TABLE `Desserts`
  ADD PRIMARY KEY (`dessert_id`);

--
-- Indexes for table `Plats`
--
ALTER TABLE `Plats`
  ADD PRIMARY KEY (`plat_id`);

--
-- Indexes for table `Tables`
--
ALTER TABLE `Tables`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `numero_table` (`numero_table`);

--
-- Indexes for table `Vue_Commande_Table`
--
ALTER TABLE `Vue_Commande_Table`
  ADD PRIMARY KEY (`vue_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `commande_id` (`commande_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Addition`
--
ALTER TABLE `Addition`
  MODIFY `addition_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Boissons`
--
ALTER TABLE `Boissons`
  MODIFY `boisson_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Commandes`
--
ALTER TABLE `Commandes`
  MODIFY `commande_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Desserts`
--
ALTER TABLE `Desserts`
  MODIFY `dessert_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Plats`
--
ALTER TABLE `Plats`
  MODIFY `plat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tables`
--
ALTER TABLE `Tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Vue_Commande_Table`
--
ALTER TABLE `Vue_Commande_Table`
  MODIFY `vue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Addition`
--
ALTER TABLE `Addition`
  ADD CONSTRAINT `Addition_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `Commandes` (`commande_id`) ON DELETE CASCADE;

--
-- Constraints for table `Commandes`
--
ALTER TABLE `Commandes`
  ADD CONSTRAINT `Commandes_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `Tables` (`table_id`) ON DELETE CASCADE;

--
-- Constraints for table `Vue_Commande_Table`
--
ALTER TABLE `Vue_Commande_Table`
  ADD CONSTRAINT `Vue_Commande_Table_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `Tables` (`table_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Vue_Commande_Table_ibfk_2` FOREIGN KEY (`commande_id`) REFERENCES `Commandes` (`commande_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
