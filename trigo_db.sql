-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2026 at 03:58 PM
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
-- Table structure for table `completed_rides`
--

CREATE TABLE `completed_rides` (
  `ride_id` int(11) NOT NULL,
  `rider_id` int(11) NOT NULL,
  `passenger_name` varchar(100) DEFAULT NULL,
  `fare_amount` decimal(10,2) NOT NULL,
  `commission_percent` decimal(5,2) DEFAULT 15.00,
  `ride_date` datetime NOT NULL,
  `status` enum('completed','cancelled') DEFAULT 'completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fare_settings`
--

CREATE TABLE `fare_settings` (
  `id` int(11) NOT NULL,
  `base_fare` decimal(10,2) NOT NULL DEFAULT 25.00,
  `included_distance` decimal(10,2) NOT NULL DEFAULT 1.00,
  `per_km_rate` decimal(10,2) NOT NULL DEFAULT 10.00,
  `company_commission` decimal(5,2) NOT NULL DEFAULT 15.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fare_settings`
--

INSERT INTO `fare_settings` (`id`, `base_fare`, `included_distance`, `per_km_rate`, `company_commission`) VALUES
(4, 50.00, 500.00, 100000.00, 80.00);

-- --------------------------------------------------------

--
-- Table structure for table `franchise`
--

CREATE TABLE `franchise` (
  `franchise_id` int(11) NOT NULL,
  `operator` varchar(255) NOT NULL,
  `assigned_vehicle` varchar(255) NOT NULL,
  `issue_date` date NOT NULL DEFAULT current_timestamp(),
  `expiry_date` date NOT NULL,
  `status` enum('active','expire') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `franchise`
--

INSERT INTO `franchise` (`franchise_id`, `operator`, `assigned_vehicle`, `issue_date`, `expiry_date`, `status`) VALUES
(1, 'jaren', 'tmx', '2026-11-06', '2026-11-10', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_application`
--

CREATE TABLE `franchise_application` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` int(12) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `op_address` varchar(255) NOT NULL,
  `business_reg` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `img` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `total_income` int(11) NOT NULL,
  `rider_earnings` int(11) NOT NULL,
  `company_income` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`id`, `total_income`, `rider_earnings`, `company_income`) VALUES
(1, 25000, 5000, 20000);

-- --------------------------------------------------------

--
-- Table structure for table `pending_applications`
--

CREATE TABLE `pending_applications` (
  `app_id` int(11) NOT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `application_type` enum('rider','franchise') NOT NULL,
  `contact_number` int(12) NOT NULL,
  `application_date` date NOT NULL,
  `status` enum('pending','reviewed','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riders`
--

CREATE TABLE `riders` (
  `rider_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `contact_number` int(12) NOT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `date_joined` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider_application`
--

CREATE TABLE `rider_application` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` int(12) NOT NULL,
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

INSERT INTO `rider_application` (`id`, `full_name`, `number`, `email`, `password`, `license_num`, `license_exp`, `model`, `plate_number`, `valid_id`, `submitted_at`, `status`) VALUES
(1, 'Zean Pacho', 2147483647, 'zeanpacho@gmail.com', '$2y$10$K/5K6UJShSvyDMRDrCSpj.jVTNzqrgesNoaXnpaLe8CcsYbOvOCTe', 2147483647, '2026-11-03', 'motorstar', 'abc 123', '', '2026-11-06', 'accepted'),
(2, 'shawn ', 123132331, 'shawn@gmail.com', '$2y$10$BhScYLQ0VBEnGuhXam/HqO1oC13VSAY/IOkeSgs7.0it4jZBT9432', 2147483647, '2026-11-28', 'tmx', '123 abc', 0x526f626c6f7853637265656e53686f7432303236313032385f3134353833333730372e706e67, '2026-11-06', 'accepted'),
(3, 'adriane', 990093131, 'adriane@gmail.com', '$2y$10$xKoOL9wp8ppN.PQbobSLZe9HOw.frUis7vn4pSMCD9S1Elitro5Ne', 12313331, '2026-11-20', 'bajaj', '2213 ddc', 0x526f626c6f7853637265656e53686f7432303236313033305f3233323631393832302e706e67, '2026-11-06', 'accepted'),
(4, 'jaren dumpang', 12313131, 'jaren@gmail.com', '$2y$10$SPjQ37ad62gWxyrITkWV6eovmTv3FtWV3/LCAPweQJ4BB4dtdc/hC', 123131, '2026-11-30', 'tmx', 'abc 123', '', '2026-11-06', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` int(12) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','rider','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `number`, `email`, `password`, `role`) VALUES
(1, 'jaren dumpang', 12313131, 'jaren@gmail.com', 'c8734841ee98ef32e2db971c718ef3c3', 'admin'),
(2, 'shawn genese', 12313313, 'shawn@gmail.com', 'shawn123', 'admin'),
(3, 'zean pacho', 12313131, 'zean@gmail.com', '$2y$10$RuhCjNg/xP1ezT8zN/o8.ezCK37IKLJmEV.ituBaD7sprDMU3MHSS', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `completed_rides`
--
ALTER TABLE `completed_rides`
  ADD PRIMARY KEY (`ride_id`);

--
-- Indexes for table `fare_settings`
--
ALTER TABLE `fare_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `franchise`
--
ALTER TABLE `franchise`
  ADD PRIMARY KEY (`franchise_id`);

--
-- Indexes for table `franchise_application`
--
ALTER TABLE `franchise_application`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_applications`
--
ALTER TABLE `pending_applications`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `riders`
--
ALTER TABLE `riders`
  ADD PRIMARY KEY (`rider_id`);

--
-- Indexes for table `rider_application`
--
ALTER TABLE `rider_application`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `completed_rides`
--
ALTER TABLE `completed_rides`
  MODIFY `ride_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fare_settings`
--
ALTER TABLE `fare_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `franchise`
--
ALTER TABLE `franchise`
  MODIFY `franchise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `franchise_application`
--
ALTER TABLE `franchise_application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pending_applications`
--
ALTER TABLE `pending_applications`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riders`
--
ALTER TABLE `riders`
  MODIFY `rider_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rider_application`
--
ALTER TABLE `rider_application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
