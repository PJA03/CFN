-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2025 at 04:17 PM
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
  `pass` varchar(30) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `contact_no` int(11) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `validated` tinyint(1) DEFAULT NULL,
  `token` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `username`, `email`, `pass`, `first_name`, `last_name`, `contact_no`, `address`, `validated`, `token`) VALUES
(1, 'plan', 'flan@wan.com', '$2y$10$2FFuIlT.sG/DfVQFosE43e1', NULL, NULL, NULL, NULL, NULL, NULL),
(2, '', 'plant@yellow.com', 'a363b8d13575101a0226e8d0d054f2', NULL, NULL, NULL, NULL, NULL, NULL),
(3, '', 'palm@street.com', 'a363b8d13575101a0226e8d0d054f2', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'yellow', 'palm@street.com', 'a363b8d13575101a0226e8d0d054f2', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'hello', 'alt@ter.com', '77762497716492f944d5319842f593', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'PJA', 'pjarahgalias27@gmail.com', '2c8bb3706eb8a18b3690d677d29494', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'hunt', 'owl@hoo.com', '5a2e29a8da807ec4e027193df83b9b', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'user', 'princessjamie.galias.cics@ust.', '9a3413c9b4c3678dbf06391c6d9e50', NULL, NULL, NULL, NULL, 0, NULL),
(9, 'admin', 'princessjamie.galias.cics@ust.', '9a3413c9b4c3678dbf06391c6d9e50', NULL, NULL, NULL, NULL, 0, NULL),
(10, 'admin', 'princessjamie.galias.cics@ust.', '9a3413c9b4c3678dbf06391c6d9e50', NULL, NULL, NULL, NULL, 0, NULL),
(11, 'admin', 'princessjamie.galias.cics@ust.', '9a3413c9b4c3678dbf06391c6d9e50', NULL, NULL, NULL, NULL, 0, NULL),
(12, 'amp', 'princessjamie.galias.cics@ust.', '9a3413c9b4c3678dbf06391c6d9e50', NULL, NULL, NULL, NULL, 0, NULL),
(13, 'testtt', 'princessjamie.galias.cics@ust.', '9a3413c9b4c3678dbf06391c6d9e50', NULL, NULL, NULL, NULL, 0, NULL),
(14, 'test2', 'princessjamie.galias.cics@ust.', '9627df7a4a5b849f67fce863e82adc', NULL, NULL, NULL, NULL, 0, NULL),
(15, 'test3', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, NULL),
(16, 'test', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, NULL),
(17, 'huhuhu', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, NULL),
(18, 'huhuhu', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, NULL),
(19, 'princess', 'princessjamie.galias.cics@ust.', 'f2750fc6d623392c1c8ad1d9d18f7e', NULL, NULL, NULL, NULL, 0, NULL),
(20, 'Arah', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, NULL),
(21, 'arah', 'princessjamie.galias.cics@ust.', 'b4a95e79be1847476da953ca231e5c', NULL, NULL, NULL, NULL, 0, NULL),
(22, 'bran', 'princessjamie.galias.cics@ust.', '068021d73a84060d0076731574883a', NULL, NULL, NULL, NULL, 0, NULL),
(26, 'hell', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '623659'),
(27, 'hnggg', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '917184'),
(28, 'arah', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '552920'),
(29, 'hello', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '601099'),
(30, 'hello', 'pjarahgalias27@gmail.com', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 1, '10974'),
(31, 'princess', 'pjarahgalias27@gmail.com', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 1, '745391'),
(32, 'star', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '705972'),
(33, 'mabel', 'wlh15724@msssg.com', '1a1dc91c907325c69271ddf0c944bc', NULL, NULL, NULL, NULL, 0, '710957'),
(34, 'princess', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '3808'),
(35, 'princess', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '850167'),
(36, 'amp', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '250789'),
(37, 'user', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '707073'),
(38, 'amp', 'princessjamie.galias.cics@ust.', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 0, '572235'),
(39, 'fwl', 'princessjamie.galias.cics@ust.', 'd887ae482658564a4cd075f30079f1', NULL, NULL, NULL, NULL, 0, '642808'),
(40, 'HELLO', 'pjarahgalias27@gmail.com', '098f6bcd4621d373cade4e832627b4', NULL, NULL, NULL, NULL, 1, '67445');

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
