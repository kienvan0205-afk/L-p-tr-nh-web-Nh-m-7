<?php
// Trang đặt sân dùng chung PDO + bảng gio_hang với module giỏ hàng/thanh toán.
require_once __DIR__ . '/../giỏ hàng, thanh toán/config/db.php';

function h_san($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$today = date('Y-m-d');
$date = $_GET['ngay'] ?? $today;
$dateObject = DateTime::createFromFormat('!Y-m-d', $date);
if (!$dateObject || $dateObject->format('Y-m-d') !== $date || $date < $today) {
    $date = $today;
}

// Lấy sân đang hoạt động.
$courts = $pdo->query(
    "SELECT s.ma_san, s.ma_chu_san, s.idLS, s.loai_san, s.ten_san,
            s.kieu_san, s.mo_ta, s.gia, s.trang_thai, s.hinh_anh,
            c.ten_chu_san, c.dia_chi
     FROM san s
     INNER JOIN chu_san c ON c.ma_chu_san = s.ma_chu_san
     WHERE s.trang_thai = 'Hoạt động'
     ORDER BY s.ma_san"
)->fetchAll();

$courtsById = [];
foreach ($courts as &$court) {
    // Ảnh được chuẩn hóa theo mã sân, luôn trỏ đúng từ thư mục trang chi tiết.
    $court['image_url'] = '../Trang chủ banner/images/san' . (int)$court['ma_san'] . '.jpg';
    $courtsById[(int)$court['ma_san']] = $court;
}
unset($court);
$selectedCourt = isset($_GET['ma_san']) ? (int)$_GET['ma_san'] : 0;
if (!isset($courtsById[$selectedCourt])) {
    $selectedCourt = $courts ? (int)$courts[0]['ma_san'] : 0;
}
$selectedCourtData = $courtsById[$selectedCourt] ?? null;

// Lấy các khung giờ đã được cấu hình sẵn trong database.
$slotRows = $pdo->query(
    "SELECT k.ma_khung_gio, k.ma_san, k.gio_bat_dau, k.gio_ket_thuc,
            k.gia_moi_gio, k.so_gio, k.trang_thai,
            s.gia AS gia_san
     FROM khung_gio k
     INNER JOIN san s ON s.ma_san = k.ma_san
     WHERE s.trang_thai = 'Hoạt động'
     ORDER BY k.ma_san, k.gio_bat_dau"
)->fetchAll();

// Những khung giờ đã có trong giỏ của phiên hiện tại vào ngày đang xem.
$cartQuery = $pdo->prepare(
    'SELECT ma_khung_gio FROM gio_hang WHERE ma_phien = ? AND ngay_choi = ?'
);
$cartQuery->execute([$phien, $date]);
$cartSlotIds = array_map('intval', $cartQuery->fetchAll(PDO::FETCH_COLUMN));
$cartSlotMap = array_fill_keys($cartSlotIds, true);

// Tính trạng thái theo ngày: trạng thái cấu hình, đơn đã đặt và giỏ hiện tại.
$slots = [];
foreach ($slotRows as $slot) {
    $slotId = (int)$slot['ma_khung_gio'];
    $rate = (float)$slot['gia_moi_gio'];
    if ($rate <= 0) {
        $rate = (float)$slot['gia_san'];
    }
    $hours = (float)$slot['so_gio'];
    $slots[] = [
        'id' => $slotId,
        'courtId' => (int)$slot['ma_san'],
        'start' => substr((string)$slot['gio_bat_dau'], 0, 5),
        'end' => substr((string)$slot['gio_ket_thuc'], 0, 5),
        'hours' => $hours,
        'rate' => $rate,
        'total' => (int)round($rate * $hours),
        'status' => (string)$slot['trang_thai'],
        'booked' => is_booked($pdo, $slotId, $date),
        'inCart' => isset($cartSlotMap[$slotId]),
    ];
}

// Xử lý thêm khung giờ vào đúng bảng gio_hang mà cart.php / xacnhan.php đang sử dụng.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $postDate = trim((string)($_POST['ngay'] ?? ''));
    $postCourt = (int)($_POST['ma_san'] ?? 0);
    $slotId = (int)($_POST['ma_khung_gio'] ?? 0);
    $d = DateTime::createFromFormat('!Y-m-d', $postDate);

    if (!$d || $d->format('Y-m-d') !== $postDate || $postDate < $today) {
        flash('Ngày đặt không hợp lệ hoặc đã qua.', 'err');
        redirect('dat_san.php');
    }

    try {
        $check = $pdo->prepare(
            "SELECT k.ma_khung_gio, k.ma_san, k.gio_bat_dau, k.gio_ket_thuc,
                    k.trang_thai AS slot_status, s.loai_san, s.trang_thai AS court_status
             FROM khung_gio k
             INNER JOIN san s ON s.ma_san = k.ma_san
             WHERE k.ma_khung_gio = ? AND k.ma_san = ?"
        );
        $check->execute([$slotId, $postCourt]);
        $slot = $check->fetch();

        if (!$slot) {
            throw new RuntimeException('Không tìm thấy khung giờ của sân đã chọn.');
        }
        if ($slot['court_status'] !== 'Hoạt động' || $slot['slot_status'] !== 'Trống') {
            throw new RuntimeException('Sân hoặc khung giờ này hiện không nhận đặt.');
        }
        if (is_booked($pdo, $slotId, $postDate)) {
            throw new RuntimeException('Khung giờ này đã có người đặt. Vui lòng chọn khung giờ khác.');
        }

        // Chặn cả khung giờ trùng hoặc chồng lấn đã nằm trong giỏ của phiên này.
        $overlap = $pdo->prepare(
            "SELECT COUNT(*)
             FROM gio_hang g
             INNER JOIN khung_gio k2 ON k2.ma_khung_gio = g.ma_khung_gio
             WHERE g.ma_phien = ? AND g.ngay_choi = ? AND k2.ma_san = ?
               AND k2.gio_bat_dau < ? AND k2.gio_ket_thuc > ?"
        );
        $overlap->execute([
            $phien,
            $postDate,
            $postCourt,
            $slot['gio_ket_thuc'],
            $slot['gio_bat_dau']
        ]);
        if ((int)$overlap->fetchColumn() > 0) {
            throw new RuntimeException('Khung giờ này bị trùng với sân đã có trong giỏ hàng.');
        }

        // Ghi đúng cấu trúc của bảng gio_hang dùng chung.
        $insert = $pdo->prepare(
            'INSERT INTO gio_hang (ma_phien, idKH, ma_san, ma_khung_gio, ngay_choi)
             VALUES (?, ?, ?, ?, ?)'
        );
        $insert->execute([
            $phien,
            $idKH > 0 ? $idKH : null,
            $postCourt,
            $slotId,
            $postDate
        ]);

        flash(
            'Đã thêm ' . $slot['loai_san'] . ' (' . substr($slot['gio_bat_dau'], 0, 5)
            . '–' . substr($slot['gio_ket_thuc'], 0, 5) . ') vào giỏ hàng.'
        );
        redirect('dat_san.php?ngay=' . urlencode($postDate) . '&ma_san=' . $postCourt);
    } catch (PDOException $ex) {
        // Bắt cả trường hợp unique key ngăn người dùng thêm lại cùng sân/khung/ngày.
        if ($ex->getCode() === '23000') {
            flash('Khung giờ này đã có trong giỏ hàng hoặc đã được đặt.', 'err');
        } else {
            flash('Không thể thêm vào giỏ hàng: ' . $ex->getMessage(), 'err');
        }
        redirect('dat_san.php?ngay=' . urlencode($postDate) . '&ma_san=' . $postCourt);
    } catch (Throwable $ex) {
        flash($ex->getMessage(), 'err');
        redirect('dat_san.php?ngay=' . urlencode($postDate) . '&ma_san=' . $postCourt);
    }
}

