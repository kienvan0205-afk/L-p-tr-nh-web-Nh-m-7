<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");
// ========================================================
// TRANG CHỦ SITE NGƯỜI DÙNG - SPORTBOOKING.COM
// Tác giả: Lê Kiến Văn (Nhóm trưởng Nhóm 07 - MSSV: 2531540498)
// Tông màu: Đỏ - Trắng Thể Thao (Red & White)
// ========================================================

require_once 'connect.php';
$isCustomer = ($_SESSION['vai_tro'] ?? '') === 'Khách hàng' && !empty($_SESSION['idKH']);
$projectPath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$cartTarget = '../giỏ hàng, thanh toán/cart.php';
$cartLoginUrl = '../Đăng nhập/dangnhap.php?notice=cart&return=' . rawurlencode($projectPath . '/giỏ hàng, thanh toán/cart.php');

// Mảng 10 sân thể thao nạp sẵn - Sử dụng đường dẫn ảnh nội bộ images/
$sample_courts = [
    [
        'ma_san' => 1,
        'loai_san' => 'Sân bóng đá 5 người',
        'ten_san' => 'Sân Bóng Đá Mini 5 Người Chảo Lửa',
        'mo_ta' => 'Sân cỏ nhân tạo chất lượng cao chuẩn FIFA, có hệ thống đèn LED cao áp và khu vực ghế nghỉ có mái che.',
        'gia' => 250000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san1.jpg',
        'ten_chu_san' => 'Nguyễn Văn Hùng',
        'dia_chi' => '123 Lê Duẩn, Quận 1, TP.HCM'
    ],
    [
        'ma_san' => 2,
        'loai_san' => 'Sân bóng đá 7 người',
        'ten_san' => 'Sân Bóng Đá 7 Người Cỏ Nhân Tạo Star',
        'mo_ta' => 'Mặt sân cỏ mới 100%, kích thước tiêu chuẩn thi đấu giao hữu doanh nghiệp, có phòng thay đồ và nước uống.',
        'gia' => 420000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san2.jpg',
        'ten_chu_san' => 'Nguyễn Văn Hùng',
        'dia_chi' => '123 Lê Duẩn, Quận 1, TP.HCM'
    ],
    [
        'ma_san' => 3,
        'loai_san' => 'Sân cầu lông',
        'ten_san' => 'Sân Cầu Lông Victor Pro',
        'mo_ta' => 'Sân thảm PVC vân cát chống trượt tiêu chuẩn thi đấu, trần cao 9m thông thoáng, đèn chiếu không chói mắt.',
        'gia' => 130000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san3.jpg',
        'ten_chu_san' => 'Trần Thị Mai',
        'dia_chi' => '45 Nguyễn Thị Minh Khai, Quận 3, TP.HCM'
    ],
    [
        'ma_san' => 4,
        'loai_san' => 'Sân tennis',
        'ten_san' => 'Sân Tennis GrandSlam Center',
        'mo_ta' => 'Mặt sân cứng DecoTurf giảm chấn thương, trang bị đầy đủ ghế trọng tài, máy bắn bóng tập luyện.',
        'gia' => 320000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san4.jpg',
        'ten_chu_san' => 'Lê Hoàng Nam',
        'dia_chi' => '789 Phạm Văn Đồng, TP. Thủ Đức'
    ],
    [
        'ma_san' => 5,
        'loai_san' => 'Sân bóng rổ trong nhà',
        'ten_san' => 'Sân Bóng Rổ Trong Nhà NBA Arena',
        'mo_ta' => 'Sân sàn gỗ sồi cao cấp chuẩn NBA, vành rổ chuyên nghiệp lò xo đôi điều chỉnh độ cao theo lứa tuổi.',
        'gia' => 350000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san5.jpg',
        'ten_chu_san' => 'Phạm Quốc Cường',
        'dia_chi' => '12 Điện Biên Phủ, Bình Thạnh'
    ],
    [
        'ma_san' => 6,
        'loai_san' => 'Sân Pickleball',
        'ten_san' => 'Sân Pickleball Trẻ Trung S-Club',
        'mo_ta' => 'Bộ môn thể thao thời thượng hot nhất hiện nay, bề mặt sân sơn nhám giảm độ nảy, có sẵn vợt và bóng thi đấu.',
        'gia' => 180000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san6.jpg',
        'ten_chu_san' => 'Trần Thị Mai',
        'dia_chi' => '45 Nguyễn Thị Minh Khai, Quận 3, TP.HCM'
    ],
    [
        'ma_san' => 7,
        'loai_san' => 'Sân bóng chuyền',
        'ten_san' => 'Sân Bóng Chuyền Tiêu Chuẩn',
        'mo_ta' => 'Sân thi đấu trong nhà có mái che, lưới chuẩn quốc gia và hệ thống vạch kẻ rõ nét, có dịch vụ tắm nóng lạnh.',
        'gia' => 200000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san7.jpg',
        'ten_chu_san' => 'Đặng Thu Thảo',
        'dia_chi' => '56 Nguyễn Văn Linh, Quận 7, TP.HCM'
    ],
    [
        'ma_san' => 8,
        'loai_san' => 'Sân bóng bàn VIP',
        'ten_san' => 'Sân Bóng Bàn VIP PingPong Club',
        'mo_ta' => 'Khu vực phòng bóng bàn máy lạnh độc lập, bàn Double Fish ITTF, sàn cao su chống mỏi chân khi thi đấu dài.',
        'gia' => 90000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san8.jpg',
        'ten_chu_san' => 'Phạm Quốc Cường',
        'dia_chi' => '12 Điện Biên Phủ, Bình Thạnh'
    ],
    [
        'ma_san' => 9,
        'loai_san' => 'Sân bóng đá 11 người',
        'ten_san' => 'Sân Bóng Đá 11 Người SVĐ Hoàng Gia',
        'mo_ta' => 'Sân cỏ tự nhiên quy mô lớn 105mx68m đạt chuẩn quốc tế, khán đài 500 chỗ ngồi thích hợp tổ chức giải phong trào.',
        'gia' => 950000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san9.jpg',
        'ten_chu_san' => 'Lê Hoàng Nam',
        'dia_chi' => '789 Phạm Văn Đồng, TP. Thủ Đức'
    ],
    [
        'ma_san' => 10,
        'loai_san' => 'Sân cầu lông VIP máy lạnh',
        'ten_san' => 'Sân Cầu Lông VIP Máy Lạnh Yonex',
        'mo_ta' => 'Sân đơn biệt lập đạt chuẩn Yonex, lắp đặt máy điều hòa nhiệt độ ổn định 25 độ C, phục vụ thi đấu chuyên sâu.',
        'gia' => 160000,
        'trang_thai' => 'Hoạt động',
        'hinh_anh' => 'images/san10.jpg',
        'ten_chu_san' => 'Đặng Thu Thảo',
        'dia_chi' => '56 Nguyễn Văn Linh, Quận 7, TP.HCM'
    ]
];

