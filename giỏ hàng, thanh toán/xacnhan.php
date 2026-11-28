<?php
// Trang "Xác nhận đơn & Lịch sử đặt sân" – nối với giỏ hàng (cart.php) và database của nhóm
require_once 'config/db.php';

/* ---------- Hàm riêng của trang ---------- */
function lay_don(PDO $pdo, int $id) {
    $st = $pdo->prepare('SELECT d.*, k.HoTen, k.SDT, k.Email, c.ten_chu_san FROM dondatsan d JOIN khachhang k ON k.idKH = d.idKH JOIN chu_san c ON c.ma_chu_san = d.ma_chu_san WHERE d.idDon = ?');
    $st->execute([$id]);
    return $st->fetch();
}
// Chi tiết đơn: mỗi dòng chitietdondatsan = 1 khung giờ của 1 sân (nối khung_gio, san, loaisan)
function chi_tiet_don(PDO $pdo, int $id): array {
    $st = $pdo->prepare('SELECT ct.ngay_choi, ct.GiaBan, ct.SoLuong, s.loai_san, s.ma_san, k.gio_bat_dau, k.gio_ket_thuc
                         FROM chitietdondatsan ct
                         JOIN san s ON s.ma_san = ct.ma_san
                         JOIN khung_gio k ON k.ma_khung_gio = ct.ma_khung_gio
                         WHERE ct.idDon = ? ORDER BY ct.ngay_choi, k.gio_bat_dau');
    $st->execute([$id]);
    $out = [];
    foreach ($st->fetchAll() as $r) {
        $out[] = ['ten' => $r['loai_san'] . ' #' . $r['ma_san'], 'ngay' => date('d/m/Y', strtotime($r['ngay_choi'])),
                  'gio' => hhmm($r['gio_bat_dau']) . ' - ' . hhmm($r['gio_ket_thuc']), 'gia' => (int)($r['SoLuong'] * $r['GiaBan'])];
    }
    return $out;
}
function pill_class(int $tt): string { return $tt === 1 || $tt === 2 ? 'done' : ($tt === 3 ? 'cancel' : ''); }
function so_dien_thoai_hop_le(string $s): bool { return (bool)preg_match('/^0\d{9,10}$/', $s); }

function form_from_pt(string $pm): array {   // ngược lại: PhuongThuc -> các ô chọn trên form
    $l = pt_loai($pm);
    if ($l === 'bank') return ['payment' => 'transfer', 'bank' => pt_bank($pm)];
    if ($l === 'wallet') return ['payment' => 'wallet', 'wallet' => $pm];
    if ($l === 'cash') return ['payment' => 'cash'];
    return [];
}
function pt_from_form(string $payment, string $bank, string $wallet): ?string {
    if ($payment === 'transfer') return isset(BANKS[$bank]) ? PT_CK_PREFIX . $bank : null;
    if ($payment === 'wallet') return isset(WALLETS[$wallet]) ? $wallet : null;
    if ($payment === 'cash') return PT_TIENMAT;
    return null;
}
$loi = null;                                                    // lỗi hiển thị trong form
$form = ['hoten' => '', 'sdt' => '', 'email' => '', 'payment' => 'transfer', 'bank' => '', 'wallet' => 'MoMo', 'stk' => ''];
$ids_chon = [];
// Mã chuyển khoản của lần thanh toán này (khách ghi vào nội dung chuyển khoản; lưu vào dondatsan.MaCK)
if (empty($_SESSION['ma_ck'])) $_SESSION['ma_ck'] = 'DS' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
$ma_ck = $_SESSION['ma_ck'];

/* ---------- Xử lý POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';

    // Từ giỏ hàng sang: chỉ hiện lại các sân đã chọn
    if ($act === '') {
        $ids_chon = posted_ids();
        if (!$ids_chon) { flash('Bạn chưa chọn sân nào để thanh toán.', 'err'); redirect('cart.php'); }
        // Phương thức đã chọn ở giỏ hàng (<select name="pm">) -> chọn sẵn ở đây
        $pm = $_POST['pm'] ?? '';
        $form = array_merge($form, form_from_pt($pm));
    }

    // Xác nhận đặt sân -> tạo đơn
    if ($act === 'dat') {
        $ids_chon = posted_ids();
        $form['hoten'] = trim($_POST['hoten'] ?? '');
        $form['sdt'] = preg_replace('/\s+/', '', $_POST['sdt'] ?? '');
        $form['email'] = trim($_POST['email'] ?? '');
        $form['payment'] = $_POST['payment'] ?? 'transfer';
        $form['bank'] = $_POST['bank'] ?? $form['bank'];
        $form['wallet'] = $_POST['wallet'] ?? 'MoMo';
        $form['stk'] = preg_replace('/\s+/', '', $_POST['stk'] ?? '');
        $pt = pt_from_form($form['payment'], $form['bank'], $form['wallet']);
        if (!$ids_chon) {
            flash('Bạn chưa chọn sân nào để thanh toán.', 'err'); redirect('cart.php');
        } elseif ($form['hoten'] === '') {
            $loi = 'Vui lòng nhập họ và tên.';
        } elseif (!so_dien_thoai_hop_le($form['sdt'])) {
            $loi = 'Số điện thoại không hợp lệ (ví dụ 0912345678).';
        } elseif ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $loi = 'Email không hợp lệ.';
        } elseif ($pt === null) {
            $loi = $form['payment'] === 'transfer' ? 'Vui lòng chọn ngân hàng của bạn.' : 'Vui lòng chọn phương thức thanh toán.';
        } elseif (pt_loai($pt) === 'bank' && !preg_match('/^\d{6,19}$/', $form['stk'])) {
            $loi = 'Nhập số tài khoản ngân hàng của bạn (6–19 chữ số).';

        } else {
            try {
                $pdo->beginTransaction();
                $items = cart_items($ids_chon);
                if (!$items) throw new Exception('Không tìm thấy sân đã chọn trong giỏ hàng.');
                foreach ($items as $it) {
                    if ($it['san_tt'] !== 'Hoạt động' || $it['kg_tt'] !== 'Trống') throw new Exception('"' . $it['loai_san'] . '" hiện không nhận đặt. Hãy xóa khỏi giỏ.');
                    if (!$it['idLS']) throw new Exception('Sân "' . $it['loai_san'] . '" chưa có loại sân (san.idLS).');
                    if (is_booked($pdo, (int)$it['ma_khung_gio'], $it['ngay_choi'])) {
                        throw new Exception('"' . $it['loai_san'] . '" ngày ' . date('d/m/Y', strtotime($it['ngay_choi'])) . ' (' . hhmm($it['gio_bat_dau']) . '–' . hhmm($it['gio_ket_thuc']) . ') vừa có người đặt. Hãy xóa khỏi giỏ và chọn khung khác.');
                    }
                }
                // Khách hàng: SĐT là duy nhất -> có rồi thì cập nhật, chưa có thì thêm
                $st = $pdo->prepare('SELECT idKH FROM khachhang WHERE SDT = ?');
                $st->execute([$form['sdt']]);
                $kh = $st->fetchColumn();
                if ($kh) {
                    // để trống email thì giữ email cũ
                    $pdo->prepare('UPDATE khachhang SET HoTen = ?, Email = COALESCE(?, Email) WHERE idKH = ?')->execute([$form['hoten'], $form['email'] ?: null, $kh]);
                } else {
                    $pdo->prepare('INSERT INTO khachhang (HoTen, SDT, Email) VALUES (?,?,?)')->execute([$form['hoten'], $form['sdt'], $form['email'] ?: null]);
                    $kh = $pdo->lastInsertId();
                }
                // Lưu phương thức vào phuongthucthanhtoan TRƯỚC, vì dondatsan(idKH, PhuongThuc) là khóa ngoại tới bảng này
                $c = $pdo->prepare('SELECT COUNT(*) FROM phuongthucthanhtoan WHERE idKH = ? AND PhuongThuc = ?');
                $c->execute([$kh, $pt]);
                if (!$c->fetchColumn()) $pdo->prepare('INSERT INTO phuongthucthanhtoan (idKH, PhuongThuc) VALUES (?,?)')->execute([$kh, $pt]);

                // Theo ERD mỗi đơn thuộc MỘT chủ sân -> giỏ có sân của nhiều chủ thì tách thành nhiều đơn
                $theo_chu = [];
                foreach ($items as $it) $theo_chu[$it['ma_chu_san']][] = $it;
                $ds_don = [];
                $laCK = pt_loai($pt) === 'bank';
                $maCK = pt_loai($pt) !== 'cash' ? $ma_ck : null;      // chuyển khoản / ví: có mã giao dịch chung cho cả đợt
                $ttCK = $laCK ? mb_substr(pt_bank($pt) . ' - ' . $form['stk'], 0, 120) : null;
                $insD = $pdo->prepare('INSERT INTO dondatsan (idKH, ma_chu_san, idNV, NgayDat, TrangThai, TongTien, PhuongThuc, MaCK, ThongTinCK) VALUES (?,?,?,?,?,?,?,?,?)');
                $insC = $pdo->prepare('INSERT INTO chitietdondatsan (idDon, idLS, ma_san, ma_khung_gio, ma_chu_san, ngay_choi, SoLuong, GiaBan) VALUES (?,?,?,?,?,?,?,?)');
                foreach ($theo_chu as $ma_chu => $ds) {
                    $tong = array_sum(array_column($ds, 'price'));
                    $insD->execute([$kh, $ma_chu, null, date('Y-m-d H:i:s'), 0, $tong, $pt, $maCK, $ttCK]);   // idNV NULL: chờ nhân viên tiếp nhận
                    $idDon = (int)$pdo->lastInsertId();
                    $ds_don[] = $idDon;
                    foreach ($ds as $it) { $insC->execute([$idDon, $it['idLS'], $it['ma_san'], $it['ma_khung_gio'], $ma_chu, $it['ngay_choi'], 1, $it['price']]); }
                }
                // Xóa các sân đã đặt khỏi giỏ
                $in = implode(',', array_fill(0, count($ids_chon), '?'));
                $pdo->prepare("DELETE FROM gio_hang WHERE ma_phien = ? AND idGH IN ($in)")->execute(array_merge([$phien], $ids_chon));
                $pdo->commit();
                unset($_SESSION['ma_ck']);   // lần thanh toán sau dùng mã mới
                $_SESSION['idKH'] = (int)$kh;
                flash('Đặt sân thành công. Mã đơn: ' . implode(', ', array_map('ma_don', $ds_don))
                    . (count($ds_don) > 1 ? ' (tách theo chủ sân, xem từng đơn ở Lịch sử bên dưới).' : '.'));
                redirect('xacnhan.php?don=' . $ds_don[0]);
            } catch (Exception $ex) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                flash($ex->getMessage(), 'err');
                redirect('cart.php');
            }
        }
    }

    // Khách báo đã thanh toán (DEMO – đơn vẫn chờ nhân viên xác nhận ở admin_donhang.php) hoặc hủy đơn
    if ($act === 'thanhtoan' || $act === 'huy') {
        $idDon = (int)($_POST['don'] ?? 0);
        $d = lay_don($pdo, $idDon);
        if ($d && (int)$d['idKH'] === $idKH && (int)$d['TrangThai'] === 0) {
            $pdo->prepare('UPDATE dondatsan SET TrangThai = ? WHERE idDon = ?')->execute([$act === 'thanhtoan' ? 1 : 3, $idDon]);
            flash($act === 'thanhtoan' ? 'Đã ghi nhận thanh toán. Cảm ơn bạn!' : 'Đã hủy đơn, các khung giờ được mở lại.');
        } else {
            flash('Không thể thực hiện với đơn này.', 'err');
        }
        redirect('xacnhan.php?don=' . $idDon);
    }
}

/* ---------- Chuẩn bị dữ liệu hiển thị ---------- */
$gio_chon = $ids_chon ? cart_items($ids_chon) : [];
if ($ids_chon && !$gio_chon) { flash('Không tìm thấy sân đã chọn trong giỏ hàng.', 'err'); redirect('cart.php'); }
$tong_chon = array_sum(array_column($gio_chon, 'price'));

// Điền sẵn thông tin nếu khách đã đặt trong phiên này
if ($idKH && $form['hoten'] === '') {
    $st = $pdo->prepare('SELECT * FROM khachhang WHERE idKH = ?');
    $st->execute([$idKH]);
    if ($k = $st->fetch()) { $form['hoten'] = $k['HoTen']; $form['sdt'] = $k['SDT']; $form['email'] = (string)$k['Email']; }
}

// Tra cứu lịch sử: theo SĐT nhập vào, hoặc khách của phiên hiện tại
$sdt_tra = preg_replace('/\s+/', '', $_GET['sdt'] ?? '');
$kh_tra = 0; $tra_khong_thay = false;
if ($sdt_tra !== '') {
    $st = $pdo->prepare('SELECT idKH FROM khachhang WHERE SDT = ?');
    $st->execute([$sdt_tra]);
    $kh_tra = (int)$st->fetchColumn();
    $tra_khong_thay = !$kh_tra;
} else {
    $kh_tra = $idKH;
}

// Đơn đang xem chi tiết (?don=ID): chủ phiên, hoặc có kèm đúng SĐT
$don = null; $don_ct = [];
if (isset($_GET['don'])) {
    $d = lay_don($pdo, (int)$_GET['don']);
    if ($d && ((int)$d['idKH'] === $idKH || ($sdt_tra !== '' && $sdt_tra === $d['SDT']))) { $don = $d; $don_ct = chi_tiet_don($pdo, (int)$d['idDon']); }
    else { flash('Không tìm thấy đơn hoặc bạn không có quyền xem.', 'err'); redirect('xacnhan.php'); }
}

$lich_su = [];
if ($kh_tra) {
    $st = $pdo->prepare('SELECT * FROM dondatsan WHERE idKH = ? ORDER BY NgayDat DESC, idDon DESC LIMIT 20');
    $st->execute([$kh_tra]);
    $lich_su = $st->fetchAll();
}

$page_title = 'Xác nhận đơn & Lịch sử';
$trang = 'xacnhan';
require 'includes/header.php';
$self_q = $sdt_tra !== '' ? '&sdt=' . urlencode($sdt_tra) : '';
?>
<h1 class="title">Xác nhận đơn &amp; Lịch sử đặt sân</h1>
<p class="sub-title">Điền thông tin người đặt, chọn cách thanh toán rồi xác nhận. Lịch sử đơn nằm ngay bên dưới.</p>

<?php if ($don): /* ===== Chi tiết 1 đơn vừa tạo / đang xem ===== */
    $tt = (int)$don['TrangThai']; $pt = $don['PhuongThuc']; $ma = ma_don($don['idDon']); $tien = (int)$don['TongTien'];
?>
<div class="box success">
  <h2>Đơn <?= e($ma) ?> <span class="pill <?= pill_class($tt) ?>"><?= e(trang_thai_text($tt, $pt)) ?></span></h2>
  <div class="cols">
    <div>
      <div class="kv">
        <span>Người đặt</span><b><?= e($don['HoTen']) ?></b>
        <span>Số điện thoại</span><b><?= e($don['SDT']) ?></b>
        <span>Chủ sân</span><b><?= e($don['ten_chu_san']) ?></b>
        <span>Ngày đặt</span><b><?= date('d/m/Y H:i', strtotime($don['NgayDat'])) ?></b>
        <span>Thanh toán</span><b><?= e($pt ?? '—') ?></b>
      </div>
      <div class="tablewrap"><table>
        <tr><th>Sân</th><th>Ngày</th><th>Giờ chơi</th><th class="r">Thành tiền</th></tr>
        <?php foreach ($don_ct as $c): ?>
          <tr><td><?= e($c['ten']) ?></td><td><?= e($c['ngay']) ?></td><td><?= e($c['gio']) ?></td><td class="r"><?= money($c['gia']) ?></td></tr>
        <?php endforeach; ?>
      </table></div>
      <div class="total-line"><span>Tổng cộng</span><span><?= money($tien) ?></span></div>
    </div>
    <div>
      <?php if ($tt === 3): ?>
        <p class="muted">Đơn này đã bị hủy, các khung giờ đã được mở lại.</p>
      <?php elseif ($tt >= 1): ?>
        <p>Đơn đã được thanh toán. Hẹn gặp bạn tại sân.</p>
      <?php elseif ($pt === PT_TIENMAT): ?>
        <p><?= pt_badge($pt) ?> Thanh toán tại quầy lễ tân <b>trước giờ chơi 30 phút</b> để giữ sân. Số tiền: <b><?= money($tien) ?></b>.</p>
      <?php elseif (pt_loai($pt) === 'bank'):
          // Một lần chuyển khoản trả cho cả đợt (các đơn tách theo chủ sân dùng chung MaCK)
          $st = $pdo->prepare('SELECT idDon, TongTien FROM dondatsan WHERE MaCK = ? AND idKH = ? AND TrangThai <> 3 ORDER BY idDon');
          $st->execute([$don['MaCK'], $don['idKH']]);
          $dot = $st->fetchAll();
          $tien_dot = (int)array_sum(array_column($dot, 'TongTien')) ?: $tien; ?>
        <div class="qrbox"><div class="qrimg" data-qr="<?= e(qr_payload($pt, $tien_dot, (string)$don['MaCK'])) ?>"></div>
          <div class="muted">Quét bằng app ngân hàng bất kỳ. Số tiền và nội dung đã điền sẵn.</div></div>
        <div class="kv"><span>Ngân hàng nhận</span><b><?= e(RECV_ACCOUNT) ?> - <?= e(RECV_OWNER) ?> (<?= e(RECV_BANK) ?>)</b>
          <span>Chuyển từ</span><b><?= e($don['ThongTinCK'] ?? pt_bank($pt)) ?></b>
          <span>Số tiền</span><b><?= money($tien_dot) ?></b><span>Mã chuyển khoản</span><b><?= e($don['MaCK']) ?></b>
          <?php if (count($dot) > 1): ?><span>Gồm các đơn</span><b><?= e(implode(', ', array_map(fn($r) => ma_don($r['idDon']), $dot))) ?></b><?php endif; ?></div>
      <?php else: $wl = $don['MaCK'] ?: $ma; ?>
        <div class="qrbox"><div class="qrimg" data-qr="<?= e(qr_payload($pt, $tien, $wl)) ?>"></div>
          <div class="muted">Quét mã bằng ứng dụng <?= e($pt) ?></div></div>
        <div class="kv"><span>Ví</span><b><?= pt_badge($pt) ?> <?= e($pt) ?></b><span>Mã giao dịch</span><b><?= e($wl) ?></b><span>Phí giao dịch</span><b>Miễn phí</b><span>Tổng thanh toán</span><b><?= money($tien) ?></b></div>
      <?php endif; ?>

      <div class="stack">
        <?php if ($tt === 0 && (int)$don['idKH'] === $idKH): ?>
          <form method="post"><input type="hidden" name="action" value="thanhtoan"><input type="hidden" name="don" value="<?= (int)$don['idDon'] ?>">
            <button class="btn block" type="submit"><?= $pt === PT_TIENMAT ? 'Tôi đã đặt cọc' : 'Tôi đã thanh toán' ?></button></form>
          <form method="post" onsubmit="return confirm('Hủy đơn này?');"><input type="hidden" name="action" value="huy"><input type="hidden" name="don" value="<?= (int)$don['idDon'] ?>">
            <button class="btn ghost block" type="submit">Hủy đơn</button></form>
        <?php endif; ?>
        <a class="btn ghost block" href="cart.php">Về giỏ hàng<?= $n_cart ? ' (' . $n_cart . ' sân)' : '' ?></a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if ($gio_chon): /* ===== Tóm tắt các sân vừa chọn trong giỏ ===== */ ?>
<div class="box">
  <h2>Sân đã chọn (<?= count($gio_chon) ?>)</h2>
  <div class="tablewrap"><table>
    <tr><th>Sân</th><th>Ngày</th><th>Giờ chơi</th><th class="r">Thành tiền</th></tr>
    <?php foreach ($gio_chon as $it): ?>
      <tr><td><?= e($it['loai_san']) ?> <span class="muted">#<?= (int)$it['ma_san'] ?> · <?= e($it['ten_chu_san']) ?></span></td><td><?= date('d/m/Y', strtotime($it['ngay_choi'])) ?></td>
        <td><?= hhmm($it['gio_bat_dau']) ?> - <?= hhmm($it['gio_ket_thuc']) ?></td><td class="r"><?= money($it['price']) ?></td></tr>
    <?php endforeach; ?>
  </table></div>
  <div class="total-line"><span>Tổng cộng</span><span><?= money($tong_chon) ?></span></div>
  <p style="margin:12px 0 0"><a href="cart.php">← Quay lại giỏ hàng</a></p>
</div>
<?php endif; ?>

<div class="container">
  <div class="box">
    <h2>Thông tin người đặt</h2>
    <?php if ($loi): ?><div class="flash err"><?= e($loi) ?></div><?php endif; ?>
    <form method="post" id="formDat">
      <input type="hidden" name="action" value="dat">
      <?php foreach ($ids_chon as $i): ?><input type="hidden" name="ids[]" value="<?= (int)$i ?>"><?php endforeach; ?>

      <label for="fullname">Họ và tên</label>
      <input type="text" id="fullname" name="hoten" placeholder="Nguyễn Văn A" value="<?= e($form['hoten']) ?>" required>
      <label for="phone">Số điện thoại</label>
      <input type="tel" id="phone" name="sdt" placeholder="09xxxxxxxx" value="<?= e($form['sdt']) ?>" required>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="example@gmail.com" value="<?= e($form['email']) ?>">

      <?php if (!$ids_chon): ?><p class="note">Chưa có sân nào được chọn. Hãy chọn sân ở <a href="cart.php">giỏ hàng</a> rồi bấm Thanh toán.</p><?php endif; ?>
      <button type="submit" class="btn"<?= $ids_chon ? '' : ' disabled' ?>>Xác nhận đặt sân<?= $ids_chon ? ' – ' . money($tong_chon) : '' ?></button>
    </form>
  </div>

  <div class="box" id="payBox">
    <h2>Phương thức thanh toán</h2>
    <?php $luu = saved_methods($idKH); if ($luu): ?><p class="note" style="margin:-4px 0 10px">★ Bạn đã dùng: <?= e(implode(', ', $luu)) ?></p><?php endif; ?>

    <label class="pay-option"><input type="radio" name="payment" value="transfer" form="formDat"<?= $form['payment'] === 'transfer' ? ' checked' : '' ?>><span><strong>Chuyển khoản ngân hàng</strong><span class="note">Chuyển đến STK: <?= e(RECV_ACCOUNT) ?> - <?= e(RECV_BANK) ?></span></span></label>
    <div class="pay-panel" id="panel-transfer" hidden>
      <div class="recipient">
        <?= bank_logo(RECV_BANK) ?>
        <div><small>Tài khoản nhận </small><strong id="recipientAcc"><?= e(RECV_ACCOUNT) ?></strong>
          <small>Chủ tài khoản: <span><?= e(RECV_OWNER) ?></span></small><small>Ngân hàng <span><?= e(RECV_BANK) ?></span></small></div>
        <button type="button" class="copy-btn" id="copyAcc">Sao chép</button>
      </div>
      <label class="acc-label" for="bankInput">Ngân hàng của bạn</label>
      <div class="combo">
        <input type="text" id="bankInput" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="bankList" value="<?= e($form['bank']) ?>" placeholder="Chọn hoặc gõ tên ngân hàng">
        <input type="hidden" name="bank" id="bankVal" form="formDat" value="<?= e($form['bank']) ?>">
        <ul class="combo-list" id="bankList" role="listbox" hidden></ul>
      </div>
      <div class="bank-result" id="bankResult" hidden></div>
      <label class="acc-label" for="accNo">Số tài khoản của bạn</label>
      <input type="text" id="accNo" name="stk" form="formDat" inputmode="numeric" maxlength="19" autocomplete="off" value="<?= e($form['stk']) ?>">
      <p class="note acc-err" id="accErr" hidden>Số tài khoản gồm 6 - 19 chữ số</p>
      <label class="acc-label" for="amount">Số tiền chuyển (VNĐ)</label>
      <input type="text" id="amount" value="<?= $tong_chon ? number_format($tong_chon, 0, ',', '.') : '' ?>" readonly>
      <label class="acc-label" for="memo">Ghi chú chuyển khoản (mã chuyển khoản)</label>
      <input type="text" id="memo" value="<?= e($ma_ck) ?>" readonly>
      <button type="submit" form="formDat" class="pay-btn"<?= $ids_chon ? '' : ' disabled' ?>>Thanh toán</button>
    </div>

    <label class="pay-option"><input type="radio" name="payment" value="wallet" form="formDat"<?= $form['payment'] === 'wallet' ? ' checked' : '' ?>><span><strong>Ví điện tử</strong><span class="note"><?= e(implode(', ', array_keys(WALLETS))) ?>...</span></span></label>
    <div class="pay-panel" id="panel-wallet" hidden>
      <div class="wallet-grid">
      <?php foreach (WALLETS as $ten => $w): ?>
        <label class="wallet-tile"><input type="radio" name="wallet" value="<?= e($ten) ?>" form="formDat"<?= $form['wallet'] === $ten ? ' checked' : '' ?>><?= wallet_logo($ten) ?><span><?= e($ten) ?></span></label>
      <?php endforeach; ?>
      </div>
      <div class="wallet-detail" id="walletDetail">
        <div class="wd-info">
          <div class="wd-row"><span>Mã giao dịch</span><b><?= e($ma_ck) ?></b></div>
          <div class="wd-row"><span>Phí giao dịch</span><b>Miễn phí</b></div>
          <div class="wd-row wd-total"><span>Tổng thanh toán</span><b><?= money($tong_chon) ?></b></div>
          <button type="submit" form="formDat" class="pay-btn"<?= $ids_chon ? '' : ' disabled' ?>>Thanh toán</button>
        </div>
        <div class="wd-qr"><div id="qrBox"></div><span>Quét mã bằng ứng dụng <b id="wdQrName"></b></span></div>
      </div>
    </div>

    <label class="pay-option"><input type="radio" name="payment" value="cash" form="formDat"<?= $form['payment'] === 'cash' ? ' checked' : '' ?>><span><strong>Đặt cọc tiền mặt tại sân</strong><span class="note">Thanh toán tại quầy lễ tân trước giờ chơi 30 phút</span></span></label>
  </div>
</div>

<div class="box" id="lichsu">
  <h2>Lịch sử đơn đã đặt</h2>
  <form class="lookup" method="get">
    <input type="tel" name="sdt" placeholder="Nhập số điện thoại để tra cứu đơn" value="<?= e($sdt_tra) ?>" aria-label="Số điện thoại tra cứu">
    <button class="btn ghost" type="submit">Tra cứu</button>
    <?php if ($sdt_tra !== ''): ?><a class="btn ghost" href="xacnhan.php#lichsu">Xem đơn của tôi</a><?php endif; ?>
  </form>
  <?php if ($tra_khong_thay): ?>
    <p class="muted">Không tìm thấy khách hàng nào có số điện thoại này.</p>
  <?php elseif (!$kh_tra): ?>
    <p class="muted">Bạn chưa có đơn nào trong phiên này. Nhập số điện thoại ở trên để tra cứu đơn đã đặt.</p>
  <?php elseif (!$lich_su): ?>
    <p class="muted">Chưa có đơn nào.</p>
  <?php else: ?>
  <div class="tablewrap"><table>
    <tr><th>Mã đơn</th><th>Ngày đặt</th><th>Sân</th><th>Giờ chơi</th><th>Thanh toán</th><th>Số tiền</th><th>Trạng thái</th></tr>
    <?php foreach ($lich_su as $o): $ct = chi_tiet_don($pdo, (int)$o['idDon']); $tt = (int)$o['TrangThai']; ?>
      <tr>
        <td><a href="xacnhan.php?don=<?= (int)$o['idDon'] . $self_q ?>"><?= e(ma_don($o['idDon'])) ?></a></td>
        <td><?= date('d/m/Y', strtotime($o['NgayDat'])) ?></td>
        <td><?php foreach ($ct as $c): ?><div><?= e($c['ten']) ?></div><?php endforeach; ?></td>
        <td><?php foreach ($ct as $c): ?><div><?= e($c['ngay'] . ' ' . $c['gio']) ?></div><?php endforeach; ?></td>
        <td><?= e($o['PhuongThuc'] ?? '—') ?></td>
        <td><?= money($o['TongTien']) ?></td>
        <td><span class="pill <?= pill_class($tt) ?>"><?= e(trang_thai_text($tt, $o['PhuongThuc'])) ?></span></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
  <?php endif; ?>
</div>

<?php
$tien_xem = $tong_chon ?: 0;
$qr_xem = [];
foreach (pt_list() as $p) { $qr_xem[$p] = qr_payload($p, $tien_xem, $ma_ck); }
$bank_js = [];
foreach (BANKS as $ten => $b) { $bank_js[] = [$ten, $b['full']] + [2 => $b['color'], 3 => $b['tag'], 4 => $b['code']]; }
?>
<script>
(function () {
  var QR = <?= json_encode($qr_xem, JSON_UNESCAPED_UNICODE) ?>, BANKS = <?= json_encode($bank_js, JSON_UNESCAPED_UNICODE) ?>, PRE = <?= json_encode(PT_CK_PREFIX, JSON_UNESCAPED_UNICODE) ?>, TIEN = <?= (int)$tien_xem ?>;
  function $(id) { return document.getElementById(id); }
  function norm(s) { return s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').toLowerCase(); }
  function val(n) { var r = document.querySelector('input[name=' + n + ']:checked'); return r ? r.value : ''; }

  /* logo: thử lần lượt các đường dẫn, lỗi hết thì giữ huy hiệu chữ */
  function applyLogo(s) {
    var urls = (s.getAttribute('data-logo') || '').split('|').filter(Boolean);
    (function next() {
      var u = urls.shift(); if (!u) return;
      var img = new Image(); img.alt = s.textContent; img.referrerPolicy = 'no-referrer';
      img.onload = function () { s.textContent = ''; s.classList.add('img'); s.appendChild(img); };
      img.onerror = next; img.src = u;
    })();
  }
  function logoEl(b) { var s = document.createElement('span'); s.className = 'logo'; s.style.background = b[2]; s.textContent = b[3]; s.setAttribute('data-logo', 'https://cdn.vietqr.io/img/' + b[4] + '.png|https://api.vietqr.io/img/' + b[4] + '.png'); applyLogo(s); return s; }
  document.querySelectorAll('.logo[data-logo]').forEach(applyLogo);

  /* ô chọn ngân hàng (combobox có logo, tìm không dấu) */
  var bankIn = $('bankInput'), bankVal = $('bankVal'), list = $('bankList'), result = $('bankResult'), selected = null, first = null;
  BANKS.forEach(function (b) { if (b[0] === bankVal.value) selected = b; });
  function setList(open) { list.hidden = !open; bankIn.setAttribute('aria-expanded', open); }
  function pick(b) {
    selected = b; bankIn.value = b[0]; bankVal.value = b[0]; setList(false);
    var t = document.createElement('div'), st = document.createElement('strong'), sm = document.createElement('small');
    st.textContent = b[1]; sm.textContent = 'Ngân hàng của bạn: ' + b[0]; t.appendChild(st); t.appendChild(sm);
    result.innerHTML = ''; result.appendChild(logoEl(b)); result.appendChild(t); result.hidden = false; refresh();
  }
  function renderBanks(q) {
    var n = norm(q), found = BANKS.filter(function (b) { return norm(b[0] + ' ' + b[1] + ' ' + b[3]).indexOf(n) >= 0; });
    first = found[0] || null; list.innerHTML = '';
    if (!found.length) { var e = document.createElement('li'); e.className = 'empty'; e.textContent = 'Không tìm thấy ngân hàng phù hợp'; list.appendChild(e); }
    found.forEach(function (b) {
      var li = document.createElement('li'), t = document.createElement('span'), bo = document.createElement('b'), sm = document.createElement('small');
      li.setAttribute('role', 'option'); bo.textContent = b[0]; sm.textContent = b[1]; t.appendChild(bo); t.appendChild(sm);
      li.appendChild(logoEl(b)); li.appendChild(t);
      li.addEventListener('mousedown', function (ev) { ev.preventDefault(); pick(b); });
      list.appendChild(li);
    });
    setList(true);
  }
  bankIn.addEventListener('focus', function () { renderBanks(selected ? '' : bankIn.value); });
  bankIn.addEventListener('input', function () { selected = null; bankVal.value = ''; result.hidden = true; renderBanks(bankIn.value); refresh(); });
  bankIn.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); if (!list.hidden && first) pick(first); }
    if (e.key === 'Escape') setList(false);
  });
  document.addEventListener('click', function (e) { if (!e.target.closest('.combo')) setList(false); });
  if (selected) pick(selected);

  var acc = $('accNo');
  acc.addEventListener('input', function () { acc.value = acc.value.replace(/\D/g, ''); $('accErr').hidden = true; });
  acc.addEventListener('blur', function () { var bad = acc.value && (acc.value.length < 6 || acc.value.length > 19); $('accErr').hidden = !bad; });
  $('copyAcc').addEventListener('click', function () {
    var b = this; try { navigator.clipboard.writeText($('recipientAcc').textContent.trim()); b.textContent = 'Đã chép'; } catch (e) { b.textContent = 'Không chép được'; }
    setTimeout(function () { b.textContent = 'Sao chép'; }, 1500);
  });

  /* mã QR xem trước: ví (khung bên phải ví) */
  function drawQr(el, text) {
    try { var q = qrcode(0, 'M'); q.addData(text); q.make(); el.innerHTML = q.createSvgTag({ cellSize: 4, margin: 0, scalable: true }); }
    catch (e) { el.textContent = 'Không tải được thư viện QR (cần Internet)'; }
  }
  function refresh() {
    var p = val('payment');
    $('panel-transfer').hidden = p !== 'transfer';
    $('panel-wallet').hidden = p !== 'wallet';
    if (p === 'wallet') {
      var w = val('wallet') || 'MoMo'; $('wdQrName').textContent = w;
      if (TIEN && QR[w]) drawQr($('qrBox'), QR[w]); else $('qrBox').textContent = '';
    }
  }
  document.querySelectorAll('input[name=payment],input[name=wallet]').forEach(function (r) { r.addEventListener('change', refresh); });
  document.getElementById('formDat').addEventListener('submit', function (ev) {
    if (val('payment') === 'transfer' && !bankVal.value) { ev.preventDefault(); alert('Vui lòng chọn ngân hàng của bạn.'); }
  });
  window.addEventListener('load', refresh);   // thư viện QR tải ở cuối trang
})();
</script>
<?php require 'includes/footer.php'; ?>