$notice = flash();
$selectedSlotId = isset($_GET['ma_khung_gio']) ? (int)$_GET['ma_khung_gio'] : 0;
if (!$selectedSlotId) {
    foreach ($slots as $slot) {
        if ($slot['courtId'] === $selectedCourt && $slot['status'] === 'Trống' && !$slot['booked'] && !$slot['inCart']) {
            $selectedSlotId = $slot['id'];
            break;
        }
    }
}
$cartCount = cart_count();
$cartUrl = '../giỏ hàng, thanh toán/cart.php';
$homeUrl = '../Trang chủ banner/index.php';
$historyUrl = '../giỏ hàng, thanh toán/xacnhan.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt sân thể thao VAA</title>
    <link rel="stylesheet" href="style_dat_san1.css">
</head>
<body>
<header>
    <nav>
        <div class="menu">
            <a href="<?= h_san($homeUrl) ?>">Trang chủ</a>
            <a href="dat_san.php">Đặt sân</a>
            <a href="<?= h_san($historyUrl) ?>">Lịch sử đặt sân</a>
            <a href="<?= h_san($cartUrl) ?>">Giỏ hàng (<?= $cartCount ?>)</a>
<?php if (!empty($_SESSION['id'])): ?><a href="../Đăng nhập/trangchu.php">Tài khoản</a><a href="../Đăng nhập/dangxuat.php">Đăng xuất</a><?php else: ?><a href="../Đăng nhập/dangnhap.php">Đăng nhập</a><?php endif; ?>
        </div>
    </nav>
