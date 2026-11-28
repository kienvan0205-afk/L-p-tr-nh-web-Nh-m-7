-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2026 at 05:30 PM
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
-- Table structure for table `chitietdondatsan`
--

CREATE TABLE `chitietdondatsan` (
  `idCT` int(11) NOT NULL,
  `idDon` int(11) NOT NULL,
  `idLS` int(11) NOT NULL,
  `ma_san` int(11) NOT NULL,
  `ma_khung_gio` int(11) NOT NULL,
  `ma_chu_san` int(11) NOT NULL,
  `ngay_choi` date NOT NULL,
  `NgaySuDung` date DEFAULT NULL,
  `GioBatDau` time DEFAULT NULL,
  `GioKetThuc` time DEFAULT NULL,
  `SoGio` decimal(10,2) DEFAULT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 1,
  `GiaBan` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `chitietdondatsan`
--

INSERT INTO `chitietdondatsan` (`idCT`, `idDon`, `idLS`, `ma_san`, `ma_khung_gio`, `ma_chu_san`, `ngay_choi`, `NgaySuDung`, `GioBatDau`, `GioKetThuc`, `SoGio`, `SoLuong`, `GiaBan`) VALUES
(1, 1, 1, 1, 2, 1, '2026-11-24', '2026-11-24', '17:30:00', '19:00:00', 1.50, 1, 375000.00),
(2, 2, 4, 3, 7, 2, '2026-11-25', '2026-11-25', '18:00:00', '19:00:00', 1.00, 1, 120000.00);

-- --------------------------------------------------------

--
-- Table structure for table `chu_san`
--

CREATE TABLE `chu_san` (
  `ma_chu_san` int(11) NOT NULL,
  `ten_chu_san` varchar(100) NOT NULL,
  `gmail` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `sdt` varchar(20) NOT NULL,
  `dia_chi` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `chu_san`
--

INSERT INTO `chu_san` (`ma_chu_san`, `ten_chu_san`, `gmail`, `mat_khau`, `sdt`, `dia_chi`) VALUES
(1, 'Nguyễn Văn Hùng', 'hung.nguyen@gmail.com', '123456', '0905111222', '123 Lê Duẩn, Quận 1, TP.HCM'),
(2, 'Trần Thị Mai', 'mai.tran@gmail.com', '$2y$10$IpFI1otqC0WmtGfWnM3SMuU5oJ7DvcJIGMm0PXegvU2P0m4rSmEwG', '0914333444', '45 Nguyễn Thị Minh Khai, Quận 3, TP.HCM'),
(3, 'Lê Hoàng Nam', 'nam.le@gmail.com', '123456', '0988555666', '789 Phạm Văn Đồng, TP. Thủ Đức'),
(4, 'Phạm Quốc Cường', 'cuong.pham@gmail.com', '123456', '0932777888', '12 Điện Biên Phủ, Bình Thạnh'),
(5, 'Đặng Thu Thảo', 'thao.dang@gmail.com', '123456', '0971999000', '56 Nguyễn Văn Linh, Quận 7, TP.HCM');

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

-- --------------------------------------------------------

--
-- Table structure for table `dondatsan`
--

CREATE TABLE `dondatsan` (
  `idDon` int(11) NOT NULL,
  `idKH` int(11) NOT NULL,
  `ma_chu_san` int(11) NOT NULL,
  `idNV` int(11) DEFAULT NULL,
  `idPT` int(11) DEFAULT NULL,
  `NgayDat` datetime NOT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Chờ xác nhận, 1: Đã xác nhận, 2: Đã hoàn thành, 3: Đã hủy',
  `TongTien` decimal(12,2) NOT NULL DEFAULT 0.00,
  `PhuongThuc` varchar(50) DEFAULT NULL,
  `MaCK` varchar(20) DEFAULT NULL,
  `ThongTinCK` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `dondatsan`
--

INSERT INTO `dondatsan` (`idDon`, `idKH`, `ma_chu_san`, `idNV`, `idPT`, `NgayDat`, `TrangThai`, `TongTien`, `PhuongThuc`, `MaCK`, `ThongTinCK`) VALUES
(1, 1, 1, 1, 1, '2026-11-23 21:18:11', 1, 375000.00, 'Chuyển khoản - Vietcombank', 'DS7K2M9Q', 'Vietcombank - 0901234567 - NGUYEN VAN AN'),
(2, 2, 2, NULL, 5, '2026-11-23 21:18:11', 0, 120000.00, 'MoMo', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang`
--

CREATE TABLE `gio_hang` (
  `idGH` int(11) NOT NULL,
  `ma_phien` varchar(64) DEFAULT NULL,
  `idKH` int(11) DEFAULT NULL,
  `ma_san` int(11) DEFAULT NULL,
  `ma_khung_gio` int(11) NOT NULL,
  `ngay_choi` date NOT NULL,
  `gio_bat_dau` time NOT NULL,
  `gio_ket_thuc` time NOT NULL,
  `gio_choi` varchar(30) DEFAULT NULL,
  `ngay_them` datetime NOT NULL DEFAULT current_timestamp(),
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gio_hang`
--

INSERT INTO `gio_hang` (`idGH`, `ma_phien`, `idKH`, `ma_san`, `ma_khung_gio`, `ngay_choi`, `gio_bat_dau`, `gio_ket_thuc`, `gio_choi`, `ngay_them`, `ngay_tao`) VALUES
(2, 'ddedfaaq8t627691f8kqq92i0m', NULL, NULL, 1, '2026-11-24', '00:00:00', '00:00:00', NULL, '2026-11-24 17:30:07', '2026-11-24 17:30:07'),
(3, 'uf9vj0vhif4qtlj3dcgh4olf8j', NULL, 1, 24, '2026-11-29', '11:30:00', '12:30:00', NULL, '2026-11-28 23:23:53', '2026-11-28 23:23:53'),
(4, 'uf9vj0vhif4qtlj3dcgh4olf8j', NULL, 6, 42, '2026-11-29', '13:30:00', '14:00:00', NULL, '2026-11-28 23:24:26', '2026-11-28 23:24:26');

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `idKH` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `ten_dang_nhap` varchar(30) DEFAULT NULL,
  `gmail` varchar(100) DEFAULT NULL,
  `mat_khau` varchar(255) DEFAULT NULL,
  `SDT` varchar(15) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`idKH`, `HoTen`, `ten_dang_nhap`, `gmail`, `mat_khau`, `SDT`, `DiaChi`, `Email`) VALUES
(1, 'Nguyễn Văn An', 'an.nguyen', 'an.nguyen@gmail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0901234567', 'Quận 1, TP.HCM', 'an.nguyen@gmail.com'),
(2, 'Trần Thị Bình', NULL, NULL, NULL, '0912345678', 'Quận 7, TP.HCM', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `khung_gio`
--

CREATE TABLE `khung_gio` (
  `ma_khung_gio` int(11) NOT NULL,
  `ma_san` int(11) NOT NULL,
  `gio_bat_dau` time NOT NULL,
  `gio_ket_thuc` time NOT NULL,
  `gia_moi_gio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gio_vang` varchar(225) NOT NULL,
  `gio_binh_thuong` text NOT NULL,
  `so_gio` decimal(10,2) NOT NULL,
  `trang_thai` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `khung_gio`
--

INSERT INTO `khung_gio` (`ma_khung_gio`, `ma_san`, `gio_bat_dau`, `gio_ket_thuc`, `gia_moi_gio`, `gio_vang`, `gio_binh_thuong`, `so_gio`, `trang_thai`) VALUES
(1, 1, '06:00:00', '08:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(2, 1, '17:00:00', '21:00:00', 350000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(3, 1, '21:00:00', '22:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(4, 2, '08:00:00', '11:00:00', 400000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(5, 2, '17:00:00', '21:00:00', 550000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(6, 3, '06:00:00', '08:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(7, 3, '17:00:00', '21:00:00', 130000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(8, 3, '21:00:00', '22:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(9, 4, '08:00:00', '11:00:00', 250000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(10, 5, '08:00:00', '11:00:00', 280000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(11, 5, '17:00:00', '21:00:00', 380000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(12, 5, '21:00:00', '22:00:00', 200000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(13, 6, '06:00:00', '08:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(14, 6, '17:00:00', '21:00:00', 220000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(15, 7, '08:00:00', '11:00:00', 170000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(16, 7, '17:00:00', '21:00:00', 230000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(17, 8, '06:00:00', '08:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(18, 8, '17:00:00', '21:00:00', 110000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(19, 9, '06:00:00', '08:00:00', 700000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(20, 9, '17:00:00', '21:00:00', 1200000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(21, 10, '06:00:00', '08:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(22, 10, '17:00:00', '21:00:00', 180000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(23, 1, '08:00:00', '11:00:00', 250000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(24, 1, '11:00:00', '14:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(25, 1, '14:00:00', '17:00:00', 250000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(26, 2, '06:00:00', '08:00:00', 300000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(27, 2, '11:00:00', '14:00:00', 300000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(28, 2, '14:00:00', '17:00:00', 400000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(29, 2, '21:00:00', '22:00:00', 300000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(30, 3, '08:00:00', '11:00:00', 90000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(31, 3, '11:00:00', '14:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(32, 3, '14:00:00', '17:00:00', 90000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(33, 4, '06:00:00', '08:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(34, 4, '11:00:00', '14:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(35, 4, '14:00:00', '17:00:00', 250000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(36, 4, '17:00:00', '21:00:00', 350000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống'),
(37, 4, '21:00:00', '22:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(38, 5, '06:00:00', '08:00:00', 200000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(39, 5, '11:00:00', '14:00:00', 200000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(40, 5, '14:00:00', '17:00:00', 280000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(41, 6, '08:00:00', '11:00:00', 150000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(42, 6, '11:00:00', '14:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(43, 6, '14:00:00', '17:00:00', 150000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(44, 6, '21:00:00', '22:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(45, 7, '06:00:00', '08:00:00', 120000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống'),
(46, 7, '11:00:00', '14:00:00', 120000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(47, 7, '14:00:00', '17:00:00', 170000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(48, 7, '21:00:00', '22:00:00', 120000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(49, 8, '08:00:00', '11:00:00', 80000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(50, 8, '11:00:00', '14:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(51, 8, '14:00:00', '17:00:00', 80000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(52, 8, '21:00:00', '22:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(53, 9, '08:00:00', '11:00:00', 950000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(54, 9, '11:00:00', '14:00:00', 700000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(55, 9, '14:00:00', '17:00:00', 950000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(56, 9, '21:00:00', '22:00:00', 700000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(57, 10, '08:00:00', '11:00:00', 140000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(58, 10, '11:00:00', '14:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống'),
(59, 10, '14:00:00', '17:00:00', 140000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống'),
(60, 10, '21:00:00', '22:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(61, 1, '22:00:00', '23:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(62, 2, '22:00:00', '23:00:00', 300000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(63, 3, '22:00:00', '23:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(64, 6, '22:00:00', '23:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(65, 4, '22:00:00', '23:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(66, 9, '22:00:00', '23:00:00', 700000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(67, 5, '22:00:00', '23:00:00', 200000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(68, 8, '22:00:00', '23:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(69, 7, '22:00:00', '23:00:00', 120000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống'),
(70, 10, '22:00:00', '23:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống');

-- --------------------------------------------------------

--
-- Table structure for table `loaisan`
--

CREATE TABLE `loaisan` (
  `idLS` int(11) NOT NULL,
  `TenLS` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `loaisan`
--

INSERT INTO `loaisan` (`idLS`, `TenLS`) VALUES
(3, 'Sân 11'),
(1, 'Sân 5'),
(2, 'Sân 7'),
(8, 'Sân bóng bàn'),
(7, 'Sân bóng chuyền'),
(6, 'Sân bóng rổ'),
(4, 'Sân cầu lông'),
(9, 'Sân Pickleball'),
(5, 'Sân tennis');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

CREATE TABLE `nhanvien` (
  `idNV` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SDT` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `nhanvien`
--

INSERT INTO `nhanvien` (`idNV`, `HoTen`, `SDT`) VALUES
(1, 'Lê Quản Lý', '0988888888');

-- --------------------------------------------------------

--
-- Table structure for table `phuongthucthanhtoan`
--

CREATE TABLE `phuongthucthanhtoan` (
  `idKH` int(11) NOT NULL,
  `PhuongThuc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `phuongthucthanhtoan`
--

INSERT INTO `phuongthucthanhtoan` (`idKH`, `PhuongThuc`) VALUES
(1, 'Chuyển khoản - Vietcombank'),
(1, 'Tiền mặt'),
(2, 'MoMo');

-- --------------------------------------------------------

--
-- Table structure for table `san`
--

CREATE TABLE `san` (
  `ma_san` int(11) NOT NULL,
  `ma_chu_san` int(11) NOT NULL,
  `idLS` int(11) DEFAULT NULL,
  `loai_san` varchar(225) NOT NULL,
  `ten_san` varchar(225) DEFAULT NULL,
  `kieu_san` varchar(30) NOT NULL DEFAULT 'Ngoài trời',
  `mo_ta` text NOT NULL,
  `gia` decimal(10,2) NOT NULL,
  `trang_thai` varchar(225) NOT NULL,
  `hinh_anh` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `san`
--

INSERT INTO `san` (`ma_san`, `ma_chu_san`, `idLS`, `loai_san`, `ten_san`, `kieu_san`, `mo_ta`, `gia`, `trang_thai`, `hinh_anh`) VALUES
(1, 1, 1, 'Sân bóng đá 5 người', 'Sân bóng đá 5 người', 'Ngoài trời', 'Sân cỏ nhân tạo chất lượng cao, có hệ thống đèn LED', 250000.00, 'Hoạt động', 'images/san1.jpg'),
(2, 1, 2, 'Sân bóng đá 7 người', 'Sân bóng đá 7 người', 'Ngoài trời', 'Sân rộng rãi, đạt chuẩn thi đấu giao hữu', 400000.00, 'Hoạt động', 'images/san2.jpg'),
(3, 2, 4, 'Sân cầu lông', 'Sân cầu lông', 'Trong nhà', 'Sân thảm PVC chống trượt, mái che kiên cố', 120000.00, 'Hoạt động', 'images/san3.jpg'),
(4, 3, 5, 'Sân tennis', 'Sân tennis', 'Ngoài trời', 'Mặt sân chuẩn cứng, trang bị đầy đủ lưới và ghế nghỉ', 300000.00, 'Bảo trì', 'images/san4.jpg'),
(5, 4, 6, 'Sân bóng rổ', 'Sân bóng rổ', 'Trong nhà', 'Sân gỗ cao cấp trong nhà, rổ nâng hạ chiều cao', 350000.00, 'Hoạt động', 'images/san5.jpg'),
(6, 2, 9, 'Sân Pickleball', 'Sân Pickleball Trẻ Trung S-Club', 'Ngoài trời', 'Bộ môn thể thao thời thượng hot nhất hiện nay, bề mặt sân sơn nhám giảm độ nảy, có sẵn vợt và bóng thi đấu.', 180000.00, 'Hoạt động', 'images/san6.jpg'),
(7, 5, 7, 'Sân bóng chuyền', 'Sân Bóng Chuyền Tiêu Chuẩn', 'Trong nhà', 'Sân thi đấu trong nhà có mái che, lưới chuẩn quốc gia và hệ thống vạch kẻ rõ nét, có dịch vụ tắm nóng lạnh.', 200000.00, 'Hoạt động', 'images/san7.jpg'),
(8, 4, 8, 'Sân bóng bàn VIP', 'Sân Bóng Bàn VIP PingPong Club', 'Trong nhà', 'Khu vực phòng bóng bàn máy lạnh độc lập, bàn Double Fish ITTF, sàn cao su chống mỏi chân khi thi đấu dài.', 90000.00, 'Hoạt động', 'images/san8.jpg'),
(9, 3, 3, 'Sân bóng đá 11 người', 'Sân Bóng Đá 11 Người SVĐ Hoàng Gia', 'Ngoài trời', 'Sân cỏ tự nhiên quy mô lớn 105mx68m đạt chuẩn quốc tế, khán đài 500 chỗ ngồi thích hợp tổ chức giải phong trào.', 950000.00, 'Hoạt động', 'images/san9.jpg'),
(10, 5, 4, 'Sân cầu lông VIP máy lạnh', 'Sân Cầu Lông VIP Máy Lạnh Yonex', 'Trong nhà', 'Sân đơn biệt lập đạt chuẩn Yonex, lắp đặt máy điều hòa nhiệt độ ổn định 25 độ C, phục vụ thi đấu chuyên sâu.', 160000.00, 'Hoạt động', 'images/san10.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan_thanhtoan`
--

CREATE TABLE `taikhoan_thanhtoan` (
  `idPT` int(11) NOT NULL,
  `loai` enum('ngan_hang','vi_dien_tu','tien_mat') NOT NULL,
  `ten` varchar(50) NOT NULL,
  `ma_bin` varchar(10) DEFAULT NULL,
  `so_tk` varchar(30) NOT NULL DEFAULT '',
  `chu_tk` varchar(100) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `qr_anh` varchar(100) DEFAULT NULL,
  `qr_noidung` varchar(255) DEFAULT NULL,
  `hoat_dong` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `taikhoan_thanhtoan`
--

INSERT INTO `taikhoan_thanhtoan` (`idPT`, `loai`, `ten`, `ma_bin`, `so_tk`, `chu_tk`, `logo`, `qr_anh`, `qr_noidung`, `hoat_dong`) VALUES
(1, 'ngan_hang', 'Vietcombank', '970436', '0123456789', 'SAN THE THAO VIET', 'vietcombank.svg', NULL, NULL, 1),
(2, 'ngan_hang', 'MB Bank', '970422', '0987654321', 'SAN THE THAO VIET', 'mbbank.svg', NULL, NULL, 1),
(3, 'ngan_hang', 'Techcombank', '970407', '19036789012', 'SAN THE THAO VIET', 'techcombank.svg', NULL, NULL, 1),
(4, 'ngan_hang', 'BIDV', '970418', '31410001234567', 'SAN THE THAO VIET', 'bidv.svg', NULL, NULL, 1),
(5, 'vi_dien_tu', 'MoMo', NULL, '0905111222', 'SAN THE THAO VIET', 'momo.svg', NULL, 'https://nhantien.momo.vn/0905111222', 1),
(6, 'vi_dien_tu', 'ZaloPay', NULL, '0905111222', 'SAN THE THAO VIET', 'zalopay.svg', NULL, 'ZALOPAY|0905111222|SAN THE THAO VIET', 1),
(7, 'vi_dien_tu', 'ShopeePay', NULL, '0905111222', 'SAN THE THAO VIET', 'shopeepay.svg', NULL, 'SHOPEEPAY|0905111222|SAN THE THAO VIET', 1),
(8, 'tien_mat', 'Đặt cọc tiền mặt tại sân', NULL, '', 'Quầy lễ tân', 'tienmat.svg', NULL, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chitietdondatsan`
--
ALTER TABLE `chitietdondatsan`
  ADD PRIMARY KEY (`idCT`),
  ADD KEY `idx_slot` (`ma_khung_gio`,`ngay_choi`),
  ADD KEY `idDon` (`idDon`),
  ADD KEY `idLS` (`idLS`),
  ADD KEY `ma_chu_san` (`ma_chu_san`),
  ADD KEY `idx_ct_san_ngay_gio` (`ma_san`,`NgaySuDung`,`GioBatDau`,`GioKetThuc`);

--
-- Indexes for table `chu_san`
--
ALTER TABLE `chu_san`
  ADD PRIMARY KEY (`ma_chu_san`),
  ADD UNIQUE KEY `gmail` (`gmail`);

--
-- Indexes for table `danh_gia_san`
--
ALTER TABLE `danh_gia_san`
  ADD PRIMARY KEY (`id_danh_gia`),
  ADD KEY `idx_danh_gia_ma_san` (`ma_san`);

--
-- Indexes for table `dondatsan`
--
ALTER TABLE `dondatsan`
  ADD PRIMARY KEY (`idDon`),
  ADD KEY `ma_chu_san` (`ma_chu_san`),
  ADD KEY `idNV` (`idNV`),
  ADD KEY `idPT` (`idPT`),
  ADD KEY `idKH` (`idKH`,`PhuongThuc`);

--
-- Indexes for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`idGH`),
  ADD UNIQUE KEY `uq_gio_phien` (`ma_phien`,`ma_khung_gio`,`ngay_choi`),
  ADD UNIQUE KEY `uq_gio_khach` (`idKH`,`ma_khung_gio`,`ngay_choi`),
  ADD KEY `idx_gio_san` (`ma_san`),
  ADD KEY `ma_khung_gio` (`ma_khung_gio`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`idKH`),
  ADD UNIQUE KEY `SDT` (`SDT`);

--
-- Indexes for table `khung_gio`
--
ALTER TABLE `khung_gio`
  ADD PRIMARY KEY (`ma_khung_gio`),
  ADD KEY `ma_san` (`ma_san`);

--
-- Indexes for table `loaisan`
--
ALTER TABLE `loaisan`
  ADD PRIMARY KEY (`idLS`),
  ADD UNIQUE KEY `TenLS` (`TenLS`);

--
-- Indexes for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`idNV`);

--
-- Indexes for table `phuongthucthanhtoan`
--
ALTER TABLE `phuongthucthanhtoan`
  ADD PRIMARY KEY (`idKH`,`PhuongThuc`);

--
-- Indexes for table `san`
--
ALTER TABLE `san`
  ADD PRIMARY KEY (`ma_san`),
  ADD KEY `ma_chu_san` (`ma_chu_san`),
  ADD KEY `idLS` (`idLS`);

--
-- Indexes for table `taikhoan_thanhtoan`
--
ALTER TABLE `taikhoan_thanhtoan`
  ADD PRIMARY KEY (`idPT`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chitietdondatsan`
--
ALTER TABLE `chitietdondatsan`
  MODIFY `idCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chu_san`
--
ALTER TABLE `chu_san`
  MODIFY `ma_chu_san` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `danh_gia_san`
--
ALTER TABLE `danh_gia_san`
  MODIFY `id_danh_gia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dondatsan`
--
ALTER TABLE `dondatsan`
  MODIFY `idDon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `idGH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `idKH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `khung_gio`
--
ALTER TABLE `khung_gio`
  MODIFY `ma_khung_gio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `loaisan`
--
ALTER TABLE `loaisan`
  MODIFY `idLS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `idNV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `san`
--
ALTER TABLE `san`
  MODIFY `ma_san` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `taikhoan_thanhtoan`
--
ALTER TABLE `taikhoan_thanhtoan`
  MODIFY `idPT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chitietdondatsan`
--
ALTER TABLE `chitietdondatsan`
  ADD CONSTRAINT `chitietdondatsan_ibfk_1` FOREIGN KEY (`idDon`) REFERENCES `dondatsan` (`idDon`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdondatsan_ibfk_2` FOREIGN KEY (`idLS`) REFERENCES `loaisan` (`idLS`),
  ADD CONSTRAINT `chitietdondatsan_ibfk_3` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`),
  ADD CONSTRAINT `chitietdondatsan_ibfk_4` FOREIGN KEY (`ma_khung_gio`) REFERENCES `khung_gio` (`ma_khung_gio`),
  ADD CONSTRAINT `chitietdondatsan_ibfk_5` FOREIGN KEY (`ma_chu_san`) REFERENCES `chu_san` (`ma_chu_san`);

--
-- Constraints for table `danh_gia_san`
--
ALTER TABLE `danh_gia_san`
  ADD CONSTRAINT `fk_danh_gia_san` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dondatsan`
--
ALTER TABLE `dondatsan`
  ADD CONSTRAINT `dondatsan_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`),
  ADD CONSTRAINT `dondatsan_ibfk_2` FOREIGN KEY (`ma_chu_san`) REFERENCES `chu_san` (`ma_chu_san`),
  ADD CONSTRAINT `dondatsan_ibfk_3` FOREIGN KEY (`idNV`) REFERENCES `nhanvien` (`idNV`),
  ADD CONSTRAINT `dondatsan_ibfk_4` FOREIGN KEY (`idPT`) REFERENCES `taikhoan_thanhtoan` (`idPT`),
  ADD CONSTRAINT `dondatsan_ibfk_5` FOREIGN KEY (`idKH`,`PhuongThuc`) REFERENCES `phuongthucthanhtoan` (`idKH`, `PhuongThuc`);

--
-- Constraints for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`) ON DELETE CASCADE,
  ADD CONSTRAINT `gio_hang_ibfk_2` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`),
  ADD CONSTRAINT `gio_hang_ibfk_3` FOREIGN KEY (`ma_khung_gio`) REFERENCES `khung_gio` (`ma_khung_gio`) ON DELETE CASCADE;

--
-- Constraints for table `khung_gio`
--
ALTER TABLE `khung_gio`
  ADD CONSTRAINT `khung_gio_ibfk_1` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`);

--
-- Constraints for table `phuongthucthanhtoan`
--
ALTER TABLE `phuongthucthanhtoan`
  ADD CONSTRAINT `phuongthucthanhtoan_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`) ON DELETE CASCADE;

--
-- Constraints for table `san`
--
ALTER TABLE `san`
  ADD CONSTRAINT `san_ibfk_1` FOREIGN KEY (`ma_chu_san`) REFERENCES `chu_san` (`ma_chu_san`),
  ADD CONSTRAINT `san_ibfk_2` FOREIGN KEY (`idLS`) REFERENCES `loaisan` (`idLS`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
