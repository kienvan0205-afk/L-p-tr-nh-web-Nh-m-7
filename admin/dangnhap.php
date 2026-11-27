<?php
require __DIR__ . '/db.php';

if ($CHU_SAN_ID > 0) { header('Location: quantri.php'); exit; }

$loi = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { http_response_code(403); exit('CSRF token không hợp lệ.'); }

    $gmail = trim($_POST['gmail'] ?? '');
    $mk    = (string)($_POST['mat_khau'] ?? '');

    $st = $pdo->prepare("SELECT ma_chu_san, mat_khau FROM chu_san WHERE gmail = ?");
    $st->execute([$gmail]);
    $cs = $st->fetch();

    $ok = false;
    if ($cs) {
        $hash = $cs['mat_khau'];
        if (password_get_info($hash)['algo']) {
            $ok = password_verify($mk, $hash);
        } elseif (hash_equals($hash, $mk)) {          // dữ liệu mẫu đang lưu mật khẩu dạng thô
            $ok = true;
            $pdo->prepare("UPDATE chu_san SET mat_khau = ? WHERE ma_chu_san = ?")
                ->execute([password_hash($mk, PASSWORD_DEFAULT), $cs['ma_chu_san']]); // nâng cấp lên hash
        }
    }

    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['ma_chu_san'] = (int)$cs['ma_chu_san'];
        header('Location: quantri.php');
        exit;
    }
    $loi = 'Gmail hoặc mật khẩu không đúng.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập chủ sân</title>
    <link rel="stylesheet" href="quantri.css">
    <style>
        body { display: flex; min-height: 100vh; align-items: center; justify-content: center; }
        .login-box { width: 360px; }
        .login-box .profile-form { max-width: none; }
    </style>
</head>
<body>
    <div class="table-section login-box" style="border:2px solid #cc0000">
        <h2>Đăng nhập chủ sân</h2>
        <?php if ($loi): ?><div class="flash flash-err"><?= e($loi) ?></div><?php endif; ?>
        <form method="post" class="profile-form">
            <?= csrf_field() ?>
            <label>Gmail</label>
            <input type="email" name="gmail" required autofocus>
            <label>Mật khẩu</label>
            <input type="password" name="mat_khau" required>
            <button class="btn btn-search" type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
