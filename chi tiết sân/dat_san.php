<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
require_once __DIR__ . '/db.php';
if (!isset($_SESSION['gioHang']) || !is_array($_SESSION['gioHang'])) {
    $_SESSION['gioHang'] = [];
}
function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| CHUẨN HÓA ĐƯỜNG DẪN HÌNH ẢNH SÂN TỪ MYSQL
|--------------------------------------------------------------------------
| Database có thể lưu:
| - san1.jpg
| - images/san1.jpg
| - uploads/san1.jpg
| - đường dẫn đầy đủ của thư mục dat-san
*/
function court_image_url($imagePath) {
    $imagePath = trim((string)$imagePath);

    if ($imagePath === '') {
        return 'images/san-vaa.jpg';
    }

    // Cho phép đường dẫn ảnh từ máy chủ bên ngoài.
    if (preg_match('~^https?://~i', $imagePath)) {
        return $imagePath;
    }

    $imagePath = str_replace('\\', '/', $imagePath);

    // Chuyển đường dẫn tuyệt đối của XAMPP về đường dẫn tương đối website.
    $projectMarker = '/dat-san/';
    $markerPosition = stripos($imagePath, $projectMarker);
    if ($markerPosition !== false) {
        $imagePath = substr($imagePath, $markerPosition + strlen($projectMarker));
    }

    $imagePath = ltrim($imagePath, '/');

    // Nếu database chỉ lưu tên file thì mặc định ảnh nằm trong images/.
    if (strpos($imagePath, '/') === false) {
        $imagePath = 'images/' . $imagePath;
    }

    // Không cho phép đường dẫn thoát ra ngoài thư mục dự án.
    if (strpos($imagePath, '../') !== false || $imagePath === '') {
        return 'images/san-vaa.jpg';
    }

    return $imagePath;
}

function flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}
function redirect_self($courtId = 0) {
    $url = 'dat_san.php';
    if ((int)$courtId > 0) {
        $url .= '?ma_san=' . (int)$courtId;
    }
    header('Location: ' . $url);
    exit;
}
function time_to_minutes($time) {
    $parts = explode(':', $time);
    return ((int)$parts[0] * 60) + (int)$parts[1];
}
/*
|--------------------------------------------------------------------------
| TÍNH GIÁ THEO KHUNG GIỜ MYSQL
|--------------------------------------------------------------------------
*/
function calculate_price($conn, $courtId, $start, $end) {
    $sql = "SELECT gio_bat_dau, gio_ket_thuc, gia_moi_gio
            FROM khung_gio
            WHERE ma_san = ?
              AND gio_bat_dau IS NOT NULL
              AND gio_ket_thuc IS NOT NULL
            ORDER BY gio_bat_dau";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Không thể truy vấn bảng giá.');
    }
    mysqli_stmt_bind_param($stmt, 'i', $courtId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $bands = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bands[] = $row;
    }
    mysqli_stmt_close($stmt);
    if (empty($bands)) {
        throw new Exception('Sân chưa có bảng giá ngày thường trong cơ sở dữ liệu.');
    }
    $cursor = time_to_minutes($start);
    $finish = time_to_minutes($end);
    $total = 0;
    while ($cursor < $finish) {
        $matched = false;
        foreach ($bands as $band) {
            $bandStart = time_to_minutes($band['gio_bat_dau']);
            $bandEnd = time_to_minutes($band['gio_ket_thuc']);
            if ($cursor >= $bandStart && $cursor < $bandEnd) {
                $segmentEnd = min($finish, $bandEnd);
                $hours = ($segmentEnd - $cursor) / 60;
                $total += $hours * (float)$band['gia_moi_gio'];
                $cursor = $segmentEnd;
                $matched = true;
                break;
            }
        }
        if (!$matched) {
            throw new Exception('Khung giờ bạn chọn chưa có bảng giá trong cơ sở dữ liệu.');
        }
    }
    return $total;
}
/*
|--------------------------------------------------------------------------
| KIỂM TRA TRÙNG LỊCH ĐẶT SÂN
|--------------------------------------------------------------------------
*/
function booking_conflict($conn, $courtId, $date, $start, $end) {
    $sql = "SELECT COUNT(*) AS total
            FROM chitietdondatsan ct
            INNER JOIN dondatsan d ON d.idDon = ct.idDon
            WHERE ct.ma_san = ?
              AND ct.NgaySuDung = ?
              AND ct.GioBatDau < ?
              AND ct.GioKetThuc > ?
              AND d.TrangThai <> 3";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Không thể kiểm tra lịch đặt sân.');
    }
    mysqli_stmt_bind_param(
        $stmt,
        'isss',
        $courtId,
        $date,
        $end,
        $start
    );
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (int)$row['total'] > 0;
}
/*
|--------------------------------------------------------------------------
| KIỂM TRA TRÙNG KHUNG GIỜ TRONG GIỎ HÀNG
|--------------------------------------------------------------------------
*/
function cart_conflict($cart, $courtId, $date, $start, $end) {
    foreach ($cart as $item) {
        if (
            (int)$item['ma_san'] === (int)$courtId
            && $item['ngay'] === $date
            && $start < $item['gio_ket_thuc']
            && $end > $item['gio_bat_dau']
        ) {
            return true;
        }
    }
    return false;
}
/*
|--------------------------------------------------------------------------
| LẤY THÔNG BÁO
|--------------------------------------------------------------------------
*/
$notice = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
/*
|--------------------------------------------------------------------------
| LẤY DANH SÁCH SÂN TỪ MYSQL
|--------------------------------------------------------------------------
*/
$courts = [];
$courtResult = mysqli_query(
    $conn,
    "SELECT s.ma_san, s.ma_chu_san, s.loai_san, s.ten_san,
            s.kieu_san, s.mo_ta, s.gia, s.hinh_anh, cs.dia_chi
     FROM san AS s
     LEFT JOIN chu_san AS cs ON cs.ma_chu_san = s.ma_chu_san
     WHERE s.trang_thai = 'Hoạt động'
     ORDER BY s.ma_san"
);
if ($courtResult) {
    while ($row = mysqli_fetch_assoc($courtResult)) {
        // Tạo đường dẫn dùng chung cho PHP và JavaScript.
        $row['hinh_anh_url'] = court_image_url($row['hinh_anh'] ?? '');
        $courts[] = $row;
    }
}
/*
|--------------------------------------------------------------------------
| LẤY BẢNG GIÁ TỪ MYSQL
|--------------------------------------------------------------------------
*/
$priceData = [];
$priceResult = mysqli_query(
    $conn,
    "SELECT ma_san, gio_bat_dau, gio_ket_thuc, gia_moi_gio
     FROM khung_gio
     WHERE gio_bat_dau IS NOT NULL
       AND gio_ket_thuc IS NOT NULL
     ORDER BY ma_san, gio_bat_dau"
);
if ($priceResult) {
    while ($row = mysqli_fetch_assoc($priceResult)) {
        $courtId = (string)$row['ma_san'];
        if (!isset($priceData[$courtId])) {
            $priceData[$courtId] = [];
        }
        $priceData[$courtId][] = [
            'start' => substr($row['gio_bat_dau'], 0, 5),
            'end' => substr($row['gio_ket_thuc'], 0, 5),
            'price' => (float)$row['gia_moi_gio']
        ];
    }
}
/*
|--------------------------------------------------------------------------
| LẤY DỮ LIỆU ĐẶT SÂN
|--------------------------------------------------------------------------
*/
$selectedCourt = isset($_POST['ma_san'])
    ? (int)$_POST['ma_san']
    : (int)($_GET['ma_san'] ?? 0);
