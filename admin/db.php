<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

const DB_HOST = 'localhost';
const DB_NAME = 'he_thong_dat_san_bong';
const DB_USER = 'root';
const DB_PASS = '';

const GIA_MIN = 50000;
const GIA_MAX = 1000000;

// Trang đăng nhập (đăng xuất xong sẽ chuyển về đây). Đổi nếu trang chủ của nhóm có form đăng nhập riêng.
const LOGIN_URL = 'dangnhap.php';

// Các loại sân cố định – PHẢI trùng với danh sách loại sân ở trang chủ.
const LOAI_SAN = [
    'Sân bóng đá 5 người',
    'Sân bóng đá 7 người',
    'Sân bóng đá 11 người',
    'Sân cầu lông',
    'Sân tennis',
    'Sân bóng rổ',
    'Sân Pickleball',
];

// Ảnh sân: lưu file trong uploads/san/, DB chỉ lưu tên file (cột san.hinh_anh)
const UPLOAD_DIR    = __DIR__ . '/uploads/san/';
const UPLOAD_URL    = 'uploads/san/';
const ANH_MAX_BYTES = 2 * 1024 * 1024; // 2MB

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

// Chưa đăng nhập thì $CHU_SAN_ID = 0; quantri.php sẽ chuyển về trang đăng nhập.
$CHU_SAN_ID = (int)($_SESSION['ma_chu_san'] ?? 0);


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

/* ---------- Ảnh sân ---------- */
// Trả về tên file mới, hoặc null nếu không chọn file. Lỗi -> RuntimeException.
function luu_anh_san(string $field): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK)  throw new RuntimeException('Tải ảnh thất bại (mã lỗi ' . $f['error'] . ').');
    if ($f['size'] > ANH_MAX_BYTES)     throw new RuntimeException('Ảnh tối đa 2MB.');

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if (!$ext || !@getimagesize($f['tmp_name'])) throw new RuntimeException('Chỉ nhận ảnh JPG, PNG hoặc WEBP.');

    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true)) throw new RuntimeException('Không tạo được thư mục lưu ảnh.');
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . $name)) throw new RuntimeException('Không lưu được ảnh.');
    return $name;
}
// Chỉ xóa file do hệ thống tự sinh (tên 24 ký tự hex) – không đụng tới file lạ.
function xoa_anh_san(?string $name): void {
    if ($name && preg_match('/^[a-f0-9]{24}\.(jpg|png|webp)$/', $name)) @unlink(UPLOAD_DIR . $name);
}
// URL ảnh nếu file tồn tại, ngược lại ''.
function anh_url(?string $name): string {
    return ($name && is_file(UPLOAD_DIR . $name)) ? UPLOAD_URL . rawurlencode($name) : '';
}
