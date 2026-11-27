<?php
require __DIR__ . '/db.php';

/* =========================================================
 * 1. XỬ LÝ HÀNH ĐỘNG (POST) – xong thì redirect (PRG)
 * ========================================================= */
function back(string $tab, string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: quantri.php#' . $tab);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        http_response_code(403);
        exit('CSRF token không hợp lệ.');
    }
    $action = $_POST['action'] ?? '';

    /* ---- Xác nhận / Từ chối đơn ----
       dondatsan.TrangThai: 0 Chờ xác nhận, 1 Đã xác nhận, 2 Đã hoàn thành, 3 Đã hủy
       Lưu ý: bảng dondatsan/chitietdondatsan không có cột nối tới san/chu_san,
       nên thao tác này áp dụng cho MỌI đơn trong hệ thống, không lọc được theo chủ sân. */
    if ($action === 'xac_nhan' || $action === 'tu_choi') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();
            $st = $pdo->prepare("SELECT * FROM dondatsan WHERE idDon = ? FOR UPDATE");
            $st->execute([$id]);
            $don = $st->fetch();

            if (!$don) throw new RuntimeException('Không tìm thấy đơn.');
            if ((int)$don['TrangThai'] !== 0) {
                throw new RuntimeException('Đơn này đã được xử lý trước đó.');
            }

            $new = $action === 'xac_nhan' ? 1 : 3;
            $pdo->prepare("UPDATE dondatsan SET TrangThai = ? WHERE idDon = ?")->execute([$new, $id]);
            $pdo->commit();
            back('orders', 'ok', $new === 1 ? 'Đã xác nhận đơn #' . $id : 'Đã hủy đơn #' . $id);
        } catch (RuntimeException $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            back('orders', 'err', $ex->getMessage());
        }
    }

    /* ---- Đánh dấu Đã hoàn thành (tiện cho vòng đời đơn, theo đúng enum có sẵn) ---- */
    if ($action === 'hoan_thanh') {
        $id = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare("UPDATE dondatsan SET TrangThai = 2 WHERE idDon = ? AND TrangThai = 1");
        $st->execute([$id]);
        back('orders', $st->rowCount() ? 'ok' : 'err',
            $st->rowCount() ? 'Đã đánh dấu hoàn thành đơn #' . $id : 'Chỉ đơn đã xác nhận mới đánh dấu hoàn thành được.');
    }

    /* ---- Cập nhật thông tin chủ sân (chỉ các trường cho phép) ---- */
    if ($action === 'cap_nhat_thong_tin') {
        $gmail  = trim($_POST['gmail'] ?? '');
        $sdt    = trim($_POST['sdt'] ?? '');
        $diachi = trim($_POST['dia_chi'] ?? '');

        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) back('profile', 'err', 'Gmail không hợp lệ.');
        if (!preg_match('/^0\d{9}$/', $sdt))             back('profile', 'err', 'Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0.');
        if ($diachi === '' || mb_strlen($diachi) > 225)  back('profile', 'err', 'Địa chỉ không hợp lệ (tối đa 225 ký tự).');

        // Không cho sửa: ten_chu_san, mat_khau (đổi mật khẩu nên tách trang riêng có xác thực mật khẩu cũ)
        $pdo->prepare("UPDATE chu_san SET gmail=?, sdt=?, dia_chi=? WHERE ma_chu_san=?")
            ->execute([$gmail, $sdt, $diachi, $CHU_SAN_ID]);
        back('profile', 'ok', 'Đã cập nhật thông tin.');
    }

    /* ---- Cập nhật giá / trạng thái sân (trong mức cho phép) ---- */
    if ($action === 'cap_nhat_san') {
        $id  = (int)($_POST['id'] ?? 0);
        $gia = (int)($_POST['gia'] ?? 0);
        $tt  = $_POST['trang_thai'] ?? '';

        if ($gia < GIA_MIN || $gia > GIA_MAX) {
            back('courts', 'err', 'Giá phải trong khoảng ' . tien(GIA_MIN) . ' – ' . tien(GIA_MAX) . '.');
        }
        if (!in_array($tt, ['Hoạt động', 'Bảo trì'], true)) back('courts', 'err', 'Trạng thái không hợp lệ.');

        $st = $pdo->prepare("UPDATE san SET gia=?, trang_thai=? WHERE ma_san=? AND ma_chu_san=?");
        $st->execute([$gia, $tt, $id, $CHU_SAN_ID]);
        back('courts', $st->rowCount() ? 'ok' : 'err', $st->rowCount() ? 'Đã cập nhật sân.' : 'Sân không hợp lệ.');
    }

    /* ---- Thêm sân mới ---- */
    if ($action === 'them_san') {
        $loai = trim($_POST['loai_san'] ?? '');
        $mota = trim($_POST['mo_ta'] ?? '');
        $gia  = (int)($_POST['gia'] ?? 0);
        $tt   = $_POST['trang_thai'] ?? 'Hoạt động';

        if ($loai === '' || mb_strlen($loai) > 225) back('addcourt', 'err', 'Tên loại sân không hợp lệ (tối đa 225 ký tự).');
        if (mb_strlen($mota) > 2000) back('addcourt', 'err', 'Mô tả quá dài (tối đa 2000 ký tự).');
        if ($gia < GIA_MIN || $gia > GIA_MAX) {
            back('addcourt', 'err', 'Giá phải trong khoảng ' . tien(GIA_MIN) . ' – ' . tien(GIA_MAX) . '.');
        }
        if (!in_array($tt, ['Hoạt động', 'Bảo trì'], true)) back('addcourt', 'err', 'Trạng thái không hợp lệ.');

        $pdo->prepare(
            "INSERT INTO san (ma_chu_san, loai_san, mo_ta, gia, trang_thai, hinh_anh) VALUES (?, ?, ?, ?, ?, '')"
        )->execute([$CHU_SAN_ID, $loai, $mota, $gia, $tt]);
        back('addcourt', 'ok', 'Đã thêm sân mới.');
    }

    /* ---- Sửa đầy đủ thông tin sân (loại sân, mô tả, giá, trạng thái) ---- */
    if ($action === 'sua_san') {
        $id   = (int)($_POST['id'] ?? 0);
        $loai = trim($_POST['loai_san'] ?? '');
        $mota = trim($_POST['mo_ta'] ?? '');
        $gia  = (int)($_POST['gia'] ?? 0);
        $tt   = $_POST['trang_thai'] ?? '';

        if ($loai === '' || mb_strlen($loai) > 225) back('addcourt', 'err', 'Tên loại sân không hợp lệ (tối đa 225 ký tự).');
        if (mb_strlen($mota) > 2000) back('addcourt', 'err', 'Mô tả quá dài (tối đa 2000 ký tự).');
        if ($gia < GIA_MIN || $gia > GIA_MAX) {
            back('addcourt', 'err', 'Giá phải trong khoảng ' . tien(GIA_MIN) . ' – ' . tien(GIA_MAX) . '.');
        }
        if (!in_array($tt, ['Hoạt động', 'Bảo trì'], true)) back('addcourt', 'err', 'Trạng thái không hợp lệ.');

        $st = $pdo->prepare(
            "UPDATE san SET loai_san=?, mo_ta=?, gia=?, trang_thai=? WHERE ma_san=? AND ma_chu_san=?"
        );
        $st->execute([$loai, $mota, $gia, $tt, $id, $CHU_SAN_ID]);
        back('addcourt', $st->rowCount() ? 'ok' : 'err', $st->rowCount() ? 'Đã cập nhật sân.' : 'Sân không hợp lệ.');
    }

    /* ---- Xóa sân (và các khung giờ của sân đó, vì khung_gio khóa ngoại tới san) ---- */
    if ($action === 'xoa_san') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();
            $st = $pdo->prepare("SELECT ma_san FROM san WHERE ma_san = ? AND ma_chu_san = ? FOR UPDATE");
            $st->execute([$id, $CHU_SAN_ID]);
            if (!$st->fetch()) throw new RuntimeException('Sân không hợp lệ.');

            $pdo->prepare("DELETE FROM khung_gio WHERE ma_san = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM san WHERE ma_san = ? AND ma_chu_san = ?")->execute([$id, $CHU_SAN_ID]);
            $pdo->commit();
            back('addcourt', 'ok', 'Đã xóa sân (và các khung giờ liên quan).');
        } catch (RuntimeException $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            back('addcourt', 'err', $ex->getMessage());
        }
    }

    /* ---- Đổi trạng thái khung giờ: Trống <-> Đã đặt ---- */
    if ($action === 'doi_khung_gio') {
        $id = (int)($_POST['id'] ?? 0);
        // Chỉ cho đổi khung giờ thuộc sân của chính chủ sân này
        $st = $pdo->prepare(
            "UPDATE khung_gio kg
             JOIN san s ON s.ma_san = kg.ma_san
             SET kg.trang_thai = IF(kg.trang_thai = 'Trống', 'Đã đặt', 'Trống')
             WHERE kg.ma_khung_gio = ? AND s.ma_chu_san = ?"
        );
        $st->execute([$id, $CHU_SAN_ID]);
        back('courts', $st->rowCount() ? 'ok' : 'err', $st->rowCount() ? 'Đã đổi trạng thái khung giờ.' : 'Khung giờ không hợp lệ.');
    }

    back('stats', 'err', 'Hành động không hợp lệ.');
}

