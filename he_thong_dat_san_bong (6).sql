-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 26, 2026 lúc 08:25 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `he_thong_dat_san_bong`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdondatsan`
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
-- Đang đổ dữ liệu cho bảng `chitietdondatsan`
--

INSERT INTO `chitietdondatsan` (`idCT`, `idDon`, `idLS`, `ma_san`, `ma_khung_gio`, `ma_chu_san`, `ngay_choi`, `NgaySuDung`, `GioBatDau`, `GioKetThuc`, `SoGio`, `SoLuong`, `GiaBan`) VALUES
(1, 1, 1, 1, 2, 1, '2026-11-24', '2026-11-24', '17:30:00', '19:00:00', 1.50, 1, 375000.00),
(2, 2, 4, 3, 7, 2, '2026-11-25', '2026-11-25', '18:00:00', '19:00:00', 1.00, 1, 120000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chu_san`
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
-- Đang đổ dữ liệu cho bảng `chu_san`
--

INSERT INTO `chu_san` (`ma_chu_san`, `ten_chu_san`, `gmail`, `mat_khau`, `sdt`, `dia_chi`) VALUES
(1, 'Nguyễn Văn Hùng', 'hung.nguyen@gmail.com', '123456', '0905111222', '123 Lê Duẩn, Quận 1, TP.HCM'),
(2, 'Trần Thị Mai', 'mai.tran@gmail.com', '$2y$10$IpFI1otqC0WmtGfWnM3SMuU5oJ7DvcJIGMm0PXegvU2P0m4rSmEwG', '0914333444', '45 Nguyễn Thị Minh Khai, Quận 3, TP.HCM'),
(3, 'Lê Hoàng Nam', 'nam.le@gmail.com', '123456', '0988555666', '789 Phạm Văn Đồng, TP. Thủ Đức'),
(4, 'Phạm Quốc Cường', 'cuong.pham@gmail.com', '123456', '0932777888', '12 Điện Biên Phủ, Bình Thạnh'),
(5, 'Đặng Thu Thảo', 'thao.dang@gmail.com', '123456', '0971999000', '56 Nguyễn Văn Linh, Quận 7, TP.HCM');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dondatsan`
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
-- Đang đổ dữ liệu cho bảng `dondatsan`
--

INSERT INTO `dondatsan` (`idDon`, `idKH`, `ma_chu_san`, `idNV`, `idPT`, `NgayDat`, `TrangThai`, `TongTien`, `PhuongThuc`, `MaCK`, `ThongTinCK`) VALUES
(1, 1, 1, 1, 1, '2026-11-23 21:18:11', 1, 375000.00, 'Chuyển khoản - Vietcombank', 'DS7K2M9Q', 'Vietcombank - 0901234567 - NGUYEN VAN AN'),
(2, 2, 2, NULL, 5, '2026-11-23 21:18:11', 0, 120000.00, 'MoMo', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

CREATE TABLE `gio_hang` (
  `idGH` int(11) NOT NULL,
  `ma_phien` varchar(64) DEFAULT NULL,
  `idKH` int(11) DEFAULT NULL,
  `ma_san` int(11) DEFAULT NULL,
  `ma_khung_gio` int(11) NOT NULL,
  `ngay_choi` date NOT NULL,
  `gio_choi` varchar(30) DEFAULT NULL,
  `ngay_them` datetime NOT NULL DEFAULT current_timestamp(),
  `ngay_tao` datetime DEFAULT current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`idGH`, `ma_phien`, `idKH`, `ma_san`, `ma_khung_gio`, `ngay_choi`, `gio_choi`, `ngay_them`, `ngay_tao`) VALUES
(2, 'ddedfaaq8t627691f8kqq92i0m', NULL, NULL, 1, '2026-11-24', NULL, '2026-11-24 17:30:07', '2026-11-24 17:30:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
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
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`idKH`, `HoTen`, `ten_dang_nhap`, `gmail`, `mat_khau`, `SDT`, `DiaChi`, `Email`) VALUES
(1, 'Nguyễn Văn An', 'an.nguyen', 'an.nguyen@gmail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0901234567', 'Quận 1, TP.HCM', 'an.nguyen@gmail.com'),
(2, 'Trần Thị Bình', NULL, NULL, NULL, '0912345678', 'Quận 7, TP.HCM', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khung_gio`
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
-- Đang đổ dữ liệu cho bảng `khung_gio`
--

