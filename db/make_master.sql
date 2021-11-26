-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 12, 2020 at 01:02 PM
-- Server version: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.3.15-3+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `order_green`
--

-- --------------------------------------------------------

--
-- Table structure for table `make_master`
--

CREATE TABLE `make_master` (
  `make_id` int(11) NOT NULL,
  `make` varchar(100) DEFAULT NULL,
  `make_logo` varchar(255) DEFAULT NULL,
  `make_status` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `make_master`
--

INSERT INTO `make_master` (`make_id`, `make`, `make_logo`, `make_status`) VALUES
(1, 'Acura', 'Acura/acura.png', 1),
(2, 'AUDI', 'Audi/Audi.png', 1),
(3, 'Alfa Romeo', 'alfa-romeo/alfa-romeo.png', 1),
(4, 'Aprilia', 'aprilia/aprilia.png', 1),
(5, 'Aptera', 'Aptera/Aptera.png', 1),
(6, 'Aston Martin', 'AstonMartin/AstonMartin.png', 1),
(7, 'Austin', 'Austin/Austin.png', 1),
(8, 'Bentley', 'Bentley/Bentley.jpg', 1),
(9, 'BMW', 'BMW/BMW.png', 1),
(10, 'Brabus', 'Brabus/Brabus.png', 1),
(11, 'Bugatti', 'Bugatti/Bugatti.png', 1),
(12, 'Buick', 'Buick/Buick.png', 1),
(13, 'Cadillac', 'Cadillac/Cadillac.png', 1),
(14, 'Changan', 'Changan/Changan.png', 1),
(15, 'Chery', 'Chery/Chery.png', 1),
(16, 'Chevrolet', 'Chevrolet/Chevrolet.png', 1),
(17, 'Daewoo', 'Daewoo/Daewoo.png', 1),
(18, 'Daihatsu', 'Daihatsu/Daihatsu.png', 1),
(19, 'Dodge', 'Dodge/Dodge.png', 1),
(20, 'FIAT', 'FIAT/FIAT.png', 1),
(21, 'Ford', 'Ford/Ford.png', 1),
(22, 'GAC', 'GAC/GAC.png', 1),
(23, 'Geely', 'Geely/Geely.png', 1),
(24, 'Honda', 'Honda/Honda.png', 1),
(25, 'HUMMER', 'HUMMER/HUMMER.png', 1),
(26, 'Hyundai', 'Hyundai/Hyundai.png', 1),
(27, 'Isuzu', 'Isuzu/Isuzu.png', 1),
(28, 'JAC', 'JAC/JAC.png', 1),
(29, 'Jeep', 'Jeep/Jeep.png', 1),
(30, 'KIA', 'KIA/KIA.png', 1),
(31, 'Land Rover', 'LandRover/LandRover.png', 1),
(32, 'Lexus', 'Lexus/Lexus.png', 1),
(33, 'Lincoln', 'Lincoln/Lincoln.png', 1),
(34, 'Maserati', 'Maserati/Maserati.png', 1),
(35, 'Mazda', 'Mazda/Mazda.png', 1),
(36, 'Mclaren', 'Mclaren/Mclaren.png', 1),
(37, 'Mercedes-Benz', 'MercedesBenz/MercedesBenz.png', 1),
(38, 'Mercury', 'Mercury/Mercury.png', 1),
(39, 'MG', 'MG/MG.jpg', 1),
(40, 'MINI', 'MINI/MINI.png', 1),
(41, 'Mitsubishi', 'Mitsubishi/Mitsubishi.jpg', 1),
(42, 'NISSAN', 'NISSAN/NISSAN.jpg', 1),
(43, 'OPEL', 'OPEL/OPEL.png', 1),
(44, 'Peugeot', 'Peugeot/Peugeot.png', 1),
(45, 'Ram', 'Ram/Ram.png', 1),
(46, 'Range Rover', 'LandRover/LandRover.png', 1),
(47, 'Renault', 'Renault/Renault.png', 1),
(48, 'Saab', 'Saab/Saab.png', 1),
(49, 'SEAT', 'SEAT/SEAT.png', 1),
(50, 'Skoda', 'Skoda/Skoda.png', 1),
(51, 'Smart', 'Smart/Smart.png', 1),
(52, 'Subaru', 'Subaru/Subaru.png', 1),
(53, 'Suzuki', 'Suzuki/Suzuki.png', 1),
(54, 'Tesla', 'Tesla/Tesla.png', 1),
(55, 'Toyota', 'Toyota/Toyota.png', 1),
(56, 'Volkswagen', 'Volkswagen/Volkswagen.png', 1),
(57, 'Volvo', 'Volvo/Volvo.jpg', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `make_master`
--
ALTER TABLE `make_master`
  ADD PRIMARY KEY (`make_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `make_master`
--
ALTER TABLE `make_master`
  MODIFY `make_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
