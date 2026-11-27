-- =====================================================================
-- DATABASE HỆ THỐNG ĐẶT SÂN BÓNG – khớp ERD của nhóm (9 bảng)
-- Phần của mình: GIỎ HÀNG (gio_hang) + PHƯƠNG THỨC THANH TOÁN (phuongthucthanhtoan)
-- Các chỗ khác so với SQL cũ của nhóm đều có chú thích  -- [CẬP NHẬT n]
-- Chạy trong phpMyAdmin (tab SQL) hoặc: mysql -u root < database.sql
-- =====================================================================
DROP DATABASE IF EXISTS he_thong_dat_san_bong;
CREATE DATABASE he_thong_dat_san_bong CHARACTER SET=utf8 COLLATE=utf8_general_ci;
USE he_thong_dat_san_bong;

-- 1. Chủ sân -----------------------------------------------------------
CREATE TABLE chu_san (
  ma_chu_san INT AUTO_INCREMENT NOT NULL,
  ten_chu_san VARCHAR(100) NOT NULL,
  gmail VARCHAR(100) NOT NULL,
  mat_khau VARCHAR(255) NOT NULL,
  sdt VARCHAR(20) NOT NULL,
  dia_chi VARCHAR(225) NOT NULL,
  PRIMARY KEY (ma_chu_san),
  UNIQUE KEY gmail (gmail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Loại sân ----------------------------------------------------------
CREATE TABLE loaisan (
  idLS INT AUTO_INCREMENT NOT NULL,
  TenLS VARCHAR(50) NOT NULL,
  PRIMARY KEY (idLS),
  UNIQUE KEY TenLS (TenLS)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3. Sân (chu_san 1-N san: "Quản lý sân") -------------------------------
CREATE TABLE san (
  ma_san INT AUTO_INCREMENT NOT NULL,
  ma_chu_san INT NOT NULL,
  idLS INT DEFAULT NULL,                         -- [CẬP NHẬT 1] nối sân với loại sân
  loai_san VARCHAR(225) NOT NULL,
  mo_ta TEXT NOT NULL,
  gia DECIMAL(10,2) NOT NULL,
  trang_thai VARCHAR(225) NOT NULL,
  hinh_anh VARCHAR(225) NOT NULL,
  PRIMARY KEY (ma_san),
  FOREIGN KEY (ma_chu_san) REFERENCES chu_san(ma_chu_san),
  FOREIGN KEY (idLS) REFERENCES loaisan(idLS)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4. Khung giờ (san 1-N khung_gio: "Có khung giờ") ------------------------
CREATE TABLE khung_gio (
  ma_khung_gio INT AUTO_INCREMENT NOT NULL,
  ma_san INT NOT NULL,
  gio_bat_dau TIME NOT NULL,                     -- [CẬP NHẬT 2] giờ bắt đầu / kết thúc dạng TIME
  gio_ket_thuc TIME NOT NULL,                    --               để kiểm tra trùng giờ và tính tiền
  gio_vang VARCHAR(225) NOT NULL,
  gio_binh_thuong TEXT NOT NULL,
  so_gio DECIMAL(10,2) NOT NULL,
  trang_thai VARCHAR(225) NOT NULL,              -- 'Trống' | 'Tạm khóa' (chủ sân khóa khung này)
  PRIMARY KEY (ma_khung_gio),
  FOREIGN KEY (ma_san) REFERENCES san(ma_san)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 5. Khách hàng ----------------------------------------------------------
CREATE TABLE khachhang (
  idKH INT AUTO_INCREMENT NOT NULL,
  HoTen VARCHAR(100) NOT NULL,
  SDT VARCHAR(15) NOT NULL,
  DiaChi VARCHAR(255) DEFAULT NULL,
  Email VARCHAR(100) DEFAULT NULL,               -- [CẬP NHẬT 3] form "Xác nhận đơn" có ô Email
  PRIMARY KEY (idKH),
  UNIQUE KEY SDT (SDT)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 6. Phương thức thanh toán (khachhang 1-N, khóa chính kép idKH + PhuongThuc)
CREATE TABLE phuongthucthanhtoan (
  idKH INT NOT NULL,
  PhuongThuc VARCHAR(50) NOT NULL,               -- 'Tiền mặt' | 'Chuyển khoản - <ngân hàng>' | tên ví: 'MoMo', 'ZaloPay', 'ShopeePay', 'VNPAY', 'Viettel Money'
  PRIMARY KEY (idKH, PhuongThuc),
  FOREIGN KEY (idKH) REFERENCES khachhang(idKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 7. Nhân viên -----------------------------------------------------------
CREATE TABLE nhanvien (
  idNV INT AUTO_INCREMENT NOT NULL,
  HoTen VARCHAR(100) NOT NULL,
  SDT VARCHAR(15) DEFAULT NULL,
  PRIMARY KEY (idNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 8. Đơn đặt sân ---------------------------------------------------------
--   khachhang "Tạo đơn", chu_san "Phụ trách đơn", nhanvien "Tiếp nhận / xác nhận"
CREATE TABLE dondatsan (
  idDon INT AUTO_INCREMENT NOT NULL,
  idKH INT NOT NULL,
  ma_chu_san INT NOT NULL,                       -- theo ERD: mỗi đơn thuộc về MỘT chủ sân
  idNV INT DEFAULT NULL,                         -- NULL = chưa có nhân viên tiếp nhận
  NgayDat DATETIME NOT NULL,
  TrangThai TINYINT NOT NULL DEFAULT 0 COMMENT '0: Chờ xác nhận, 1: Đã xác nhận, 2: Đã hoàn thành, 3: Đã hủy',
  TongTien DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  PhuongThuc VARCHAR(50) DEFAULT NULL,           -- [CẬP NHẬT 4] phương thức khách chọn cho đơn này
  MaCK VARCHAR(20) DEFAULT NULL,                 -- [CẬP NHẬT 7] mã chuyển khoản (nội dung CK); các đơn tách theo chủ sân của cùng 1 lần thanh toán dùng chung
  ThongTinCK VARCHAR(120) DEFAULT NULL,          -- [CẬP NHẬT 7] ngân hàng - số TK - tên chủ TK khách dùng để chuyển
  PRIMARY KEY (idDon),
  FOREIGN KEY (idKH) REFERENCES khachhang(idKH),
  FOREIGN KEY (ma_chu_san) REFERENCES chu_san(ma_chu_san),
  FOREIGN KEY (idNV) REFERENCES nhanvien(idNV),
  -- [CẬP NHẬT 4] đơn chỉ được dùng phương thức mà khách đã đăng ký trong phuongthucthanhtoan
  FOREIGN KEY (idKH, PhuongThuc) REFERENCES phuongthucthanhtoan(idKH, PhuongThuc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 9. Chi tiết đơn đặt sân (mỗi dòng = 1 khung giờ của 1 sân trong 1 ngày) -----
CREATE TABLE chitietdondatsan (
  idCT INT AUTO_INCREMENT NOT NULL,              -- [CẬP NHẬT 5] khóa chính thay thế
  idDon INT NOT NULL,
  idLS INT NOT NULL,
  ma_san INT NOT NULL,
  ma_khung_gio INT NOT NULL,
  ma_chu_san INT NOT NULL,
  ngay_choi DATE NOT NULL,                       -- [CẬP NHẬT 6] ERD chưa có ngày chơi
  SoLuong INT NOT NULL DEFAULT 1,
  GiaBan DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (idCT),
  KEY idx_slot (ma_khung_gio, ngay_choi),
  FOREIGN KEY (idDon) REFERENCES dondatsan(idDon) ON DELETE CASCADE,
  FOREIGN KEY (idLS) REFERENCES loaisan(idLS),
  FOREIGN KEY (ma_san) REFERENCES san(ma_san),
  FOREIGN KEY (ma_khung_gio) REFERENCES khung_gio(ma_khung_gio),
  FOREIGN KEY (ma_chu_san) REFERENCES chu_san(ma_chu_san)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 10. GIỎ HÀNG – bảng mới (phần của mình) --------------------------------------
--   Khách chọn (khung giờ + ngày) cho vào giỏ trước, bấm Thanh toán mới sinh dondatsan.
CREATE TABLE gio_hang (
  idGH INT AUTO_INCREMENT NOT NULL,
  ma_phien VARCHAR(64) NOT NULL,                 -- session_id(): chưa cần đăng nhập
  ma_khung_gio INT NOT NULL,
  ngay_choi DATE NOT NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idGH),
  UNIQUE KEY uq_gio (ma_phien, ma_khung_gio, ngay_choi),   -- không thêm trùng 1 khung giờ
  FOREIGN KEY (ma_khung_gio) REFERENCES khung_gio(ma_khung_gio) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- =====================================================================
-- DỮ LIỆU MẪU
-- =====================================================================
INSERT INTO chu_san (ten_chu_san, gmail, mat_khau, sdt, dia_chi) VALUES
('Nguyễn Văn Hùng', 'hung.nguyen@gmail.com', '123456', '0905111222', '123 Lê Duẩn, Quận 1, TP.HCM'),
('Trần Thị Mai', 'mai.tran@gmail.com', '123456', '0914333444', '45 Nguyễn Thị Minh Khai, Quận 3, TP.HCM'),
('Lê Hoàng Nam', 'nam.le@gmail.com', '123456', '0988555666', '789 Phạm Văn Đồng, TP. Thủ Đức'),
('Phạm Quốc Cường', 'cuong.pham@gmail.com', '123456', '0932777888', '12 Điện Biên Phủ, Bình Thạnh'),
('Đặng Thu Thảo', 'thao.dang@gmail.com', '123456', '0971999000', '56 Nguyễn Văn Linh, Quận 7, TP.HCM');

INSERT INTO loaisan (TenLS) VALUES ('Sân 5'), ('Sân 7'), ('Sân 11'), ('Sân cầu lông'), ('Sân tennis'), ('Sân bóng rổ');

-- (Đã xóa dữ liệu mẫu danh sách sân: bảng san và khung_gio để trống, thành viên quản lý sân sẽ tự thêm)

INSERT INTO khachhang (HoTen, SDT, DiaChi, Email) VALUES
('Nguyễn Văn An', '0901234567', 'Quận 1, TP.HCM', 'an.nguyen@gmail.com'),
('Trần Thị Bình', '0912345678', 'Quận 7, TP.HCM', NULL);

INSERT INTO phuongthucthanhtoan (idKH, PhuongThuc) VALUES
(1, 'Tiền mặt'), (1, 'Chuyển khoản - Vietcombank'), (2, 'MoMo');

INSERT INTO nhanvien (HoTen, SDT) VALUES ('Lê Quản Lý', '0988888888');

-- =====================================================================
-- TRUY VẤN MẪU (dùng cho báo cáo / kiểm tra)
-- =====================================================================
-- (a) Xem giỏ hàng của 1 phiên kèm thành tiền và chủ sân (tách nhóm theo chủ sân như Shopee)
-- SELECT g.idGH, c.ten_chu_san, s.loai_san, g.ngay_choi, k.gio_bat_dau, k.gio_ket_thuc, s.gia*k.so_gio AS ThanhTien
-- FROM gio_hang g JOIN khung_gio k ON k.ma_khung_gio=g.ma_khung_gio JOIN san s ON s.ma_san=k.ma_san
-- JOIN chu_san c ON c.ma_chu_san=s.ma_chu_san WHERE g.ma_phien='abc' ORDER BY c.ma_chu_san, g.ngay_choi;

-- (b) Khung giờ đã có người đặt trong 1 ngày (đơn chưa hủy)
SELECT k.ma_khung_gio, s.loai_san, k.gio_bat_dau, k.gio_ket_thuc, d.idDon
FROM chitietdondatsan ct JOIN dondatsan d ON d.idDon=ct.idDon
JOIN khung_gio k ON k.ma_khung_gio=ct.ma_khung_gio JOIN san s ON s.ma_san=ct.ma_san
WHERE d.TrangThai<>3 AND ct.ngay_choi=DATE_ADD(CURDATE(), INTERVAL 1 DAY);

-- (c) Lịch sử đơn của khách kèm phương thức thanh toán
SELECT d.idDon, k.HoTen, c.ten_chu_san, d.NgayDat, d.PhuongThuc, d.TrangThai, d.TongTien
FROM dondatsan d JOIN khachhang k ON k.idKH=d.idKH JOIN chu_san c ON c.ma_chu_san=d.ma_chu_san
WHERE k.SDT='0901234567' ORDER BY d.NgayDat DESC;

-- (d) Doanh thu theo phương thức thanh toán (đơn đã xác nhận / hoàn thành)
SELECT PhuongThuc, COUNT(*) AS SoDon, SUM(TongTien) AS DoanhThu
FROM dondatsan WHERE TrangThai IN (1,2) GROUP BY PhuongThuc;

-- (e) Doanh thu theo chủ sân
SELECT c.ten_chu_san, COUNT(d.idDon) AS SoDon, SUM(d.TongTien) AS DoanhThu
FROM chu_san c LEFT JOIN dondatsan d ON d.ma_chu_san=c.ma_chu_san AND d.TrangThai IN (1,2)
GROUP BY c.ma_chu_san, c.ten_chu_san;

-- (f) Kiểm tra TongTien của đơn = tổng GiaBan các dòng chi tiết (phải trả về 0 dòng)
SELECT d.idDon FROM dondatsan d
JOIN (SELECT idDon, SUM(SoLuong*GiaBan) AS t FROM chitietdondatsan GROUP BY idDon) x ON x.idDon=d.idDon
WHERE d.TongTien<>x.t;
