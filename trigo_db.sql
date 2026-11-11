-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 10:40 AM
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
(9, 4, 5, 'angeles', 'sanfernando', 3.80, 65.00, 'accepted', '2026-11-07 16:13:09', '2026-11-08 00:13:18'),
(10, 7, 5, 'angeles', 'Municipal Hall', 3.80, 65.00, 'accepted', '2026-11-09 13:07:08', '2026-11-10 07:39:08'),
(11, 4, 5, 'Angeles, Central Luzon, Philippines', 'Mabalacat, Pampanga, Central Luzon, Philippines', 11.94, 65.00, 'accepted', '2026-11-09 23:40:01', '2026-11-10 07:40:17'),
(12, 4, 5, 'Remy Street, Villa Teresa Subdivision, Santo Rosario, Angeles, Central Luzon, 1992, Philippines', 'Cristina Drive, Villa Teresa Subdivision, Santo Rosario, Angeles, Central Luzon, 1992, Philippines', 0.61, 65.00, 'accepted', '2026-11-09 23:46:40', '2026-11-10 07:46:44'),
(13, 4, 5, 'Angeles, Central Luzon, Philippines', 'Museum of Philippine Social History, Santo Entierro Street, Santo Rosario, Angeles, Central Luzon, 1992, Philippines', 0.62, 65.00, 'accepted', '2026-11-10 02:59:55', '2026-11-10 11:00:11'),
(14, 4, 5, 'Angeles, Central Luzon, Philippines', 'L.P. CAndelaria Pharmacy, MacArthur Highway, Aguso, Santa Ines, San Francisco, Mabalacat, Pampanga, Central Luzon, 2010, Philippines', 11.59, 289.75, 'accepted', '2026-11-10 03:15:01', '2026-11-10 11:16:09'),
(15, 9, 5, 'Angeles, Central Luzon, Philippines', 'Espada Psychological Consultancy, Santo Entierro Street, Doña Belen Subdivision, Santo Cristo, Angeles, Central Luzon, 1992, Philippines', 1.10, 27.50, 'accepted', '2026-11-10 03:20:40', '2026-11-10 11:21:36'),
(16, 4, 5, 'Angeles, Central Luzon, Philippines', 'Mabalacat, Pampanga, Central Luzon, Philippines', 11.94, 298.50, 'accepted', '2026-11-10 03:36:05', '2026-11-10 11:36:22');

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
(4, 50.00, 500.00, 25.00, 80.00);

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
(4, 'jaren dumpang', 12313131, 'jaren@gmail.com', '$2y$10$SPjQ37ad62gWxyrITkWV6eovmTv3FtWV3/LCAPweQJ4BB4dtdc/hC', 123131, '2026-11-30', 'tmx', 'abc 123', '', '2026-11-06', 'accepted'),
(5, 'Zean basis', 2147483647, 'audreypacho@gmail.com', '$2y$10$KkGzf.7gbjqpABJLpbVkTu4OHCagAhaug3dKpEkvH8k/HJg2vE4Gm', 2147483647, '2029-06-12', 'motorstar', 'abc 123', 0x57494e5f32303236303831325f30385f32335f35345f50726f2e6a7067, '2026-11-10', 'accepted');

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
(3, 'zean pacho', 12313131, 'zean@gmail.com', '$2y$10$RuhCjNg/xP1ezT8zN/o8.ezCK37IKLJmEV.ituBaD7sprDMU3MHSS', 'admin'),
(4, 'adriane', 123131, 'adriane@gmail.com', '$2y$10$GxJr5IAHq8uBN5n4Xwg5xe6x5aHJE79/Zykyj7mRjUKbBgaDB8dI2', 'user'),
(5, 'john vincent natividad', 1313131311, 'vincent@gmail.com', '$2y$10$sC.shHUIOqfM2n1wipkt2.MSqbvwUXeMg.RXsv2w/j.kHLtA3khh2', 'rider'),
(6, 'zeanbasis', 963256757, 'zeanpacho7@gmail.com', 'zeanbasis', 'admin'),
(7, 'audreybasis', 990093131, 'zeanpacho56@gmail.com', '$2y$10$4mkJ/lzPiUqm..VD.zTvXO6fl35Ytw4bGTVlDOggnO3LZCtNwfeTi', 'user'),
(8, 'ralph ong', 231313313, 'ralph@gmail.com', '$2y$10$OKQX3p0pkwHqiQOhhblJ6eKiQGqPJ8oghKxTyMu4fmnHRN7e7AsWy', 'rider'),
(9, 'edwingarcia', 2147483647, 'edwin@gmail.com', '$2y$10$SUq/pG31UhcmhET1tfsTaekXvlNX5AHNjookh7yujxXdiz8miQdbK', 'user'),
(10, 'zianpacho', 2147483647, 'basis@mail.com', '$2y$10$7p8HmvT4Bb28BLLKadX8kuzDn23fiLQaaripjGCq0JveS/9cvNVcq', 'admin');

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