/* =========================================================
 * 2. LẤY DỮ LIỆU HIỂN THỊ
 * ========================================================= */
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ---- Sân + khung giờ của chủ sân này ----
$st = $pdo->prepare("SELECT * FROM san WHERE ma_chu_san = ? ORDER BY ma_san");
$st->execute([$CHU_SAN_ID]);
$dsSan = $st->fetchAll();

$st = $pdo->prepare(
    "SELECT kg.*, s.loai_san
     FROM khung_gio kg JOIN san s ON s.ma_san = kg.ma_san
     WHERE s.ma_chu_san = ? ORDER BY kg.ma_san, kg.ma_khung_gio"
);
$st->execute([$CHU_SAN_ID]);
$dsKhungGio = $st->fetchAll();

$khungGioTheoSan = [];
foreach ($dsKhungGio as $kg) {
    $khungGioTheoSan[$kg['ma_san']][] = $kg;
}

// "Hết" = sân có ít nhất 1 khung giờ và KHÔNG còn khung giờ nào 'Trống'
$soSanHet = 0;
foreach ($dsSan as $s) {
    $slots = $khungGioTheoSan[$s['ma_san']] ?? [];
    if ($slots && !array_filter($slots, fn($k) => $k['trang_thai'] === 'Trống')) {
        $soSanHet++;
    }
}

