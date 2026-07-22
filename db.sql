-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2026 at 07:23 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ta`
--

-- --------------------------------------------------------

--
-- Table structure for table `board`
--

CREATE TABLE `board` (
  `id_board` int(11) NOT NULL,
  `board_uid` varchar(50) NOT NULL,
  `board_name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `board`
--

INSERT INTO `board` (`id_board`, `board_uid`, `board_name`, `location`, `status`, `created_at`) VALUES
(1, 'ESP32_GATEWAY_1', 'Gateway Pusat', 'Ruang Server', '1', '2026-07-05 19:48:06');

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `id_device` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `id_type` int(11) NOT NULL,
  `id_board` int(11) DEFAULT 1,
  `location` varchar(100) NOT NULL,
  `pin_gpio` int(11) DEFAULT 0,
  `active_state` int(11) DEFAULT 0,
  `last_state` int(11) DEFAULT 0,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`id_device`, `device_name`, `id_type`, `id_board`, `location`, `pin_gpio`, `active_state`, `last_state`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Pintu B', 1, 1, 'B', 14, 0, 0, 1, '2026-03-03 12:54:27', '2026-07-05 20:08:28', 0, 1),
(2, 'Pintu A', 1, 1, 'A', 0, 0, 0, 0, '2026-03-18 14:39:36', '2026-07-05 03:02:41', 0, 1),
(3, 'Lampu A', 2, 1, 'A', 0, 0, 0, 0, '2026-03-18 14:43:32', '2026-07-05 03:02:56', 1, 1),
(4, 'Lampu B', 2, 1, 'B', 27, 1, 0, 1, '2026-07-05 03:04:08', '2026-07-05 20:08:40', 1, 1),
(5, 'Lampu C', 2, 1, 'C', 9, 0, 0, 1, '2026-07-05 20:09:04', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `device_logs`
--

CREATE TABLE `device_logs` (
  `id_device_logs` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_device` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `device_logs`
--

INSERT INTO `device_logs` (`id_device_logs`, `id_user`, `id_device`, `action`, `created_at`) VALUES
(1, 1, 1, '0', '2026-04-24 14:42:54'),
(2, 1, 1, '0', '2026-04-24 14:57:23'),
(3, 1, 1, '0', '2026-04-24 14:58:03'),
(4, 1, 1, '0', '2026-04-24 14:59:13'),
(5, 1, 1, '0', '2026-04-24 14:59:22'),
(6, 1, 1, '0', '2026-04-24 14:59:37'),
(7, 1, 1, '0', '2026-04-24 15:02:39'),
(8, 1, 1, '0', '2026-04-24 15:02:55'),
(9, 1, 1, '0', '2026-04-24 15:03:29'),
(10, 1, 1, '0', '2026-04-24 15:03:33'),
(11, 1, 1, '0', '2026-04-24 15:09:59'),
(12, 1, 1, '0', '2026-04-24 15:10:04'),
(13, 1, 1, '0', '2026-04-24 15:14:21'),
(14, 1, 1, '0', '2026-04-24 15:21:16'),
(15, 1, 1, '0', '2026-04-24 15:21:23'),
(16, 1, 1, '0', '2026-04-24 15:22:54'),
(17, 1, 1, '0', '2026-04-24 15:22:58'),
(18, 1, 1, '0', '2026-04-24 15:24:03'),
(19, 1, 1, '0', '2026-04-24 15:31:17'),
(20, 1, 1, '0', '2026-04-24 15:32:16'),
(21, 1, 1, '0', '2026-04-24 15:32:21'),
(22, 1, 1, '0', '2026-04-24 15:37:37'),
(23, 1, 1, '0', '2026-04-24 15:37:42'),
(24, 1, 1, '0', '2026-04-24 15:40:36'),
(25, 1, 1, '0', '2026-04-24 15:59:02'),
(26, 1, 1, '0', '2026-04-24 15:59:28'),
(27, 1, 1, '0', '2026-04-24 15:59:54'),
(28, 1, 1, '0', '2026-04-24 16:00:02'),
(29, 1, 1, '0', '2026-04-24 16:03:31'),
(30, 1, 1, '0', '2026-04-24 16:03:36'),
(31, 1, 1, '0', '2026-04-24 16:19:42'),
(32, 1, 1, '0', '2026-04-24 16:19:46'),
(33, 1, 1, '0', '2026-04-24 16:19:52'),
(34, 1, 1, '0', '2026-04-24 16:20:44'),
(35, 1, 1, '0', '2026-04-24 16:36:50'),
(36, 1, 1, '0', '2026-04-24 16:36:55'),
(37, 1, 1, '0', '2026-04-24 16:37:00'),
(38, 1, 1, '0', '2026-04-24 16:37:05'),
(39, 1, 1, '0', '2026-04-24 16:37:18'),
(40, 1, 1, '0', '2026-04-24 16:37:26'),
(41, 1, 1, '0', '2026-04-24 16:37:35'),
(42, 1, 1, '0', '2026-04-24 16:37:42'),
(43, 1, 1, '0', '2026-04-24 16:39:30'),
(44, 1, 1, '0', '2026-04-24 16:39:35'),
(45, 1, 1, '0', '2026-04-24 16:40:01'),
(46, 1, 1, '0', '2026-04-24 16:40:06'),
(47, 1, 1, '0', '2026-04-24 16:40:17'),
(48, 1, 1, '0', '2026-04-24 16:40:23'),
(49, 1, 1, '0', '2026-04-24 16:40:30'),
(50, 1, 1, '0', '2026-04-24 16:40:40'),
(51, 1, 1, '0', '2026-04-24 16:40:47'),
(52, 1, 1, '0', '2026-04-24 16:40:57'),
(53, 1, 1, '0', '2026-04-24 16:41:18'),
(54, 1, 1, '0', '2026-04-24 16:41:24'),
(55, 1, 1, '0', '2026-04-24 16:43:50'),
(56, 1, 1, '0', '2026-04-24 16:43:54'),
(57, 1, 1, '0', '2026-04-24 16:44:06'),
(58, 1, 1, '0', '2026-04-24 16:44:24'),
(59, 1, 1, '0', '2026-04-24 16:44:29'),
(60, 1, 1, '0', '2026-04-24 16:51:00'),
(61, 1, 1, '0', '2026-04-24 16:51:05'),
(62, 1, 1, '0', '2026-04-24 17:34:02'),
(63, 1, 1, '0', '2026-04-24 17:34:07'),
(64, 1, 1, '0', '2026-04-24 17:34:22'),
(65, 1, 1, '0', '2026-04-24 17:35:42'),
(66, 1, 1, '0', '2026-04-24 17:35:51'),
(67, 1, 1, '0', '2026-04-24 17:36:03'),
(68, 1, 1, '0', '2026-04-24 17:36:07'),
(69, 1, 1, '0', '2026-04-24 17:36:23'),
(70, 1, 1, '0', '2026-04-24 17:52:21'),
(71, 1, 1, '0', '2026-04-24 17:52:26'),
(72, 1, 1, '0', '2026-04-24 17:52:33'),
(73, 1, 1, '0', '2026-05-20 16:00:08'),
(74, 1, 1, '0', '2026-05-20 16:00:15'),
(75, 1, 1, '0', '2026-05-20 16:02:55'),
(76, 1, 1, '0', '2026-05-20 16:02:59'),
(77, 1, 1, '0', '2026-05-20 16:03:55'),
(78, 1, 1, '0', '2026-05-20 16:03:59'),
(79, 1, 1, '0', '2026-05-20 16:04:11'),
(80, 1, 1, '0', '2026-05-20 16:04:36'),
(81, 1, 1, '0', '2026-05-20 16:05:25'),
(82, 1, 1, '0', '2026-05-20 16:11:25'),
(83, 1, 1, '0', '2026-05-20 16:11:30'),
(84, 1, 1, '0', '2026-06-04 07:59:24'),
(85, 1, 1, '0', '2026-06-04 10:20:39'),
(86, 1, 1, '0', '2026-06-04 10:21:50'),
(87, 1, 1, '0', '2026-06-04 10:22:49'),
(88, 1, 1, '0', '2026-06-04 10:22:53'),
(89, 1, 1, '0', '2026-06-04 10:24:23'),
(90, 1, 1, '0', '2026-06-04 10:24:42'),
(91, 1, 1, '0', '2026-06-04 10:25:01'),
(92, 1, 1, '0', '2026-06-04 10:25:23'),
(93, 1, 1, '0', '2026-06-04 10:25:28'),
(94, 1, 1, '0', '2026-06-04 10:27:46'),
(95, 1, 1, '0', '2026-06-04 10:28:29'),
(96, 1, 1, '0', '2026-06-04 10:28:33'),
(97, 1, 1, '0', '2026-06-04 10:28:45'),
(98, 1, 1, '0', '2026-06-04 10:29:05'),
(99, 1, 1, '0', '2026-06-04 10:29:27'),
(100, 1, 1, '0', '2026-06-04 10:29:33'),
(101, 1, 1, '0', '2026-06-04 10:31:41'),
(102, 1, 1, '0', '2026-06-04 10:31:45'),
(103, 1, 1, '0', '2026-06-04 11:19:09'),
(104, 1, 1, '0', '2026-06-04 11:19:48'),
(105, 1, 1, '0', '2026-06-04 11:21:55'),
(106, 1, 1, '0', '2026-06-04 11:22:20'),
(107, 1, 1, '0', '2026-06-04 11:22:41'),
(108, 1, 1, '0', '2026-06-04 11:24:06'),
(109, 1, 1, '0', '2026-06-04 11:24:37'),
(110, 1, 1, '0', '2026-06-04 11:25:34'),
(111, 1, 1, '0', '2026-06-04 14:48:22'),
(112, 1, 1, '0', '2026-06-04 15:37:58'),
(113, 1, 1, '0', '2026-06-04 15:38:17'),
(114, 1, 1, '0', '2026-06-04 15:38:24'),
(115, 1, 1, '0', '2026-06-04 15:38:43'),
(116, 1, 1, '0', '2026-06-04 15:38:47'),
(117, 1, 1, '0', '2026-06-04 15:38:51'),
(118, 1, 1, '0', '2026-06-04 15:39:23'),
(119, 1, 1, '0', '2026-06-04 15:39:37'),
(120, 1, 1, '0', '2026-06-04 15:39:58'),
(121, 1, 1, '0', '2026-06-04 15:40:02'),
(122, 1, 1, '0', '2026-06-04 15:40:09'),
(123, 1, 1, '0', '2026-06-10 20:41:29'),
(124, 1, 1, '0', '2026-06-10 20:41:37'),
(125, 1, 1, '0', '2026-06-10 20:41:42'),
(126, 1, 1, '0', '2026-06-11 13:39:14'),
(127, 1, 1, '0', '2026-06-11 13:39:22'),
(128, 1, 1, '0', '2026-06-11 13:43:37'),
(129, 1, 1, '0', '2026-06-11 13:45:36'),
(130, 1, 1, '0', '2026-06-11 13:46:04'),
(131, 1, 1, '0', '2026-06-11 13:48:31'),
(132, 1, 1, '0', '2026-06-11 13:49:49'),
(133, 1, 1, '0', '2026-06-11 13:51:54'),
(134, 1, 1, '0', '2026-06-11 13:54:17'),
(135, 1, 1, '0', '2026-06-11 13:55:18'),
(136, 1, 1, '0', '2026-06-11 13:55:25'),
(137, 1, 1, '0', '2026-06-11 14:16:44'),
(138, 1, 1, '0', '2026-06-11 14:16:48'),
(139, 1, 1, '0', '2026-06-11 14:17:19'),
(140, 1, 1, '0', '2026-06-11 14:18:15'),
(141, 1, 1, '0', '2026-06-11 14:18:19'),
(142, 1, 1, '0', '2026-06-11 14:18:23'),
(143, 1, 1, '0', '2026-06-11 14:18:51'),
(144, 1, 1, '0', '2026-06-11 14:20:36'),
(145, 1, 1, '0', '2026-06-11 14:20:52'),
(146, 1, 1, '0', '2026-06-11 14:22:25'),
(147, 1, 1, '0', '2026-06-11 14:22:40'),
(148, 1, 1, '0', '2026-06-11 14:22:53'),
(149, 1, 1, '0', '2026-06-11 14:23:25'),
(150, 1, 1, '0', '2026-06-11 14:24:19'),
(151, 1, 1, '0', '2026-06-11 14:24:45'),
(152, 1, 1, '0', '2026-06-11 14:26:28'),
(153, 1, 1, '0', '2026-06-17 11:45:11'),
(154, 1, 1, '0', '2026-06-17 11:45:15'),
(155, 1, 1, '0', '2026-06-17 11:45:22'),
(156, 1, 1, '0', '2026-06-18 11:21:03'),
(157, 1, 1, '0', '2026-06-18 11:21:09'),
(158, 1, 1, '0', '2026-07-06 04:49:00'),
(159, 1, 4, 'NYALAKAN', '2026-07-06 07:04:53'),
(160, 1, 4, 'NYALAKAN', '2026-07-06 07:08:12'),
(161, 1, 4, 'NYALAKAN', '2026-07-06 07:12:41'),
(162, 1, 4, 'NYALAKAN', '2026-07-06 07:15:13'),
(163, 1, 4, 'NYALAKAN', '2026-07-06 07:23:17'),
(164, 1, 1, 'BUKA', '2026-07-09 06:59:47'),
(165, 1, 1, 'BUKA', '2026-07-09 07:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `device_type`
--

CREATE TABLE `device_type` (
  `id_type` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `label_on` varchar(50) NOT NULL,
  `label_off` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `device_type`
--

INSERT INTO `device_type` (`id_type`, `type_name`, `label_on`, `label_off`) VALUES
(1, 'Pintu Pintar', 'BUKA', 'KUNCI'),
(2, 'Lampu Pintar', 'NYALAKAN', 'MATIKAN');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `email`, `name`, `password`, `is_admin`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'admin@example.com', 'Admin', '$2y$10$b5SII1eQO5e5Efa5ACCV1ebOeAzPPUrWla/khNWmKoz0AOoCjiNbS', 1, 1, '2024-05-01 21:37:05', '2024-05-01 21:37:05', 0, NULL),
(4, 'coba@coba.asd', 'test', '$2y$10$x0C0jBjA.R.LKb.p7aI.CeO21hCfDe9AUq9bTbxlD6zBhq4Ow6bdm', 0, 1, '0000-00-00 00:00:00', '2026-07-05 20:04:09', 0, 1),
(5, 'test1@gmail.com', 'coba lagi', '$2y$10$cL8R0RFsVUqkoHc78kKRLux3I/QRqF8pPiMOc/sIuFKCfbN5UcAxO', 0, 1, '0000-00-00 00:00:00', '2026-07-05 03:04:35', 0, 1),
(6, 'coba1@gmail.com', 'coba 1', '$2y$10$b5SII1eQO5e5Efa5ACCV1ebOeAzPPUrWla/khNWmKoz0AOoCjiNbS', 0, 1, '2026-03-18 14:52:03', '2026-07-05 20:03:49', 1, 1),
(7, 'user_a@gmail.com', 'user A', '$2y$10$ASTkLko0TOSfQzvvVyOi5Oft8slq1D9sWct1FKGc9NlCpRYoTJMJy', 0, 1, '2026-07-05 20:04:35', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_access`
--

CREATE TABLE `user_access` (
  `id_user_access` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_device` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_access`
--

INSERT INTO `user_access` (`id_user_access`, `id_user`, `id_device`) VALUES
(6, 5, 1),
(7, 4, 4),
(8, 6, 1),
(9, 6, 4),
(10, 7, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `board`
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`id_board`),
  ADD UNIQUE KEY `board_uid` (`board_uid`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id_device`);

--
-- Indexes for table `device_logs`
--
ALTER TABLE `device_logs`
  ADD PRIMARY KEY (`id_device_logs`);

--
-- Indexes for table `device_type`
--
ALTER TABLE `device_type`
  ADD PRIMARY KEY (`id_type`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `user_access`
--
ALTER TABLE `user_access`
  ADD PRIMARY KEY (`id_user_access`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `board`
--
ALTER TABLE `board`
  MODIFY `id_board` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `id_device` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `device_logs`
--
ALTER TABLE `device_logs`
  MODIFY `id_device_logs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `device_type`
--
ALTER TABLE `device_type`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_access`
--
ALTER TABLE `user_access`
  MODIFY `id_user_access` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
