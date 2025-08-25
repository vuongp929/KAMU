-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 21, 2025 at 03:08 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kamu-datn`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled','shipping','delivered') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','failed','cod') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `total_price` decimal(10,2) NOT NULL,
  `final_total` decimal(10,2) DEFAULT NULL,
  `discount_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart` text COLLATE utf8mb4_unicode_ci,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `payment_status`, `total_price`, `final_total`, `discount_code`, `discount_amount`, `shipping_fee`, `shipping_address`, `cart`, `payment_method`, `created_at`, `updated_at`) VALUES
(1, 1, 'delivered', 'unpaid', '0.00', NULL, NULL, NULL, '0.00', 'Address for user Admin', NULL, NULL, '2025-07-01 01:32:59', '2025-07-31 14:12:47'),
(2, NULL, 'completed', 'unpaid', '0.00', NULL, NULL, NULL, '0.00', 'Address for user Oscar Kreiger', NULL, NULL, '2025-07-01 01:32:59', '2025-07-31 14:14:56'),
(3, NULL, 'pending', 'unpaid', '1788320.00', NULL, NULL, NULL, '0.00', 'Address for user Miss Renee Stokes', NULL, NULL, '2025-07-01 01:32:59', '2025-07-01 01:32:59'),
(4, NULL, 'pending', 'unpaid', '659190.00', NULL, NULL, NULL, '0.00', 'Address for user Hattie Fritsch', NULL, NULL, '2025-07-01 01:32:59', '2025-07-01 01:32:59'),
(5, NULL, 'pending', 'unpaid', '2544975.00', NULL, NULL, NULL, '0.00', 'Address for user Miss Veda O\'Keefe', NULL, NULL, '2025-07-01 01:32:59', '2025-07-01 01:32:59'),
(6, NULL, 'pending', 'unpaid', '936041.00', NULL, NULL, NULL, '0.00', 'Address for user Valentina Rodriguez', NULL, NULL, '2025-07-01 01:32:59', '2025-07-01 01:32:59'),
(7, 8, 'cancelled', 'unpaid', '414542.00', NULL, NULL, NULL, '0.00', 'Thái Bình', NULL, 'cod', '2025-07-31 12:53:43', '2025-07-31 13:59:50'),
(8, 8, 'completed', 'unpaid', '414542.00', NULL, NULL, NULL, '0.00', 'Hà Đông', NULL, 'cod', '2025-07-31 13:20:58', '2025-07-31 14:12:34'),
(9, 8, 'pending', 'unpaid', '271121.60', NULL, NULL, NULL, '0.00', 'Thái Bình', NULL, 'cod', '2025-07-31 14:35:40', '2025-07-31 14:35:40'),
(10, 8, 'shipping', 'unpaid', '414542.00', NULL, NULL, NULL, '0.00', 'Thái Bình', NULL, 'cod', '2025-07-31 22:10:44', '2025-07-31 22:14:48'),
(11, 8, 'delivered', 'unpaid', '338902.00', NULL, NULL, NULL, '0.00', 'Long Biên', NULL, 'cod', '2025-07-31 22:13:35', '2025-07-31 22:15:05'),
(12, 9, 'pending', 'cod', '414542.00', NULL, NULL, NULL, '0.00', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"email\":\"kiennguyentrung07092005@gmail.com\",\"phone\":\"0828893282\",\"address\":\"Th\\u00e1i B\\u00ecnh\"}', NULL, 'cod', '2025-08-01 17:35:52', '2025-08-01 17:35:52'),
(13, 9, 'pending', 'cod', '338902.00', NULL, NULL, NULL, '0.00', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"email\":\"kiennguyentrung07092005@gmail.com\",\"phone\":\"03673241060\",\"address\":\"Long Bi\\u00ean\"}', NULL, 'cod', '2025-08-01 17:41:56', '2025-08-01 17:41:56'),
(14, 9, 'pending', 'cod', '414542.00', NULL, NULL, NULL, '0.00', '{\"name\":\"Nguy\\u1ec5n V\\u0103n A\",\"email\":\"kiennguyentrung07092005@gmail.com\",\"phone\":\"0828893282\",\"address\":\"H\\u00e0 \\u0110\\u00f4ng\"}', NULL, 'cod', '2025-08-01 17:43:10', '2025-08-01 17:43:10'),
(15, 8, 'pending', 'unpaid', '314542.00', NULL, NULL, NULL, '0.00', 'Tây Mỗ', NULL, 'cod', '2025-08-07 14:55:47', '2025-08-07 14:55:47'),
(16, 8, 'cancelled', 'unpaid', '829084.00', NULL, NULL, NULL, '0.00', 'Thái Bình', NULL, 'cod', '2025-08-18 04:50:04', '2025-08-18 05:24:48'),
(17, 8, 'pending', 'unpaid', '414542.00', '314542.00', 'MN2', '100000.00', '0.00', 'Hà Đông', NULL, 'cod', '2025-08-18 05:26:21', '2025-08-18 05:26:21'),
(18, 8, 'pending', 'unpaid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', 'Phú Thọ', NULL, 'cod', '2025-08-18 07:17:43', '2025-08-18 07:17:43'),
(19, 8, 'pending', 'unpaid', '414542.00', '290179.40', 'JKL25', '124362.60', '0.00', 'Long Biên', NULL, 'cod', '2025-08-18 07:28:44', '2025-08-18 07:28:44'),
(20, 8, 'pending', 'unpaid', '414542.00', '290179.40', 'JKL25', '124362.60', '0.00', 'Thái Bình', NULL, 'cod', '2025-08-18 08:13:42', '2025-08-18 08:13:42'),
(21, 8, 'processing', 'paid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', '\"khu 6\"', NULL, 'vnpay', '2025-08-20 13:42:27', '2025-08-20 13:44:32'),
(22, 8, 'pending', 'unpaid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', '\"khu 6\"', NULL, 'vnpay', '2025-08-20 14:17:48', '2025-08-20 14:17:48'),
(23, 8, 'pending', 'unpaid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', '\"khu 6\"', NULL, 'vnpay', '2025-08-20 14:21:41', '2025-08-20 14:21:41'),
(24, 8, 'pending', 'unpaid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', '\"khu 6\"', NULL, 'momo', '2025-08-20 14:44:39', '2025-08-20 14:44:39'),
(25, 8, 'pending', 'unpaid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'vnpay', '2025-08-20 15:04:25', '2025-08-20 15:04:25'),
(26, 8, 'pending', 'unpaid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'vnpay', '2025-08-20 15:23:52', '2025-08-20 15:23:52'),
(27, 8, 'cancelled', 'paid', '414542.00', '264542.00', 'KL', '150000.00', '0.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'vnpay', '2025-08-20 15:24:29', '2025-08-20 16:20:01'),
(28, 8, 'pending', 'unpaid', '338902.00', '188902.00', 'KL', '150000.00', '36501.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'momo', '2025-08-20 16:21:42', '2025-08-20 16:21:42'),
(29, 8, 'pending', 'unpaid', '338902.00', '188902.00', 'KL', '150000.00', '44000.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'momo', '2025-08-20 16:31:43', '2025-08-20 16:31:43'),
(30, 8, 'pending', 'unpaid', '338902.00', '188902.00', 'KL', '150000.00', '36501.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'momo', '2025-08-20 16:37:28', '2025-08-20 16:37:28'),
(31, 8, 'pending', 'unpaid', '338902.00', '188902.00', 'KL', '150000.00', '36501.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'momo', '2025-08-20 16:43:46', '2025-08-20 16:43:46'),
(32, 8, 'pending', 'unpaid', '338902.00', '188902.00', 'KL', '150000.00', '31501.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'momo', '2025-08-20 16:47:13', '2025-08-20 16:47:13'),
(33, 8, 'processing', 'paid', '338902.00', '188902.00', 'KL', '150000.00', '34000.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'vnpay', '2025-08-20 16:54:47', '2025-08-20 16:55:30'),
(34, 8, 'cancelled', 'paid', '414542.00', '264542.00', 'KL', '150000.00', '36501.00', '\"sn 12 ng\\u00f5 59\"', NULL, 'vnpay', '2025-08-21 14:53:24', '2025-08-21 15:05:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
