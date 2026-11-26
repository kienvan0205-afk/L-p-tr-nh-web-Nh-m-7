
<?php
require_once __DIR__ . '/khachhang.php';
require_once __DIR__ . '/chusan.php';

$noticeMessages = [
    'book' => 'Bạn cần đăng nhập tài khoản khách hàng để đặt sân.',
    'cart' => 'Bạn cần đăng nhập tài khoản khách hàng để xem giỏ hàng.',
    'customer' => 'Bạn cần đăng nhập tài khoản khách hàng để sử dụng chức năng này.',
];
$msg = $noticeMessages[$_GET['notice'] ?? ''] ?? '';
$returnTo = safe_return_path($_GET['return'] ?? $_POST['return'] ?? null);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail = trim((string)($_POST['gmail'] ?? ''));
    $mk = (string)($_POST['mat_khau'] ?? '');
    $laChuSan = isset($_POST['chu_san']);

    if ($gmail === '' || $mk === '') {
        $msg = 'Vui lòng nhập gmail và mật khẩu!';
    } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Gmail không đúng định dạng!';
    } else {
        try {
            $u = $laChuSan ? (new ChuSan())->dangNhap($gmail, $mk) : (new KhachHang())->dangNhap($gmail, $mk);
            if ($u) {
                global $pdo;
                // Đổi session ID an toàn và chuyển giỏ hàng phiên hiện tại sang session mới.
                $oldSession = session_id();
                session_regenerate_id(true);
                $newSession = session_id();
                if ($laChuSan) {
                    $st = $pdo->prepare('UPDATE gio_hang SET ma_phien = ? WHERE ma_phien = ?');
                    $st->execute([$newSession, $oldSession]);
                    unset($_SESSION['idKH']);
                    $_SESSION['id'] = (int)$u['id'];
                    $_SESSION['ma_chu_san'] = (int)$u['id'];
                    $_SESSION['vai_tro'] = 'Chủ sân';
                } else {
                    $st = $pdo->prepare('UPDATE gio_hang SET ma_phien = ?, idKH = ? WHERE ma_phien = ?');
                    $st->execute([$newSession, (int)$u['idKH'], $oldSession]);
                    $_SESSION['id'] = (int)$u['idKH'];
                    $_SESSION['idKH'] = (int)$u['idKH'];
                    unset($_SESSION['ma_chu_san']);
                    $_SESSION['vai_tro'] = 'Khách hàng';
                }
                $_SESSION['HoTen'] = $u['HoTen'];
                header('Location: ' . ($laChuSan ? '../admin/quantri.php' : ($returnTo ?? '../Trang chủ banner/index.php')));
                exit;
            }
            $msg = 'Sai gmail hoặc mật khẩu, hoặc tài khoản không tồn tại!';
        } catch (Throwable $e) {
            $msg = 'Không thể đăng nhập. Hãy kiểm tra đã chạy file bo_sung_dang_nhap.sql chưa. Chi tiết: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập SportBooking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="khung">
        <h1>ĐẶT LỊCH SÂN THỂ THAO ONLINE</h1>
        <h2>ĐĂNG NHẬP HỆ THỐNG</h2>
        <?php if ($msg !== ''): ?><p class="loi"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <form method="post" autocomplete="on">
            <?php if ($returnTo !== null): ?><input type="hidden" name="return" value="<?= htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
            <input type="email" name="gmail" placeholder="Nhập gmail" required>
            <input type="password" name="mat_khau" placeholder="Nhập mật khẩu" required>
            <div class="checkbox-row">
                <input type="checkbox" id="isChuSan" name="chu_san">
                <label for="isChuSan">Chủ sân</label>
            </div>
            <button type="submit">Đăng nhập</button>
            <p><a href="quenmatkhau.html">Quên mật khẩu?</a> | <a href="dangky.php">Đăng ký</a></p>
            <p><a href="../Trang chủ banner/index.php">Xem danh sách sân</a></p>
        </form>
    </div>
</body>
</html>
