-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2026 at 02:51 PM
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
-- Database: `trigo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `rider_application`
--

CREATE TABLE `rider_application` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `license_num` int(20) NOT NULL,
  `license_exp` date NOT NULL,
  `model` varchar(255) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `valid_id` blob NOT NULL,
  `submitted_at` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('accepted','rejected','pending') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rider_application`
--

INSERT INTO `rider_application` (`id`, `full_name`, `contact_number`, `email`, `password`, `license_num`, `license_exp`, `model`, `plate_number`, `valid_id`, `submitted_at`, `status`) VALUES
(1, 'Zean Pacho', '2147483647', 'zeanpacho@gmail.com', '$2y$10$K/5K6UJShSvyDMRDrCSpj.jVTNzqrgesNoaXnpaLe8CcsYbOvOCTe', 2147483647, '2026-11-03', 'motorstar', 'abc 123', '', '2026-11-06', 'accepted'),
(2, 'shawn ', '123132331', 'shawn@gmail.com', '$2y$10$BhScYLQ0VBEnGuhXam/HqO1oC13VSAY/IOkeSgs7.0it4jZBT9432', 2147483647, '2026-11-28', 'tmx', '123 abc', 0x526f626c6f7853637265656e53686f7432303236313032385f3134353833333730372e706e67, '2026-11-06', 'accepted'),
(3, 'adriane', '990093131', 'adriane@gmail.com', '$2y$10$xKoOL9wp8ppN.PQbobSLZe9HOw.frUis7vn4pSMCD9S1Elitro5Ne', 12313331, '2026-11-20', 'bajaj', '2213 ddc', 0x526f626c6f7853637265656e53686f7432303236313033305f3233323631393832302e706e67, '2026-11-06', 'accepted'),
(4, 'jaren dumpang', '12313131', 'jaren@gmail.com', '$2y$10$SPjQ37ad62gWxyrITkWV6eovmTv3FtWV3/LCAPweQJ4BB4dtdc/hC', 123131, '2026-11-30', 'tmx', 'abc 123', '', '2026-11-06', 'accepted'),
(6, 'adriane ', '123131231', 'jarendumpang16@gmail.com', '$2y$10$FjjXTtJLjCTr5Wwzr0Jz8O8JXqC7V/XGgeeZQHPSVUS4uIs3/tgrK', 13213131, '2026-11-30', 'CLICK', 'ads 213', '', '2026-11-14', 'rejected');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rider_application`
--
ALTER TABLE `rider_application`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rider_application`
--
ALTER TABLE `rider_application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
