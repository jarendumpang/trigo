-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2026 at 02:39 PM
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
-- Table structure for table `franchises`
--

CREATE TABLE `franchises` (
  `franchise_id` int(11) NOT NULL,
  `franchise_name` varchar(100) NOT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `location` varchar(150) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `date_registered` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive','pending') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `franchises`
--

INSERT INTO `franchises` (`franchise_id`, `franchise_name`, `owner_name`, `location`, `contact_number`, `date_registered`, `status`) VALUES
(1, 'San Isidro Main Branch', 'ralph ong', 'San Isidro, Pampanga', '09632567978', '2026-11-03 21:14:04', 'active'),
(2, 'Apalit Service Hub', 'jaren dumpang', 'Apalit, Pampanga', '096547987', '2026-11-03 21:14:04', 'active'),
(3, 'Mabalacat Terminal', NULL, 'Mabalacat, Pampanga', NULL, '2026-11-03 21:14:04', 'active'),
(4, 'Angeles City Office', NULL, 'Angeles City, Pampanga', NULL, '2026-11-03 21:14:04', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_application`
--

CREATE TABLE `franchise_application` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
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
-- Table structure for table `income_overview`
--

CREATE TABLE `income_overview` (
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
-- Table structure for table `pending_applications`
--

CREATE TABLE `pending_applications` (
  `app_id` int(11) NOT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `application_type` enum('rider','franchise') NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `application_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','reviewed','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riders`
--

CREATE TABLE `riders` (
  `rider_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `date_joined` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider_application`
--

CREATE TABLE `rider_application` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `licence_num` int(11) NOT NULL,
  `license_exp` datetime NOT NULL,
  `model` varchar(255) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `valid_id` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `completed_rides`
--
ALTER TABLE `completed_rides`
  ADD PRIMARY KEY (`ride_id`);

--
-- Indexes for table `franchises`
--
ALTER TABLE `franchises`
  ADD PRIMARY KEY (`franchise_id`);

--
-- Indexes for table `franchise_application`
--
ALTER TABLE `franchise_application`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `income_overview`
--
ALTER TABLE `income_overview`
  ADD PRIMARY KEY (`ride_id`);

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
-- AUTO_INCREMENT for table `franchises`
--
ALTER TABLE `franchises`
  MODIFY `franchise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `franchise_application`
--
ALTER TABLE `franchise_application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_overview`
--
ALTER TABLE `income_overview`
  MODIFY `ride_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
