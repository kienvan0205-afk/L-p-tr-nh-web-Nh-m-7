-- =============================================================
-- CAP NHAT KHUNG GIO VA BANG GIA SAN THE THAO VAA
-- Database: he_thong_dat_san_bong (MariaDB 10.4 / MySQL)
-- Tao 7 khung gio lien tuc tu 06:00 den 23:00 cho moi san.
-- Khong xoa bang, don dat san, chi tiet don hoac danh gia.
-- Cac ma_khung_gio 1..22 duoc giu lai de bao toan lien ket FK.
-- Nen Export database truoc khi chay de co ban sao du phong.
-- =============================================================
SET NAMES utf8mb4;
START TRANSACTION;

-- 1. Cap nhat cac dong khung_gio hien co trong file SQL ban gui.
UPDATE khung_gio SET ma_san=1, gio_bat_dau='06:00:00', gio_ket_thuc='08:00:00', gia_moi_gio=180000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=2.00, trang_thai='Trống' WHERE ma_khung_gio=1;
UPDATE khung_gio SET ma_san=1, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=350000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=2;
UPDATE khung_gio SET ma_san=1, gio_bat_dau='21:00:00', gio_ket_thuc='22:00:00', gia_moi_gio=180000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=1.00, trang_thai='Trống' WHERE ma_khung_gio=3;
UPDATE khung_gio SET ma_san=2, gio_bat_dau='08:00:00', gio_ket_thuc='11:00:00', gia_moi_gio=400000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Bình thường', so_gio=3.00, trang_thai='Trống' WHERE ma_khung_gio=4;
UPDATE khung_gio SET ma_san=2, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=550000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=5;
UPDATE khung_gio SET ma_san=3, gio_bat_dau='06:00:00', gio_ket_thuc='08:00:00', gia_moi_gio=60000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=2.00, trang_thai='Trống' WHERE ma_khung_gio=6;
UPDATE khung_gio SET ma_san=3, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=130000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=7;
UPDATE khung_gio SET ma_san=3, gio_bat_dau='21:00:00', gio_ket_thuc='22:00:00', gia_moi_gio=60000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=1.00, trang_thai='Trống' WHERE ma_khung_gio=8;
UPDATE khung_gio SET ma_san=4, gio_bat_dau='08:00:00', gio_ket_thuc='11:00:00', gia_moi_gio=250000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Bình thường', so_gio=3.00, trang_thai='Trống' WHERE ma_khung_gio=9;
UPDATE khung_gio SET ma_san=5, gio_bat_dau='08:00:00', gio_ket_thuc='11:00:00', gia_moi_gio=280000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Bình thường', so_gio=3.00, trang_thai='Trống' WHERE ma_khung_gio=10;
UPDATE khung_gio SET ma_san=5, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=380000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=11;
UPDATE khung_gio SET ma_san=5, gio_bat_dau='21:00:00', gio_ket_thuc='22:00:00', gia_moi_gio=200000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=1.00, trang_thai='Trống' WHERE ma_khung_gio=12;
UPDATE khung_gio SET ma_san=6, gio_bat_dau='06:00:00', gio_ket_thuc='08:00:00', gia_moi_gio=100000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=2.00, trang_thai='Trống' WHERE ma_khung_gio=13;
UPDATE khung_gio SET ma_san=6, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=220000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=14;
UPDATE khung_gio SET ma_san=7, gio_bat_dau='08:00:00', gio_ket_thuc='11:00:00', gia_moi_gio=170000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Bình thường', so_gio=3.00, trang_thai='Trống' WHERE ma_khung_gio=15;
UPDATE khung_gio SET ma_san=7, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=230000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=16;
UPDATE khung_gio SET ma_san=8, gio_bat_dau='06:00:00', gio_ket_thuc='08:00:00', gia_moi_gio=60000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=2.00, trang_thai='Trống' WHERE ma_khung_gio=17;
UPDATE khung_gio SET ma_san=8, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=110000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=18;
UPDATE khung_gio SET ma_san=9, gio_bat_dau='06:00:00', gio_ket_thuc='08:00:00', gia_moi_gio=700000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=2.00, trang_thai='Trống' WHERE ma_khung_gio=19;
UPDATE khung_gio SET ma_san=9, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=1200000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=20;
UPDATE khung_gio SET ma_san=10, gio_bat_dau='06:00:00', gio_ket_thuc='08:00:00', gia_moi_gio=100000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Thấp điểm', so_gio=2.00, trang_thai='Trống' WHERE ma_khung_gio=21;
UPDATE khung_gio SET ma_san=10, gio_bat_dau='17:00:00', gio_ket_thuc='21:00:00', gia_moi_gio=180000.00, gio_vang='17:00 - 21:00', gio_binh_thuong='Cao điểm', so_gio=4.00, trang_thai='Trống' WHERE ma_khung_gio=22;

