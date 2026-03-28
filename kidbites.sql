-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 28 مارس 2026 الساعة 19:22
-- إصدار الخادم: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kidbites`
--

-- --------------------------------------------------------

--
-- بنية الجدول `blocked_users`
--

CREATE TABLE `blocked_users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `blocked_users`
--

INSERT INTO `blocked_users` (`id`, `firstName`, `lastName`, `emailAddress`) VALUES
(12, 't', 't', 'test@gmail.com'),
(19, '', '', '');

-- --------------------------------------------------------

--
-- بنية الجدول `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `comment` text,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- بنية الجدول `favourites`
--

CREATE TABLE `favourites` (
  `userID` int(11) DEFAULT NULL,
  `recipeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `favourites`
--

INSERT INTO `favourites` (`userID`, `recipeID`) VALUES
(1, 1),
(1, 5),
(2, 2);

-- --------------------------------------------------------

--
-- بنية الجدول `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) DEFAULT NULL,
  `ingredientName` varchar(100) DEFAULT NULL,
  `ingredientQuantity` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- بنية الجدول `instructions`
--

CREATE TABLE `instructions` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) DEFAULT NULL,
  `step` text,
  `stepOrder` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- بنية الجدول `likes`
--

CREATE TABLE `likes` (
  `userID` int(11) DEFAULT NULL,
  `recipeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `likes`
--

INSERT INTO `likes` (`userID`, `recipeID`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(1, 2),
(2, 2),
(3, 2),
(1, 3),
(2, 3),
(1, 4),
(2, 4),
(1, 5),
(2, 5),
(3, 5);

-- --------------------------------------------------------

--
-- بنية الجدول `recipe`
--

CREATE TABLE `recipe` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `categoryID` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `photoFileName` varchar(255) DEFAULT NULL,
  `videoFilePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `recipe`
--

INSERT INTO `recipe` (`id`, `userID`, `categoryID`, `name`, `description`, `photoFileName`, `videoFilePath`) VALUES
(1, 6, 2, 'Rainbow Veggie Pasta Cups', 'Warm, cozy pasta cups that parents trust and kids enjoy. Great for school lunchboxes and quick dinners.\r\n', 'pasta.jpg', 'https://youtu.be/8UQkmNW5jec?si=PbSU4QBJX5a9yWRU'),
(2, 6, 1, 'Banana Oat Pancakes', 'Soft and healthy pancakes made with simple ingredients, perfect for picky kids.', 'banana-pancakes.jpg', 'https://youtu.be/Stpe90s2pD4?si=Ky3jBuOxqy9zc5yd'),
(3, 7, 2, 'Chicken & Avocado Mini Wraps', 'Small wraps filled with chicken and avocado, easy to eat and nutritious.', 'chicken-wraps.jpg', 'No Video'),
(4, 6, 3, 'Apple Peanut Butter Sandwich Bites', 'Apple slices with peanut butter, shaped like mini sandwiches.', 'apple-sandwich.jpg', 'https://youtu.be/ANKtVORw05U?si=DDzevPe9DAggxJqQ'),
(5, 8, 1, 'Egg & Cheese Breakfast Muffins', 'Mini egg muffins that are easy to prepare and great for busy mornings.', 'egg-muffins.jpg', 'No Video');

-- --------------------------------------------------------

--
-- بنية الجدول `recipecategory`
--

CREATE TABLE `recipecategory` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `recipecategory`
--

INSERT INTO `recipecategory` (`id`, `categoryName`) VALUES
(1, 'Breakfast'),
(2, 'Lunch'),
(3, 'Dessert');

-- --------------------------------------------------------

--
-- بنية الجدول `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `recipeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `userType` enum('user','admin') NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photoFileName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `userType`, `firstName`, `lastName`, `emailAddress`, `password`, `photoFileName`) VALUES
(6, 'user', 'shatha', 'mana', 'Shatha@gmail.com', '$2y$10$n3kz8.s0E9dw0/1vpCDuDeBkIN8SkVJB0jgcI4ujFkQd62ORHHbQe', 'default.png'),
(7, 'user', 'Jood', 'Abdullah', 'jood@gmail.com', '$2y$10$gABwfjcLFzfy2alxkaYMqOahV.K8MeUU69Q.nVzrVu9ReA.ZzFV52', 'Sara.jpg'),
(8, 'user', 'Jood', 'Abdullah', 'jood5@gmail.com', '$2y$10$xXJRHluaYAsNgUayuhFJ0enloX33.Tsk94v6QlwaRNbzmHGzaQzhS', 'default.png'),
(9, 'user', 'ghada', 'f', 'RRR@GMAIL.COM', '$2y$10$68errXAPtJVuXdm5GHvWqOAhvRfqVxkHkFVh0pnmY7W4l1t3eq0oK', 'Ghada.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blocked_users`
--
ALTER TABLE `blocked_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instructions`
--
ALTER TABLE `instructions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipecategory`
--
ALTER TABLE `recipecategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blocked_users`
--
ALTER TABLE `blocked_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instructions`
--
ALTER TABLE `instructions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recipecategory`
--
ALTER TABLE `recipecategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
