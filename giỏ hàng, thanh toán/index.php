<?php
// Trang công khai: chọn NGÀY -> mỗi sân hiện các khung giờ (bảng khung_gio) -> thêm vào giỏ hàng (bảng gio_hang)
$page_title = 'Danh sách sân';
$trang = 'home';
require 'includes/header.php';

$ngay = $_GET['ngay'] ?? date('Y-m-d');
$d = DateTime::createFromFormat('Y-m-d', $ngay);
if (!$d || $d->format('Y-m-d') !== $ngay || $ngay < date('Y-m-d')) $ngay = date('Y-m-d');

$sans = $pdo->query('SELECT s.*, c.ten_chu_san, c.dia_chi FROM san s JOIN chu_san c ON c.ma_chu_san = s.ma_chu_san ORDER BY s.ma_san')->fetchAll();
$kgs = [];
foreach ($pdo->query('SELECT * FROM khung_gio ORDER BY ma_san, gio_bat_dau') as $r) { $kgs[$r['ma_san']][] = $r; }

// Khung giờ đã có trong giỏ của phiên này (trong ngày đang xem)
$trong_gio = [];
$st = $pdo->prepare('SELECT ma_khung_gio FROM gio_hang WHERE ma_phien = ? AND ngay_choi = ?');
$st->execute([$phien, $ngay]);
foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $k) $trong_gio[$k] = true;

function san_tag(string $loai): string {
    foreach (['5 người' => 'BĐ5', '7 người' => 'BĐ7', 'cầu lông' => 'CL', 'tennis' => 'TN', 'bóng rổ' => 'BR'] as $k => $v) {
        if (mb_stripos($loai, $k) !== false) return $v;
    }
    return 'S';
}
?>
<h1 class="title">Chọn sân và thêm vào giỏ hàng</h1>
<p class="sub-title">Chọn ngày chơi, bấm vào khung giờ muốn đặt. Có thể thêm nhiều sân (kể cả của chủ sân khác nhau) rồi thanh toán một lần.</p>

<form class="datebar" method="get">
  <label for="ngay">Ngày chơi</label>
  <input type="date" id="ngay" name="ngay" value="<?= e($ngay) ?>" min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
  <button class="btn ghost" type="submit">Xem khung giờ</button>
</form>

<div class="grid">
<?php foreach ($sans as $s): $hoat_dong = $s['trang_thai'] === 'Hoạt động'; ?>
  <div class="court">
    <div class="court-top">
      <div class="tile"><?= e(san_tag($s['loai_san'])) ?></div>
      <div><h3><?= e($s['loai_san']) ?></h3><div class="muted">Sân #<?= (int)$s['ma_san'] ?> · <?= e($s['ten_chu_san']) ?></div></div>
    </div>
    <div class="court-body">
      <div class="muted"><?= e($s['mo_ta']) ?></div>
      <div class="muted">📍 <?= e($s['dia_chi']) ?></div>
      <div class="price"><?= money($s['gia']) ?> <small>/ giờ (giá cơ bản)</small></div>
      <?php if (!$hoat_dong): ?>
        <button class="btn block" disabled>Sân đang <?= e(mb_strtolower($s['trang_thai'])) ?></button>
      <?php else: ?>
        <div class="slots">
        <?php foreach ($kgs[$s['ma_san']] ?? [] as $k):
            $giaGio = (float)$k['gia_moi_gio'] > 0 ? (float)$k['gia_moi_gio'] : (float)$s['gia'];
            $gia = (int)round($giaGio * $k['so_gio']);
            $ban = is_booked($pdo, (int)$k['ma_khung_gio'], $ngay);
            $khoa = $k['trang_thai'] !== 'Trống';
            $co = isset($trong_gio[$k['ma_khung_gio']]);
        ?>
          <form method="post" action="cart_action.php" class="slot">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="ma_khung_gio" value="<?= (int)$k['ma_khung_gio'] ?>">
            <input type="hidden" name="ngay_choi" value="<?= e($ngay) ?>">
            <div><b><?= hhmm($k['gio_bat_dau']) ?> – <?= hhmm($k['gio_ket_thuc']) ?></b>
              <div class="muted"><?= $k['so_gio'] + 0 ?> giờ · <?= money($gia) ?></div></div>
            <?php if ($ban): ?><span class="pill cancel">Đã đặt</span>
            <?php elseif ($khoa): ?><span class="pill cancel">Tạm khóa</span>
            <?php elseif ($co): ?><a class="pill done" href="cart.php">Trong giỏ</a>
            <?php else: ?><button class="btn small" type="submit">Thêm vào giỏ</button><?php endif; ?>
          </form>
        <?php endforeach; ?>
        <?php if (empty($kgs[$s['ma_san']])): ?><div class="muted">Chưa có khung giờ.</div><?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php require 'includes/footer.php'; ?>
