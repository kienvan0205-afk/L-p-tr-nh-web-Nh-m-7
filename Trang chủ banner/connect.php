<?php
// Cấu hình thông tin kết nối CSDL MySQL
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'he_thong_dat_san_bong';

// Tắt báo lỗi fatal tự động của mysqli để xử lý êm dịu
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
$db_connected = true;

if (!$conn) {
    $db_connected = false;
    $db_error = mysqli_connect_error();
} else {
    // Thiết lập font chữ tiếng Việt không bị lỗi hiển thị
    mysqli_set_charset($conn, "utf8mb4");
}
?>
