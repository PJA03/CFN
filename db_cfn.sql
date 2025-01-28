-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2025 at 09:38 AM
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
  `email` varchar(30) DEFAULT NULL,
  `pass` varchar(30) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `contact_no` int(11) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `validated` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `username`, `email`, `pass`, `first_name`, `last_name`, `contact_no`, `address`, `validated`) VALUES
(1, 'plan', 'flan@wan.com', '$2y$10$2FFuIlT.sG/DfVQFosE43e1', NULL, NULL, NULL, NULL, NULL),
(2, '', 'plant@yellow.com', 'a363b8d13575101a0226e8d0d054f2', NULL, NULL, NULL, NULL, NULL),
(3, '', 'palm@street.com', 'a363b8d13575101a0226e8d0d054f2', NULL, NULL, NULL, NULL, NULL),
(4, 'yellow', 'palm@street.com', 'a363b8d13575101a0226e8d0d054f2', NULL, NULL, NULL, NULL, NULL),
(5, 'hello', 'alt@ter.com', '77762497716492f944d5319842f593', NULL, NULL, NULL, NULL, NULL),
(6, 'PJA', 'pjarahgalias27@gmail.com', '2c8bb3706eb8a18b3690d677d29494', NULL, NULL, NULL, NULL, NULL),
(7, 'hunt', 'owl@hoo.com', '5a2e29a8da807ec4e027193df83b9b', NULL, NULL, NULL, NULL, NULL);

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
