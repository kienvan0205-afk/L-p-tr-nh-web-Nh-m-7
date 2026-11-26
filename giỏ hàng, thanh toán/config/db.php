<?php
// ===== KẾT NỐI DATABASE CHÍNH (cả nhóm dùng chung file này) =====
// Database: he_thong_dat_san_bong (import database.sql trong phpMyAdmin).
// XAMPP mặc định: user = root, mật khẩu rỗng. Database ở máy khác thì đổi DB_HOST thành IP máy đó.
const DB_HOST = 'localhost';
const DB_NAME = 'he_thong_dat_san_bong';
const DB_USER = 'root';
const DB_PASS = '';

// Danh sách ngân hàng cho khách chọn: tên => [tên đầy đủ, màu, chữ viết tắt, mã logo VietQR]
const BANKS = [
    'Agribank' => ['full' => 'Ngân hàng TMCP Nông nghiệp và Phát triển Nông thôn Việt Nam', 'color' => '#ae1c3f', 'tag' => 'AGR', 'code' => 'VBA'],
    'Vietcombank' => ['full' => 'Ngân hàng TMCP Ngoại thương Việt Nam', 'color' => '#007a3d', 'tag' => 'VCB', 'code' => 'VCB'],
    'BIDV' => ['full' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam', 'color' => '#1e5aa8', 'tag' => 'BIDV', 'code' => 'BIDV'],
    'VietinBank' => ['full' => 'Ngân hàng TMCP Công thương Việt Nam', 'color' => '#1a4a94', 'tag' => 'CTG', 'code' => 'ICB'],
    'MB' => ['full' => 'Ngân hàng TMCP Quân Đội', 'color' => '#2b2f8f', 'tag' => 'MB', 'code' => 'MB'],
    'Techcombank' => ['full' => 'Ngân hàng TMCP Kỹ Thương Việt Nam', 'color' => '#e30613', 'tag' => 'TCB', 'code' => 'TCB'],
    'VPBank' => ['full' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng', 'color' => '#00a651', 'tag' => 'VPB', 'code' => 'VPB'],
    'ACB' => ['full' => 'Ngân hàng TMCP Á Châu', 'color' => '#0b57a4', 'tag' => 'ACB', 'code' => 'ACB'],
    'Sacombank' => ['full' => 'Ngân hàng TMCP Sài Gòn Thương Tín', 'color' => '#0a5ea8', 'tag' => 'STB', 'code' => 'STB'],
    'HDBank' => ['full' => 'Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh', 'color' => '#e4002b', 'tag' => 'HDB', 'code' => 'HDB'],
    'TPBank' => ['full' => 'Ngân hàng TMCP Tiên Phong', 'color' => '#6b2d8f', 'tag' => 'TPB', 'code' => 'TPB'],
    'VIB' => ['full' => 'Ngân hàng TMCP Quốc Tế', 'color' => '#00a0e3', 'tag' => 'VIB', 'code' => 'VIB'],
    'SHB' => ['full' => 'Ngân hàng TMCP Sài Gòn - Hà Nội', 'color' => '#f58220', 'tag' => 'SHB', 'code' => 'SHB'],
    'OCB' => ['full' => 'Ngân hàng TMCP Phương Đông', 'color' => '#00a650', 'tag' => 'OCB', 'code' => 'OCB'],
    'SeABank' => ['full' => 'Ngân hàng TMCP Đông Nam Á', 'color' => '#e4002b', 'tag' => 'SEA', 'code' => 'SEAB'],
    'MSB' => ['full' => 'Ngân hàng TMCP Hàng Hải', 'color' => '#e2231a', 'tag' => 'MSB', 'code' => 'MSB'],
    'Eximbank' => ['full' => 'Ngân hàng TMCP Xuất Nhập Khẩu Việt Nam', 'color' => '#0071bc', 'tag' => 'EIB', 'code' => 'EIB'],
    'LPBank' => ['full' => 'Ngân hàng TMCP Lộc Phát Việt Nam (Lộc Phát)', 'color' => '#f7931e', 'tag' => 'LPB', 'code' => 'LPB'],
    'Bac A Bank' => ['full' => 'Ngân hàng TMCP Bắc Á', 'color' => '#c8102e', 'tag' => 'BAB', 'code' => 'BAB'],
    'ABBank' => ['full' => 'Ngân hàng TMCP An Bình', 'color' => '#00923f', 'tag' => 'ABB', 'code' => 'ABB'],
    'Nam A Bank' => ['full' => 'Ngân hàng TMCP Nam Á', 'color' => '#00a79d', 'tag' => 'NAB', 'code' => 'NAB'],
    'PGBank' => ['full' => 'Ngân hàng TMCP Thịnh vượng và Phát triển', 'color' => '#f37021', 'tag' => 'PGB', 'code' => 'PGB'],
    'Vietbank' => ['full' => 'Ngân hàng TMCP Việt Nam Thương Tín', 'color' => '#00529b', 'tag' => 'VBB', 'code' => 'VIETBANK'],
    'KienlongBank' => ['full' => 'Ngân hàng TMCP Kiên Long', 'color' => '#f58220', 'tag' => 'KLB', 'code' => 'KLB'],
    'Saigonbank' => ['full' => 'Ngân hàng TMCP Sài Gòn Công Thương', 'color' => '#0072bc', 'tag' => 'SGB', 'code' => 'SGICB'],
    'BaoViet Bank' => ['full' => 'Ngân hàng TMCP Bảo Việt', 'color' => '#005baa', 'tag' => 'BVB', 'code' => 'BVB'],
    'BVBank' => ['full' => 'Ngân hàng TMCP Bản Việt', 'color' => '#0a8f4e', 'tag' => 'BVB2', 'code' => 'VCCB'],
    'NCB' => ['full' => 'Ngân hàng TMCP Quốc Dân', 'color' => '#e09a00', 'tag' => 'NCB', 'code' => 'NCB'],
    'Viet A Bank' => ['full' => 'Ngân hàng TMCP Việt Á', 'color' => '#00923f', 'tag' => 'VAB', 'code' => 'VAB'],
    'CB' => ['full' => 'Ngân hàng Thương mại TNHH MTV Xây dựng Việt Nam', 'color' => '#0a4f9e', 'tag' => 'CB', 'code' => 'CBB'],
    'OceanBank' => ['full' => 'Ngân hàng Thương mại TNHH MTV Đại Dương', 'color' => '#0a7bc4', 'tag' => 'OJB', 'code' => 'Oceanbank'],
    'GPBank' => ['full' => 'Ngân hàng Thương mại TNHH MTV Dầu khí Toàn Cầu', 'color' => '#e4002b', 'tag' => 'GPB', 'code' => 'GPB'],
    'VBSP' => ['full' => 'Ngân hàng Chính sách xã hội', 'color' => '#d4202c', 'tag' => 'CSXH', 'code' => 'VBSP'],
    'VDB' => ['full' => 'Ngân hàng Phát triển Việt Nam', 'color' => '#1b5e9b', 'tag' => 'VDB', 'code' => 'VDB'],
    'Indovina Bank' => ['full' => 'Ngân hàng TNHH Indovina (IVB)', 'color' => '#0072bc', 'tag' => 'IVB', 'code' => 'IVB'],
    'VRB' => ['full' => 'Ngân hàng Liên doanh Việt - Nga', 'color' => '#c8102e', 'tag' => 'VRB', 'code' => 'VRB'],
];
// Ví điện tử: tên => [tên đầy đủ, màu, chữ viết tắt, trang mở ví]
const WALLETS = [
    'MoMo' => ['full' => 'Ví MoMo', 'color' => '#a50064', 'tag' => 'MoMo', 'url' => 'https://momo.vn'],
    'ZaloPay' => ['full' => 'Ví ZaloPay', 'color' => '#0068ff', 'tag' => 'Zalo', 'url' => 'https://zalopay.vn'],
    'ShopeePay' => ['full' => 'Ví ShopeePay', 'color' => '#ee4d2d', 'tag' => 'Shopee', 'url' => 'https://shopeepay.vn'],
    'VNPAY' => ['full' => 'Ví VNPAY', 'color' => '#005baa', 'tag' => 'VNPAY', 'url' => 'https://vnpay.vn'],
    'Viettel Money' => ['full' => 'Ví Viettel Money', 'color' => '#e4002b', 'tag' => 'Viettel', 'url' => 'https://viettelmoney.vn'],
];
// Tài khoản NHẬN TIỀN của cửa hàng (một tài khoản duy nhất; QR VietQR quét được bằng app của mọi ngân hàng)
const RECV_BIN     = '970436';
const RECV_BANK    = 'Vietcombank';
const RECV_ACCOUNT = '0123456789';
const RECV_OWNER   = 'SAN THE THAO';
const MOMO_PHONE    = '0900000000';
const ZALOPAY_PHONE = '0900000000';

// Các phương thức thanh toán. Chuỗi này lưu vào dondatsan.PhuongThuc và phuongthucthanhtoan.PhuongThuc (VARCHAR 50)
// Chuyển khoản lưu kèm tên ngân hàng: 'Chuyển khoản - Vietcombank'
const PT_CK_PREFIX = 'Chuyển khoản - ';
const PT_TIENMAT   = 'Tiền mặt';
function pt_list(): array {   // tất cả phương thức hệ thống hỗ trợ
    $l = [];
    foreach (array_keys(BANKS) as $b) $l[] = PT_CK_PREFIX . $b;
    return array_merge($l, array_keys(WALLETS), [PT_TIENMAT]);
}
function pt_loai(?string $pt): string {   // 'bank' | 'wallet' | 'cash'
    if ($pt === null || $pt === '') return '';
    if (strpos($pt, PT_CK_PREFIX) === 0) return 'bank';
    if (isset(WALLETS[$pt])) return 'wallet';
    return $pt === PT_TIENMAT ? 'cash' : '';
}
function pt_bank(?string $pt): ?string { return pt_loai($pt) === 'bank' ? substr($pt, strlen(PT_CK_PREFIX)) : null; }


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (PDOException $ex) {
    http_response_code(500);
    die('<meta charset="utf-8"><h3>Không kết nối được database</h3>'
        . '<p>Bật <b>MySQL</b> trong XAMPP và import <b>database.sql</b> (database <code>' . DB_NAME . '</code>) bằng phpMyAdmin.</p>'
        . '<pre>' . htmlspecialchars($ex->getMessage()) . '</pre>');
}
// Kiểm tra database đã import đúng file database.sql (có gio_hang, cột ngay_choi, PhuongThuc...)
try {
    $pdo->query('SELECT idGH, ma_khung_gio FROM gio_hang LIMIT 1');
    $pdo->query('SELECT idCT, ma_khung_gio, ma_chu_san, ngay_choi FROM chitietdondatsan LIMIT 1');
    $pdo->query('SELECT ma_chu_san, PhuongThuc FROM dondatsan LIMIT 1');
    $pdo->query('SELECT gio_bat_dau, gio_ket_thuc FROM khung_gio LIMIT 1');
} catch (PDOException $ex) {
    die('<meta charset="utf-8"><h3>Database chưa đúng cấu trúc</h3>'
        . '<p>Hãy import lại file <b>database.sql</b> (đã khớp ERD của nhóm) bằng phpMyAdmin.</p>'
        . '<pre>' . htmlspecialchars($ex->getMessage()) . '</pre>');
}

$phien = session_id();                 // giỏ hàng gắn theo phiên truy cập
$idKH  = (int)($_SESSION['idKH'] ?? 0); // khách đã xác nhận đơn trong phiên này (0 = chưa có)

/* ============================ Hàm dùng chung ============================ */
function money($n): string { return number_format((float)$n, 0, ',', '.') . 'đ'; }
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect(string $to): void { header('Location: ' . $to); exit; }
function is_customer_session(): bool {
    return ($_SESSION['vai_tro'] ?? '') === 'Khách hàng' && (int)($_SESSION['idKH'] ?? 0) > 0;
}
function safe_return_path(?string $path): ?string {
    $path = trim((string)$path);
    if ($path === '' || !str_starts_with($path, '/') || str_starts_with($path, '//')) return null;
    $parts = parse_url($path);
    if ($parts === false || isset($parts['scheme'], $parts['host'], $parts['user'], $parts['pass'])) return null;
    return $path;
}
function customer_login_url(?string $returnPath = null): string {
    $url = '../Đăng nhập/dangnhap.php?notice=customer';
    $returnPath = safe_return_path($returnPath);
    return $returnPath === null ? $url : $url . '&return=' . rawurlencode($returnPath);
}
function require_customer(): void {
    if (is_customer_session()) return;
    $returnPath = $_SERVER['REQUEST_METHOD'] === 'GET' ? rawurldecode($_SERVER['REQUEST_URI'] ?? '') : '';
    header('Location: ' . customer_login_url($returnPath));
    exit;
}
function flash(?string $msg = null, string $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function hhmm(string $t): string { return substr($t, 0, 5); }
function ma_don($id): string { return 'DH' . str_pad((string)(int)$id, 3, '0', STR_PAD_LEFT); }

// dondatsan.TrangThai: 0 chờ xác nhận, 1 đã xác nhận, 2 đã hoàn thành, 3 đã hủy
function trang_thai_text(int $tt, ?string $pt): string {
    if ($tt === 0) return $pt === PT_TIENMAT ? 'Chờ cọc' : 'Chờ thanh toán';
    return [1 => 'Đã thanh toán', 2 => 'Hoàn thành', 3 => 'Đã hủy'][$tt] ?? '—';
}

// Khung giờ (ma_khung_gio) của sân đã bị đơn chưa hủy đặt vào ngày đó chưa? (tính cả khung giờ chồng lên nhau)
function is_booked(PDO $pdo, int $ma_khung_gio, string $ngay): bool {
    $st = $pdo->prepare(
        'SELECT COUNT(*) FROM chitietdondatsan ct
         JOIN dondatsan d ON d.idDon = ct.idDon
         JOIN khung_gio k2 ON k2.ma_khung_gio = ct.ma_khung_gio
         JOIN khung_gio k1 ON k1.ma_san = k2.ma_san
         WHERE k1.ma_khung_gio = ? AND ct.ngay_choi = ? AND d.TrangThai <> 3
           AND k2.gio_bat_dau < k1.gio_ket_thuc AND k2.gio_ket_thuc > k1.gio_bat_dau');
    $st->execute([$ma_khung_gio, $ngay]);
    return $st->fetchColumn() > 0;
}

function cart_count(): int {
    global $pdo, $phien;
    $st = $pdo->prepare('SELECT COUNT(*) FROM gio_hang WHERE ma_phien = ?');
    $st->execute([$phien]);
    return (int)$st->fetchColumn();
}

// Dòng giỏ hàng (gio_hang -> khung_gio -> san -> chu_san), thành tiền = san.gia × khung_gio.so_gio
// Có thể lọc theo danh sách idGH. Sắp xếp theo chủ sân để nhóm giống Shopee.
function cart_items(array $ids = []): array {
    global $pdo, $phien;
    $sql = 'SELECT g.idGH, g.ngay_choi, k.ma_khung_gio, k.gio_bat_dau, k.gio_ket_thuc, k.so_gio, k.gia_moi_gio, k.trang_thai AS kg_tt,
                   s.ma_san, s.idLS, s.loai_san, s.gia, s.trang_thai AS san_tt,
                   c.ma_chu_san, c.ten_chu_san, c.dia_chi
            FROM gio_hang g
            JOIN khung_gio k ON k.ma_khung_gio = g.ma_khung_gio
            JOIN san s ON s.ma_san = k.ma_san
            JOIN chu_san c ON c.ma_chu_san = s.ma_chu_san
            WHERE g.ma_phien = ?';
    $args = [$phien];
    if ($ids) { $sql .= ' AND g.idGH IN (' . implode(',', array_fill(0, count($ids), '?')) . ')'; $args = array_merge($args, $ids); }
    $st = $pdo->prepare($sql . ' ORDER BY c.ma_chu_san, g.ngay_choi, k.gio_bat_dau');
    $st->execute($args);
    $rows = $st->fetchAll();
    foreach ($rows as &$r) {
        $r['gia_ap_dung'] = (float)$r['gia_moi_gio'] > 0 ? (float)$r['gia_moi_gio'] : (float)$r['gia'];
        $r['price'] = (int)round($r['gia_ap_dung'] * (float)$r['so_gio']);
    }
    return $rows;
}
function posted_ids(): array { return array_values(array_unique(array_map('intval', $_POST['ids'] ?? []))); }

// Phương thức khách đã lưu (bảng phuongthucthanhtoan) – dùng để gợi ý sẵn trong giỏ hàng
function saved_methods(int $idKH): array {
    global $pdo;
    if (!$idKH) return [];
    $st = $pdo->prepare('SELECT PhuongThuc FROM phuongthucthanhtoan WHERE idKH = ?');
    $st->execute([$idKH]);
    return $st->fetchAll(PDO::FETCH_COLUMN);
}

/* ---- Mã QR ---- */
function tlv(string $id, string $v): string { return $id . str_pad((string)strlen($v), 2, '0', STR_PAD_LEFT) . $v; }
function crc16(string $s): string {
    $c = 0xFFFF;
    for ($i = 0, $n = strlen($s); $i < $n; $i++) {
        $c ^= ord($s[$i]) << 8;
        for ($k = 0; $k < 8; $k++) $c = ($c & 0x8000) ? (($c << 1) ^ 0x1021) & 0xFFFF : ($c << 1) & 0xFFFF;
    }
    return strtoupper(str_pad(dechex($c), 4, '0', STR_PAD_LEFT));
}
// Nội dung QR chuyển khoản chuẩn VietQR: quét bằng app ngân hàng bất kỳ, tự điền số tiền + nội dung
function vietqr_payload(?string $bank, int $amount, string $info): string {
    $m = tlv('00', 'A000000727') . tlv('01', tlv('00', RECV_BIN) . tlv('01', RECV_ACCOUNT)) . tlv('02', 'QRIBFTTA');
    $p = tlv('00', '01') . tlv('01', '12') . tlv('38', $m) . tlv('53', '704') . tlv('54', (string)$amount)
       . tlv('58', 'VN') . tlv('62', tlv('08', $info)) . '6304';
    return $p . crc16($p);
}
// QR ví: MoMo đúng định dạng chuyển tiền theo SĐT; ví khác không công khai định dạng nên chỉ minh họa
function wallet_payload(string $pt, int $amount, string $info): string {
    return $pt === 'MoMo' ? '2|99|' . MOMO_PHONE . '|||0|0|' . $amount . '|' . $info . '|transfer_myqr'
                          : implode('|', [$pt, ZALOPAY_PHONE, $info, $amount]);
}
// Một nội dung QR duy nhất cho mọi phương thức (null nếu là tiền mặt)
function qr_payload(string $pt, int $amount, string $info): ?string {
    $l = pt_loai($pt);
    if ($l === 'bank') return vietqr_payload(pt_bank($pt), $amount, $info);
    if ($l === 'wallet') return wallet_payload($pt, $amount, $info);
    return null;
}
// Logo ngân hàng: thử cdn.vietqr.io rồi api.vietqr.io; lỗi hết thì hiện huy hiệu chữ (giống pttt.html)
function bank_logo(string $ten): string {
    $b = BANKS[$ten] ?? ['code' => '?', 'color' => '#888', 'tag' => '?'];
    $c = e($b['code']);
    return '<span class="logo" style="background:' . e($b['color']) . '" data-logo="https://cdn.vietqr.io/img/' . $c . '.png|https://api.vietqr.io/img/' . $c . '.png">' . e($b['tag']) . '</span>';
}
function wallet_logo(string $ten): string {
    $w = WALLETS[$ten] ?? ['color' => '#888', 'tag' => '?'];
    return '<span class="logo" style="background:' . e($w['color']) . '">' . e($w['tag']) . '</span>';
}
function pt_badge(string $pt): string {
    $l = pt_loai($pt);
    if ($l === 'bank') return bank_logo(pt_bank($pt));
    if ($l === 'wallet') return wallet_logo($pt);
    return '<span class="logo" style="background:#5d4037">₫</span>';
}
