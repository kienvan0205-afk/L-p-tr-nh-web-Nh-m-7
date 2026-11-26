\
<?php
require_once __DIR__ . '/ketnoi.php';
if (!isset($_SESSION['id'])) {
    header('Location: dangnhap.php');
    exit;
}
if (($_SESSION['vai_tro'] ?? '') === 'Chủ sân') {
    header('Location: ../admin/quantri.php');
    exit;
}
$pdo = (new Database())->pdo;
$sans = $pdo->query('SELECT ma_san, ten_san, loai_san, kieu_san, gia, trang_thai FROM san ORDER BY ma_san')->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trang tài khoản - SportBooking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="khung rong">
        <h1>ĐẶT LỊCH SÂN THỂ THAO ONLINE</h1>
        <p>Xin chào, <b><?= htmlspecialchars((string)($_SESSION['HoTen'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b>
           (<?= htmlspecialchars((string)($_SESSION['vai_tro'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)
           | <a href="dangxuat.php">Đăng xuất</a></p>
        <p>Số sân: <?= count($sans) ?> | Số tài khoản khách hàng: <?= (int)$pdo->query('SELECT COUNT(*) FROM khachhang')->fetchColumn() ?></p>
<p><a href="../Trang chủ banner/index.php">Danh sách sân và khung giờ</a> |
<a href="../chi tiết sân/dat_san.php">Đặt sân chi tiết</a> |
<a href="../giỏ hàng, thanh toán/cart.php">Giỏ hàng</a></p>
        <h2>DANH SÁCH SÂN</h2>
        <table>
            <tr><th>Mã sân</th><th>Tên sân</th><th>Loại sân</th><th>Kiểu sân</th><th>Giá cơ bản</th><th>Trạng thái</th></tr>
            <?php foreach ($sans as $r): ?>
            <tr>
                <td><?= (int)$r['ma_san'] ?></td>
                <td><?= htmlspecialchars((string)($r['ten_san'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)$r['loai_san'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)$r['kieu_san'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= number_format((float)$r['gia'], 0, ',', '.') ?>đ</td>
                <td><?= htmlspecialchars((string)$r['trang_thai'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
