-- Chạy 1 lần trên database ĐÃ CÓ dữ liệu (không cần nếu tạo mới từ he_thong_dat_san_bong.sql)
USE he_thong_dat_san_bong;
ALTER TABLE san ADD COLUMN dia_chi VARCHAR(255) NOT NULL DEFAULT '' AFTER mo_ta;