-- 2. Them cac khung gio con thieu. NOT EXISTS giup chay lai khong bi nhan doi.
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 1, '08:00:00', '11:00:00', 250000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=1 AND gio_bat_dau='08:00:00' AND gio_ket_thuc='11:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 1, '11:00:00', '14:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=1 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 1, '14:00:00', '17:00:00', 250000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=1 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 2, '06:00:00', '08:00:00', 300000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=2 AND gio_bat_dau='06:00:00' AND gio_ket_thuc='08:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 2, '11:00:00', '14:00:00', 300000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=2 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 2, '14:00:00', '17:00:00', 400000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=2 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 2, '21:00:00', '22:00:00', 300000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=2 AND gio_bat_dau='21:00:00' AND gio_ket_thuc='22:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 3, '08:00:00', '11:00:00', 90000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=3 AND gio_bat_dau='08:00:00' AND gio_ket_thuc='11:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 3, '11:00:00', '14:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=3 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 3, '14:00:00', '17:00:00', 90000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=3 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 4, '06:00:00', '08:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=4 AND gio_bat_dau='06:00:00' AND gio_ket_thuc='08:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 4, '11:00:00', '14:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=4 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 4, '14:00:00', '17:00:00', 250000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=4 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 4, '17:00:00', '21:00:00', 350000.00, '17:00 - 21:00', 'Cao điểm', 4.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=4 AND gio_bat_dau='17:00:00' AND gio_ket_thuc='21:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 4, '21:00:00', '22:00:00', 180000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=4 AND gio_bat_dau='21:00:00' AND gio_ket_thuc='22:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 5, '06:00:00', '08:00:00', 200000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=5 AND gio_bat_dau='06:00:00' AND gio_ket_thuc='08:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 5, '11:00:00', '14:00:00', 200000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=5 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 5, '14:00:00', '17:00:00', 280000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=5 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 6, '08:00:00', '11:00:00', 150000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=6 AND gio_bat_dau='08:00:00' AND gio_ket_thuc='11:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 6, '11:00:00', '14:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=6 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 6, '14:00:00', '17:00:00', 150000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=6 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 6, '21:00:00', '22:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=6 AND gio_bat_dau='21:00:00' AND gio_ket_thuc='22:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 7, '06:00:00', '08:00:00', 120000.00, '17:00 - 21:00', 'Thấp điểm', 2.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=7 AND gio_bat_dau='06:00:00' AND gio_ket_thuc='08:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 7, '11:00:00', '14:00:00', 120000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=7 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 7, '14:00:00', '17:00:00', 170000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=7 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 7, '21:00:00', '22:00:00', 120000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=7 AND gio_bat_dau='21:00:00' AND gio_ket_thuc='22:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 8, '08:00:00', '11:00:00', 80000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=8 AND gio_bat_dau='08:00:00' AND gio_ket_thuc='11:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 8, '11:00:00', '14:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=8 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 8, '14:00:00', '17:00:00', 80000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=8 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 8, '21:00:00', '22:00:00', 60000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=8 AND gio_bat_dau='21:00:00' AND gio_ket_thuc='22:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 9, '08:00:00', '11:00:00', 950000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=9 AND gio_bat_dau='08:00:00' AND gio_ket_thuc='11:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 9, '11:00:00', '14:00:00', 700000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=9 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 9, '14:00:00', '17:00:00', 950000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=9 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 9, '21:00:00', '22:00:00', 700000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=9 AND gio_bat_dau='21:00:00' AND gio_ket_thuc='22:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 10, '08:00:00', '11:00:00', 140000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=10 AND gio_bat_dau='08:00:00' AND gio_ket_thuc='11:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 10, '11:00:00', '14:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=10 AND gio_bat_dau='11:00:00' AND gio_ket_thuc='14:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 10, '14:00:00', '17:00:00', 140000.00, '17:00 - 21:00', 'Bình thường', 3.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=10 AND gio_bat_dau='14:00:00' AND gio_ket_thuc='17:00:00');
INSERT INTO khung_gio (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio, gio_vang, gio_binh_thuong, so_gio, trang_thai) SELECT 10, '21:00:00', '22:00:00', 100000.00, '17:00 - 21:00', 'Thấp điểm', 1.00, 'Trống' WHERE NOT EXISTS (SELECT 1 FROM khung_gio WHERE ma_san=10 AND gio_bat_dau='21:00:00' AND gio_ket_thuc='22:00:00');

-- 3. Bo sung khung cuoi 22:00-23:00 cho tung san.
-- Gia duoc sao chep tu khung 21:00-22:00 cua CHINH san do.
-- NOT EXISTS giup chay lai khong tao dong trung.
INSERT INTO khung_gio
    (ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio,
     gio_vang, gio_binh_thuong, so_gio, trang_thai)
SELECT s.ma_san, '22:00:00', '23:00:00',
       kg.gia_moi_gio, kg.gio_vang, kg.gio_binh_thuong,
       1.00, 'Trống'
FROM san s
JOIN khung_gio kg
  ON kg.ma_san = s.ma_san
 AND kg.gio_bat_dau = '21:00:00'
 AND kg.gio_ket_thuc = '22:00:00'
WHERE NOT EXISTS (
    SELECT 1
    FROM khung_gio da_co
    WHERE da_co.ma_san = s.ma_san
      AND da_co.gio_bat_dau = '22:00:00'
      AND da_co.gio_ket_thuc = '23:00:00'
);

-- 4. Kiem tra: moi ma_san nen co du 7 khung gio tu 06:00 den 23:00.
SELECT s.ma_san, s.loai_san, kg.gio_bat_dau, kg.gio_ket_thuc,
       kg.gio_binh_thuong AS muc_gia, kg.gia_moi_gio, kg.trang_thai
FROM san s
LEFT JOIN khung_gio kg ON kg.ma_san = s.ma_san
ORDER BY s.ma_san, kg.gio_bat_dau;

COMMIT;