// ---- Thống kê đơn — LƯU Ý: không lọc được theo chủ sân (xem ghi chú ở trên) ----
$st = $pdo->query(
    "SELECT COUNT(*) AS tong_don,
            COALESCE(SUM(TrangThai = 0), 0) AS cho_xac_nhan,
            COALESCE(SUM(CASE WHEN TrangThai = 2 THEN TongTien END), 0) AS doanh_thu,
            COALESCE(SUM(CASE WHEN TrangThai = 2 AND DATE(NgayDat) = CURDATE() THEN TongTien END), 0) AS dong_tien_hom_nay
     FROM dondatsan"
);
$stats = $st->fetch();

// ---- Danh sách đơn (tìm kiếm + lọc) — toàn hệ thống ----
$q   = trim($_GET['q'] ?? '');
$tt  = $_GET['tt'] ?? 'all';
$sql = "SELECT d.*, k.HoTen, k.SDT,
          (SELECT GROUP_CONCAT(CONCAT(l.TenLS, ' x', ct.SoLuong) SEPARATOR ', ')
             FROM chitietdondatsan ct JOIN loaisan l ON l.idLS = ct.idLS
            WHERE ct.idDon = d.idDon) AS chi_tiet
        FROM dondatsan d JOIN khachhang k ON k.idKH = d.idKH
        WHERE 1=1";
$par = [];
if ($q !== '') {
    $sql .= " AND (k.HoTen LIKE :q OR d.idDon = :q2)";
    $par[':q']  = '%' . $q . '%';
    $par[':q2'] = is_numeric(ltrim($q, '#')) ? (int)ltrim($q, '#') : 0;
}
if (ctype_digit((string)$tt) && isset(NHAN_TRANG_THAI_DON[(int)$tt])) {
    $sql .= " AND d.TrangThai = :tt";
    $par[':tt'] = (int)$tt;
}
$sql .= " ORDER BY (d.TrangThai = 0) DESC, d.NgayDat DESC LIMIT 200";
$st = $pdo->prepare($sql);
$st->execute($par);
$dsDon = $st->fetchAll();

