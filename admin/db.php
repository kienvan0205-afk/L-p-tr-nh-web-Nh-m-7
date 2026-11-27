<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

const DB_HOST = 'localhost';
const DB_NAME = 'he_thong_dat_san_bong';
const DB_USER = 'root';
const DB_PASS = '';

const GIA_MIN = 50000;
const GIA_MAX = 1000000;

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
);
$pdo->exec("SET time_zone = '+07:00'");

if (empty($_SESSION['ma_chu_san'])) {
    $_SESSION['ma_chu_san'] = 1;
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

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function tien($n): string { return number_format((float)$n, 0, ',', '.') . ' VNĐ'; }

const NHAN_TRANG_THAI_DON = [
    0 => ['pending',   'Chờ xác nhận'],
    1 => ['approved',  'Đã xác nhận'],
    2 => ['completed', 'Đã hoàn thành'],
    3 => ['rejected',  'Đã hủy'],
];
