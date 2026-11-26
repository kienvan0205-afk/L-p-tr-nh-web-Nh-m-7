
<?php
require_once __DIR__ . '/khachhang.php';
$msg = '';
$ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten = trim((string)($_POST['hoten'] ?? ''));
    $gmail = trim((string)($_POST['gmail'] ?? ''));
    $sdt = preg_replace('/\s+/', '', (string)($_POST['sdt'] ?? ''));
    $diachi = trim((string)($_POST['diachi'] ?? ''));
    $mk = (string)($_POST['mat_khau'] ?? '');
    $xn = (string)($_POST['xacnhan'] ?? '');

    if ($hoten === '' || $gmail === '' || $sdt === '' || $mk === '') {
        $msg = 'Vui lòng nhập đủ thông tin!';
    } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Gmail không đúng định dạng!';
    } elseif (strlen($mk) < 6) {
        $msg = 'Mật khẩu phải từ 6 ký tự trở lên!';
    } elseif (!preg_match('/^0\d{9,10}$/', $sdt)) {
        $msg = 'Số điện thoại không hợp lệ!';
    } elseif ($mk !== $xn) {
        $msg = 'Mật khẩu xác nhận không khớp!';
    } else {
        try {
            $kh = new KhachHang();
            if ($kh->tonTai($gmail, $sdt)) {
                $msg = 'Gmail hoặc số điện thoại đã tồn tại!';
            } else {
                $kh->themMoi($hoten, $gmail, $mk, $sdt, $diachi);
                $msg = 'Đăng ký thành công! Bạn có thể đăng nhập ngay.';
                $ok = true;
            }
        } catch (Throwable $e) {
            $msg = 'Không thể đăng ký: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký SportBooking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="khung">
        <h1>ĐẶT LỊCH SÂN THỂ THAO ONLINE</h1>
        <h2>ĐĂNG KÝ TÀI KHOẢN</h2>
        <?php if ($msg !== ''): ?><p class="<?= $ok ? 'thanhcong' : 'loi' ?>"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <form method="post" autocomplete="on">
            <input type="text" name="hoten" placeholder="Họ và tên" required>
            <input type="email" name="gmail" placeholder="Gmail (dùng để đăng nhập)" required>
            <input type="text" name="sdt" placeholder="Số điện thoại" required>
            <input type="text" name="diachi" placeholder="Địa chỉ">
            <input type="password" name="mat_khau" placeholder="Mật khẩu (từ 6 ký tự)" minlength="6" required>
            <input type="password" name="xacnhan" placeholder="Xác nhận mật khẩu" minlength="6" required>
            <button type="submit">Đăng ký</button>
            <p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></p>
        </form>
    </div>
</body>
</html>
