
<?php
require_once dirname(__DIR__) . '/giỏ hàng, thanh toán/config/db.php';
// Chỉ xóa thông tin đăng nhập, giữ session_id để giỏ hàng khách vẫn còn.
unset($_SESSION['id'], $_SESSION['idKH'], $_SESSION['ma_chu_san'], $_SESSION['HoTen'], $_SESSION['vai_tro']);
header('Location: ../Trang chủ banner/index.php?logout=1');
exit;