// Danh sách cố định cho 2 dropdown
$ds_mon = ['Sân bóng đá 5 người','Sân bóng đá 7 người','Sân bóng đá 11 người','Sân cầu lông','Sân cầu lông VIP máy lạnh','Sân tennis','Sân Pickleball','Sân bóng rổ trong nhà','Sân bóng chuyền','Sân bóng bàn VIP'];
$ds_khu_vuc = ['Quận 1','Quận 3','Quận 7','Bình Thạnh','Thủ Đức'];

// Tiếp nhận điều kiện tìm kiếm: Bộ môn, Khu vực, Ngày đặt
$mon = isset($_GET['mon']) ? trim($_GET['mon']) : '';
$khu_vuc = isset($_GET['khu_vuc']) ? trim($_GET['khu_vuc']) : '';
$ngay_dat = isset($_GET['ngay_dat']) ? trim($_GET['ngay_dat']) : '';
$has_filter = !empty($mon) || !empty($khu_vuc) || !empty($ngay_dat);
$courts_list = [];

// Truy vấn MySQL
if ($db_connected) {
    $where_parts = [];
    if (!empty($mon)) {
        $safe_mon = mysqli_real_escape_string($conn, $mon);
        $where_parts[] = "s.loai_san LIKE '%$safe_mon%'";
    }
    if (!empty($khu_vuc)) {
        $safe_kv = mysqli_real_escape_string($conn, $khu_vuc);
        $where_parts[] = "cs.dia_chi LIKE '%$safe_kv%'";
    }
    $where_sql = !empty($where_parts) ? 'WHERE ' . implode(' AND ', $where_parts) : '';
    // Trang chủ chỉ hiển thị tối đa 10 sân để giao diện luôn gọn và dễ xem.
    $sql = "SELECT s.*, cs.ten_chu_san, cs.dia_chi FROM san s LEFT JOIN chu_san cs ON s.ma_chu_san = cs.ma_chu_san $where_sql ORDER BY s.ma_san ASC LIMIT 10";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $courts_list[] = $row;
        }
    }
}

