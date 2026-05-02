-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2026 at 10:34 AM
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
-- Database: `carhub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `car_images`
--

CREATE TABLE `car_images` (
  `image_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_images`
--

INSERT INTO `car_images` (`image_id`, `listing_id`, `file_path`, `is_main`) VALUES
(1, 1, 'images/car2.jpg', 1),
(2, 1, 'images/car2-2.jpg', 0),
(3, 1, 'images/car2-3.jpg', 0),
(4, 2, 'images/car3.jpg', 1),
(5, 2, 'images/car3-2.jpg', 0),
(6, 2, 'images/car3-3.jpg', 0),
(7, 3, 'images/car1.jpg', 1),
(8, 3, 'images/car1-2.jpg', 0),
(9, 3, 'images/car1-3.jpg', 0),
(15, 5, 'uploads/cars/e0dba54d35017321d83db332e14f23a6.jpg', 1),
(16, 5, 'uploads/cars/ff7c70e9877f2ed001bd3fcd932074b4.jpg', 0),
(17, 5, 'uploads/cars/a7a167834e5390484bcc5b94e3440012.jpg', 0),
(18, 5, 'uploads/cars/163c64b1140de4e8fd8ff174d404e7af.jpg', 0),
(19, 5, 'uploads/cars/28a945126ab14960fbdc107a1d5027e8.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `car_listings`
--

CREATE TABLE `car_listings` (
  `listing_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` smallint(6) NOT NULL,
  `mileage` int(11) NOT NULL DEFAULT 0,
  `price` decimal(12,2) NOT NULL,
  `fuel_type` enum('Petrol','Diesel','Hybrid','Electric') NOT NULL,
  `transmission` enum('Manual','Automatic','CVT') NOT NULL,
  `condition_status` enum('New','Used') NOT NULL DEFAULT 'Used',
  `description` text DEFAULT NULL,
  `status` enum('Pending','Active','Sold','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_listings`
--

INSERT INTO `car_listings` (`listing_id`, `seller_id`, `make`, `model`, `year`, `mileage`, `price`, `fuel_type`, `transmission`, `condition_status`, `description`, `status`, `created_at`) VALUES
(1, 2, 'BMW', 'M4 CSL', 2023, 5000, 8800000.00, 'Petrol', 'Automatic', 'Used', 'Limited 50 Years of M edition. 543hp twin-turbo straight-six.', 'Active', '2026-05-01 05:58:59'),
(2, 3, 'Porsche', '911 Turbo S', 2020, 35000, 27850000.00, 'Petrol', 'Automatic', 'Used', '650hp flat-six, AWD, PDK 8-speed.', 'Active', '2026-05-01 05:58:59'),
(3, 4, 'Toyota', 'GR Yaris GRMN', 2022, 5000, 4650000.00, 'Petrol', 'Manual', 'Used', 'Track-focused homologation special.', 'Active', '2026-05-01 05:58:59'),
(5, 2, 'BMW', 'M550D', 2019, 1000, 4000000.00, 'Diesel', 'Automatic', 'New', 'Diesel M5', 'Active', '2026-05-01 08:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `message_body` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('Buyer','Seller','Admin') NOT NULL DEFAULT 'Buyer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `role`, `is_active`, `last_login`, `created_at`) VALUES
(1, 'Site Admin', 'admin@carhub.local', '$2y$10$RMpUOf/k6dIZGXq1kHdy4u/ExZ6Lbhkej54AhzxZRa9pnIYvjhrf2', '57000000', 'Admin', 1, '2026-05-01 12:31:48', '2026-05-01 05:58:59'),
(2, 'Aditya Ramdeworsing', 'aditya@carhub.local', '$2y$10$j35T3tsCn9HzxYLD38o/runPfF2zcYMG1VwMrMT.qnax8Bst2qwtW', '57111111', 'Seller', 1, '2026-05-01 12:28:45', '2026-05-01 05:58:59'),
(3, 'Priyanka Teeluckdharee', 'priyanka@carhub.local', '$2y$10$j35T3tsCn9HzxYLD38o/runPfF2zcYMG1VwMrMT.qnax8Bst2qwtW', '57222222', 'Seller', 1, NULL, '2026-05-01 05:58:59'),
(4, 'Yash Shewdeen', 'yash@carhub.local', '$2y$10$j35T3tsCn9HzxYLD38o/runPfF2zcYMG1VwMrMT.qnax8Bst2qwtW', '57333333', 'Seller', 1, NULL, '2026-05-01 05:58:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car_images`
--
ALTER TABLE `car_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `car_listings`
--
ALTER TABLE `car_listings`
  ADD PRIMARY KEY (`listing_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_make_model` (`make`,`model`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car_images`
--
ALTER TABLE `car_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `car_listings`
--
ALTER TABLE `car_listings`
  MODIFY `listing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car_images`
--
ALTER TABLE `car_images`
  ADD CONSTRAINT `car_images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `car_listings` (`listing_id`) ON DELETE CASCADE;

--
-- Constraints for table `car_listings`
--
ALTER TABLE `car_listings`
  ADD CONSTRAINT `car_listings_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`listing_id`) REFERENCES `car_listings` (`listing_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