INSERT INTO `khung_gio` (`ma_khung_gio`, `ma_san`, `gio_bat_dau`, `gio_ket_thuc`, `gia_moi_gio`, `gio_vang`, `gio_binh_thuong`, `so_gio`, `trang_thai`) VALUES
(1, 1, '06:00:00', '07:30:00', 250000.00, '17:30 - 20:30', '06:00 - 17:30', 1.50, 'Trống'),
(2, 1, '17:30:00', '19:00:00', 250000.00, '17:30 - 20:30', '06:00 - 17:30', 1.50, 'Trống'),
(3, 1, '19:00:00', '20:30:00', 250000.00, '17:30 - 20:30', '06:00 - 17:30', 1.50, 'Trống'),
(4, 2, '08:00:00', '10:00:00', 400000.00, '17:30 - 20:30', '06:00 - 17:30', 2.00, 'Trống'),
(5, 2, '17:30:00', '19:30:00', 400000.00, '17:30 - 20:30', '06:00 - 17:30', 2.00, 'Trống'),
(6, 3, '07:00:00', '08:00:00', 120000.00, '18:00 - 21:00', '07:00 - 18:00', 1.00, 'Trống'),
(7, 3, '18:00:00', '19:00:00', 120000.00, '18:00 - 21:00', '07:00 - 18:00', 1.00, 'Trống'),
(8, 3, '19:00:00', '20:30:00', 120000.00, '18:00 - 21:00', '07:00 - 18:00', 1.50, 'Trống'),
(9, 4, '08:00:00', '09:30:00', 300000.00, '17:00 - 20:00', '06:00 - 17:00', 1.50, 'Trống'),
(10, 5, '09:00:00', '10:30:00', 350000.00, '17:00 - 21:00', '07:00 - 17:00', 1.50, 'Trống'),
(11, 5, '18:00:00', '19:30:00', 350000.00, '17:00 - 21:00', '07:00 - 17:00', 1.50, 'Trống'),
(12, 5, '19:30:00', '21:00:00', 350000.00, '17:00 - 21:00', '07:00 - 17:00', 1.50, 'Tạm khóa'),
(13, 6, '07:00:00', '09:00:00', 180000.00, '18:00 - 21:00', '06:00 - 18:00', 2.00, 'Trống'),
(14, 6, '18:00:00', '20:00:00', 180000.00, '18:00 - 21:00', '06:00 - 18:00', 2.00, 'Trống'),
(15, 7, '08:00:00', '10:00:00', 200000.00, '18:00 - 21:00', '06:00 - 18:00', 2.00, 'Trống'),
(16, 7, '18:00:00', '20:00:00', 200000.00, '18:00 - 21:00', '06:00 - 18:00', 2.00, 'Trống'),
(17, 8, '07:00:00', '08:00:00', 90000.00, '18:00 - 21:00', '06:00 - 18:00', 1.00, 'Trống'),
(18, 8, '19:00:00', '20:00:00', 90000.00, '18:00 - 21:00', '06:00 - 18:00', 1.00, 'Trống'),
(19, 9, '07:00:00', '09:00:00', 950000.00, '18:00 - 21:00', '06:00 - 18:00', 2.00, 'Trống'),
(20, 9, '18:00:00', '20:00:00', 950000.00, '18:00 - 21:00', '06:00 - 18:00', 2.00, 'Trống'),
(21, 10, '07:00:00', '08:30:00', 160000.00, '18:00 - 21:00', '06:00 - 18:00', 1.50, 'Trống'),
(22, 10, '18:00:00', '19:30:00', 160000.00, '18:00 - 21:00', '06:00 - 18:00', 1.50, 'Trống');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaisan`
--

CREATE TABLE `loaisan` (
  `idLS` int(11) NOT NULL,
  `TenLS` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loaisan`
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
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `idNV` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SDT` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`idNV`, `HoTen`, `SDT`) VALUES
(1, 'Lê Quản Lý', '0988888888');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phuongthucthanhtoan`
--

CREATE TABLE `phuongthucthanhtoan` (
  `idKH` int(11) NOT NULL,
  `PhuongThuc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phuongthucthanhtoan`
--

INSERT INTO `phuongthucthanhtoan` (`idKH`, `PhuongThuc`) VALUES
(1, 'Chuyển khoản - Vietcombank'),
(1, 'Tiền mặt'),
(2, 'MoMo');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san`
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
-- Đang đổ dữ liệu cho bảng `san`
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
-- Cấu trúc bảng cho bảng `taikhoan_thanhtoan`
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
-- Đang đổ dữ liệu cho bảng `taikhoan_thanhtoan`
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
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdondatsan`
--
ALTER TABLE `chitietdondatsan`
  ADD PRIMARY KEY (`idCT`),
  ADD KEY `idx_slot` (`ma_khung_gio`,`ngay_choi`),
  ADD KEY `idDon` (`idDon`),
  ADD KEY `idLS` (`idLS`),
  ADD KEY `ma_chu_san` (`ma_chu_san`),
  ADD KEY `idx_ct_san_ngay_gio` (`ma_san`,`NgaySuDung`,`GioBatDau`,`GioKetThuc`);

--
-- Chỉ mục cho bảng `chu_san`
--
ALTER TABLE `chu_san`
  ADD PRIMARY KEY (`ma_chu_san`),
  ADD UNIQUE KEY `gmail` (`gmail`);

--
-- Chỉ mục cho bảng `dondatsan`
--
ALTER TABLE `dondatsan`
  ADD PRIMARY KEY (`idDon`),
  ADD KEY `ma_chu_san` (`ma_chu_san`),
  ADD KEY `idNV` (`idNV`),
  ADD KEY `idPT` (`idPT`),
  ADD KEY `idKH` (`idKH`,`PhuongThuc`);

--
-- Chỉ mục cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`idGH`),
  ADD UNIQUE KEY `uq_gio_phien` (`ma_phien`,`ma_khung_gio`,`ngay_choi`),
  ADD UNIQUE KEY `uq_gio_khach` (`idKH`,`ma_khung_gio`,`ngay_choi`),
  ADD KEY `idx_gio_san` (`ma_san`),
  ADD KEY `ma_khung_gio` (`ma_khung_gio`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`idKH`),
  ADD UNIQUE KEY `SDT` (`SDT`);

