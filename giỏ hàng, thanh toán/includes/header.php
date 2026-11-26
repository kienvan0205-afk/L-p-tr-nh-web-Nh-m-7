<?php
// Mỗi trang gọi: require 'includes/header.php';  (đặt $page_title và $trang trước khi gọi)
require_once __DIR__ . '/../config/db.php';
$n_cart = cart_count();
$flash = flash();
$trang = $trang ?? '';
$isCustomer = is_customer_session();
$projectPath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$cartLoginUrl = customer_login_url($projectPath . '/giỏ hàng, thanh toán/cart.php');
$confirmLoginUrl = customer_login_url($projectPath . '/giỏ hàng, thanh toán/xacnhan.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title ?? 'Đặt sân thể thao') ?> | Sân Thể Thao</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="pg-<?= e($trang ?? "") ?>">
<header class="topbar"><div class="wrap">
  <a class="logo" href="index.php">Sân Thể Thao</a>
  <nav class="nav" aria-label="Điều hướng">
    <a href="index.php"<?= $trang === 'home' ? ' class="on"' : '' ?>>Danh sách sân</a>
            <a href="../chi tiết sân/dat_san.php">Đặt sân chi tiết</a>
    <a href="<?= e($isCustomer ? 'xacnhan.php' : $confirmLoginUrl) ?>"<?= $trang === 'xacnhan' ? ' class="on"' : '' ?>>Xác nhận đơn &amp; lịch sử</a>
    <a class="cart-link<?= $trang === 'cart' ? ' on' : '' ?>" href="<?= e($isCustomer ? 'cart.php' : $cartLoginUrl) ?>">Giỏ hàng<?php if ($n_cart && $isCustomer): ?><span class="badge"><?= $n_cart ?></span><?php endif; ?></a>
            <?php if (!empty($_SESSION['id'])): ?><a href="../Đăng nhập/trangchu.php"><?= e($_SESSION['HoTen'] ?? 'Tài khoản') ?></a><a href="../Đăng nhập/dangxuat.php">Đăng xuất</a><?php else: ?><a href="../Đăng nhập/dangnhap.php">Đăng nhập</a><?php endif; ?>
  </nav>
</div></header>
<div class="wrap"><main>
<?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>" role="status"><?= e($flash['msg']) ?></div><?php endif; ?>