// Không hiển thị bộ chọn sân trên giao diện: dùng mã sân từ trang trước.
// Nếu mở trực tiếp dat_san.php để chạy thử, tự lấy sân đang hoạt động đầu tiên.
if ($selectedCourt <= 0 && !empty($courts)) {
    $selectedCourt = (int)$courts[0]['ma_san'];
}
$validSelectedCourt = false;
foreach ($courts as $courtItem) {
    if ((int)$courtItem['ma_san'] === $selectedCourt) {
        $validSelectedCourt = true;
        break;
    }
}
if (!$validSelectedCourt && !empty($courts)) {
    $selectedCourt = (int)$courts[0]['ma_san'];
}
$date = $_POST['ngay'] ?? $_GET['ngay'] ?? date('Y-m-d');
$start = $_POST['gioBatDau'] ?? '';
$end = $_POST['gioKetThuc'] ?? '';
$action = $_POST['action'] ?? '';
/*
|--------------------------------------------------------------------------
| XỬ LÝ THÊM VÀO GIỎ HÀNG
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // GỬI ĐÁNH GIÁ CỦA KHÁCH HÀNG
        if ($action === 'review') {
            $selectedCourt = (int)($_POST['ma_san'] ?? $selectedCourt);
            $courtExistsForReview = false;
            foreach ($courts as $courtItem) {
                if ((int)$courtItem['ma_san'] === $selectedCourt) {
                    $courtExistsForReview = true;
                    break;
                }
            }
            if (!$courtExistsForReview) {
                throw new Exception('Không xác định được sân cần đánh giá.');
            }
            $tenDanhGia = trim($_POST['ten_danh_gia'] ?? '');
            $soSao = (int)($_POST['so_sao'] ?? 0);
            $noiDungDanhGia = trim($_POST['noi_dung_danh_gia'] ?? '');

            if ($tenDanhGia === '' || mb_strlen($tenDanhGia) > 60) {
                throw new Exception('Vui lòng nhập biệt danh (tối đa 60 ký tự).');
            }
            if ($soSao < 1 || $soSao > 5) {
                throw new Exception('Vui lòng chọn số sao từ 1 đến 5.');
            }
            if ($noiDungDanhGia === '' || mb_strlen($noiDungDanhGia) > 1000) {
                throw new Exception('Vui lòng nhập nội dung đánh giá (tối đa 1.000 ký tự).');
            }

            $sqlReview = "INSERT INTO danh_gia_san
                          (ma_san, ten_nguoi_danh_gia, so_sao, noi_dung, ngay_danh_gia)
                          VALUES (?, ?, ?, ?, NOW())";
            $stmtReview = mysqli_prepare($conn, $sqlReview);
            if (!$stmtReview) {
                throw new Exception('Không thể chuẩn bị lưu đánh giá. Hãy kiểm tra bảng danh_gia_san.');
            }
            mysqli_stmt_bind_param($stmtReview, 'isis', $selectedCourt, $tenDanhGia, $soSao, $noiDungDanhGia);
            if (!mysqli_stmt_execute($stmtReview)) {
                mysqli_stmt_close($stmtReview);
                throw new Exception('Không thể lưu đánh giá vào cơ sở dữ liệu.');
            }
            mysqli_stmt_close($stmtReview);
            flash('Cảm ơn bạn đã gửi đánh giá!');
            redirect_self($selectedCourt);
        }

        if ($action === 'add') {
            $courtFound = false;
            foreach ($courts as $court) {
                if ((int)$court['ma_san'] === $selectedCourt) {
                    $courtFound = true;
                    break;
                }
            }
            if (!$courtFound) {
                throw new Exception('Vui lòng chọn sân đang hoạt động.');
            }
            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            if (
                !$dateObj
                || $dateObj->format('Y-m-d') !== $date
                || $date < date('Y-m-d')
            ) {
                throw new Exception('Ngày đặt không hợp lệ hoặc đã qua.');
            }
            if (
                !preg_match('/^\d{2}:\d{2}$/', $start)
                || !preg_match('/^\d{2}:\d{2}$/', $end)
            ) {
                throw new Exception('Vui lòng chọn giờ bắt đầu và giờ kết thúc.');
            }
            $startMinutes = time_to_minutes($start);
            $endMinutes = time_to_minutes($end);
            if (
                $startMinutes < 360
                || $endMinutes > 1380
                || $endMinutes <= $startMinutes
                || $startMinutes % 30 !== 0
                || $endMinutes % 30 !== 0
            ) {
                throw new Exception('Vui lòng chọn giờ hợp lệ từ 06:00 đến 23:00, cách nhau 30 phút.');
            }
            if ($date === date('Y-m-d')) {
                $currentMinutes = ((int)date('H') * 60) + (int)date('i');
                if ($startMinutes <= $currentMinutes) {
                    throw new Exception('Giờ bắt đầu phải muộn hơn thời điểm hiện tại.');
                }
            }
            if (
                booking_conflict(
                    $conn,
                    $selectedCourt,
                    $date,
                    $start . ':00',
                    $end . ':00'
                )
            ) {
                throw new Exception('Sân đã có đơn đặt trong khung giờ này. Vui lòng chọn giờ khác.');
            }
            if (
                cart_conflict(
                    $_SESSION['gioHang'],
                    $selectedCourt,
                    $date,
                    $start,
                    $end
                )
            ) {
                throw new Exception('Khung giờ này đã có trong giỏ hàng.');
            }
            $previewPrice = calculate_price(
                $conn,
                $selectedCourt,
                $start . ':00',
                $end . ':00'
            );
            $courtName = '';
            foreach ($courts as $court) {
                if ((int)$court['ma_san'] === $selectedCourt) {
                    $courtName = !empty($court['ten_san'])
                        ? $court['ten_san']
                        : $court['loai_san'];
                    $courtName .= ' - ' . $court['kieu_san'];
                    break;
                }
            }
            $_SESSION['gioHang'][] = [
                'ma_san' => $selectedCourt,
                'ten_san' => $courtName,
                'ngay' => $date,
                'gio_bat_dau' => $start,
                'gio_ket_thuc' => $end,
                'so_gio' => (
                    time_to_minutes($end) - time_to_minutes($start)
                ) / 60,
                'gia_tien' => $previewPrice
            ];
            flash('Đã thêm sân vào giỏ hàng.');
            redirect_self($selectedCourt);
        }
    } catch (Throwable $e) {
        $notice = [
            'message' => $e->getMessage(),
            'type' => 'error'
        ];
    }
}
/*
|--------------------------------------------------------------------------
| LẤY THÔNG TIN SÂN ĐANG CHỌN
|--------------------------------------------------------------------------
*/
$selectedName = 'Chưa chọn';
$selectedCourtData = null;
foreach ($courts as $court) {
    if ((int)$court['ma_san'] === $selectedCourt) {
        $selectedCourtData = $court;
        $selectedName = (!empty($court['ten_san']) ? $court['ten_san'] : $court['loai_san'])
            . ' - '
            . $court['kieu_san']
            . ' (#'
            . $court['ma_san']
            . ')';
        break;
    }
}

