<?php
$page_title = 'Giỏ hàng';
$trang = 'cart';
require 'includes/header.php';

$items = cart_items();

// Đơn đã tạo nhưng chưa thanh toán xong -> link sang trang Xác nhận đơn để xem / quét QR
$cho = [];
if ($idKH) {
    $st = $pdo->prepare('SELECT * FROM dondatsan WHERE idKH = ? AND TrangThai = 0 ORDER BY idDon DESC');
    $st->execute([$idKH]);
    $cho = $st->fetchAll();
}
$luu = saved_methods($idKH);   // phương thức khách đã lưu (phuongthucthanhtoan)
$pm_all = pt_list();
$pm_mac_dinh = $luu[0] ?? $pm_all[0];
function san_tag(string $loai): string {
    foreach (['5 người' => 'BĐ5', '7 người' => 'BĐ7', 'cầu lông' => 'CL', 'tennis' => 'TN', 'bóng rổ' => 'BR'] as $k => $v) {
        if (mb_stripos($loai, $k) !== false) return $v;
    }
    return 'S';
}
?>
<h1 class="title">Giỏ hàng của bạn</h1>
<p class="sub-title">Tích chọn các sân muốn thanh toán, bỏ sân không cần bằng nút Xóa.</p>

<?php if ($cho): ?>
<div class="panel pending">
  <h2>Đơn chờ thanh toán (<?= count($cho) ?>)</h2>
  <?php foreach ($cho as $o): ?>
    <div class="row">
      <span><?= ma_don($o['idDon']) ?> · <?= e($o['PhuongThuc'] ?? '—') ?> · <b><?= money($o['TongTien']) ?></b></span>
      <a class="btn ghost" href="xacnhan.php?don=<?= (int)$o['idDon'] ?>">Xem đơn / quét QR</a>
    </div>
  <?php endforeach; ?>
  <div class="muted" style="margin-top:8px"><a href="xacnhan.php#lichsu">Xem lịch sử đặt sân →</a></div>
</div>
<?php endif; ?>

<?php if (!$items): ?>
  <div class="empty"><strong>Giỏ hàng của bạn còn trống</strong>
    <span class="muted">Chọn sân ở trang danh sách để thêm vào giỏ.</span>
    <a class="btn" href="index.php">Đi đặt sân ngay</a></div>
<?php else: ?>
<form id="cartForm" method="post" action="xacnhan.php">
  <div class="cart">
    <div class="cart-head">
      <div><input type="checkbox" id="checkAll" aria-label="Chọn tất cả"></div>
      <div>Sân</div><div>Ngày &amp; giờ</div><div>Đơn giá</div><div>Thành tiền</div><div>Thao tác</div>
    </div>
    <?php $shop = null; foreach ($items as $it):
        if ($shop !== $it['ma_chu_san']): $shop = $it['ma_chu_san']; ?>
    <div class="shop-row">🏪 <b><?= e($it['ten_chu_san']) ?></b> <span class="muted">· <?= e($it['dia_chi']) ?></span></div>
    <?php endif; ?>
    <div class="cart-row">
      <div><input type="checkbox" class="item-check" name="ids[]" value="<?= (int)$it['idGH'] ?>" data-price="<?= $it['price'] ?>" aria-label="Chọn <?= e($it['loai_san']) ?>"></div>
      <div class="cname">
        <div class="tile"><?= e(san_tag($it['loai_san'])) ?></div>
        <div style="min-width:0"><b><?= e($it['loai_san']) ?></b><div class="muted">Sân #<?= (int)$it['ma_san'] ?></div></div>
      </div>
      <div><?= date('d/m/Y', strtotime($it['ngay_choi'])) ?>
        <div class="muted"><?= hhmm($it['gio_bat_dau']) ?> – <?= hhmm($it['gio_ket_thuc']) ?> (<?= $it['so_gio'] + 0 ?> giờ)</div></div>
      <div><?= money($it['gia_ap_dung']) ?>/giờ</div>
      <div class="sub"><?= money($it['price']) ?></div>
      <div><button type="submit" class="linkbtn" name="remove_id" value="<?= (int)$it['idGH'] ?>"
            formaction="cart_action.php" formnovalidate onclick="return confirm('Xóa sân này khỏi giỏ hàng?');">Xóa</button></div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="paybar">
    <label for="pm"><b>Thanh toán bằng</b></label>
    <select id="pm" name="pm">
      <?php foreach ([['Chuyển khoản ngân hàng', array_filter($pm_all, fn($x) => pt_loai($x) === 'bank')], ['Ví điện tử', array_filter($pm_all, fn($x) => pt_loai($x) === 'wallet')], ['Tiền mặt', [PT_TIENMAT]]] as [$nhom, $ds]): ?>
      <optgroup label="<?= e($nhom) ?>">
        <?php foreach ($ds as $pm): ?><option value="<?= e($pm) ?>"<?= $pm === $pm_mac_dinh ? ' selected' : '' ?>><?= e(pt_loai($pm) === 'bank' ? pt_bank($pm) : $pm) ?><?= in_array($pm, $luu, true) ? ' ★ đã lưu' : '' ?></option><?php endforeach; ?>
      </optgroup>
      <?php endforeach; ?>
    </select>
    <span class="muted">Chọn ở đây, sang bước Xác nhận đơn sẽ được chọn sẵn và hiện mã QR sau khi đặt.</span>
  </div>

  <div class="cart-foot">
    <label style="display:flex;gap:8px;align-items:center;cursor:pointer"><input type="checkbox" id="checkAll2"> Chọn tất cả (<?= count($items) ?>)</label>
    <button type="submit" class="linkbtn" name="action" value="remove_selected" formaction="cart_action.php" formnovalidate
            onclick="return confirm('Xóa các sân đã chọn khỏi giỏ hàng?');">Xóa đã chọn</button>
    <div class="grow"></div>
    <div class="tot">Tổng thanh toán (<span id="selCount">0</span> sân):<b id="selTotal">0đ</b></div>
    <button type="submit" class="btn" id="btnPay" disabled>Thanh toán</button>
  </div>
</form>
<script>
(function () {
  var checks = document.querySelectorAll('.item-check'), a1 = document.getElementById('checkAll'), a2 = document.getElementById('checkAll2'), btn = document.getElementById('btnPay');
  function update() {
    var total = 0, count = 0;
    checks.forEach(function (c) { c.closest('.cart-row').classList.toggle('sel', c.checked); if (c.checked) { total += +c.dataset.price; count++; } });
    document.getElementById('selCount').textContent = count;
    document.getElementById('selTotal').textContent = total.toLocaleString('vi-VN') + 'đ';
    btn.disabled = count === 0;
    btn.textContent = 'Thanh toán' + (count ? ' (' + count + ')' : '');
    a1.checked = a2.checked = (count === checks.length);
  }
  function all(v) { checks.forEach(function (c) { c.checked = v; }); update(); }
  checks.forEach(function (c) { c.addEventListener('change', update); });
  a1.addEventListener('change', function () { all(a1.checked); });
  a2.addEventListener('change', function () { all(a2.checked); });
  update();
})();
</script>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
