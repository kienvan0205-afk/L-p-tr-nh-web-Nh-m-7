<?php
// Thêm / xóa khung giờ trong giỏ hàng (bảng gio_hang)
require_once 'config/db.php';
require_customer();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('cart.php');

// Nút Xóa từng dòng gửi remove_id nên không cần field action
$action = $_POST['action'] ?? (isset($_POST['remove_id']) ? 'remove' : '');

if ($action === 'add') {
    $kg   = (int)($_POST['ma_khung_gio'] ?? 0);
    $ngay = $_POST['ngay_choi'] ?? '';
    $back = 'index.php?ngay=' . urlencode($ngay);

    $st = $pdo->prepare('SELECT k.*, s.ma_san, s.loai_san, s.trang_thai AS san_tt FROM khung_gio k JOIN san s ON s.ma_san = k.ma_san WHERE k.ma_khung_gio = ?');
    $st->execute([$kg]);
    $k = $st->fetch();
    $d = DateTime::createFromFormat('Y-m-d', $ngay);
    $ngay_ok = $d && $d->format('Y-m-d') === $ngay;

    if (!$k || !$ngay_ok) {
        flash('Dữ liệu không hợp lệ.', 'err'); $back = 'index.php';
    } elseif ($k['san_tt'] !== 'Hoạt động' || $k['trang_thai'] !== 'Trống') {
        flash('Khung giờ này hiện không nhận đặt.', 'err');
    } elseif ($ngay < date('Y-m-d')) {
        flash('Không thể đặt sân trong quá khứ.', 'err');
    } elseif (is_booked($pdo, $kg, $ngay)) {
        flash('Khung giờ này đã có người đặt, vui lòng chọn khung khác.', 'err');
    } else {
        $dup = $pdo->prepare('SELECT COUNT(*) FROM gio_hang WHERE ma_phien = ? AND ma_khung_gio = ? AND ngay_choi = ?');
        $dup->execute([$phien, $kg, $ngay]);
        if ($dup->fetchColumn() > 0) {
            flash('Khung giờ này đã có trong giỏ hàng của bạn.', 'err');
        } else {
            $pdo->prepare('INSERT INTO gio_hang (ma_phien, idKH, ma_san, ma_khung_gio, ngay_choi) VALUES (?,?,?,?,?)')->execute([$phien, $idKH > 0 ? $idKH : null, (int)$k['ma_san'], $kg, $ngay]);
            flash('Đã thêm "' . $k['loai_san'] . ' ' . hhmm($k['gio_bat_dau']) . '–' . hhmm($k['gio_ket_thuc']) . '" vào giỏ hàng.');
        }
    }
    redirect($back);
}

if ($action === 'remove') {
    $pdo->prepare('DELETE FROM gio_hang WHERE idGH = ? AND ma_phien = ?')->execute([(int)($_POST['remove_id'] ?? 0), $phien]);
    flash('Đã xóa sân khỏi giỏ hàng.');
    redirect('cart.php');
}

if ($action === 'remove_selected') {
    $ids = posted_ids();
    if ($ids) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $pdo->prepare("DELETE FROM gio_hang WHERE ma_phien = ? AND idGH IN ($in)")->execute(array_merge([$phien], $ids));
        flash('Đã xóa ' . count($ids) . ' sân khỏi giỏ hàng.');
    } else {
        flash('Bạn chưa chọn sân nào để xóa.', 'err');
    }
    redirect('cart.php');
}
redirect('cart.php');