</header>

<a href="<?= h_san($cartUrl) ?>" class="gio-hang" id="gioHang">
    🛒 Giỏ hàng <span><?= $cartCount ?></span>
</a>

<div class="khung">
    <div class="hinh-san">
        <img id="hinhAnhSan"
            src="<?= h_san($selectedCourtData['image_url'] ?? '') ?>"
             alt="Hình ảnh sân thể thao">
    </div>
    <div class="gioi-thieu-san">
        <h2 id="tenSanGioiThieu"><?= h_san($selectedCourtData['ten_san'] ?? $selectedCourtData['loai_san'] ?? 'Sân thể thao VAA') ?></h2>
        <p><b>Loại sân:</b> <span id="loaiSanGioiThieu"><?= h_san($selectedCourtData['loai_san'] ?? 'Chưa chọn sân') ?></span></p>
        <p><b>Kiểu sân:</b> <span id="kieuSanGioiThieu"><?= h_san($selectedCourtData['kieu_san'] ?? 'Chưa chọn sân') ?></span></p>
        <p><b>Mã sân:</b> <span id="maSanGioiThieu"><?= $selectedCourtData ? '#' . (int)$selectedCourtData['ma_san'] : 'Chưa chọn' ?></span></p>
        <p><b>Chủ sân:</b> <span id="chuSanGioiThieu"><?= h_san($selectedCourtData['ten_chu_san'] ?? '—') ?></span></p>
        <p><b>Địa chỉ:</b> <span id="diaChiSanGioiThieu"><?= h_san($selectedCourtData['dia_chi'] ?? '—') ?></span></p>
        <p><b>Mô tả:</b> <span id="moTaSanGioiThieu"><?= h_san($selectedCourtData['mo_ta'] ?? 'Vui lòng chọn sân để xem thông tin chi tiết.') ?></span></p>
    </div>
</div>

<?php if ($notice): ?>
    <p class="thong-bao" role="status"
       style="<?= ($notice['type'] ?? '') === 'err' ? 'background:#ffe8e8;color:#a40000;' : 'background:#e8f5e9;color:#176b2c;' ?>">
        <?= h_san($notice['msg'] ?? '') ?>
    </p>
<?php endif; ?>

<?php if (!$courts): ?>
    <p class="thong-bao">Hiện chưa có sân hoạt động trong cơ sở dữ liệu.</p>
