CREATE DATABASE IF NOT EXISTS he_thong_dat_san_bong CHARACTER SET=utf8 COLLATE=utf8_general_ci;
USE he_thong_dat_san_bong;

CREATE TABLE chu_san (
    ma_chu_san INT AUTO_INCREMENT NOT NULL,
    ten_chu_san VARCHAR(100) NOT NULL,
    gmail VARCHAR(100) NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    sdt VARCHAR(20) NOT NULL,
    dia_chi VARCHAR(225) NOT NULL,
    PRIMARY KEY (ma_chu_san)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE san (
    ma_san INT AUTO_INCREMENT NOT NULL,
    ma_chu_san INT NOT NULL,
    loai_san VARCHAR(225) NOT NULL,
    mo_ta TEXT NOT NULL,
    dia_chi VARCHAR(255) NOT NULL DEFAULT '',
    gia DECIMAL(10,2) NOT NULL,
    trang_thai VARCHAR(225) NOT NULL,
    hinh_anh VARCHAR(225) NOT NULL,
    PRIMARY KEY (ma_san),
    FOREIGN KEY (ma_chu_san) REFERENCES chu_san(ma_chu_san)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE khung_gio (
    ma_khung_gio INT AUTO_INCREMENT NOT NULL,
    ma_san INT NOT NULL,
    gio_vang VARCHAR(225) NOT NULL,
    gio_binh_thuong TEXT NOT NULL,
    so_gio DECIMAL(10,2) NOT NULL,
    trang_thai VARCHAR(225) NOT NULL,
    PRIMARY KEY (ma_khung_gio),
    FOREIGN KEY (ma_san) REFERENCES san(ma_san)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE khachhang (
    idKH INT(11) NOT NULL AUTO_INCREMENT,
    HoTen VARCHAR(100) NOT NULL,
    SDT VARCHAR(15) NOT NULL,
    DiaChi VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (idKH),
    UNIQUE KEY SDT (SDT)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE phuongthucthanhtoan (
    idKH INT(11) NOT NULL,
    PhuongThuc VARCHAR(50) NOT NULL,
    PRIMARY KEY (idKH, PhuongThuc),
    FOREIGN KEY (idKH) REFERENCES khachhang(idKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE nhanvien (
    idNV INT(11) NOT NULL AUTO_INCREMENT,
    HoTen VARCHAR(100) NOT NULL,
    SDT VARCHAR(15) DEFAULT NULL,
    PRIMARY KEY (idNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE loaisan (
    idLS INT(11) NOT NULL AUTO_INCREMENT,
    TenLS VARCHAR(50) NOT NULL,
    PRIMARY KEY (idLS),
    UNIQUE KEY TenLS (TenLS)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE dondatsan (
    idDon INT(11) NOT NULL AUTO_INCREMENT,
    idKH INT(11) NOT NULL,
    idNV INT(11) DEFAULT NULL,
    NgayDat DATETIME NOT NULL,
    TrangThai TINYINT(4) NOT NULL DEFAULT '0'
        COMMENT '0: Chờ xác nhận, 1: Đã xác nhận, 2: Đã hoàn thành, 3: Đã hủy',
    TongTien DECIMAL(12,2) NOT NULL DEFAULT '0.00',
    PRIMARY KEY (idDon),
    FOREIGN KEY (idKH) REFERENCES khachhang(idKH),
    FOREIGN KEY (idNV) REFERENCES nhanvien(idNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE chitietdondatsan (
    idDon INT(11) NOT NULL,
    idLS INT(11) NOT NULL,
    SoLuong INT(11) NOT NULL DEFAULT '1',
    GiaBan DECIMAL(12,2) NOT NULL DEFAULT '0.00',
    PRIMARY KEY (idDon, idLS),
    FOREIGN KEY (idDon) REFERENCES dondatsan(idDon) ON DELETE CASCADE,
    FOREIGN KEY (idLS) REFERENCES loaisan(idLS)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO chu_san (ten_chu_san, gmail, mat_khau, sdt, dia_chi) VALUES
('Nguyễn Văn Hùng', 'hung.nguyen@gmail.com', '123456', '0905111222', '123 Lê Duẩn, Quận 1, TP.HCM'),
('Trần Thị Mai', 'mai.tran@gmail.com', '123456', '0914333444', '45 Nguyễn Thị Minh Khai, Quận 3, TP.HCM'),
('Lê Hoàng Nam', 'nam.le@gmail.com', '123456', '0988555666', '789 Phạm Văn Đồng, TP. Thủ Đức'),
('Phạm Quốc Cường', 'cuong.pham@gmail.com', '123456', '0932777888', '12 Điện Biên Phủ, Bình Thạnh'),
('Đặng Thu Thảo', 'thao.dang@gmail.com', '123456', '0971999000', '56 Nguyễn Văn Linh, Quận 7, TP.HCM');

INSERT INTO san (ma_chu_san, loai_san, mo_ta, gia, trang_thai, hinh_anh) VALUES
(1, 'Sân bóng đá 5 người', 'Sân cỏ nhân tạo chất lượng cao, có hệ thống đèn LED', 250000, 'Hoạt động', 'san5a.jpg'),
(1, 'Sân bóng đá 7 người', 'Sân rộng rãi, đạt chuẩn thi đấu giao hữu', 400000, 'Hoạt động', 'san7b.jpg'),
(2, 'Sân cầu lông', 'Sân thảm PVC chống trượt, mái che kiên cố', 120000, 'Hoạt động', 'caulongc.jpg'),
(3, 'Sân tennis', 'Mặt sân chuẩn cứng, trang bị đầy đủ lưới và ghế nghỉ', 300000, 'Bảo trì', 'tennisd.jpg'),
(4, 'Sân bóng rổ', 'Sân gỗ cao cấp trong nhà, rổ nâng hạ chiều cao', 350000, 'Hoạt động', 'bongroe.jpg');

INSERT INTO khung_gio (ma_san, gio_vang, gio_binh_thuong, so_gio, trang_thai) VALUES
(1, '17:30 - 20:30', '06:00 - 17:30', 1.5, 'Trống'),
(1, '17:30 - 20:30', '06:00 - 17:30', 1.5, 'Đã đặt'),
(2, '17:30 - 20:30', '06:00 - 17:30', 2.0, 'Trống'),
(3, '18:00 - 21:00', '07:00 - 18:00', 1.0, 'Trống'),
(3, '18:00 - 21:00', '07:00 - 18:00', 1.5, 'Đã đặt');

INSERT INTO khachhang (HoTen, SDT, DiaChi) VALUES
('Nguyễn Văn An', '0901234567', 'Quận 1, TP.HCM'),
('Trần Thị Bình', '0912345678', 'Quận 7, TP.HCM');

INSERT INTO phuongthucthanhtoan (idKH, PhuongThuc) VALUES
(1, 'Tiền mặt'), (1, 'Chuyển khoản'), (2, 'Momo');

INSERT INTO nhanvien (HoTen, SDT) VALUES ('Lê Quản Lý', '0988888888');

INSERT INTO loaisan (TenLS) VALUES ('Sân 5'), ('Sân 7'), ('Sân 11');

INSERT INTO dondatsan (idKH, idNV, NgayDat, TrangThai) VALUES (1, 1, NOW(), 1);

INSERT INTO chitietdondatsan (idDon, idLS, SoLuong, GiaBan) VALUES
(1, 1, 2, 300000),
(1, 2, 1, 500000);

UPDATE dondatsan d
JOIN (SELECT idDon, SUM(SoLuong*GiaBan) AS Tong FROM chitietdondatsan GROUP BY idDon) x
ON d.idDon = x.idDon
SET d.TongTien = x.Tong;
