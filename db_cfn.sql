-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 09:56 AM
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
-- Table structure for table `tb_bestsellers`
--

CREATE TABLE `tb_bestsellers` (
  `bestseller_id` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_bestsellers`
--

INSERT INTO `tb_bestsellers` (`bestseller_id`, `productID`, `display_order`, `created_at`, `updated_at`) VALUES
(7, 17, 2, '2025-03-22 14:41:11', '2025-03-22 14:41:11'),
(9, 8, 4, '2025-03-22 14:50:54', '2025-03-22 14:50:54'),
(15, 22, 5, '2025-03-23 13:49:31', '2025-03-23 13:49:31'),
(17, 11, 6, '2025-03-25 04:42:29', '2025-03-25 04:42:29'),
(18, 35, 7, '2025-03-25 04:42:36', '2025-03-25 04:42:36'),
(19, 37, 8, '2025-03-25 04:42:46', '2025-03-25 04:42:46'),
(20, 33, 9, '2025-03-25 04:42:55', '2025-03-25 04:42:55');

-- --------------------------------------------------------

--
-- Table structure for table `tb_cart`
--

CREATE TABLE `tb_cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `price_total` decimal(10,2) GENERATED ALWAYS AS (`price` * `quantity`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_cart`
--

INSERT INTO `tb_cart` (`cart_id`, `user_id`, `productID`, `quantity`, `price`) VALUES
(11, 89, 11, 1, 190.00),
(12, 89, 19, 1, 180.00),
(13, 89, 15, 1, 180.00),
(14, 79, 9, 1, 140.00),
(15, 79, 9, 1, 140.00),
(16, 79, 9, 1, 140.00),
(17, 79, 9, 1, 140.00),
(18, 79, 9, 1, 140.00),
(19, 79, 9, 1, 140.00),
(20, 79, 9, 1, 140.00),
(28, 91, 10, 1, 130.00),
(29, 91, 13, 1, 180.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_orders`
--

CREATE TABLE `tb_orders` (
  `orderID` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `productID` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `payment_option` varchar(255) NOT NULL,
  `payment_proof` varchar(255) NOT NULL,
  `isApproved` int(11) DEFAULT NULL,
  `price_total` double NOT NULL,
  `trackingLink` varchar(255) DEFAULT NULL,
  `delivered_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_orders`
--

INSERT INTO `tb_orders` (`orderID`, `order_date`, `productID`, `product_name`, `user_id`, `email`, `first_name`, `last_name`, `quantity`, `status`, `payment_option`, `payment_proof`, `isApproved`, `price_total`, `trackingLink`, `delivered_date`) VALUES
(3, '2025-03-21 16:16:52', 10, 'Buster D’ Acne Serum 30ml', 89, 'princessjamie.galias.cics@ust.edu.ph', 'Arah', '0', 1, 'Delivered', '0', '67dd2074ef482-484292276_507224642440350_5531290597758342471_n.jpg', 1, 130, '930000123456', '2025-03-21 17:16:18'),
(4, '2025-03-21 16:24:38', 11, 'Elixir Day Cream SPF50 PA+++ 50g', 89, 'princessjamie.galias.cics@ust.edu.ph', 'Arah', '0', 1, 'Delivered', '0', '67dd2246288bf-484292276_507224642440350_5531290597758342471_n.jpg', 1, 190, 'dsafsadf', '2025-03-25 16:02:42'),
(5, '2025-03-24 20:01:41', 10, 'Buster D’ Acne Serum 30ml', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 4, 'Processing', '0', '67e149a57849b-bank qr.jpg', 1, 1430, '', NULL),
(6, '2025-03-24 20:01:41', 11, 'Elixir Day Cream SPF50 PA+++ 50g', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 1, 'Shipped', '0', '67e149a57849b-bank qr.jpg', 1, 1430, '234edffs', NULL),
(7, '2025-03-24 20:01:41', 12, 'SET + Box Packaging', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 1, 'Cancelled', '0', '67e149a57849b-bank qr.jpg', 0, 1430, NULL, NULL),
(8, '2025-03-25 12:28:11', 10, 'Buster D’ Acne Serum 30ml', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 1, 'Processing', '0', '67e230db2b311-Screenshot 2024-12-08 222746.png', 1, 130, '', NULL),
(9, '2025-03-25 13:04:42', 11, 'Elixir Day Cream SPF50 PA+++ 50g', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 3, 'Cancelled', '0', '67e2396a4c3f9-Screenshot 2024-12-08 222734.png', 0, 1880, NULL, NULL),
(10, '2025-03-25 13:04:42', 17, 'CollaBoost Foam Wash 100ml w/ Silicone Brush', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 1, 'Waiting for Payment', '0', '67e2396a4c3f9-Screenshot 2024-12-08 222734.png', 0, 1880, NULL, NULL),
(11, '2025-03-25 13:04:42', 22, 'Bright Radiance Toner 100ml', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 1, 'Waiting for Payment', '0', '67e2396a4c3f9-Screenshot 2024-12-08 222734.png', 0, 1880, NULL, NULL),
(12, '2025-03-25 13:04:42', 25, '250ml Gluta Kojic InstaWhite SPF50 Body Lotion', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 4, 'Waiting for Payment', '0', '67e2396a4c3f9-Screenshot 2024-12-08 222734.png', 0, 1880, NULL, NULL),
(14, '2025-03-30 21:16:13', 11, 'Elixir Day Cream SPF50 PA+++ 50g', 72, 'pjarahgalias27@gmail.com', 'Arah', '0', 3, 'Delivered', '0', '67e9441d183ad-frassati.jpg', 0, 570, 'dsafsadf', '2025-03-30 21:17:09');

-- --------------------------------------------------------

--
-- Table structure for table `tb_payment_qr_codes`
--

CREATE TABLE `tb_payment_qr_codes` (
  `id` int(11) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `qr_image` varchar(255) NOT NULL,
  `uploaded_by` varchar(30) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_payment_qr_codes`
--

INSERT INTO `tb_payment_qr_codes` (`id`, `payment_type`, `qr_image`, `uploaded_by`, `upload_date`) VALUES
(1, 'bank_transfer_bpi', 'bank_transfer_bpi_67e148a573adc.jpg', 'Admin', '2025-03-24 11:57:25'),
(4, 'gcash', 'gcash_67e148adcedcc.jpg', 'Admin', '2025-03-24 11:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `tb_products`
--

CREATE TABLE `tb_products` (
  `productID` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_desc` text DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_products`
--

INSERT INTO `tb_products` (`productID`, `product_name`, `product_desc`, `category`, `product_image`, `created_at`, `updated_at`) VALUES
(8, 'Buster D’ Acne Foam Wash 100ml w/ Silicone Brush', 'It is for rinsing away dirt, excess sebum, and other impurities without stripping the skin of its natural moisture balance.', 'Face', 'uploads/img_67c438d0d2ee75.43147062.jpg', '2025-03-02 18:54:08', '2025-03-20 00:16:24'),
(10, 'Buster D’ Acne Serum 30ml', 'Best for oily and acne-prone skin. It is a mattifying, pore-refining treatment that eaves skin flawless.', 'Face', 'uploads/Screenshot 2025-03-02 185610.jpg', '2025-03-02 18:55:50', '2025-03-20 00:20:54'),
(11, 'Elixir Day Cream SPF50 PA+++ 50g', 'It contains safe, natural chemicals and minerals that are used as sunscreen to whiten, lubricate, and provide sun protection. It provides broad-spectrum UVA and UVB protection. Easy to apply, water-resistant, with sun protection.', 'Skin', 'uploads/Screenshot 2025-03-02 194023.jpg', '2025-03-02 18:57:49', '2025-03-20 00:21:01'),
(12, 'SET + Box Packaging', 'It is formulated to treat acne-prone & oily skin and minimize pores. It reduces sebum production and kills bacteria to modulate oil and prevent pimples and breakouts. This set comes with the Elixir Day Cream for all skin types.', 'Face', 'uploads/img_67c439cfa57c16.10447680.jpg', '2025-03-02 18:58:23', '2025-03-20 00:21:13'),
(13, 'Biocare ReVitagen Foam Wash 100ml w/ Silicone Brush', 'A mild, non-sulfate cleansing foam wash that gently removes debris, revitalizes skin, gives anti-aging effects, and reduces oil instantly.', 'Face', '', '2025-03-02 19:00:22', '2025-03-24 00:52:09'),
(14, 'Biocare ReVitagen Toner 100ml', 'A mild toner that helps decrease the appearance of fine lines and wrinkles, counteract oxidative stress, and restore skin barriers.', 'Face', '', '2025-03-02 19:01:00', '2025-03-24 00:54:30'),
(15, 'Biocare ReVitagen Serum 30ml', 'An anti-aging serum that serves as a muscle relaxant, reducing the appearance of crow\\\'s feet or creases and wrinkles around the eyes.', 'Face', 'uploads/67d997563fe6a_1742313302.png', '2025-03-02 19:01:56', '2025-03-24 00:54:23'),
(16, 'SET + Box Packaging', 'It is a facial treatment and regimen that revitalize and regenerate the skin for a healthier and younger look.', 'Face', '', '2025-03-02 19:03:49', '2025-03-24 00:54:34'),
(17, 'CollaBoost Foam Wash 100ml w/ Silicone Brush', 'A hydrating cleanser that washes away dirt, makeup residue, and excess oil, leaving the skin smooth, supple, and soft.', 'Face', '', '2025-03-02 19:04:43', '2025-03-24 00:54:40'),
(18, 'CollaBoost Toner 100ml', 'It hydrates and conditions the skin, promoting a healthy and vivid complexion while preventing dryness.', 'Face', 'uploads/67d9972ea67c9_1742313262.png', '2025-03-02 19:05:23', '2025-03-20 00:29:04'),
(19, 'CollaBoost BabyColla Serum 30ml', 'It makes the skin softer and more resilient, similar to newborn skin.', 'Face', '', '2025-03-02 19:06:04', '2025-03-20 00:29:09'),
(20, 'SET + Box Packaging', 'It is formulated with type III collagen clinically proven to restore skin softness and resilience by producing and nurturing soft baby collagen. This set comes with the Elixir Day Cream for all skin types.', 'Face', '', '2025-03-02 19:11:56', '2025-03-20 00:29:36'),
(21, 'Bright Radiance Foam Wash 100ml w/ Silicone Brush', 'It is a foaming cleanser that dissolves makeup and grime as a defense for daily pollutants, for radiant, youthful-looking skin.', 'Face', '', '2025-03-02 19:13:16', '2025-03-20 00:29:43'),
(22, 'Bright Radiance Toner 100ml', 'It is a toner that calms redness that helps minimize pores and helps brighten and lightens skin.', 'Face', '', '2025-03-02 19:13:49', '2025-03-20 00:29:53'),
(23, 'Bright Radiance Serum 30ml', 'It enhances skin tone by brightening and lightening the skin while decreasing shine.', 'Face', '', '2025-03-02 19:14:36', '2025-03-20 00:30:00'),
(24, 'SET + Box Packaging', 'It can boost micro-circulation just beneath\\\\r\\\\nthe skin’s surface that improves the skin tone\\\\r\\\\nand illuminate radiance.', 'Face', '', '2025-03-02 19:15:23', '2025-03-20 00:30:07'),
(25, '250ml Gluta Kojic InstaWhite SPF50 Body Lotion', 'No description yet.', 'Skin', '', '2025-03-02 19:29:16', '2025-03-20 00:30:14'),
(26, '50ml Advance White Corrector Cream', 'No description yet.', 'Skin ', '', '2025-03-02 19:30:27', '2025-03-20 00:30:31'),
(27, '100g Buster D’ Acne Face and Body Soap', 'No description yet.', 'Face', '', '2025-03-02 19:31:54', '2025-03-20 00:30:44'),
(28, '100g Clear Blanc Face and Body Soap', 'A Multi-functional 17-in1 all-natural Anti-Aging, Whitening & Skin Brightening Soap. It decreases age spots on the face and smoothens the body. Regular use results in whiter and brighter skin in four weeks. ', 'Skin', '', '2025-03-02 19:32:30', '2025-03-20 00:31:09'),
(29, '100g Pour Homme and Body Soap', 'A Moisturizing, Anti-aging, Pore Minimizing, Oil-control, and Antiperspirant soap bar specially formulated for men. It contains all-natural plant extracts and is free from harmful chemicals. Regular use results in lighter and smoother skin.  ', 'Skin', '', '2025-03-02 19:32:58', '2025-03-24 00:51:24'),
(30, '100g Ceramide Oat Face and Body Soap', 'No description yet.', 'Skin', '', '2025-03-02 19:33:24', '2025-03-20 00:32:00'),
(31, 'Shampoo Bar 50g (Conditioning, Clarifying, Hair Growth & Scalp Care) with BOX', 'Formulated for Hair Fall Control & Hair Growth.', 'Hair', '', '2025-03-02 19:34:50', '2025-03-24 00:50:45'),
(32, 'Conditioner Bar 60g (Argan Repairing, Aloe Vera, Keratin) with BOX', 'Formulated to treat, hydrate, and moisturize hair leaving it silky soft.', 'Hair', '', '2025-03-02 19:36:41', '2025-03-24 00:50:58'),
(33, '30ml Hair Biotin Serum', 'It can suppress root sheath aging,\\\\r\\\\nand activate root sheath and\\\\r\\\\ndermal papilla adhesion proteins.\\\\r\\\\nIt increases hair development by\\\\r\\\\nacting on hair follicles.', 'Hair', '', '2025-03-02 19:38:39', '2025-03-20 00:32:28'),
(34, '100ml Hair Activator Spray', 'It can strengthen hair follicle structure,delay the aging process of hair nfollicles, stop hair loss, and instantly give hair moisture and plumpness.', 'Hair', 'uploads/67d9970765864_1742313223.png', '2025-03-02 19:39:10', '2025-03-24 00:49:22'),
(35, 'AMOII Parfum 85ml with BOX(HOMME)', 'No description yet.', 'Perfume', 'uploads/67d9978fcba27_1742313359.png', '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(36, 'AMOII Parfum 60ml with BOX (HOMME)', 'No description yet.', 'Perfume', '', '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(37, 'AMOII Parfum 85ml with BOX (FEMME)', 'AMOII which means \\\'beauty\\\' is an inspired perfume formulated with long-lasting distinct fragrances that elevate your fashion and boost your confidence with its finishing touch.\\r\\n\\r\\nCAUTION:\\r\\nKeep away from children\\\'s reach. Avoid contact with eyes', 'Perfume', 'uploads/67d997ab41804_1742313387.png', '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(38, 'AMOII Parfum 60ml with BOX (FEMME)', 'No description yet.', 'Perfume', '', '2025-03-02 20:03:58', '2025-03-02 20:03:58');

-- --------------------------------------------------------

--
-- Table structure for table `tb_productvariants`
--

CREATE TABLE `tb_productvariants` (
  `variant_id` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `variant_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `sku` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_productvariants`
--

INSERT INTO `tb_productvariants` (`variant_id`, `productID`, `variant_name`, `price`, `stock`, `sku`, `is_default`, `created_at`, `updated_at`) VALUES
(10, 8, 'Default', 220.00, 1, '', 1, '2025-03-02 18:54:08', '2025-03-20 00:16:24'),
(12, 10, 'Default', 130.00, 1, '', 1, '2025-03-02 18:55:50', '2025-03-20 00:20:54'),
(13, 11, 'Default', 190.00, 1, '', 1, '2025-03-02 18:57:49', '2025-03-20 00:21:01'),
(14, 12, 'Default', 720.00, 1, '', 1, '2025-03-02 18:58:23', '2025-03-20 00:21:13'),
(15, 13, 'Default', 180.00, 1, '', 1, '2025-03-02 19:00:22', '2025-03-24 00:52:09'),
(16, 14, 'Default', 130.00, 1, '', 1, '2025-03-02 19:01:00', '2025-03-24 00:54:30'),
(17, 15, 'Default', 180.00, 1, '', 1, '2025-03-02 19:01:56', '2025-03-24 00:54:23'),
(18, 16, 'default', 720.00, 1, '', 1, '2025-03-02 19:03:49', '2025-03-24 00:54:34'),
(19, 17, 'Default', 190.00, 1, '', 1, '2025-03-02 19:04:43', '2025-03-24 00:54:40'),
(20, 18, 'default', 120.00, 1, '', 1, '2025-03-02 19:05:23', '2025-03-20 00:29:04'),
(21, 19, 'Default', 180.00, 1, '', 1, '2025-03-02 19:06:04', '2025-03-20 00:29:09'),
(22, 20, 'Default', 720.00, 1, '', 1, '2025-03-02 19:11:56', '2025-03-20 00:29:36'),
(23, 21, 'Default', 180.00, 1, '', 1, '2025-03-02 19:13:16', '2025-03-20 00:29:43'),
(24, 22, 'Default', 120.00, 1, '', 1, '2025-03-02 19:13:49', '2025-03-20 00:29:53'),
(25, 23, 'Default', 130.00, 1, '', 1, '2025-03-02 19:14:36', '2025-03-20 00:30:00'),
(26, 24, 'Default', 660.00, 1, '', 1, '2025-03-02 19:15:23', '2025-03-20 00:30:07'),
(27, 25, 'Default', 280.00, 1, '', 1, '2025-03-02 19:29:16', '2025-03-20 00:30:14'),
(28, 26, 'Default', 290.00, 1, '', 1, '2025-03-02 19:30:27', '2025-03-20 00:30:31'),
(29, 27, 'Default', 99.00, 1, '', 1, '2025-03-02 19:31:54', '2025-03-20 00:30:44'),
(30, 28, 'Default', 145.00, 1, '', 1, '2025-03-02 19:32:30', '2025-03-20 00:31:09'),
(31, 29, 'Default', 112.00, 1, '', 1, '2025-03-02 19:32:58', '2025-03-24 00:51:24'),
(32, 30, 'Default', 102.00, 1, '', 1, '2025-03-02 19:33:24', '2025-03-20 00:32:00'),
(33, 31, 'Default', 164.00, 1, '', 1, '2025-03-02 19:34:50', '2025-03-24 00:50:45'),
(34, 32, 'Default', 266.00, 1, '', 1, '2025-03-02 19:36:41', '2025-03-24 00:50:58'),
(35, 33, 'Default', 180.00, 1, '', 1, '2025-03-02 19:38:39', '2025-03-20 00:32:28'),
(36, 34, 'Default', 230.00, 1, '', 1, '2025-03-02 19:39:10', '2025-03-24 00:49:22'),
(37, 35, 'Confident', 350.00, 1, '', 1, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(38, 35, 'Vibrant', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(39, 35, 'Modern', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(40, 35, 'Zesty', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(41, 35, 'Fresh Aqua M', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(42, 35, 'Musk', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(43, 35, 'Spunky', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(44, 35, 'Sporty', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(45, 35, 'Aqua Aromatica', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(46, 35, 'Desir Bleu', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(47, 35, 'Victorious', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(48, 35, 'Spontaneous M', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(49, 35, 'Revealing', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(50, 35, 'Impressive', 350.00, 1, '', 0, '2025-03-02 19:44:25', '2025-03-20 00:32:45'),
(51, 36, 'Confident', 280.00, 1, '', 1, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(52, 36, 'Vibrant', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(53, 36, 'Modern', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(54, 36, 'Zesty', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(55, 36, 'Fresh Aqua M', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(56, 36, 'Musk', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(57, 36, 'Spunky', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(58, 36, 'Sporty', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(59, 36, 'Aqua Aromatica', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(60, 36, 'Desir Bleu', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(61, 36, 'Victorious', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(62, 36, 'Spontaneous M', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(63, 36, 'Revealing', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(64, 36, 'Impressive', 280.00, 1, '', 0, '2025-03-02 19:46:39', '2025-03-02 19:47:41'),
(65, 37, 'Cantaloupe', 350.00, 1, '', 1, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(66, 37, 'Fruity', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(67, 37, 'Fresh Blossom', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(68, 37, 'Active', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(69, 37, 'Fresh Aqua W', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(70, 37, 'Casual', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(71, 37, 'Le Delice', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(72, 37, 'Sparkling', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(73, 37, 'Elegant', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(74, 37, 'Luscious', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(75, 37, 'Diamant', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(76, 37, 'Candid', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(77, 37, 'Mulberry', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(78, 37, 'Glorious', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(79, 37, 'Chic', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(80, 37, 'Captivating', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(81, 37, 'Stunning', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(82, 37, 'Pleasant', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(83, 37, 'Naive', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(84, 37, 'Seductive', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(85, 37, 'Lovely', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(86, 37, 'Spontaneous W', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(87, 37, 'Claire Bleu', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(88, 37, 'Sweet', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(89, 37, 'Dynamic', 350.00, 1, '', 0, '2025-03-02 19:52:13', '2025-03-19 21:37:18'),
(90, 38, 'Cantaloupe', 280.00, 1, '', 1, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(91, 38, 'Fruity', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(92, 38, 'Fresh Blossom', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(93, 38, 'Active', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(94, 38, 'Fresh Aqua W', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(95, 38, 'Casual', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(96, 38, 'Le Delice', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(97, 38, 'Sparkling', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(98, 38, 'Elegant', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(99, 38, 'Luscious', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(100, 38, 'Diamant', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(101, 38, 'Candid', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(102, 38, 'Mulberry', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(103, 38, 'Glorious', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(104, 38, 'Chic', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(105, 38, 'Captivating', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(106, 38, 'Stunning', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(107, 38, 'Pleasant', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(108, 38, 'Naive', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(109, 38, 'Seductive', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(110, 38, 'Lovely', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(111, 38, 'Spontaneous W', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(112, 38, 'Claire Bleu', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(113, 38, 'Sweet', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58'),
(114, 38, 'Dynamic', 280.00, 1, '', 0, '2025-03-02 20:03:58', '2025-03-02 20:03:58');

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
(72, 'ironman123', 'pjarahgalias27@gmail.com', '$2y$10$ooz99XqFVVJgxMP7sDAZc.wU1Phl32pbsEn/dvLn8.Mu8ZX5ME0lO', 'Arah', 'Galias', '09669463472', 'Makati', 'user', '../uploads/ey.png', 1, '', '2025-03-05 13:10:47'),
(79, 'Admin', 'galias.pja@gmail.com', '$2y$10$xZh8zzSPbdyxUa7cng7aDu7LsPboC1x29eitjMxjd/gTU0GP3bcL.', 'Arah', 'Galias', '09669463472', 'dyan lang', 'admin', NULL, 1, '', '2025-03-10 19:50:28'),
(89, 'pjarahgalias27', 'princessjamie.galias.cics@ust.edu.ph', '$2y$10$iSWy.9Kp9yJ7mZD4q9/THOGG31y3aRyXI98axhH4Ahb0Vj2MUYfYi', 'Arah', 'Galias', '09669463472', 'Blk 1 Lot 1 Ibañez St', 'user', '../uploads/100ml-frost-glass-bottle-silver-spray-1.png', 1, '', '2025-03-19 07:05:30'),
(90, 'iSmooth', 'judesamonte1@gmail.com', '$2y$10$BE2XGuhaWyQT4MMoAhM.xOY5AhiAa8YVCbMNEX2ZrXPHlssSwCJqS', NULL, NULL, NULL, NULL, 'user', NULL, 0, '818349', '2025-03-20 18:10:25'),
(92, 'SE 2', 'waffles@waffles.com', '$2y$10$kjy4YAoUumSXi8vSxkPa1O35s87tR2HFbC0w.95OUWR0./HWtFXEq', NULL, NULL, NULL, NULL, 'user', NULL, 0, '268114', '2025-03-24 19:25:36'),
(93, 'pjarahgalias', 'waffles1@waffles.com', '$2y$10$GDlceD0pD7lVGdD1ZlySC.xjTy68f3IZKcZLDfE1qG0LUumIt5HwS', NULL, NULL, NULL, NULL, 'user', NULL, 0, '98663', '2025-03-24 19:26:20'),
(95, 'waffles', 'brandonfredrick.gomez.cics@ust.edu.ph', '$2y$10$qt4fgOcIaIUn/G916z3HuOeAkcirArAvqgjO0/NNEomin1SkauV/.', 'Brandon', 'Gomez', '09098412774', 'Taytay, Rizal', 'superadmin', NULL, 1, '', '2025-03-25 07:48:56');

-- --------------------------------------------------------

--
-- Table structure for table `tb_vouchers`
--

CREATE TABLE `tb_vouchers` (
  `voucherID` int(11) NOT NULL,
  `discount` decimal(5,2) NOT NULL,
  `details` varchar(255) NOT NULL,
  `valid_until` date NOT NULL,
  `code` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_vouchers`
--

INSERT INTO `tb_vouchers` (`voucherID`, `discount`, `details`, `valid_until`, `code`, `created_at`, `updated_at`) VALUES
(15, 6.00, 'For sets', '2025-03-27', 'SIX', '2025-03-22 04:04:49', '2025-03-22 14:41:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_bestsellers`
--
ALTER TABLE `tb_bestsellers`
  ADD PRIMARY KEY (`bestseller_id`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `tb_cart`
--
ALTER TABLE `tb_cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `tb_orders`
--
ALTER TABLE `tb_orders`
  ADD PRIMARY KEY (`orderID`);

--
-- Indexes for table `tb_payment_qr_codes`
--
ALTER TABLE `tb_payment_qr_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_type` (`payment_type`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `tb_products`
--
ALTER TABLE `tb_products`
  ADD PRIMARY KEY (`productID`);

--
-- Indexes for table `tb_productvariants`
--
ALTER TABLE `tb_productvariants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `fk_productID` (`productID`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uk_username` (`username`);

--
-- Indexes for table `tb_vouchers`
--
ALTER TABLE `tb_vouchers`
  ADD PRIMARY KEY (`voucherID`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_bestsellers`
--
ALTER TABLE `tb_bestsellers`
  MODIFY `bestseller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tb_cart`
--
ALTER TABLE `tb_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tb_orders`
--
ALTER TABLE `tb_orders`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_payment_qr_codes`
--
ALTER TABLE `tb_payment_qr_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_products`
--
ALTER TABLE `tb_products`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `tb_productvariants`
--
ALTER TABLE `tb_productvariants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `tb_vouchers`
--
ALTER TABLE `tb_vouchers`
  MODIFY `voucherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_bestsellers`
--
ALTER TABLE `tb_bestsellers`
  ADD CONSTRAINT `tb_bestsellers_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `tb_products` (`productID`) ON DELETE CASCADE;

--
-- Constraints for table `tb_payment_qr_codes`
--
ALTER TABLE `tb_payment_qr_codes`
  ADD CONSTRAINT `tb_payment_qr_codes_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `tb_user` (`username`);

--
-- Constraints for table `tb_productvariants`
--
ALTER TABLE `tb_productvariants`
  ADD CONSTRAINT `fk_productID` FOREIGN KEY (`productID`) REFERENCES `tb_products` (`productID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
