-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2026 at 08:50 PM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.3.36

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
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rider_id` int(11) DEFAULT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `distance_km` decimal(8,2) DEFAULT NULL,
  `fare` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','accepted','ongoing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `rider_id`, `pickup_location`, `dropoff_location`, `distance_km`, `fare`, `status`, `created_at`, `accepted_at`) VALUES
(1, 1, 1, 'angeles ', 'mabalacat', 2.00, 50.00, 'pending', '2026-11-07 13:54:49', NULL),
(2, 4, 5, 'angeles', 'mabalacat', 3.80, 65.00, 'accepted', '2026-11-07 14:42:39', '2026-11-07 22:47:25'),
(3, 4, 5, 'San Isidro Public Market', 'Municipal Hall', 3.80, 65.00, 'accepted', '2026-11-07 14:42:46', '2026-11-07 23:45:29'),
(4, 4, 5, 'mabalacat', 'angeles', 3.80, 65.00, 'accepted', '2026-11-07 15:48:53', '2026-11-07 23:49:05'),
(5, 4, 5, 'angeles', 'manila', 3.80, 65.00, 'accepted', '2026-11-07 15:51:49', '2026-11-07 23:51:58'),
(6, 4, 5, 'manila', 'cebu', 3.80, 65.00, 'accepted', '2026-11-07 16:01:11', '2026-11-08 00:01:17'),
(7, 4, 5, 'manila', 'olongapo', 3.80, 65.00, 'accepted', '2026-11-07 16:06:21', '2026-11-08 00:06:56'),
(8, 4, 5, 'bataan', 'zambales', 3.80, 65.00, 'accepted', '2026-11-07 16:08:15', '2026-11-08 00:08:23'),
(9, 4, 5, 'angeles', 'sanfernando', 3.80, 65.00, 'accepted', '2026-11-07 16:13:09', '2026-11-08 00:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `booking_messages`
--

CREATE TABLE `booking_messages` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('customer','rider','admin') NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id` int(11) NOT NULL,
  `rider_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` varchar(30) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `license_num` varchar(50) DEFAULT NULL,
  `license_exp` date NOT NULL,
  `model` varchar(255) NOT NULL,
  `plate_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riders`
--

INSERT INTO `riders` (`id`, `rider_id`, `full_name`, `number`, `email`, `license_num`, `license_exp`, `model`, `plate_number`) VALUES
(1, 13, 'jaypee', '2147483647', 'jaypeepaminutan98@gmail.com', '541210846', '2026-12-03', 'tmx125', 'T9A832'),
(3, 15, 'Zean Pacho', '2147483647', 'zeanpacho7@gmail.com', '2147483647', '2026-11-20', 'motorstar123', 'M4T339');

-- --------------------------------------------------------

--
-- Table structure for table `rider_application`
--

CREATE TABLE `rider_application` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` varchar(30) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `license_num` varchar(50) DEFAULT NULL,
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
(1, 'Zean Pacho', '2147483647', 'zeanpacho@gmail.com', '$2y$10$K/5K6UJShSvyDMRDrCSpj.jVTNzqrgesNoaXnpaLe8CcsYbOvOCTe', '2147483647', '2026-11-03', 'motorstar', 'abc 123', '', '2026-11-06', 'accepted'),
(2, 'shawn ', '123132331', 'shawn@gmail.com', '$2y$10$BhScYLQ0VBEnGuhXam/HqO1oC13VSAY/IOkeSgs7.0it4jZBT9432', '2147483647', '2026-11-28', 'tmx', '123 abc', 0x526f626c6f7853637265656e53686f7432303236313032385f3134353833333730372e706e67, '2026-11-06', 'accepted'),
(3, 'adriane', '990093131', 'adriane@gmail.com', '$2y$10$xKoOL9wp8ppN.PQbobSLZe9HOw.frUis7vn4pSMCD9S1Elitro5Ne', '12313331', '2026-11-20', 'bajaj', '2213 ddc', 0x526f626c6f7853637265656e53686f7432303236313033305f3233323631393832302e706e67, '2026-11-06', 'accepted'),
(4, 'jaren dumpang', '12313131', 'jaren@gmail.com', '$2y$10$SPjQ37ad62gWxyrITkWV6eovmTv3FtWV3/LCAPweQJ4BB4dtdc/hC', '123131', '2026-11-30', 'tmx', 'abc 123', '', '2026-11-06', 'accepted'),
(5, 'jarendumpang', '2147483647', 'jarendumpang@gmail.com', '$2y$10$ieoILgi3.LTya7WFbzZ18uOLmoQkDeHewrJTPTA1U/FYzEP32pGTG', '45420154', '2026-11-18', 'tmx125', 'G9J942', 0x6a7065672d686f6d652e6a7067, '2026-11-13', 'accepted'),
(6, 'bien barrato', '2147483647', 'bien@gmail.com', '$2y$10$/rqJATFQJSqXmy3pNw4rxe2iH6lv/XPzpHYrEgN1H/l0lPXXE7Fgy', '2147483647', '2026-11-17', 'bajaj 155', 'B1J785', 0x6a7065672d686f6d652e6a7067, '2026-11-13', 'accepted'),
(13, 'jaypee', '2147483647', 'jaypeepaminutan98@gmail.com', '$2y$10$RKp6.TtLNKgAOXyJmgPay.oVXDynM8RAqwVxDgBZbKN48Bo7rbtM6', '541210846', '2026-12-03', 'tmx125', 'T9A832', 0x6a7065672d686f6d652e6a7067, '2026-11-16', 'accepted'),
(15, 'Zean Pacho', '2147483647', 'zeanpacho7@gmail.com', '$2y$10$RzI8Dma/APhRa/tjhwkKi.ZPqzZ4j.h7A.gxuYPYni5vChPK/jY3K', '2147483647', '2026-11-20', 'motorstar123', 'M4T339', 0x6a7065672d686f6d652e6a7067, '2026-11-16', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','rider','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `number`, `email`, `password`, `role`) VALUES
(1, 'jaren dumpang', '12313131', 'jaren@gmail.com', 'c8734841ee98ef32e2db971c718ef3c3', 'admin'),
(2, 'shawn genese', '12313313', 'shawn@gmail.com', 'shawn123', 'admin'),
(3, 'zean pacho', '12313131', 'zean@gmail.com', '$2y$10$RuhCjNg/xP1ezT8zN/o8.ezCK37IKLJmEV.ituBaD7sprDMU3MHSS', 'admin'),
(4, 'adriane', '123131', 'adriane@gmail.com', '$2y$10$GxJr5IAHq8uBN5n4Xwg5xe6x5aHJE79/Zykyj7mRjUKbBgaDB8dI2', 'user'),
(5, 'john vincent natividad', '1313131311', 'vincent@gmail.com', '$2y$10$sC.shHUIOqfM2n1wipkt2.MSqbvwUXeMg.RXsv2w/j.kHLtA3khh2', 'rider');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_rider` (`rider_id`),
  ADD KEY `idx_booking_queue` (`status`,`rider_id`,`created_at`);

--
-- Indexes for table `booking_messages`
--
ALTER TABLE `booking_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `sent_at` (`sent_at`);

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
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `booking_messages`
--
ALTER TABLE `booking_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rider_application`
--
ALTER TABLE `rider_application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