<?php else: ?>
<div class="dat-san">
    <div class="chon-loai-san">
        <h3>Chọn sân</h3>
        <div class="chon-kieu-san">
            <button type="button" class="btn-kieu-san" data-kieu="Ngoài trời">Ngoài trời</button>
            <button type="button" class="btn-kieu-san" data-kieu="Trong nhà">Trong nhà</button>
        </div>
        <label for="ma_san">Chọn sân cụ thể</label>
        <select name="ma_san" id="ma_san" form="formDatSan">
            <?php foreach ($courts as $court): ?>
                <option value="<?= (int)$court['ma_san'] ?>"
                        data-kieu="<?= h_san($court['kieu_san']) ?>"
                        <?= $selectedCourt === (int)$court['ma_san'] ? 'selected' : '' ?>>
                    <?= h_san(($court['ten_san'] ?: $court['loai_san']) . ' (#' . $court['ma_san'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p style="margin-top:12px">Chỉ hiển thị sân đang hoạt động.</p>
    </div>

    <div class="lich-co-san">
        <h3>Chọn ngày</h3>
        <form method="get" id="formNgay">
            <input type="hidden" name="ma_san" id="maSanNgay" value="<?= $selectedCourt ?>">
            <label for="ngay">Ngày sử dụng</label>
            <input type="date" id="ngay" name="ngay" value="<?= h_san($date) ?>"
                   min="<?= h_san($today) ?>" required>
            <button type="submit" class="nut-chon">Xem lịch ngày này</button>
        </form>
        <p>Chọn ngày hiện tại hoặc ngày trong tương lai để xem tình trạng khung giờ.</p>
    </div>

    <div class="chon-gio">
        <h3>Chọn khung giờ</h3>
        <label for="ma_khung_gio">Khung giờ có sẵn trong hệ thống</label>
        <select name="ma_khung_gio" id="ma_khung_gio" form="formDatSan">
            <option value="">-- Chọn khung giờ --</option>
        </select>
        <p id="trangThaiKhungGio" style="color:#fff;margin-top:14px">Vui lòng chọn sân và khung giờ.</p>
    </div>

    <div class="bang-gia">
        <h3>Bảng giá sân</h3>
        <div id="noiDungBangGia"><p>Chọn sân để xem bảng giá.</p></div>
        <small>Giá tính theo đơn giá của khung giờ nhân với số giờ. Tổng tiền sẽ được tính lại ở giỏ hàng.</small>
    </div>
</div>

<div class="tong-ket">
    <form method="post" id="formDatSan">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="ngay" id="ngayHidden" value="<?= h_san($date) ?>">
        <div class="thong-tin-tong">
            <span>Sân đã chọn</span>
            <strong id="hienThiSan"><?= h_san($selectedCourtData['loai_san'] ?? 'Chưa chọn') ?></strong>
        </div>
        <div class="thong-tin-tong">
            <span>Ngày sử dụng</span>
            <strong id="hienThiNgay"><?= h_san($date) ?></strong>
        </div>
        <div class="thong-tin-tong">
            <span>Khung giờ</span>
            <strong id="hienThiGio">--:-- – --:--</strong>
        </div>
        <div class="thong-tin-tong">
            <span>Số giờ thuê</span>
            <strong id="hienThiSoGio">0 giờ</strong>
        </div>
        <div class="thong-tin-tong">
            <span>Thành tiền</span>
            <strong id="hienThiThanhTien">0đ</strong>
        </div>
        <div class="nut-thanh-toan">
            <button type="submit" class="nut-chon" id="btnThemGioHang" disabled>Thêm vào giỏ hàng</button>
            <a class="nut-thanh-toan-nhanh" href="<?= h_san($cartUrl) ?>" style="text-decoration:none;text-align:center">Xem giỏ hàng</a>
        </div>
        <p id="thongBaoGia" style="color:#fff;margin-top:10px"></p>
    </form>
</div>
<?php endif; ?>

<script>
const COURTS = <?= json_encode($courts, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const SLOTS = <?= json_encode($slots, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const INITIAL_SLOT_ID = <?= (int)$selectedSlotId ?>;
const CART_URL = <?= json_encode($cartUrl, JSON_UNESCAPED_UNICODE) ?>;

(function () {
    const courtSelect = document.getElementById('ma_san');
    if (!courtSelect) return;
    const slotSelect = document.getElementById('ma_khung_gio');
    const dateInput = document.getElementById('ngay');
    const dateHidden = document.getElementById('ngayHidden');
    const dateCourt = document.getElementById('maSanNgay');
    const addButton = document.getElementById('btnThemGioHang');
    const money = n => Math.round(Number(n)).toLocaleString('vi-VN') + 'đ';
    const byId = id => COURTS.find(c => Number(c.ma_san) === Number(id));
    const currentSlots = () => SLOTS.filter(s => Number(s.courtId) === Number(courtSelect.value));

    function updateCourtInfo() {
        const c = byId(courtSelect.value);
        if (!c) return;
        document.getElementById('hinhAnhSan').src = c.image_url || '';
        document.getElementById('tenSanGioiThieu').textContent = c.ten_san || c.loai_san || 'Sân thể thao';
        document.getElementById('loaiSanGioiThieu').textContent = c.loai_san || '';
        document.getElementById('kieuSanGioiThieu').textContent = c.kieu_san || '';
        document.getElementById('maSanGioiThieu').textContent = '#' + c.ma_san;
        document.getElementById('chuSanGioiThieu').textContent = c.ten_chu_san || '';
        document.getElementById('diaChiSanGioiThieu').textContent = c.dia_chi || '';
        document.getElementById('moTaSanGioiThieu').textContent = c.mo_ta || 'Chưa có mô tả.';
        document.getElementById('hienThiSan').textContent = c.loai_san || c.ten_san || '';
        dateCourt.value = courtSelect.value;
    }

    function renderSlotOptions(preferredId) {
        const rows = currentSlots();
        slotSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '-- Chọn khung giờ --';
        slotSelect.appendChild(placeholder);

        rows.forEach(s => {
            const option = document.createElement('option');
            option.value = s.id;
            let suffix = '';
            if (s.booked) suffix = ' — Đã đặt';
            else if (s.status !== 'Trống') suffix = ' — Tạm khóa';
            else if (s.inCart) suffix = ' — Đã trong giỏ';
            option.textContent = s.start + ' – ' + s.end + ' · ' + s.hours + ' giờ · ' + money(s.total) + suffix;
            option.disabled = !!s.booked || s.status !== 'Trống' || !!s.inCart;
            slotSelect.appendChild(option);
        });

        const available = rows.find(s => !s.booked && s.status === 'Trống' && !s.inCart);
        const wanted = rows.find(s => Number(s.id) === Number(preferredId));
        slotSelect.value = wanted && !wanted.booked && wanted.status === 'Trống' && !wanted.inCart
            ? String(wanted.id)
            : (available ? String(available.id) : '');
        updateSummary();
    }

    function renderPriceTable() {
        const target = document.getElementById('noiDungBangGia');
        const rows = currentSlots();
        target.innerHTML = '';
        if (!rows.length) {
            const p = document.createElement('p'); p.textContent = 'Sân này chưa được cấu hình khung giờ.'; target.appendChild(p); return;
        }
        rows.forEach(s => {
            const p = document.createElement('p');
            p.appendChild(document.createTextNode(s.start + ' – ' + s.end + ' (' + s.hours + ' giờ)'));
            const span = document.createElement('span');
            span.textContent = money(s.rate) + '/giờ';
            p.appendChild(span);
            target.appendChild(p);
        });
    }

    function updateSummary() {
        const selected = currentSlots().find(s => Number(s.id) === Number(slotSelect.value));
        dateHidden.value = dateInput.value;
        document.getElementById('hienThiNgay').textContent = dateInput.value || 'Chưa chọn';
        if (!selected) {
            document.getElementById('hienThiGio').textContent = '--:-- – --:--';
            document.getElementById('hienThiSoGio').textContent = '0 giờ';
            document.getElementById('hienThiThanhTien').textContent = '0đ';
            document.getElementById('trangThaiKhungGio').textContent = 'Vui lòng chọn một khung giờ còn trống.';
            document.getElementById('thongBaoGia').textContent = '';
            addButton.disabled = true;
            return;
        }
        document.getElementById('hienThiGio').textContent = selected.start + ' – ' + selected.end;
        document.getElementById('hienThiSoGio').textContent = selected.hours + ' giờ';
        document.getElementById('hienThiThanhTien').textContent = money(selected.total);
        document.getElementById('trangThaiKhungGio').textContent = 'Đơn giá ' + money(selected.rate) + '/giờ';
        document.getElementById('thongBaoGia').textContent = 'Khung giờ sẽ được kiểm tra lại khi thêm vào giỏ hàng.';
        addButton.disabled = !!selected.booked || selected.status !== 'Trống' || !!selected.inCart;
    }

    courtSelect.addEventListener('change', function () {
        updateCourtInfo(); renderSlotOptions(0); renderPriceTable();
    });
    slotSelect.addEventListener('change', updateSummary);
    dateInput.addEventListener('change', function () { dateHidden.value = dateInput.value; });
    document.getElementById('formNgay').addEventListener('submit', function () {
        dateCourt.value = courtSelect.value;
    });
    document.getElementById('formDatSan').addEventListener('submit', function (event) {
        dateHidden.value = dateInput.value;
        if (!slotSelect.value) {
            event.preventDefault();
            alert('Vui lòng chọn một khung giờ còn trống.');
        }
    });

    document.querySelectorAll('.btn-kieu-san').forEach(button => {
        button.addEventListener('click', function () {
            const type = this.dataset.kieu;
            const option = Array.from(courtSelect.options).find(o => o.dataset.kieu === type);
            if (option) {
                courtSelect.value = option.value;
                document.querySelectorAll('.btn-kieu-san').forEach(b => b.classList.toggle('active', b === button));
                updateCourtInfo(); renderSlotOptions(0); renderPriceTable();
            }
        });
    });

    updateCourtInfo();
    const currentCourt = byId(courtSelect.value);
    if (currentCourt) {
        document.querySelectorAll('.btn-kieu-san').forEach(b => b.classList.toggle('active', b.dataset.kieu === currentCourt.kieu_san));
    }
    renderSlotOptions(INITIAL_SLOT_ID);
    renderPriceTable();
})();
</script>
</body>
</html>
