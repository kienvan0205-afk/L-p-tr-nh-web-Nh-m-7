-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2026 at 08:57 PM
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
-- Database: `he_thong_dat_san_bong`
--

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia_san`
--

CREATE TABLE `danh_gia_san` (
  `id_danh_gia` int(11) NOT NULL,
  `ma_san` int(11) DEFAULT NULL,
  `ten_nguoi_danh_gia` varchar(60) NOT NULL,
  `so_sao` tinyint(4) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_danh_gia` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `danh_gia_san`
--

INSERT INTO `danh_gia_san` (`id_danh_gia`, `ma_san`, `ten_nguoi_danh_gia`, `so_sao`, `noi_dung`, `ngay_danh_gia`) VALUES
(1, 1, 'Ngocj', 5, 'San thoang mat', '2026-11-28 01:09:49'),
(2, 1, 'mickey', 2, 'bình thường', '2026-11-28 02:26:45'),
(3, 1, 'kitty', 5, 'sạch sẽ nói chung ok', '2026-11-28 02:39:02'),
(4, 5, 'kitty', 5, 'ok', '2026-11-28 02:39:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `danh_gia_san`
--
ALTER TABLE `danh_gia_san`
  ADD PRIMARY KEY (`id_danh_gia`),
  ADD KEY `idx_danh_gia_ma_san` (`ma_san`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `danh_gia_san`
--
ALTER TABLE `danh_gia_san`
  MODIFY `id_danh_gia` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `danh_gia_san`
--
ALTER TABLE `danh_gia_san`
  ADD CONSTRAINT `fk_danh_gia_san` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