/*
|--------------------------------------------------------------------------
| LẤY DANH SÁCH ĐÁNH GIÁ TỪ MYSQL
|--------------------------------------------------------------------------
*/
$reviews = [];
$reviewCount = 0;
$averageRating = 0;
$reviewResult = mysqli_query(
    $conn,
    "SELECT ten_nguoi_danh_gia, so_sao, noi_dung, ngay_danh_gia
     FROM danh_gia_san
     WHERE ma_san = " . (int)$selectedCourt . "
     ORDER BY ngay_danh_gia DESC"
);
if ($reviewResult) {
    while ($reviewRow = mysqli_fetch_assoc($reviewResult)) {
        $reviews[] = $reviewRow;
    }
    $reviewCount = count($reviews);
    if ($reviewCount > 0) {
        $sumStars = 0;
        foreach ($reviews as $reviewRow) {
            $sumStars += (int)$reviewRow['so_sao'];
        }
        $averageRating = round($sumStars / $reviewCount, 1);
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt sân thể thao VAA</title>
    <link rel="stylesheet" href="style_dat_san1.css?v=20261130">
</head>
<body>
<header>
    <nav>
        <div class="menu">
            <a href="trang-chu.php">Trang chủ</a>
            <a href="dat_san.php">Đặt sân</a>
            <a href="lien-he.php">Liên hệ</a>
        </div>
    </nav>
</header>
<!-- NÚT GIỎ HÀNG NỔI -->
<a href="#gioHang" class="gio-hang" id="gioHang">
    🛒 Giỏ hàng
    <span>
        <?php echo count($_SESSION['gioHang']); ?>
    </span>
</a>
<!-- THÔNG TIN SÂN ĐƯỢC CHỌN -->
<div class="khung">
    <div class="hinh-san">
        <img
            id="hinhAnhSan"
            src="<?php
                echo h($selectedCourtData['hinh_anh_url'] ?? 'images/san-vaa.jpg');
            ?>"
            alt="Hình ảnh sân thể thao"
        >
    </div>
    <div class="gioi-thieu-san">
        <h2 id="tenSanGioiThieu">
            <?php
                echo $selectedCourtData
                    ? h(!empty($selectedCourtData['ten_san']) ? $selectedCourtData['ten_san'] : $selectedCourtData['loai_san'])
                    : 'Sân thể thao VAA';
            ?>
        </h2>
        <p>
            <b>Kiểu sân:</b>
            <span id="kieuSanGioiThieu">
                <?php
                    echo $selectedCourtData
                        ? h($selectedCourtData['kieu_san'])
                        : 'Chưa chọn sân';
                ?>
            </span>
        </p>
        <p>
            <b>Mã sân:</b>
            <span id="maSanGioiThieu">
                <?php
                    echo $selectedCourtData
                        ? '#' . (int)$selectedCourtData['ma_san']
                        : 'Chưa chọn';
                ?>
            </span>
        </p>
        <p>
            <b>Địa chỉ:</b>
            <span id="diaChiSanGioiThieu">
                <?php echo $selectedCourtData && !empty($selectedCourtData['dia_chi'])
                    ? h($selectedCourtData['dia_chi'])
                    : 'Chưa cập nhật địa chỉ.'; ?>
            </span>
        </p>
        <p>
            <b>Đánh giá:</b>
            <span id="diemDanhGia">
                <?php if ($reviewCount > 0): ?>
                    <span style="color:#e6a000;">★</span>
                    <?php echo number_format($averageRating, 1); ?>/5
                    (<?php echo $reviewCount; ?> đánh giá)
                <?php else: ?>
                    Chưa có đánh giá
                <?php endif; ?>
            </span><br>
            <b>Mô tả:</b>
            <span id="moTaSanGioiThieu">
                <?php
                    echo $selectedCourtData && !empty($selectedCourtData['mo_ta'])
                        ? h($selectedCourtData['mo_ta'])
                        : 'Thông tin sân đang được cập nhật.';
                ?>
            </span>
        </p>
    </div>
</div>
<div style="clear:both"></div>
<?php if ($notice): ?>
    <p
        class="thong-bao"
        role="status"
        style="<?php echo $notice['type'] === 'error'
            ? 'background:#ffe8e8;color:#a40000;'
            : ''; ?>"
    >
        <?php echo h($notice['message']); ?>
    </p>
<?php endif; ?>
<?php if (empty($courts)): ?>
    <p class="thong-bao" style="background:#fff0f0;color:#a40000;">
        Hiện chưa có sân nào đang hoạt động. Hãy kiểm tra dữ liệu bảng san trong MySQL.
    </p>
<?php endif; ?>
<div class="dat-san">
    <!-- Danh sách sân được giữ ẩn để JavaScript và chức năng đặt sân nhận ma_san từ URL -->
    <select id="ma_san" aria-hidden="true" tabindex="-1"
            style="position:absolute;left:-9999px;width:1px;height:1px;">
        <option value="">-- Chưa chọn sân --</option>
        <?php foreach ($courts as $court): ?>
            <option value="<?php echo (int)$court['ma_san']; ?>"
                <?php echo $selectedCourt === (int)$court['ma_san'] ? 'selected' : ''; ?>>
                <?php echo h($court['loai_san'] . ' (#' . $court['ma_san'] . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="lich-co-san">
        <h3>Chọn ngày</h3>
        <label for="ngay">Ngày sử dụng</label>
        <input
            type="date"
            id="ngay"
            value="<?php echo h($date); ?>"
            min="<?php echo date('Y-m-d'); ?>"
            form="formDatSan"
            name="ngay"
            required
        >
        <p>Chỉ nhận ngày hiện tại hoặc ngày trong tương lai.</p>
    </div>

    <div class="chon-gio">
        <h3>Chọn giờ</h3>
        <label for="gioBatDau">Giờ bắt đầu</label>
        <select id="gioBatDau" name="gioBatDau" form="formDatSan" required>
            <option value="">-- Chọn giờ --</option>
            <?php for ($i = 360; $i <= 1350; $i += 30):
                $v = sprintf('%02d:%02d', floor($i / 60), $i % 60); ?>
                <option value="<?php echo $v; ?>" <?php echo $start === $v ? 'selected' : ''; ?>>
                    <?php echo $v; ?>
                </option>
            <?php endfor; ?>
        </select>

        <label for="gioKetThuc">Giờ kết thúc</label>
        <select id="gioKetThuc" name="gioKetThuc" form="formDatSan" required>
            <option value="">-- Chọn giờ --</option>
            <?php for ($i = 390; $i <= 1380; $i += 30):
                $v = sprintf('%02d:%02d', floor($i / 60), $i % 60); ?>
                <option value="<?php echo $v; ?>" <?php echo $end === $v ? 'selected' : ''; ?>>
                    <?php echo $v; ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="bang-gia">
        <h3>Bảng giá</h3>
        <div id="noiDungBangGia">
            <p>Bảng giá của sân đang xem sẽ hiển thị tại đây.</p>
        </div>
    </div>
</div>
<!-- THANH TỔNG KẾT -->
<div class="tong-ket">
    <form method="post" id="formDatSan">
        <input type="hidden" name="action" value="add">
        <input
            type="hidden"
            name="ma_san"
            id="maSanHidden"
            value="<?php echo $selectedCourt; ?>"
        >
        <div class="thong-tin-tong">
            <span>Sân đã chọn</span>
            <strong id="hienThiSan">
                <?php echo h($selectedName); ?>
            </strong>
        </div>
        <div class="thong-tin-tong">
            <span>Ngày sử dụng</span>
            <strong id="hienThiNgay">
                <?php echo h($date ?: 'Chưa chọn'); ?>
            </strong>
        </div>
        <div class="thong-tin-tong">
            <span>Khung giờ</span>
            <strong id="hienThiGio">
                <?php
                echo h($start ?: '--:--')
                    . ' – '
                    . h($end ?: '--:--');
                ?>
            </strong>
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
            <button type="submit" class="nut-chon">
                Thêm vào giỏ hàng
            </button>
            <button
                type="button"
                class="nut-thanh-toan-nhanh"
                disabled
                title="Chức năng thanh toán sẽ được kết nối ở bước xử lý đơn hàng"
            >
                Thanh toán nhanh
            </button>
        </div>
        <p id="thongBaoGia" style="color:#a40000;margin-top:10px;"></p>
    </form>
</div>
<!-- KHU VỰC ĐÁNH GIÁ KHÁCH HÀNG -->
<section class="khu-vuc-danh-gia" id="khuVucDanhGia">
    <div class="tieu-de-danh-gia">
        <h2>✎ Viết đánh giá của bạn</h2>
    </div>

    <form method="post" class="form-danh-gia" id="formDanhGia">
        <input type="hidden" name="action" value="review">
        <input type="hidden" name="ma_san" value="<?php echo (int)$selectedCourt; ?>">

        <div class="truong-danh-gia">
            <label for="ten_danh_gia">Tên của bạn</label>
            <input
                type="text"
                id="ten_danh_gia"
                name="ten_danh_gia"
                maxlength="60"
                placeholder="Nhập biệt danh của bạn"
                required
            >
        </div>

        <div class="truong-danh-gia chon-sao">
            <label>Chọn số sao</label>
            <div class="chon-sao-danh-gia" role="radiogroup" aria-label="Chọn số sao">
                <?php for ($star = 1; $star <= 5; $star++): ?>
                    <input type="radio" id="sao<?php echo $star; ?>"
                           name="so_sao" value="<?php echo $star; ?>" required>
                    <label for="sao<?php echo $star; ?>" title="<?php echo $star; ?> sao">★</label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="truong-danh-gia noi-dung-danh-gia">
            <label for="noi_dung_danh_gia">Nội dung đánh giá</label>
            <textarea
                id="noi_dung_danh_gia"
                name="noi_dung_danh_gia"
                maxlength="1000"
                rows="2"
                placeholder="Viết bình luận của bạn..."
                required
            ></textarea>
        </div>

        <button type="submit" class="nut-gui-danh-gia">➤ Gửi đánh giá</button>
    </form>

    <div class="khu-vuc-xem-danh-gia">
        <button type="button" class="nut-xem-danh-gia"
                id="nutXemDanhGia" aria-expanded="false"
                aria-controls="danhSachDanhGia">
            <span id="bieuTuongXemDanhGia">⌄</span>
            Xem tất cả đánh giá (<?php echo $reviewCount; ?>)
        </button>

        <div class="danh-sach-danh-gia" id="danhSachDanhGia" hidden>
            <h3>★ Danh sách đánh giá</h3>

            <?php if ($reviewCount === 0): ?>
                <p class="chua-co-danh-gia">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sân!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <article class="mot-danh-gia">
                        <div class="avatar-danh-gia">●</div>
                        <div class="thong-tin-nguoi-danh-gia">
                            <strong><?php echo h($review['ten_nguoi_danh_gia']); ?></strong>
                            <span><?php echo date('d/m/Y', strtotime($review['ngay_danh_gia'])); ?></span>
                        </div>
                        <div class="so-sao-hien-thi" aria-label="<?php echo (int)$review['so_sao']; ?> trên 5 sao">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="<?php echo $i <= (int)$review['so_sao'] ? 'sao-day' : 'sao-rong'; ?>">
                                    <?php echo $i <= (int)$review['so_sao'] ? '★' : '☆'; ?>
                                </span>
                            <?php endfor; ?>
                        </div>
                        <p><?php echo nl2br(h($review['noi_dung'])); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <button type="button" class="nut-thu-gon-danh-gia" id="nutThuGonDanhGia">
                Thu gọn đánh giá ⌃
            </button>
        </div>
    </div>
</section>

<script>
/*
|--------------------------------------------------------------------------
| DỮ LIỆU SÂN VÀ BẢNG GIÁ TỪ MYSQL
|--------------------------------------------------------------------------
*/
var courtData = <?php
    echo json_encode(
        $courts,
        JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
        | JSON_HEX_AMP
    );
?>;
var priceData = <?php
    echo json_encode(
        $priceData,
        JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
        | JSON_HEX_AMP
    );
?>;
/*
|--------------------------------------------------------------------------
| CẬP NHẬT THÔNG TIN SÂN KHI CHỌN SÂN KHÁC
|--------------------------------------------------------------------------
*/
(function () {
    var selectSan = document.getElementById('ma_san');
    var hinhAnhSan = document.getElementById('hinhAnhSan');
    var tenSan = document.getElementById('tenSanGioiThieu');
    var kieuSan = document.getElementById('kieuSanGioiThieu');
    var maSan = document.getElementById('maSanGioiThieu');
    var moTaSan = document.getElementById('moTaSanGioiThieu');
    var diaChiSan = document.getElementById('diaChiSanGioiThieu');
    var fallbackImage = 'images/san-vaa.jpg';
    function capNhatThongTinSan() {
        var courtId = selectSan.value;
        var court = courtData.find(function (item) {
            return String(item.ma_san) === String(courtId);
        });
        if (!court) {
            hinhAnhSan.src = fallbackImage;
            tenSan.textContent = 'Sân thể thao VAA';
            kieuSan.textContent = 'Chưa chọn sân';
            maSan.textContent = 'Chưa chọn';
            moTaSan.textContent = 'Thông tin sân đang được cập nhật.';
            diaChiSan.textContent = 'Chưa cập nhật địa chỉ.';
            return;
        }
        tenSan.textContent = court.ten_san || court.loai_san || 'Sân thể thao';
        kieuSan.textContent = court.kieu_san || 'Chưa cập nhật';
        maSan.textContent = '#' + court.ma_san;
        diaChiSan.textContent = court.dia_chi || 'Chưa cập nhật địa chỉ.';
        moTaSan.textContent = court.mo_ta || 'Chưa có mô tả cho sân này.';
        hinhAnhSan.src = court.hinh_anh_url || fallbackImage;
        hinhAnhSan.onerror = function () {
            this.onerror = null;
            this.src = fallbackImage;
        };
    }
    selectSan.addEventListener('change', capNhatThongTinSan);
    capNhatThongTinSan();
})();
/*
|--------------------------------------------------------------------------
| CẬP NHẬT BẢNG GIÁ VÀ TÍNH TIỀN - CHỈ NGÀY THƯỜNG
|--------------------------------------------------------------------------
*/
(function () {
    var selectSan = document.getElementById('ma_san');
    var ngay = document.getElementById('ngay');
    var gioBatDau = document.getElementById('gioBatDau');
    var gioKetThuc = document.getElementById('gioKetThuc');
    var hienThiSan = document.getElementById('hienThiSan');
    var hienThiNgay = document.getElementById('hienThiNgay');
    var hienThiGio = document.getElementById('hienThiGio');
    var hienThiSoGio = document.getElementById('hienThiSoGio');
    var hienThiThanhTien = document.getElementById('hienThiThanhTien');
    var noiDungBangGia = document.getElementById('noiDungBangGia');
    var thongBaoGia = document.getElementById('thongBaoGia');
    var maSanHidden = document.getElementById('maSanHidden');
    function toMinutes(time) {
        if (!time) return 0;
        var parts = time.split(':');
        return Number(parts[0]) * 60 + Number(parts[1]);
    }
    function formatMoney(value) {
        return Math.round(value).toLocaleString('vi-VN') + 'đ';
    }
    function formatHours(value) {
        return Number.isInteger(value)
            ? value + ' giờ'
            : value.toLocaleString('vi-VN') + ' giờ';
    }
    function calculateSelectedPrice() {
        var courtId = selectSan.value;
        var start = gioBatDau.value;
        var end = gioKetThuc.value;
        var startMinutes = toMinutes(start);
        var endMinutes = toMinutes(end);
        if (!courtId || !start || !end || endMinutes <= startMinutes) {
            hienThiSoGio.textContent = '0 giờ';
            hienThiThanhTien.textContent = '0đ';
            thongBaoGia.textContent = '';
            return;
        }
        var totalMinutes = endMinutes - startMinutes;
        var totalHours = totalMinutes / 60;
        hienThiSoGio.textContent = formatHours(totalHours);
        var bands = priceData[courtId];
        if (!bands || bands.length === 0) {
            hienThiThanhTien.textContent = 'Chưa có giá';
            thongBaoGia.textContent =
                'Sân này chưa có bảng giá ngày thường.';
            return;
        }
        var cursor = startMinutes;
        var total = 0;
        while (cursor < endMinutes) {
            var matched = false;
            for (var i = 0; i < bands.length; i++) {
                var bandStart = toMinutes(bands[i].start);
                var bandEnd = toMinutes(bands[i].end);
                if (cursor >= bandStart && cursor < bandEnd) {
                    var segmentEnd = Math.min(endMinutes, bandEnd);
                    var hours = (segmentEnd - cursor) / 60;
                    total += hours * Number(bands[i].price);
                    cursor = segmentEnd;
                    matched = true;
                    break;
                }
            }
            if (!matched) {
                hienThiThanhTien.textContent = 'Chưa có giá';
                thongBaoGia.textContent =
                    'Khung giờ đã chọn chưa có giá trong bảng khung_gio.';
                return;
            }
        }
        hienThiThanhTien.textContent = formatMoney(total);
        thongBaoGia.textContent = '';
    }
    function updatePriceTable() {
        var courtId = selectSan.value;
        var bands = priceData[courtId];
        if (!courtId) {
            noiDungBangGia.innerHTML =
                '<p>Vui lòng chọn sân để xem bảng giá.</p>';
            return;
        }
        if (!bands || bands.length === 0) {
            noiDungBangGia.innerHTML =
                '<p>Chưa có bảng giá ngày thường cho sân này.</p>';
            return;
        }
        // Gom các khung giờ có cùng mức giá thành một nhóm.
        var groups = {};
        bands.forEach(function (band) {
            var priceKey = String(Math.round(Number(band.price) * 100) / 100);
            if (!groups[priceKey]) {
                groups[priceKey] = { price: Number(band.price), times: [] };
            }
            groups[priceKey].times.push(band.start + ' - ' + band.end);
        });

        var html = '';
        Object.keys(groups).forEach(function (key) {
            var group = groups[key];
            html += '<div class="nhom-gia">'
                + '<div class="nhom-gia-thoigian">'
                + group.times.join('<br>')
                + '</div>'
                + '<strong class="nhom-gia-tien">'
                + formatMoney(group.price)
                + '/giờ</strong>'
                + '</div>';
        });
        noiDungBangGia.innerHTML = html;
    }
    function capNhatLuaChon() {
        var option = selectSan.options[selectSan.selectedIndex];
        hienThiSan.textContent =
            selectSan.value ? option.text : 'Chưa chọn';
        maSanHidden.value = selectSan.value;
        hienThiNgay.textContent =
            ngay.value || 'Chưa chọn';
        hienThiGio.textContent =
            (gioBatDau.value || '--:--')
            + ' – '
            + (gioKetThuc.value || '--:--');
        updatePriceTable();
        calculateSelectedPrice();
    }
    selectSan.addEventListener('change', capNhatLuaChon);
    ngay.addEventListener('change', capNhatLuaChon);
    gioBatDau.addEventListener('change', capNhatLuaChon);
    gioKetThuc.addEventListener('change', capNhatLuaChon);
    capNhatLuaChon();
})();
/*
|--------------------------------------------------------------------------
| HIỆU ỨNG THÊM SÂN VÀO GIỎ HÀNG
|--------------------------------------------------------------------------
*/
(function () {
    var form = document.getElementById('formDatSan');
    if (!form) return;
    var submitting = false;
    form.addEventListener('submit', function (event) {
        if (submitting) return;
        var button = event.submitter;
        if (
            !button
            || button.textContent.indexOf('Thêm vào giỏ') === -1
        ) {
            return;
        }
        event.preventDefault();
        var cart = document.querySelector('.gio-hang');
        var buttonRect = button.getBoundingClientRect();
        var cartRect = cart
            ? cart.getBoundingClientRect()
            : null;
        if (!cartRect) {
            submitting = true;
            form.submit();
            return;
        }
        var dot = document.createElement('div');
        dot.className = 'hieu-ung-bay';
        dot.textContent = '+1';
        dot.style.left = buttonRect.left + 'px';
        dot.style.top = buttonRect.top + 'px';
        document.body.appendChild(dot);
        setTimeout(function () {
            dot.style.left = cartRect.left + 'px';
            dot.style.top = cartRect.top + 'px';
            dot.style.opacity = '0';
            dot.style.transform = 'scale(.3)';
        }, 30);
        setTimeout(function () {
            dot.remove();
            submitting = true;
            form.submit();
        }, 600);
    });
})();

/* MỞ RỘNG VÀ THU GỌN DANH SÁCH ĐÁNH GIÁ */
(function () {
    var btnOpen = document.getElementById('nutXemDanhGia');
    var btnClose = document.getElementById('nutThuGonDanhGia');
    var list = document.getElementById('danhSachDanhGia');
    var icon = document.getElementById('bieuTuongXemDanhGia');

    function openReviews() {
        list.hidden = false;
        btnOpen.setAttribute('aria-expanded', 'true');
        icon.textContent = '⌃';
    }

    function closeReviews() {
        list.hidden = true;
        btnOpen.setAttribute('aria-expanded', 'false');
        icon.textContent = '⌄';
    }

    btnOpen.addEventListener('click', function () {
        if (list.hidden) openReviews();
        else closeReviews();
    });

    btnClose.addEventListener('click', closeReviews);
})();

</script>
</body>
</html>