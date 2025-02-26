-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2025 at 01:20 PM
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
-- Database: `db_cfn`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `pass` varchar(255) NOT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `contact_no` varchar(11) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `validated` tinyint(1) DEFAULT NULL,
  `token` varchar(32) DEFAULT NULL,
  `token_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `username`, `email`, `pass`, `first_name`, `last_name`, `contact_no`, `address`, `role`, `profile_image`, `validated`, `token`, `token_created_at`) VALUES
(68, 'pja', 'pjarahgalias27@gmail.com', '$2y$10$Tcux0yAx2.MUSk4T0iYKJu5BxKcTVmzetXlL28OA/NBqxt2ZQeEhe', 'Arah', 'Galias', '09669463472', '839 Tolentino Street, Sampaloc, Manila ', NULL, '../uploads/Beige Minimalist Bridal Shower Bingo Card.png', 1, '', '2025-02-15 09:32:46'),
(69, 'ironman', 'princessjamie.galias.cics@ust.edu.ph', '$2y$10$v3UZxJJSrDUip7tu3c.9OOEgz12qoua.wy/tLYXgAUKsRDBWUwhjq', 'US-Letter', 'x 11', '09669463472', 'Blk 1 Lot 1 Ibañez St', NULL, '', 1, '', '2025-02-17 13:18:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
