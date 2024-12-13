-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 13, 2024 at 10:47 PM
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
-- Database: `july_gallery`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(8, 'Aftermath and Impact'),
(4, 'Documents and Articles'),
(1, 'Historical Events'),
(3, 'Locations and Landmarks'),
(5, 'Media and Press Coverage'),
(10, 'NSFW / Sensitive Content'),
(2, 'People and Personalities'),
(9, 'Personal Stories'),
(11, 'Propaganda'),
(6, 'Protest Art'),
(7, 'Public Gatherings and Protests');

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection_images`
--

CREATE TABLE `collection_images` (
  `collection_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `is_nsfw` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) NOT NULL DEFAULT 0,
  `is_own_work` tinyint(1) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `likes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `user_id`, `filename`, `title`, `description`, `category_id`, `event_date`, `is_nsfw`, `is_approved`, `created_at`, `views`, `is_own_work`, `featured`, `likes`) VALUES
(57, 1, '1733149649_Bangladesh_quota_reform_movement_2024.jpg', 'A female student carrying a sign reading \"Quota or merit? Merit! Merit!\"', 'Students launched the \"Bangla Blockade\" following a one-point demand for scrapping all illogical and discriminatory quotas in public service through enactment of a law and keeping a minimum quota for marginalised citizens in line with the constitution. Credit: Rayhan9d', 7, '2024-07-11', 0, 1, '2024-12-02 14:27:29', 0, 0, 0, 0),
(58, 1, '1733151926_1732196284_The_victory_celebration_of_Bangladeshi_student\'s_one_point_movement.jpg', 'Victory march by protesters after the resignation of Sheikh Hasina in 2024.', 'Victory march by protesters after the resignation of Sheikh Hasina in 2024.', 1, '2024-08-05', 0, 1, '2024-12-02 15:05:27', 0, 0, 1, 0),
(59, 1, '1733152021_1732096131_People_cheering_in_front_of_the_Prime_Minister\'s_Office_after_Sheikh_Hasina\'s_resignation.jpg', 'People cheering in front of the Prime Minister\'s Office after Sheikh Hasina\'s resignation', 'Victory procession after the resignation of Prime Minister Sheikh Hasina in Shahbagh, Dhaka', 1, '2024-08-05', 0, 1, '2024-12-02 15:07:02', 0, 0, 0, 0),
(60, 1, '1733152607_1732660623_inbound7558491749798032394.jpg', 'Protesters at Central Shaheed Minar', 'One point movement of Bangladesh in Shaheed Minar', 7, '2024-08-04', 0, 1, '2024-12-02 15:16:47', 0, 0, 0, 1),
(61, 2, '1733156149_One_point_movement_of_Bangladesh_in_DU_35.jpg', 'Student–People\'s uprising at the University of Dhaka', 'One point movement of Bangladesh in DU', 7, '2024-08-04', 0, 1, '2024-12-02 16:15:50', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `image_approval`
--

CREATE TABLE `image_approval` (
  `id` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `reason` text DEFAULT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `image_tags`
--

CREATE TABLE `image_tags` (
  `image_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `image_tags`
--