--
-- Chỉ mục cho bảng `khung_gio`
--
ALTER TABLE `khung_gio`
  ADD PRIMARY KEY (`ma_khung_gio`),
  ADD KEY `ma_san` (`ma_san`);

--
-- Chỉ mục cho bảng `loaisan`
--
ALTER TABLE `loaisan`
  ADD PRIMARY KEY (`idLS`),
  ADD UNIQUE KEY `TenLS` (`TenLS`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`idNV`);

--
-- Chỉ mục cho bảng `phuongthucthanhtoan`
--
ALTER TABLE `phuongthucthanhtoan`
  ADD PRIMARY KEY (`idKH`,`PhuongThuc`);

--
-- Chỉ mục cho bảng `san`
--
ALTER TABLE `san`
  ADD PRIMARY KEY (`ma_san`),
  ADD KEY `ma_chu_san` (`ma_chu_san`),
  ADD KEY `idLS` (`idLS`);

--
-- Chỉ mục cho bảng `taikhoan_thanhtoan`
--
ALTER TABLE `taikhoan_thanhtoan`
  ADD PRIMARY KEY (`idPT`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chitietdondatsan`
--
ALTER TABLE `chitietdondatsan`
  MODIFY `idCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `chu_san`
--
ALTER TABLE `chu_san`
  MODIFY `ma_chu_san` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `dondatsan`
--
ALTER TABLE `dondatsan`
  MODIFY `idDon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `idGH` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `idKH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `khung_gio`
--
ALTER TABLE `khung_gio`
  MODIFY `ma_khung_gio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `loaisan`
--
ALTER TABLE `loaisan`
  MODIFY `idLS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `idNV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `san`
--
ALTER TABLE `san`
  MODIFY `ma_san` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `taikhoan_thanhtoan`
--
ALTER TABLE `taikhoan_thanhtoan`
  MODIFY `idPT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdondatsan`
--
ALTER TABLE `chitietdondatsan`
  ADD CONSTRAINT `chitietdondatsan_ibfk_1` FOREIGN KEY (`idDon`) REFERENCES `dondatsan` (`idDon`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdondatsan_ibfk_2` FOREIGN KEY (`idLS`) REFERENCES `loaisan` (`idLS`),
  ADD CONSTRAINT `chitietdondatsan_ibfk_3` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`),
  ADD CONSTRAINT `chitietdondatsan_ibfk_4` FOREIGN KEY (`ma_khung_gio`) REFERENCES `khung_gio` (`ma_khung_gio`),
  ADD CONSTRAINT `chitietdondatsan_ibfk_5` FOREIGN KEY (`ma_chu_san`) REFERENCES `chu_san` (`ma_chu_san`);

--
-- Các ràng buộc cho bảng `dondatsan`
--
ALTER TABLE `dondatsan`
  ADD CONSTRAINT `dondatsan_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`),
  ADD CONSTRAINT `dondatsan_ibfk_2` FOREIGN KEY (`ma_chu_san`) REFERENCES `chu_san` (`ma_chu_san`),
  ADD CONSTRAINT `dondatsan_ibfk_3` FOREIGN KEY (`idNV`) REFERENCES `nhanvien` (`idNV`),
  ADD CONSTRAINT `dondatsan_ibfk_4` FOREIGN KEY (`idPT`) REFERENCES `taikhoan_thanhtoan` (`idPT`),
  ADD CONSTRAINT `dondatsan_ibfk_5` FOREIGN KEY (`idKH`,`PhuongThuc`) REFERENCES `phuongthucthanhtoan` (`idKH`, `PhuongThuc`);

--
-- Các ràng buộc cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`) ON DELETE CASCADE,
  ADD CONSTRAINT `gio_hang_ibfk_2` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`),
  ADD CONSTRAINT `gio_hang_ibfk_3` FOREIGN KEY (`ma_khung_gio`) REFERENCES `khung_gio` (`ma_khung_gio`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `khung_gio`
--
ALTER TABLE `khung_gio`
  ADD CONSTRAINT `khung_gio_ibfk_1` FOREIGN KEY (`ma_san`) REFERENCES `san` (`ma_san`);

--
-- Các ràng buộc cho bảng `phuongthucthanhtoan`
--
ALTER TABLE `phuongthucthanhtoan`
  ADD CONSTRAINT `phuongthucthanhtoan_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `san`
--
ALTER TABLE `san`
  ADD CONSTRAINT `san_ibfk_1` FOREIGN KEY (`ma_chu_san`) REFERENCES `chu_san` (`ma_chu_san`),
  ADD CONSTRAINT `san_ibfk_2` FOREIGN KEY (`idLS`) REFERENCES `loaisan` (`idLS`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
