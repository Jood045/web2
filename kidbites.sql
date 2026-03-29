-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 29 مارس 2026 الساعة 06:58
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
  `userID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- بنية الجدول `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) DEFAULT NULL,
  `ingredientName` varchar(100) DEFAULT NULL,
  `ingredientQuantity` varchar(50) DEFAULT NULL
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
  `userID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(1, 6, 2, 'Rainbow Veggie Pasta Cups', 'Warm, cozy pasta cups that parents trust and kids love', 'pasta.jpg', ''),
(2, 11, 1, 'Banana Oat Pancakes', 'Soft and healthy pancakes made with simple ingredients', 'banana-pancakes.jpg', ''),
(3, 6, 3, 'Apple Peanut Butter Sandwich Bites', 'Apple slices with peanut butter shaped like mini burgers', 'apple-sandwich.jpg', ''),
(4, 6, 1, 'Egg & Cheese Breakfast Muffins', 'Mini egg muffins that are easy to prepare', 'egg-muffins.jpg', '');

-- --------------------------------------------------------

--
-- بنية الجدول `recipecategory`
--

CREATE TABLE `recipecategory` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(50) NOT NULL
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
(6, 'user', 'shatha', 'mana', 'Shatha@gmail.com', '$2y$10$n3kz8.s0E9dw0/1vpCDuDeBkIN8SkVJB0jgcI4ujFkQd62ORHHbQe', 'sara.jpg'),
(7, 'user', 'Jood', 'Abdullah', 'jood@gmail.com', '$2y$10$gABwfjcLFzfy2alxkaYMqOahV.K8MeUU69Q.nVzrVu9ReA.ZzFV52', 'Sara.jpg'),
(8, 'user', 'Jood', 'Abdullah', 'jood5@gmail.com', '$2y$10$xXJRHluaYAsNgUayuhFJ0enloX33.Tsk94v6QlwaRNbzmHGzaQzhS', 'default.png'),
(9, 'user', 'ghada', 'f', 'RRR@GMAIL.COM', '$2y$10$68errXAPtJVuXdm5GHvWqOAhvRfqVxkHkFVh0pnmY7W4l1t3eq0oK', 'Ghada.jpg'),
(10, 'user', 'lama', 'L', 'L@gmail.com', '$2y$10$K71hu.4FIuVvHJeeJtlTb.6Lt77kc9BJLAUXdav2t926BzxPFxq3W', 'default.png'),
(11, 'user', 'sara', 's', 's@gmail.com', '$2y$10$TRqhT.W.uIvEZS3afBMQueb.wgw1bukUe7REydole6R1zQSGGpLAa', 'user.jpg');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipeID` (`recipeID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`userID`,`recipeID`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `instructions`
--
ALTER TABLE `instructions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`userID`,`recipeID`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `categoryID` (`categoryID`);

--
-- Indexes for table `recipecategory`
--
ALTER TABLE `recipecategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `recipeID` (`recipeID`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- قيود الجداول المحفوظة
--

--
-- القيود للجدول `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);

--
-- القيود للجدول `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `favourites_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favourites_ibfk_2` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`);

--
-- القيود للجدول `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`);

--
-- القيود للجدول `instructions`
--
ALTER TABLE `instructions`
  ADD CONSTRAINT `instructions_ibfk_1` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`);

--
-- القيود للجدول `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`);

--
-- القيود للجدول `recipe`
--
ALTER TABLE `recipe`
  ADD CONSTRAINT `recipe_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recipe_ibfk_2` FOREIGN KEY (`categoryID`) REFERENCES `recipecategory` (`id`);

--
-- القيود للجدول `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
