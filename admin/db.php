<?php
// Dùng kết nối PDO và session chung của toàn hệ thống.
require_once __DIR__ . '/../giỏ hàng, thanh toán/config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

const GIA_MIN = 50000;
const GIA_MAX = 10000000;

$pdo->exec("SET time_zone = '+07:00'");

if (($_SESSION['vai_tro'] ?? '') !== 'Chủ sân' || empty($_SESSION['ma_chu_san'])) {
    header('Location: ../Đăng nhập/dangnhap.php');
    exit;
}
$CHU_SAN_ID = (int)$_SESSION['ma_chu_san'];


if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function csrf_ok(): bool {
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function tien($n): string { return number_format((float)$n, 0, ',', '.') . ' VNĐ'; }

const NHAN_TRANG_THAI_DON = [
    0 => ['pending',   'Chờ xác nhận'],
    1 => ['approved',  'Đã xác nhận'],
    2 => ['completed', 'Đã hoàn thành'],
    3 => ['rejected',  'Đã hủy'],
];