// ---- Khách hàng (toàn hệ thống — cùng lý do như trên) ----
$st = $pdo->query(
    "SELECT k.*, GROUP_CONCAT(p.PhuongThuc SEPARATOR ', ') AS phuong_thuc
     FROM khachhang k LEFT JOIN phuongthucthanhtoan p ON p.idKH = k.idKH
     GROUP BY k.idKH ORDER BY k.idKH"
);
$dsKH = $st->fetchAll();

// ---- Thông tin chủ sân ----
$st = $pdo->prepare("SELECT * FROM chu_san WHERE ma_chu_san = ?");
$st->execute([$CHU_SAN_ID]);
$chuSan = $st->fetch();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Chủ Sân & Admin</title>
    <link rel="stylesheet" href="quantri.css">
</head>
<body>
<div class="admin-container">
    <aside class="sidebar">
        <h2><?= e($chuSan['ten_chu_san'] ?? 'Chủ Sân Admin') ?></h2>
        <nav>
            <ul>
                <li><a href="#stats" class="nav-item active" onclick="switchTab(event, 'stats')">Thống kê tổng quan</a></li>
                <li><a href="#courts" class="nav-item" onclick="switchTab(event, 'courts')">Quản lý sân</a></li>
                <li><a href="#addcourt" class="nav-item" onclick="switchTab(event, 'addcourt')">Thêm sân</a></li>
                <li>
                    <a href="#orders" class="nav-item" onclick="switchTab(event, 'orders')">
                        Quản lý đơn đặt
                        <?php if ($stats['cho_xac_nhan'] > 0): ?>
                            <span class="badge"><?= (int)$stats['cho_xac_nhan'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="#users" class="nav-item" onclick="switchTab(event, 'users')">Quản lý khách hàng</a></li>
                <li><a href="#profile" class="nav-item" onclick="switchTab(event, 'profile')">Thông tin của tôi</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <h1 id="page-title">Thống Kê Tổng Quan</h1>
        </header>

        <?php if ($flash): ?>
            <div class="flash <?= $flash['type'] === 'ok' ? 'flash-ok' : 'flash-err' ?>"><?= e($flash['msg']) ?></div>
        <?php endif; ?>

        <!-- ============ THỐNG KÊ ============ -->
        <section id="stats" class="content-section active">
            <div class="kpi-section">
                <div class="kpi-card">
                    <h3>Dòng tiền hôm nay</h3>
                    <p class="kpi-value"><?= tien($stats['dong_tien_hom_nay']) ?></p>
                </div>
                <div class="kpi-card">
                    <h3>Tổng doanh thu</h3>
                    <p class="kpi-value"><?= tien($stats['doanh_thu']) ?></p>
                </div>
                <div class="kpi-card">
                    <h3>Tổng đơn đặt</h3>
                    <p class="kpi-value"><?= (int)$stats['tong_don'] ?> Đơn</p>
                </div>
                <div class="kpi-card highlight">
                    <h3>Đơn chờ xác nhận</h3>
                    <p class="kpi-value"><?= (int)$stats['cho_xac_nhan'] ?> Đơn</p>
                </div>
                <div class="kpi-card">
                    <h3>Sân đang hết</h3>
                    <p class="kpi-value"><?= $soSanHet ?>/<?= count($dsSan) ?> Sân</p>
                </div>
            </div>
        </section>

        <!-- ============ QUẢN LÝ SÂN ============ -->
        <section id="courts" class="content-section">
            <div class="table-section">
                <h2>Sân của tôi</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Loại sân</th><th>Mô tả</th>
                            <th>Giá (<?= number_format(GIA_MIN, 0, ',', '.') ?> – <?= number_format(GIA_MAX, 0, ',', '.') ?>)</th>
                            <th>Trạng thái sân</th><th>Tình trạng hiện tại</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dsSan as $s):
                        $slots = $khungGioTheoSan[$s['ma_san']] ?? [];
                        $het = $slots && !array_filter($slots, fn($k) => $k['trang_thai'] === 'Trống');
                    ?>
                        <tr>
                            <td>
                                <form method="post" id="san-<?= (int)$s['ma_san'] ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="cap_nhat_san">
                                    <input type="hidden" name="id" value="<?= (int)$s['ma_san'] ?>">
                                </form>
                                <?= e($s['loai_san']) ?>
                            </td>
                            <td><?= e($s['mo_ta']) ?></td>
                            <td><input class="inline-input" form="san-<?= (int)$s['ma_san'] ?>" type="number" name="gia"
                                       min="<?= GIA_MIN ?>" max="<?= GIA_MAX ?>" step="1000" value="<?= (int)$s['gia'] ?>"></td>
                            <td>
                                <select class="inline-input" form="san-<?= (int)$s['ma_san'] ?>" name="trang_thai">
                                    <option value="Hoạt động" <?= $s['trang_thai'] === 'Hoạt động' ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="Bảo trì"   <?= $s['trang_thai'] === 'Bảo trì'   ? 'selected' : '' ?>>Bảo trì</option>
                                </select>
                            </td>
                            <td>
                                <?php if ($s['trang_thai'] === 'Bảo trì'): ?>
                                    <span class="status rejected">Bảo trì</span>
                                <?php elseif (!$slots): ?>
                                    <span class="status pending">Chưa có khung giờ</span>
                                <?php elseif ($het): ?>
                                    <span class="status rejected">Hết</span>
                                <?php else: ?>
                                    <span class="status approved">Còn trống</span>
                                <?php endif; ?>
                            </td>
                            <td><button class="btn btn-edit" form="san-<?= (int)$s['ma_san'] ?>" type="submit">Lưu</button></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$dsSan): ?><tr><td colspan="6">Chưa có sân nào.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-section">
                <h2>Khung giờ</h2>
                <table class="data-table">
                    <thead><tr><th>Sân</th><th>Giờ vàng</th><th>Giờ bình thường</th><th>Số giờ</th><th>Trạng thái</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($dsKhungGio as $kg): ?>
                        <tr>
                            <td><?= e($kg['loai_san']) ?></td>
                            <td><?= e($kg['gio_vang']) ?></td>
                            <td><?= e($kg['gio_binh_thuong']) ?></td>
                            <td><?= rtrim(rtrim((string)$kg['so_gio'], '0'), '.') ?> giờ</td>
                            <td><span class="status <?= $kg['trang_thai'] === 'Trống' ? 'approved' : 'rejected' ?>"><?= e($kg['trang_thai']) ?></span></td>
                            <td>
                                <form method="post" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="doi_khung_gio">
                                    <input type="hidden" name="id" value="<?= (int)$kg['ma_khung_gio'] ?>">
                                    <button class="btn btn-lock" type="submit">Đổi trạng thái</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$dsKhungGio): ?><tr><td colspan="6">Chưa có khung giờ nào.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============ THÊM / SỬA / XÓA SÂN ============ -->
        <section id="addcourt" class="content-section">
            <div class="table-section">
                <h2 id="addcourt-title">Thêm sân mới</h2>
                <form method="post" class="profile-form" id="addcourt-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="them_san" id="addcourt-action">
                    <input type="hidden" name="id" value="" id="addcourt-id">

                    <label>Loại sân</label>
                    <input type="text" name="loai_san" id="addcourt-loai" maxlength="225" required
                           placeholder="VD: Sân bóng đá 5 người">

                    <label>Mô tả</label>
                    <textarea name="mo_ta" id="addcourt-mota" rows="3" maxlength="2000"></textarea>

                    <label>Giá (<?= number_format(GIA_MIN, 0, ',', '.') ?> – <?= number_format(GIA_MAX, 0, ',', '.') ?>)</label>
                    <input type="number" name="gia" id="addcourt-gia" min="<?= GIA_MIN ?>" max="<?= GIA_MAX ?>"
                           step="1000" required>

                    <label>Trạng thái</label>
                    <select name="trang_thai" id="addcourt-trangthai">
                        <option value="Hoạt động">Hoạt động</option>
                        <option value="Bảo trì">Bảo trì</option>
                    </select>

                    <button class="btn btn-search" type="submit" id="addcourt-submit">Thêm sân</button>
                    <button class="btn btn-lock" type="button" id="addcourt-cancel"
                            onclick="resetAddCourtForm()" style="display:none">Hủy</button>
                </form>
            </div>

            <div class="table-section">
                <h2>Danh sách sân hiện có</h2>
                <table class="data-table">
                    <thead>
                        <tr><th>Loại sân</th><th>Mô tả</th><th>Giá</th><th>Trạng thái</th><th>Thao tác</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dsSan as $s): ?>
                        <tr>
                            <td><?= e($s['loai_san']) ?></td>
                            <td><?= e($s['mo_ta']) ?></td>
                            <td><?= tien($s['gia']) ?></td>
                            <td><span class="status <?= $s['trang_thai'] === 'Hoạt động' ? 'approved' : 'rejected' ?>">
                                <?= e($s['trang_thai']) ?></span></td>
                            <td>
                                <button class="btn btn-edit" type="button"
                                    onclick='editCourt(<?= (int)$s["ma_san"] ?>, <?= json_encode($s["loai_san"], JSON_UNESCAPED_UNICODE) ?>, <?= json_encode($s["mo_ta"], JSON_UNESCAPED_UNICODE) ?>, <?= (int)$s["gia"] ?>, <?= json_encode($s["trang_thai"], JSON_UNESCAPED_UNICODE) ?>)'>Sửa</button>
                                <form method="post" class="inline-form"
                                      onsubmit="return confirm('Xóa sân này? Toàn bộ khung giờ của sân cũng sẽ bị xóa.')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="xoa_san">
                                    <input type="hidden" name="id" value="<?= (int)$s['ma_san'] ?>">
                                    <button class="btn btn-cancel" type="submit">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$dsSan): ?><tr><td colspan="5">Chưa có sân nào.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============ QUẢN LÝ ĐƠN ============ -->
        <section id="orders" class="content-section">
            <div class="table-section">
                <h2>Quản lý Đơn Đặt (<?= count($dsDon) ?> đơn hiển thị · <?= (int)$stats['cho_xac_nhan'] ?> chờ xác nhận · toàn hệ thống)</h2>

                <form method="get" action="quantri.php#orders" class="filter-bar">
                    <div class="search-box">
                        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Tìm theo Mã đơn, Tên khách hàng...">
                        <button class="btn btn-search" type="submit">Tìm kiếm</button>
                    </div>
                    <div class="filter-group">
                        <label>Trạng thái:</label>
                        <select name="tt" onchange="this.form.submit()">
                            <option value="all" <?= $tt === 'all' ? 'selected' : '' ?>>Tất cả</option>
                            <?php foreach (NHAN_TRANG_THAI_DON as $code => [$cls, $lbl]): ?>
                                <option value="<?= $code ?>" <?= (string)$tt === (string)$code ? 'selected' : '' ?>><?= $lbl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <table class="data-table">
                    <thead>
                        <tr><th>Mã đơn</th><th>Khách hàng</th><th>Chi tiết</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Thao tác</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dsDon as $d): [$cls, $lbl] = NHAN_TRANG_THAI_DON[(int)$d['TrangThai']]; ?>
                        <tr>
                            <td>#<?= (int)$d['idDon'] ?></td>
                            <td><?= e($d['HoTen']) ?><br><small><?= e($d['SDT']) ?></small></td>
                            <td><?= e($d['chi_tiet'] ?? '—') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($d['NgayDat'])) ?></td>
                            <td><?= tien($d['TongTien']) ?></td>
                            <td><span class="status <?= $cls ?>"><?= $lbl ?></span></td>
                            <td>
                                <form method="post" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$d['idDon'] ?>">
                                    <?php if ((int)$d['TrangThai'] === 0): ?>
                                        <button class="btn btn-approve" name="action" value="xac_nhan" type="submit">Xác nhận</button>
                                        <button class="btn btn-cancel" name="action" value="tu_choi" type="submit"
                                                onclick="return confirm('Từ chối đơn này?')">Từ chối</button>
                                    <?php elseif ((int)$d['TrangThai'] === 1): ?>
                                        <button class="btn btn-edit" name="action" value="hoan_thanh" type="submit">Hoàn thành</button>
                                    <?php else: ?>—<?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$dsDon): ?><tr><td colspan="7">Không có đơn nào.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============ KHÁCH HÀNG ============ -->
        <section id="users" class="content-section">
            <div class="table-section">
                <h2>Khách hàng (toàn hệ thống)</h2>
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Họ và Tên</th><th>SĐT</th><th>Địa chỉ</th><th>Phương thức thanh toán</th></tr></thead>
                    <tbody>
                    <?php foreach ($dsKH as $k): ?>
                        <tr>
                            <td>KH<?= str_pad($k['idKH'], 2, '0', STR_PAD_LEFT) ?></td>
                            <td><?= e($k['HoTen']) ?></td>
                            <td><?= e($k['SDT']) ?></td>
                            <td><?= e($k['DiaChi'] ?? '') ?></td>
                            <td><?= e($k['phuong_thuc'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$dsKH): ?><tr><td colspan="5">Chưa có khách hàng.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============ THÔNG TIN CHỦ SÂN ============ -->
        <section id="profile" class="content-section">
            <div class="table-section">
                <h2>Thông tin của tôi</h2>
                <form method="post" class="profile-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="cap_nhat_thong_tin">

                    <label>Tên chủ sân <small>(không thể sửa)</small></label>
                    <input type="text" value="<?= e($chuSan['ten_chu_san']) ?>" disabled>

                    <label>Gmail</label>
                    <input type="email" name="gmail" value="<?= e($chuSan['gmail']) ?>" maxlength="100" required>

                    <label>Số điện thoại</label>
                    <input type="text" name="sdt" value="<?= e($chuSan['sdt']) ?>" maxlength="10" pattern="0[0-9]{9}" required>

                    <label>Địa chỉ</label>
                    <input type="text" name="dia_chi" value="<?= e($chuSan['dia_chi']) ?>" maxlength="225" required>

                    <button class="btn btn-search" type="submit">Lưu thay đổi</button>
                </form>
            </div>
        </section>
    </main>
</div>

<script>
const TITLES = {
    stats: 'Thống Kê Tổng Quan',
    courts: 'Quản Lý Sân',
    addcourt: 'Thêm Sân',
    orders: 'Quản Lý Đơn Đặt',
    users: 'Quản Lý Khách Hàng',
    profile: 'Thông Tin Của Tôi'
};

function showTab(tabId) {
    if (!TITLES[tabId]) tabId = 'stats';
    document.querySelectorAll('.nav-item').forEach(i =>
        i.classList.toggle('active', i.getAttribute('href') === '#' + tabId));
    document.querySelectorAll('.content-section').forEach(s =>
        s.classList.toggle('active', s.id === tabId));
    document.getElementById('page-title').innerText = TITLES[tabId];
}

function switchTab(event, tabId) {
    event.preventDefault();
    history.replaceState(null, '', '#' + tabId);
    showTab(tabId);
}

function editCourt(id, loai, mota, gia, trangThai) {
    document.getElementById('addcourt-title').innerText = 'Cập nhật sân';
    document.getElementById('addcourt-action').value = 'sua_san';
    document.getElementById('addcourt-id').value = id;
    document.getElementById('addcourt-loai').value = loai;
    document.getElementById('addcourt-mota').value = mota;
    document.getElementById('addcourt-gia').value = gia;
    document.getElementById('addcourt-trangthai').value = trangThai;
    document.getElementById('addcourt-submit').innerText = 'Cập nhật sân';
    document.getElementById('addcourt-cancel').style.display = 'inline-block';
    document.getElementById('addcourt-form').scrollIntoView({ behavior: 'smooth' });
}

function resetAddCourtForm() {
    document.getElementById('addcourt-title').innerText = 'Thêm sân mới';
    document.getElementById('addcourt-action').value = 'them_san';
    document.getElementById('addcourt-id').value = '';
    document.getElementById('addcourt-form').reset();
    document.getElementById('addcourt-submit').innerText = 'Thêm sân';
    document.getElementById('addcourt-cancel').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    showTab(location.hash.replace('#', ''));

    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.target.closest('button, input, select, form')) return;
            this.parentElement.querySelectorAll('tr').forEach(r => r.classList.remove('selected-row'));
            this.classList.add('selected-row');
        });
    });
});
</script>
</body>
</html>