INSERT INTO `image_tags` (`image_id`, `tag_id`) VALUES
(57, 1);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `image_id`, `created_at`) VALUES
(31, 1, 3, '2024-11-22 00:38:29'),
(32, 1, 5, '2024-11-22 01:23:33'),
(33, 1, 4, '2024-11-22 01:48:35'),
(34, 1, 10, '2024-11-25 17:28:40'),
(37, 1, 8, '2024-11-26 00:05:01'),
(38, 1, 2, '2024-11-26 00:05:08'),
(39, 1, 9, '2024-11-26 00:44:04'),
(40, 1, 26, '2024-11-26 12:49:50'),
(41, 1, 61, '2024-12-03 03:38:20'),
(42, 1, 60, '2024-12-03 03:38:24');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(4, 'Art'),
(5, 'Freedom'),
(6, 'Martyrs'),
(3, 'Police Brutality'),
(1, 'Protest'),
(2, 'Resistance');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','member') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `profile_picture`, `name`, `bio`) VALUES
(1, 'sarwar', 'sarwar.uddin@northsouth.edu', '$2y$10$i4kbJ0ik/1P8L3I4EkFGWutRfS3n350yAx7YhkI8zt/Kusyub1KtS', 'admin', '2024-11-19 10:51:00', '1732106519.png', 'Sarwar Uddin', 'Hello'),
(2, 'user1', 'user1@gmail.com', '$2y$10$gtiACGeqswHPWtj3jQn9MOQjNQx699SJMPy7SItgiTr2RVZg50SbS', 'user', '2024-11-20 09:41:48', '1733157131.jpg', 'Yusuf Sorker', ''),
(3, 'user2', 'user2@gmail.com', '$2y$10$Xs0zM6FqBbe7vs7GiLkyi.WNq7L4wBaUBo7rdUYf7PWNTEY8ZCsB.', 'member', '2024-11-21 13:30:04', '1733146696.jpg', 'User 2', ''),
(4, 'rhasan', 'ragib.hasan@northsouth.edu', '$2y$10$1e8Cx6EHyOHFNgOnlvxUY.PDBcHSJa/KH36QUK7N3uSkHWT134ryO', 'admin', '2024-11-30 13:52:36', NULL, NULL, NULL),
(5, '2012954', 'ibnhasan99@gmail.com', '$2y$10$0mWO2Z3UjUg5F/.qLEg6.uYHACaoodFd7hw5aF6PVvY7dbGrOLevO', 'user', '2024-11-30 14:06:23', NULL, NULL, NULL),
(6, 'pascal', 'gamingpascal.gg@gmail.com', '$2y$10$hKd2jfDT67QoT./aFtCRp.Kd1GuCclwU8Zj4QuAAaWR8Yqezf4suC', 'member', '2024-11-30 16:58:18', NULL, NULL, NULL),
(7, 'fghfg', 'ragib.hasanfg@hsouth.edu', '$2y$10$4wAOi5OpJpBmwSGKZuH3XOilCqSx9aT9.fOph5vVbxAUPIM8YvK/G', 'user', '2024-12-02 11:32:05', NULL, NULL, NULL),
(8, 'rhasan123', 'rhasan123@gmail.com', '$2y$10$SjfO09Zys5nN9jAFk5ikIedn7LgnepVCjVOzNlQwrcF6QesxtItqe', 'user', '2024-12-03 09:35:18', NULL, NULL, NULL),
(9, 'aaa', 'a@a.com', '$2y$10$1BGhYFv3AeYLWDrpmc1C6uE8RnpdEKJRroHobTy7K1vZjTE8WWEs6', 'member', '2024-12-03 10:20:32', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `collection_images`
--
ALTER TABLE `collection_images`
  ADD PRIMARY KEY (`collection_id`,`image_id`),
  ADD KEY `image_id` (`image_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `image_id` (`image_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `image_approval`
--
ALTER TABLE `image_approval`
  ADD PRIMARY KEY (`id`),
  ADD KEY `image_id` (`image_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `image_tags`
--
ALTER TABLE `image_tags`
  ADD PRIMARY KEY (`image_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`image_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `image_approval`
--
ALTER TABLE `image_approval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `collections`
--
ALTER TABLE `collections`
  ADD CONSTRAINT `collections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collection_images`
--
ALTER TABLE `collection_images`
  ADD CONSTRAINT `collection_images_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collection_images_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `images_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `image_approval`
--
ALTER TABLE `image_approval`
  ADD CONSTRAINT `image_approval_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `image_approval_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `image_tags`
--
ALTER TABLE `image_tags`
  ADD CONSTRAINT `image_tags_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `image_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