// Fallback lọc trên mảng mẫu
if (empty($courts_list)) {
    if ($has_filter) {
        foreach ($sample_courts as $c) {
            $ok = true;
            if (!empty($mon) && mb_stripos($c['loai_san'], $mon, 0, 'UTF-8') === false) $ok = false;
            if (!empty($khu_vuc) && mb_stripos($c['dia_chi'], $khu_vuc, 0, 'UTF-8') === false) $ok = false;
            if ($ok) $courts_list[] = $c;
        }
    } else {
        $courts_list = $sample_courts;
    }
}

$total_found = count($courts_list);
$filter_parts = [];
if (!empty($mon)) $filter_parts[] = $mon;
if (!empty($khu_vuc)) $filter_parts[] = $khu_vuc;
if (!empty($ngay_dat)) $filter_parts[] = 'Ngày ' . date('d/m/Y', strtotime($ngay_dat));
$filter_desc = implode(' · ', $filter_parts);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportBooking.com - Đặt Sân Thể Thao Trực Tuyến 24/7</title>
    <!-- Phông chữ Plus Jakarta Sans hiện đại -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===================================================
           TÔNG MÀU CHÍNH: ĐỎ - TRẮNG THỂ THAO (RED & WHITE)
           =================================================== */
        :root {
            --primary: #dc2626;         /* Đỏ thể thao chủ đạo */
            --primary-dark: #b91c1c;    /* Đỏ đậm khi hover */
            --primary-deep: #7f1d1d;    /* Đỏ mận cho nền banner */
            --primary-light: #fee2e2;   /* Đỏ hồng nhạt cho tag/badge */
            --primary-border: #fca5a5;  /* Viền đỏ nhạt */
            --accent: #ef4444;          /* Đỏ tươi nổi bật */
            --success: #16a34a;         /* Xanh lá trạng thái hoạt động */
            --dark: #0f172a;            /* Chữ đen đậm sắc nét */
            --gray: #64748b;            /* Xám phụ đề */
            --bg-light: #fff5f5;        /* Nền trang phớt đỏ nhẹ */
            --card-bg: #ffffff;         /* Nền thẻ trắng tinh khiết */
            --border: #e2e8f0;          /* Viền ngăn cách */
            --shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.08), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-hover: 0 14px 24px -4px rgba(220, 38, 38, 0.18), 0 4px 6px -4px rgba(0,0,0,0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-light);
            color: var(--dark);
            line-height: 1.6;
        }
        html { scroll-behavior: smooth; }
        #home, #search-box, #courts-list { scroll-margin-top: 92px; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* 1. HEADER ĐỎ - TRẮNG */
        header {
            background: #ffffff;
            border-bottom: 2px solid var(--primary-border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(220, 38, 38, 0.06);
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .brand-logo .logo-badge {
            background: linear-gradient(135deg, var(--primary), #ef4444);
            color: #ffffff;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 22px;
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            font-size: 14.5px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-cart {
            position: relative;
            background: #ffffff;
            border: 1.5px solid var(--primary-border);
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-cart:hover {
            background: var(--primary-light);
        }

        .btn-auth {
            background-color: var(--primary);
            color: #ffffff !important;
            padding: 9px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
        }

        .btn-auth:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(220, 38, 38, 0.4);
        }

        /* 2. HERO BANNER - GRADIENT ĐỎ QUÝ TỘC */
        .hero-banner {
            background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 45%, #ef4444 100%);
            color: #ffffff;
            padding: 55px 0 45px;
            text-align: center;
            border-radius: 0 0 28px 28px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(185, 28, 28, 0.2);
        }

        .hero-pill {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        .hero-banner h1 {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .hero-banner p {
            font-size: 17px;
            color: #fee2e2;
            max-width: 650px;
            margin: 0 auto 25px;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-item {
            text-align: center;
        }

        .stat-num {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            display: block;
        }

        .stat-label {
            font-size: 13px;
            color: #fecaca;
        }

        /* 3. BỘ LỌC TÌM KIẾM ĐỎ - TRẮNG */
        .search-section {
            margin-top: -35px;
            position: relative;
            z-index: 10;
            margin-bottom: 35px;
        }

        .search-card {
            background: #ffffff;
            padding: 24px 28px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1.5px solid var(--primary-border);
        }

        .search-card h2 {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select,
        .filter-group input[type="date"] {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: var(--dark);
            background: #ffffff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .filter-group select:focus,
        .filter-group input[type="date"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #ffffff;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
            white-space: nowrap;
            height: 46px;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.45);
        }

        /* Gợi ý từ khóa */
        .quick-tags {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            flex-wrap: wrap;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }

        .quick-tags-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--gray);
        }

        .quick-tag {
            background: #ffffff;
            border: 1px solid var(--border);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.15s;
        }

        .quick-tag:hover, .quick-tag.active {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary);
            font-weight: 700;
        }

        /* 4. THANH TRẠNG THÁI KẾT QUẢ */
        .search-status-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid var(--border);
            border-left: 4px solid var(--primary);
        }

        .search-status-text {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
        }

        .search-status-text mark {
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
        }

        .btn-reset-search {
            text-decoration: none;
            color: var(--primary);
            font-size: 13.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* 5. LƯỚI 10 SÂN THỂ THAO */
        .courts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 24px;
            margin-bottom: 50px;
        }

        .court-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .court-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-border);
        }

        .court-thumb {
            position: relative;
            height: 195px;
            background-color: #fee2e2;
            overflow: hidden;
        }

        .court-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
            display: block;
        }
        .court-detail-link { display: block; height: 100%; }

        .court-card:hover .court-thumb img {
            transform: scale(1.06);
        }

        .badge-status {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(22, 163, 74, 0.95);
            backdrop-filter: blur(4px);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-category {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(185, 28, 28, 0.92);
            backdrop-filter: blur(4px);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .court-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .court-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
            line-height: 1.35;
        }
        .court-title a { color: inherit; text-decoration: none; }

        .court-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--gray);
            font-size: 12.5px;
            margin-bottom: 10px;
        }

        .court-desc {
            color: #475569;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 16px;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .court-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .court-price {
            display: flex;
            flex-direction: column;
        }

        .price-label {
            font-size: 11px;
            color: var(--gray);
            font-weight: 600;
            text-transform: uppercase;
        }

        .price-val {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
        }

        .btn-book {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
        }

        .btn-book:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.35);
        }

        /* Khi không có kết quả */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            background: #ffffff;
            padding: 60px 20px;
            border-radius: 16px;
            border: 1px dashed var(--primary-border);
        }

        .empty-icon { font-size: 48px; margin-bottom: 12px; }
        .empty-state h3 { font-size: 18px; color: var(--dark); margin-bottom: 8px; }
        .empty-state p { color: var(--gray); margin-bottom: 20px; font-size: 14px; }

        /* FOOTER ĐỎ - TRẮNG */
        footer {
            background: #ffffff;
            border-top: 2px solid var(--primary-border);
            padding: 30px 0;
            text-align: center;
            color: var(--gray);
            font-size: 13.5px;
        }

        footer strong { color: var(--primary); }

        @media (max-width: 768px) {
            .filter-grid { grid-template-columns: 1fr; }
        }

        /* Lớp tinh chỉnh giao diện: giữ nguyên cấu trúc PHP và các class sẵn có. */
        :root {
            --primary: #dc2626;
            --primary-dark: #b91c1c;
            --primary-deep: #7f1d1d;
            --primary-light: #fee2e2;
            --primary-border: #fecaca;
            --accent: #f59e0b;
            --success: #15803d;
            --dark: #0f172a;
            --gray: #64748b;
            --bg-light: #f6f8fb;
            --border: #dbe4ee;
            --shadow: 0 12px 28px -20px rgba(15, 23, 42, .38);
            --shadow-hover: 0 22px 35px -22px rgba(15, 118, 110, .48);
        }

        html { scroll-behavior: smooth; }
        body { background: var(--bg-light); letter-spacing: -.01em; }
        ::selection { background: var(--primary-light); color: var(--primary-deep); }
        :focus-visible { outline: 3px solid rgba(220, 38, 38, .42); outline-offset: 3px; }
        .container { max-width: 1240px; padding: 0 24px; }
        header { border-bottom: 1px solid rgba(220, 38, 38, .14); box-shadow: 0 4px 18px rgba(15, 23, 42, .05); }
        .nav-inner { height: 76px; }
        .brand-logo { color: var(--primary-deep); font-size: 21px; }
        .brand-logo .logo-badge { width: 42px; height: 42px; border-radius: 13px; background: var(--primary); box-shadow: 0 10px 18px -10px rgba(220, 38, 38, .75); }
        .nav-links { gap: 28px; }
        .nav-links a { color: #475569; font-size: 14px; }
        .nav-links a:hover { color: var(--primary); }
        .btn-cart { border-color: #cbd5e1; color: #334155; border-radius: 10px; }
        .btn-cart:hover { background: #fff1f2; border-color: var(--primary-border); }
        .btn-auth, .search-btn, .btn-book { background: var(--primary); box-shadow: 0 10px 18px -12px rgba(220, 38, 38, .8); }
        .btn-auth { border-radius: 10px; padding: 10px 17px; }
        .btn-auth:hover, .search-btn:hover, .btn-book:hover { background: var(--primary-dark); }
        .hero-banner { background: radial-gradient(circle at 86% 10%, rgba(251, 113, 133, .26), transparent 25%), linear-gradient(120deg, #450a0a 0%, #991b1b 52%, #dc2626 100%); padding: 72px 0 82px; border-radius: 0 0 36px 36px; box-shadow: none; }
        .hero-pill { background: rgba(204, 251, 241, .13); border-color: rgba(204, 251, 241, .28); padding: 7px 14px; font-size: 11px; letter-spacing: .1em; }
        .hero-banner h1 { font-size: clamp(2rem, 4vw, 3.4rem); max-width: 800px; margin-left: auto; margin-right: auto; letter-spacing: -.035em; text-wrap: balance; }
        .hero-banner p { color: #fee2e2; max-width: 620px; font-size: 16px; }
        .hero-stats { max-width: 660px; margin: 28px auto 0; gap: 0; border-top-color: rgba(255,255,255,.18); }
        .stat-item { flex: 1; padding: 0 16px; }
        .stat-item + .stat-item { border-left: 1px solid rgba(255,255,255,.18); }
        .stat-num { font-size: 23px; }
        .stat-label { color: #fee2e2; }
        .search-section { margin-top: -42px; margin-bottom: 38px; }
        .search-card { padding: 25px 28px 20px; border: 0; border-radius: 18px; box-shadow: 0 24px 45px -28px rgba(15, 23, 42, .45); }
        .search-card h2 { color: var(--dark); font-size: 17px; }
        .filter-group label { color: #475569; }
        .filter-group select, .filter-group input[type="date"] { border-color: #cbd5e1; background: #fbfdff; border-radius: 10px; }
        .filter-group select:hover, .filter-group input[type="date"]:hover { border-color: #94a3b8; }
        .filter-group select:focus, .filter-group input[type="date"]:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(220,38,38,.14); }
        .search-btn { border-radius: 10px; }
        .quick-tags { border-top-color: #edf2f7; }
        .quick-tags-title { color: #64748b; }
        .quick-tag { border-color: #dbe4ee; background: #fff; color: #475569; }
        .quick-tag:hover, .quick-tag.active { background: #fff1f2; border-color: #fca5a5; color: #b91c1c; }
        .search-status-bar { background: transparent; padding: 4px 0 18px; border: 0; border-radius: 0; }
        .search-status-text { font-size: 15px; }
        .search-status-text mark { background: #fee2e2; color: #991b1b; }
        .courts-grid { gap: 22px; }
        .court-card { border: 0; border-radius: 15px; box-shadow: var(--shadow); }
        .court-card:hover { transform: translateY(-6px); border-color: transparent; box-shadow: var(--shadow-hover); }
        .court-thumb { height: 205px; }
        .badge-status { background: rgba(21, 128, 61, .95); }
        .badge-category { background: rgba(15, 23, 42, .86); border-radius: 999px; }
        .court-content { padding: 20px; }
        .court-title { font-size: 17px; letter-spacing: -.02em; }
        .court-location { color: #64748b; }
        .court-desc { color: #526277; }
        .price-val { color: var(--primary); }
        .btn-book { border-radius: 9px; }
        .empty-state { border-color: #99f6e4; }
        footer { margin-top: 16px; border-top: 1px solid #dbe4ee; }

        @media (max-width: 820px) {
            .nav-links { display: none; }
            .filter-grid { grid-template-columns: 1fr 1fr; }
            .search-btn { width: 100%; justify-content: center; }
        }
        @media (max-width: 560px) {
            .container { padding: 0 16px; }
            .nav-inner { height: 66px; }
            .brand-logo { font-size: 18px; }
            .brand-logo .logo-badge { width: 36px; height: 36px; font-size: 18px; border-radius: 11px; }
            .btn-cart { display: none; }
            .btn-auth { padding: 8px 11px; font-size: 12px; }
            .hero-banner { padding: 48px 0 70px; border-radius: 0 0 25px 25px; }
            .hero-banner p { font-size: 14px; }
            .hero-stats { gap: 0; margin-top: 22px; padding-top: 16px; }
            .stat-item { padding: 0 7px; }
            .stat-num { font-size: 19px; }
            .stat-label { font-size: 10px; line-height: 1.35; }
            .search-section { margin-top: -34px; margin-bottom: 30px; }
            .search-card { padding: 20px 16px 16px; border-radius: 15px; }
            .filter-grid { grid-template-columns: 1fr; gap: 12px; }
            .quick-tags { gap: 7px; }
            .quick-tag { padding: 5px 10px; font-size: 12px; }
            .search-status-bar { align-items: flex-start; gap: 12px; }
            .btn-reset-search { white-space: nowrap; }
            .courts-grid { grid-template-columns: 1fr; gap: 16px; }
            .court-thumb { height: 190px; }
            .court-content { padding: 18px; }
        }
    </style>
</head>
<body>

    <!-- 1. HEADER -->
    <header>
        <div class="container nav-inner">
            <a href="index.php" class="brand-logo">
                <div class="logo-badge">⚽</div>
                <span>SportBooking</span>
            </a>
            <ul class="nav-links">
                <li><a href="#home">Trang chủ</a></li>
                <li><a href="#courts-list">Danh sách sân (<?php echo (int)$total_found; ?>)</a></li>
                <li><a href="#search-box">Tìm kiếm</a></li>
            </ul>
            <div class="nav-actions">
    <a href="<?= htmlspecialchars($isCustomer ? $cartTarget : $cartLoginUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-cart">🛒 Giỏ ca sân</a>
                <?php if (isset($_SESSION['HoTen'])): ?>
                    <span style="font-size: 14px; font-weight: 700; color: var(--primary);">👤 <?php echo htmlspecialchars($_SESSION['HoTen']); ?> (<?php echo htmlspecialchars($_SESSION['vai_tro'] ?? 'Khách'); ?>)</span>
                    <a href="../Đăng nhập/dangxuat.php" style="font-size: 13px; color: #dc2626; text-decoration: underline; margin-left: 5px;">Đăng xuất</a>
                <?php else: ?>
    <a href="../Đăng nhập/dangnhap.php" class="btn-auth">Đăng nhập / Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- 2. HERO BANNER -->
    <section class="hero-banner" id="home">
        <div class="container">
            <span class="hero-pill">⚡ NỀN TẢNG ĐẶT SÂN THỂ THAO TRỰC TUYẾN 24/7</span>
            <h1>THUẬN TIỆN - DỄ DÀNG - NHANH CHÓNG</h1>
            <p>Tra cứu lịch trống tức thời, đặt sân giữ chỗ uy tín tại TP.HCM. Bảng giá minh bạch, tiện ích chuẩn thi đấu.</p>
            <div class="hero-stats">
                <div class="stat-item"><span class="stat-num">10+</span><span class="stat-label">Sân đấu tiêu chuẩn</span></div>
                <div class="stat-item"><span class="stat-num">100%</span><span class="stat-label">Bảng giá minh bạch</span></div>
                <div class="stat-item"><span class="stat-num">24/7</span><span class="stat-label">Hỗ trợ giữ chỗ tức thì</span></div>
            </div>
        </div>
    </section>

    <!-- 3. BỘ LỌC TÌM KIẾM: 3 ĐIỀU KIỆN (BỘ MÔN, KHU VỰC, NGÀY ĐẶT) -->
    <div class="container search-section" id="search-box">
        <div class="search-card">
            <h2>🔍 Tìm sân thể thao phù hợp</h2>

            <form action="index.php" method="GET">
                <div class="filter-grid">

                    <!-- Dropdown 1: Chọn Bộ môn -->
                    <div class="filter-group">
                        <label>🏅 Bộ môn thể thao</label>
                        <select name="mon">
                            <option value="">-- Tất cả bộ môn --</option>
                            <?php foreach ($ds_mon as $m): ?>
                                <option value="<?php echo htmlspecialchars($m); ?>" <?php echo ($mon === $m) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($m); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dropdown 2: Chọn Khu vực -->
                    <div class="filter-group">
                        <label>📍 Khu vực / Quận</label>
                        <select name="khu_vuc">
                            <option value="">-- Tất cả khu vực --</option>
                            <?php foreach ($ds_khu_vuc as $kv): ?>
                                <option value="<?php echo htmlspecialchars($kv); ?>" <?php echo ($khu_vuc === $kv) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kv); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Input 3: Chọn Ngày đặt sân -->
                    <div class="filter-group">
                        <label>📅 Ngày muốn đặt sân</label>
                        <input type="date" name="ngay_dat" value="<?php echo htmlspecialchars($ngay_dat); ?>" min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <!-- Nút bấm Tìm -->
                    <button type="submit" class="search-btn">🔎 Tìm sân</button>
                </div>
            </form>

            <!-- Gợi ý nhanh bấm 1 click -->
            <div class="quick-tags">
                <span class="quick-tags-title">Lọc nhanh:</span>
                <a href="index.php" class="quick-tag <?php echo !$has_filter ? 'active' : ''; ?>">Tất cả</a>
                <a href="index.php?mon=Sân+bóng+đá+5+người" class="quick-tag <?php echo $mon == 'Sân bóng đá 5 người' ? 'active' : ''; ?>">⚽ Sân 5</a>
                <a href="index.php?mon=Sân+bóng+đá+7+người" class="quick-tag <?php echo $mon == 'Sân bóng đá 7 người' ? 'active' : ''; ?>">⚽ Sân 7</a>
                <a href="index.php?mon=Sân+cầu+lông" class="quick-tag <?php echo $mon == 'Sân cầu lông' ? 'active' : ''; ?>">🏸 Cầu lông</a>
                <a href="index.php?mon=Sân+tennis" class="quick-tag <?php echo $mon == 'Sân tennis' ? 'active' : ''; ?>">🎾 Tennis</a>
                <a href="index.php?mon=Sân+Pickleball" class="quick-tag <?php echo $mon == 'Sân Pickleball' ? 'active' : ''; ?>">🏓 Pickleball</a>
                <a href="index.php?mon=Sân+bóng+rổ" class="quick-tag <?php echo $mon == 'Sân bóng rổ trong nhà' ? 'active' : ''; ?>">🏀 Bóng rổ</a>
                <a href="index.php?khu_vuc=Quận+1" class="quick-tag <?php echo $khu_vuc == 'Quận 1' ? 'active' : ''; ?>">📍 Quận 1</a>
                <a href="index.php?khu_vuc=Quận+7" class="quick-tag <?php echo $khu_vuc == 'Quận 7' ? 'active' : ''; ?>">📍 Quận 7</a>
            </div>
        </div>
    </div>

    <!-- 4. DANH SÁCH 10 SÂN THỂ THAO -->
    <main class="container" id="courts-list">

        <!-- Thanh trạng thái kết quả -->
        <div class="search-status-bar">
            <div class="search-status-text">
                <?php if ($has_filter): ?>
                    🎯 Tìm thấy <strong><?php echo $total_found; ?></strong> sân phù hợp với bộ lọc: <mark><?php echo htmlspecialchars($filter_desc); ?></mark>
                <?php else: ?>
                    Danh sách toàn bộ <strong><?php echo $total_found; ?></strong> sân thể thao
                <?php endif; ?>
            </div>
            <?php if ($has_filter): ?>
                <a href="index.php" class="btn-reset-search">✕ Xóa bộ lọc / Xem tất cả</a>
            <?php endif; ?>
        </div>

        <!-- Lưới thẻ sân -->
        <div class="courts-grid">
            <?php if ($total_found > 0): ?>
                <?php foreach ($courts_list as $court): ?>
                    <?php
                        $court_name = isset($court['ten_san']) ? $court['ten_san'] : $court['loai_san'];
                        $court_id = (int)($court['ma_san'] ?? 1);
                        // Ảnh được đặt trong Trang chủ banner/images với tên san1.jpg ... san10.jpg
                        $court_img = 'images/san' . $court_id . '.jpg';
                        $court_price = number_format($court['gia'], 0, ',', '.');
                        $court_address = isset($court['dia_chi']) ? $court['dia_chi'] : 'TP. Hồ Chí Minh';
                        $detailUrl = '../chi tiết sân/dat_san.php?ma_san=' . $court_id . ($ngay_dat !== '' ? '&ngay=' . urlencode($ngay_dat) : '');
                        $returnPath = $projectPath . '/chi tiết sân/dat_san.php?ma_san=' . $court_id . ($ngay_dat !== '' ? '&ngay=' . urlencode($ngay_dat) : '');
                        $bookingUrl = $isCustomer ? $detailUrl : '../Đăng nhập/dangnhap.php?notice=book&return=' . rawurlencode($returnPath);
                    ?>
                    <div class="court-card">
                        <!-- Khung hình ảnh (Sử dụng ảnh nội bộ trong thư mục images/) -->
                        <div class="court-thumb">
                            <a class="court-detail-link" href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>">
                            <img src="<?php echo htmlspecialchars($court_img); ?>" 
                                 alt="<?php echo htmlspecialchars($court_name); ?>" 
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='images/san1.jpg';">
                            </a>
                            <span class="badge-status">● <?php echo htmlspecialchars($court['trang_thai']); ?></span>
                            <span class="badge-category"><?php echo htmlspecialchars($court['loai_san']); ?></span>
                        </div>

                        <!-- Nội dung sân -->
                        <div class="court-content">
                            <h3 class="court-title"><a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars($court_name); ?></a></h3>
                            <div class="court-location">
                                <span>📍</span>
                                <span><?php echo htmlspecialchars($court_address); ?></span>
                            </div>
                            <p class="court-desc"><?php echo htmlspecialchars($court['mo_ta']); ?></p>
                            <div class="court-footer">
                                <div class="court-price">
                                    <span class="price-label">Giá thuê từ</span>
                                    <span class="price-val"><?php echo $court_price; ?> đ<small style="font-size:12px;font-weight:600;color:var(--gray);">/giờ</small></span>
                                </div>
                                <a href="<?= htmlspecialchars($bookingUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-book">Đặt sân</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">🔎</div>
                    <h3>Không tìm thấy sân nào phù hợp!</h3>
                    <p>Không có sân nào khớp với bộ lọc đã chọn. Hãy thử đổi bộ môn hoặc khu vực khác.</p>
                    <a href="index.php" class="btn-book" style="display:inline-flex;">Xem lại toàn bộ sân</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- 5. FOOTER -->
    <footer>
        <div class="container">
            <p><strong>SportBooking.com</strong> &copy; 2026 - Hệ thống đặt sân thể thao trực tuyến</p>
            <p style="margin-top:5px;font-size:12.5px;">Đồ án môn Lập Trình Web - Nhóm 07 - Lớp 25ĐHAB02 - Học viện Hàng không Việt Nam</p>
        </div>
    </footer>

    <!-- JAVASCRIPT XỬ LÝ TƯƠNG TÁC CÁC NÚT BẤM -->
    <script>
        // 1. Nút "Tìm sân": Kiểm tra người dùng đã chọn ít nhất 1 tiêu chí tìm kiếm hay chưa
        const searchForm = document.querySelector('form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(event) {
                const mon = document.querySelector('select[name="mon"]').value;
                const khuVuc = document.querySelector('select[name="khu_vuc"]').value;
                const ngayDat = document.querySelector('input[name="ngay_dat"]').value;

                // Nếu cả 3 tiêu chí đều rỗng thì ngăn gửi form và hiển thị alert
                if (mon === "" && khuVuc === "" && ngayDat === "") {
                    event.preventDefault(); // Ngăn form gửi đi
                    alert("Vui lòng chọn ít nhất một tiêu chí tìm kiếm (Bộ môn, Khu vực hoặc Ngày đặt)!");
                }
            });
        }

        // 2. Chỉ chặn liên kết mẫu còn sót lại, không chặn liên kết thật.
        const cartButton = document.querySelector('.btn-cart');
        if (cartButton) {
            cartButton.addEventListener('click', function(event) {
                if (this.getAttribute('href') === '#') {
                    event.preventDefault();
                }
            });
        }

    </script>
</body>
</html>
