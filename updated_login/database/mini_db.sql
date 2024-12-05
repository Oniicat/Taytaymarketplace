-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 04:05 AM
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
-- Database: `mini_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_name`, `activity_type`, `date_time`) VALUES
(1, 'ibanezned13@gmail.com', 'Logged In', '2024-11-30 10:46:57'),
(2, 'rhandy@gmail.com', 'Logged In', '2024-11-30 10:47:27'),
(3, 'ibanezned13@gmail.com', 'Logged In', '2024-11-30 10:53:41'),
(4, 'irwin@gmail.com', 'Logged In', '2024-11-30 11:05:29'),
(5, 'commit.suicide26@gmail.com', 'Logged In', '2024-11-30 12:04:19'),
(6, 'commit.suicide26@gmail.com', 'Logged In', '2024-11-30 12:05:22'),
(7, 'commit.suicide26@gmail.com', 'Logged In', '2024-11-30 23:18:45'),
(8, 'commit.suicide26@gmail.com', 'Logged In', '2024-11-30 23:23:54'),
(9, 'commit.suicide26@gmail.com', 'Logged In', '2024-11-30 23:25:46'),
(10, 'commit.suicide26@gmail.com', 'Logged In', '2024-11-30 23:33:25'),
(11, 'commit.suicide26@gmail.com', 'Logged In', '2024-11-30 23:34:21'),
(12, 'ibanezned13@gmail.com', 'Logged In', '2024-12-01 00:02:47'),
(13, 'ibanezned13@gmail.com', 'Logged In', '2024-12-01 00:03:19'),
(14, 'ibanezned13@gmail.com', 'Logged In', '2024-12-01 00:08:52'),
(15, 'ibanezned13@gmail.com', 'Logged In', '2024-12-01 00:09:28'),
(16, 'rhandy@gmail.com', 'Logged In', '2024-12-01 00:13:55'),
(17, 'rhandy@gmail.com', 'Logged In', '2024-12-01 00:18:02'),
(18, 'rhandy@gmail.com', 'Logged In', '2024-12-01 00:28:11'),
(19, 'ibanezned13@gmail.com', 'Logged In', '2024-12-01 00:28:42'),
(20, 'irwin@gmail.com', 'Logged In', '2024-12-01 02:14:17'),
(21, 'adrian@gmail.com', 'Logged In', '2024-12-01 02:23:08'),
(22, 'adrian@gmail.com', 'Logged In', '2024-12-01 02:24:21'),
(23, 'ibanezned13@gmail.com', 'Logged In', '2024-12-01 02:28:32'),
(24, 'ibanezned13@gmail.com', 'Logged In', '2024-12-01 02:28:56'),
(25, 'rhandy@gmail.com', 'Logged In', '2024-12-01 02:34:44');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `email` varchar(255) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_failed_attempt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`email`, `attempts`, `last_failed_attempt`) VALUES
('adrian@gmail.com', 0, '2024-12-01 02:24:26'),
('commit.suicide26@gmail.com', 0, '2024-12-01 02:27:39'),
('ibanezned13@gmail.com', 0, '2024-12-01 02:32:04'),
('irwin@gmail.com', 0, '2024-12-01 02:14:47'),
('rhandy@gmail.com', 0, '2024-12-01 02:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `shop_description` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `shopee_link` varchar(255) DEFAULT NULL,
  `lazada_link` varchar(255) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_email`, `shop_description`, `contact_number`, `shopee_link`, `lazada_link`, `municipality`) VALUES
(46, 'rhandy@gmail.com', 'asdasdasd', '639294561234', 'https://www.lazada.com.ph/?spm=a2o4l.searchlist.header.dhome.922b9e2fKlQMUcdsource=sharelaz_share_info=1166853768_1_9300_3093395_1166855768_nulllaz_token=839c01faea4c982e1eba081e258d9e3a#?', 'https://www.lazada.com.ph/?spm=a2o4l.searchlist.header.dhome.922b9e2fKlQMUcdsource=sharelaz_share_info=1166853768_1_9300_3093395_1166855768_nulllaz_token=839c01faea4c982e1eba081e258d9e3a#?', 'tay'),
(47, 'ned@gmail.com', 'dsadsadadadasdas', '639294561234', 'https://www.lazada.com.ph/?spm=a2o4l.searchlist.header.dhome.922b9e2fKlQMUcdsource=sharelaz_share_info=1166853768_1_9300_3093395_1166855768_nulllaz_token=839c01faea4c982e1eba081e258d9e3a#?', 'https://www.lazada.com.ph/?spm=a2o4l.searchlist.header.dhome.922b9e2fKlQMUcdsource=sharelaz_share_info=1166853768_1_9300_3093395_1166855768_nulllaz_token=839c01faea4c982e1eba081e258d9e3a#?', 'tay'),
(48, 'commit.suicide26@gmail.com', '', '0', '', '', 'ang'),
(49, 'commit.suicide26@gmail.com', '', '0', '', '', 'ang'),
(50, 'commit.suicide26@gmail.com', '', '0', '', '', 'ang'),
(51, 'commit.suicide26@gmail.com', '', '0', '', '', 'ang');

-- --------------------------------------------------------

--
-- Table structure for table `shop_information`
--

CREATE TABLE `shop_information` (
  `info_id` int(11) NOT NULL,
  `f_name` varchar(30) NOT NULL,
  `m_name` varchar(30) NOT NULL,
  `l_name` varchar(30) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `shop_name` varchar(50) NOT NULL,
  `business_permit_number` int(50) NOT NULL,
  `stall_number` int(20) NOT NULL,
  `shop_contact_number` varchar(20) NOT NULL,
  `municipality` varchar(50) NOT NULL,
  `barangay` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_information`
--

INSERT INTO `shop_information` (`info_id`, `f_name`, `m_name`, `l_name`, `contact_number`, `shop_name`, `business_permit_number`, `stall_number`, `shop_contact_number`, `municipality`, `barangay`) VALUES
(42, 'Ned Christian', 'Briones', 'Ibañez', '+639294561234', 'Water Station', 123123, 13, '+639294561234', 'tay', 'San Juan'),
(43, 'Rhandy Gian', 'Herrera', 'Baradas', '+639294561222', 'Moto Parts', 321321, 14, '+639294561222', 'tay', 'Conception'),
(44, 'Irwin James', 'Galit', 'Julio', '+639294561222', 'Bigas Station', 321321, 12, '+639294561222', 'tay', 'awdadwdawda'),
(45, 'Adrian', 'Bolante', 'Bernal', '+639294561562', 'Eatery', 456789, 15, '+639294561562', 'bin', 'Tagpos, Binangonan Rizal');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` timestamp NULL DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_token_expiry` timestamp NULL DEFAULT NULL,
  `lastlogin_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `email`, `password`, `created_at`, `otp`, `otp_expiry`, `password_reset_token`, `password_reset_token_expiry`, `lastlogin_time`) VALUES
(1, 'adrian', 'adrian@gmail.com', '$2y$10$l9Snd7Fz6OBqkQNx8FC6H.0LCsdaGzZl5T/JyLfetAEPscNBLWnzC', '2024-11-28 07:04:10', NULL, NULL, NULL, NULL, '2024-12-01 10:24:21'),
(3, 'ibanezned13', 'ibanezned13@gmail.com', '$2y$10$RmXNgBhz5FhCHrLQcDOn3OqnDmMFOaZw2h50VbQkVabShW85BI3S2', '2024-11-30 09:55:12', '949730', '2024-11-30 04:14:06', NULL, NULL, '2024-12-01 10:28:56'),
(4, 'irwin', 'irwin@gmail.com', '$2y$10$vn.54U0ZxXS9VZtmmrzV9eOAtXKmsan1cKXcKELXhVp7SVvbf19Ue', '2024-11-26 14:20:30', NULL, NULL, NULL, NULL, '2024-12-01 10:14:17'),
(5, 'ned', 'ned@gmail.com', '$2y$10$D.elYHIlOZhddJtc7B0kqOrBNsXJwtMbyq8PbqVguz13owJxFQqrO', '2024-11-26 14:16:44', NULL, NULL, NULL, NULL, NULL),
(6, 'nedibanez0', 'nedibanez0@gmail.com', '$2y$10$SK4/h/1JFI511ogoX9T.Ne9aPrFAhJ93nY1zBw.nl7b1eJgi2xqsS', '2024-11-30 08:53:26', NULL, NULL, NULL, NULL, NULL),
(7, 'rhandy', 'rhandy@gmail.com', '$2y$10$xtRcVLM5GJOWLAwtMkwGS.hM0cr1RvUNRqrYPuWQCvRnkS/u.Dyjq', '2024-11-26 14:17:44', NULL, NULL, NULL, NULL, '2024-12-01 10:34:44'),
(8, 'commit.suicide26', 'commit.suicide26@gmail.com', '$2y$10$NrkHG1KyzbSsbAYtCViXcunx.bcG9GvYLzn8F6DmefKyf1GFczXFS', '2024-11-30 12:03:40', '200951', '2024-11-30 16:45:09', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_information`
--
ALTER TABLE `shop_information`
  ADD PRIMARY KEY (`info_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `shop_information`
--
ALTER TABLE `shop_information`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
