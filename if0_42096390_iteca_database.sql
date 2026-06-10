-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql302.infinityfree.com
-- Generation Time: Jun 10, 2026 at 04:51 PM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42096390_iteca_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Clothing'),
(2, 'Electronics'),
(3, 'Furniture'),
(4, 'Books'),
(5, 'Beauty'),
(6, 'Sports'),
(7, 'Homeware');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `order_status`, `created_at`) VALUES
(1, 7, '5000.00', 'Completed', '2026-05-26 18:46:02'),
(2, 7, '5000.00', 'Completed', '2026-05-26 19:09:18'),
(3, 7, '5000.00', 'Completed', '2026-05-29 16:01:30'),
(4, 7, '5000.00', 'Completed', '2026-05-29 16:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `subtotal`) VALUES
(1, 1, 6, 1, '5000.00'),
(2, 2, 6, 1, '5000.00'),
(3, 3, 6, 1, '5000.00'),
(4, 4, 6, 1, '5000.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `user_id`, `category_id`, `product_name`, `description`, `price`, `image`, `stock`, `created_at`) VALUES
(6, 6, NULL, 'Laptop', 'jsdsafnjhdsjf', '5000.00', '1779818683_zuka-zurabishvili-u_JdncwdncM-unsplash.jpg', 3, '2026-05-26 18:04:43'),
(12, 6, NULL, 'DSTV', 'DSTV decoder, brand new, never used. I bought it but I never used it, I use Apple TV most of the time :)', '1200.00', '1779912138_DSTV Packages In Ghana, Subscription Prices And More.jpg', 5, '2026-05-27 20:02:18'),
(14, 9, 2, 'iPhone 17 pro', 'The Apple iPhone 17 Pro is Apple\'s premium flagship smartphone, designed for users who want top-level performance, advanced photography, and professional video features. It features a durable aluminum unibody design, a powerful A19 Pro chip, an advanced cooling system, and Apple\'s longest-lasting battery in a Pro iPhone', '22000.00', '1780591172_OIP.webp', 5, '2026-06-04 16:39:32'),
(15, 9, 5, 'Shea moisture shampoo', 'Shea Moisture Shampoo is a nourishing hair care product formulated with natural ingredients to gently cleanse and moisturize the hair. it helps restore moisture, strengthen hair, and improve manageability. The shampoo effectively removes dirt and product buildup without stripping the hair of its natural oils, leaving it soft, healthy, and refreshed. Suitable for a variety of hair types.', '350.00', '1780591558_shea moisture.jpg', 20, '2026-06-04 16:45:58'),
(18, 10, 2, 'Galaxy buds', '2022 samsung Galaxy buds, used', '900.00', '1780646076_IMG_7058.HEIC', 1, '2026-06-05 07:54:36'),
(19, 10, 2, 'Samsung Note 10+', 'Samsung galaxy note 10+, cracked back glass but still working 100%', '2000.00', '1780646190_IMG_7059.HEIC', 1, '2026-06-05 07:56:30'),
(20, 10, 2, 'Batteries', 'Batteries, 60 pieces', '100.00', '1780654049_IMG_7057.HEIC', 10, '2026-06-05 10:07:29');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Seller'),
(2, 'Customer'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `created_at`) VALUES
(1, 3, 'Tshepang', 'Modiba', 'modibatrust15@gmail.com', '$2y$10$q3RMUsHYbV59OtaF3BZ65emQCyF8ihtgjSoXA9H9/nIOPnzdIUbA.', NULL, '2026-05-21 08:19:28'),
(6, 1, 'Thabiso', 'Modiba', 'thabiso79@gmail.com', '$2y$10$XzGMnOun.3n0LBlKRzZjKeqxXZ1Qv4zjHNVa/fPeW3Dy4XuHNCX5m', NULL, '2026-05-25 09:18:29'),
(7, 2, 'Lloyd', 'Modiba', 'lloydmodiba23@gmail.com', '$2y$10$.4pZ/GOoK.M9cOL/QHF1LOkbppiP.JnhH6U9.e3l94BYWDfQyvpTi', NULL, '2026-05-26 18:25:16'),
(8, 2, 'Lloyd', 'Modiba', 'modiballoyd79@gmail.com', '$2y$10$uzI6NXhIevJNsD4dYyeKRufVuKDSxgnh0K5LbTXSWKgtfN4yYISs.', NULL, '2026-06-03 20:32:04'),
(9, 1, 'Shamine', 'Moraila', 'morailashamine@gmail.com', '$2y$10$9o.EkbxOzhRJ8zi0l78/0O6XiJCxXo4d9MuyEGGZeSpsMuDgqzFGS', NULL, '2026-06-04 14:56:03'),
(10, 1, 'Mogau', 'Mohlala', 'mohlalamogau12@gmail.com', '$2y$10$SORc1eVB.SyAHdT2VHjMIu2FstNb3/ECvrutYMOvk42Jp29NBUgmC', NULL, '2026-06-05 07:48:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
