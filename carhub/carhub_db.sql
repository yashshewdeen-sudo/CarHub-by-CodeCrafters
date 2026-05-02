-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 02, 2026 at 05:29 PM
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
(19, 5, 'uploads/cars/28a945126ab14960fbdc107a1d5027e8.jpg', 0),
(20, 6, 'uploads/cars/2306d1a33e6793f506d365d2a6e6881d.webp', 1),
(21, 6, 'uploads/cars/a5e17f638a9c2a52c9c9d1935ebd340c.jpg', 0),
(22, 7, 'uploads/cars/5d2ea82d1c1a3c833471cc4135ed54a0.jpeg', 1),
(23, 7, 'uploads/cars/d3549daf0baecd6bcb9bd78819a24bce.jpeg', 0),
(24, 7, 'uploads/cars/49f4818552035831622630b45a63f1a9.jpeg', 0),
(25, 7, 'uploads/cars/3b7237d5c5b70b93be8ffbab760eae0a.jpeg', 0),
(26, 7, 'uploads/cars/7cd91d9d4d8b6c44f5a79e169e97dcbf.jpeg', 0),
(27, 8, 'uploads/cars/c01a6fb3ffd2fdf026dda42e06987a2c.jpeg', 1),
(28, 8, 'uploads/cars/782fe144569934722e075428787de1ec.jpeg', 0),
(29, 8, 'uploads/cars/ebe9f773d35f40c9c7078f46623c152e.jpeg', 0),
(30, 8, 'uploads/cars/76ef45a98273d874cd979f030b982e88.jpeg', 0),
(31, 9, 'uploads/cars/38001671afad968acddcdf902aff4fd6.jpeg', 1),
(32, 9, 'uploads/cars/ed7272a56c3ef8d3184bb18fe04ce9d5.jpeg', 0),
(33, 9, 'uploads/cars/bfd0deb07ef0baa21d9b0390ae10b8c5.jpeg', 0),
(34, 9, 'uploads/cars/364ef16a8e0527422cfb52bd4c05eb04.jpeg', 0),
(35, 9, 'uploads/cars/197e5dcfb1b314171ce844f802db2ba1.jpeg', 0),
(36, 10, 'uploads/cars/7ccd9a350e43d26d10fc6363f89ab72f.jpeg', 1),
(37, 10, 'uploads/cars/46875d9a7a19a5cc3f6d03a75ed15816.jpeg', 0),
(38, 10, 'uploads/cars/c93df28539e6578ac8d4a6c4fe328cdc.jpeg', 0),
(39, 10, 'uploads/cars/500dbf9a63e38913af50dd1c16ba6b49.jpeg', 0),
(40, 10, 'uploads/cars/99906a0fd4d959c10c50f134336a7431.jpeg', 0);

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
(5, 2, 'BMW', 'M550D', 2019, 1000, 4000000.00, 'Diesel', 'Automatic', 'New', 'Diesel M5', 'Active', '2026-05-01 08:31:22'),
(6, 7, 'BMW', 'BMW M3 GTR', 2001, 5000, 8500000.00, 'Diesel', 'Manual', 'Used', 'This is a rare and iconic BMW M3 GTR (E46), inspired by the legendary race car. Finished in a striking blue and silver livery, this vehicle features a widebody kit, aerodynamic enhancements, and a race-prepped interior with roll cage.\r\n\r\nPowered by a high-performance V8 engine, the car delivers exceptional speed and handling. Maintained in excellent condition with low mileage, it is ideal for collectors, enthusiasts, or track use.', 'Active', '2026-05-01 11:17:28'),
(7, 7, 'Honda', 'Civic Type R', 2023, 1000, 7000000.00, 'Petrol', 'Manual', 'New', 'The 2023 Honda Civic Type R (FL5) is a high-performance, front-wheel-drive hot hatch based on the 11th-generation Civic. It features a 2.0-liter turbocharged VTEC engine producing 315 horsepower and 310 lb-ft of torque, paired with a 6-speed manual transmission. Known for track-ready performance, refined styling, and daily usability, it features a 4-mode drive system (+R, Individual, Sport, Comfort).', 'Active', '2026-05-02 15:08:31'),
(8, 7, 'Suzuki', 'Jimny', 2026, 1000, 1500000.00, 'Petrol', 'Manual', 'New', 'The Suzuki Jimny is a compact, ladder-frame 4x4, renowned for its iconic boxy styling and serious off-road capabilities. Available in 3-door and 5-door (GLX) variants in Mauritius, it features a 1.5L petrol engine, Part-time 4WD (ALLGRIP PRO), and high ground clearance', 'Sold', '2026-05-02 15:14:02'),
(9, 7, 'Porsche', 'Taycan Black Edition', 2019, 1000, 15000000.00, 'Electric', 'Automatic', 'New', 'The Porsche Taycan is a high-performance battery-electric luxury sedan and shooting brake (estate) produced since 2019, serving as Porsche\'s first series-production EV. It is renowned for its 800V architecture enabling fast charging (10–80% in under 18 minutes), exceptional handling, and rapid acceleration (0-100 km/h in 2.7s for the Turbo S), offering 408 hp to over 1,000 hp.', 'Sold', '2026-05-02 15:17:21'),
(10, 4, 'Lamborghini', 'Revuelto', 2026, 1000, 30000000.00, 'Hybrid', 'Automatic', 'New', 'The Lamborghini Revuelto is a high-performance electrified vehicle (HPEV) hybrid super sports car, serving as the V12 flagship successor to the Aventador. It features a 6.5-liter V12 engine paired with three electric motors, delivering a combined 1,015 CV (1,001 hp) and a 0–100 km/h time of 2.5 seconds, with a top speed exceeding 350 km/h.', 'Sold', '2026-05-02 15:27:23');

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
(1, 'Site Admin', 'admin@carhub.local', '$2y$10$RMpUOf/k6dIZGXq1kHdy4u/ExZ6Lbhkej54AhzxZRa9pnIYvjhrf2', '57000000', 'Admin', 1, '2026-05-02 16:02:20', '2026-05-01 05:58:59'),
(2, 'Aditya Ramdeworsing', 'aditya@carhub.local', '$2y$10$j35T3tsCn9HzxYLD38o/runPfF2zcYMG1VwMrMT.qnax8Bst2qwtW', '57111111', 'Seller', 1, '2026-05-01 12:28:45', '2026-05-01 05:58:59'),
(3, 'Priyanka Teeluckdharee', 'priyanka@carhub.local', '$2y$10$j35T3tsCn9HzxYLD38o/runPfF2zcYMG1VwMrMT.qnax8Bst2qwtW', '57222222', 'Seller', 1, NULL, '2026-05-01 05:58:59'),
(4, 'Yash Shewdeen', 'yash@carhub.local', '$2y$10$j35T3tsCn9HzxYLD38o/runPfF2zcYMG1VwMrMT.qnax8Bst2qwtW', '57333333', 'Seller', 1, '2026-05-02 19:25:48', '2026-05-01 05:58:59'),
(7, 'Yassoy', 'yassoy13@gmail.com', '$2y$10$c6zNHeEeSTt3bceD90BE6uaPxTyoyCNmb52L23u3Z2pRa4PPrtZB.', '54893205', 'Seller', 1, '2026-05-02 18:45:33', '2026-05-01 11:09:10');

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
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `car_listings`
--
ALTER TABLE `car_listings`
  MODIFY `listing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
