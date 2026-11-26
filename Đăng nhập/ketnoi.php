
<?php
// Dùng chung kết nối PDO của toàn bộ hệ thống SportBooking.
require_once dirname(__DIR__) . '/giỏ hàng, thanh toán/config/db.php';

class Database {
    public PDO $pdo;
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
}

/**
 * Hỗ trợ dữ liệu mật khẩu cũ trong hai SQL của nhóm:
 * - SHA-256 dạng 64 ký tự hex;
 * - plaintext demo ở một số dòng chủ sân của database cũ;
 * - password_hash() cho tài khoản đăng ký mới.
 * Sau lần đăng nhập hợp lệ đầu tiên, mật khẩu cũ được nâng cấp sang password_hash().
 */
function auth_password_verify_and_upgrade(PDO $pdo, string $table, string $idColumn, int $id, ?string $stored, string $plain): bool {
    if ($stored === null || $stored === '') return false;

    $ok = false;
    $info = password_get_info($stored);
    if (!empty($info['algo'])) {
        $ok = password_verify($plain, $stored);
    } elseif (preg_match('/^[a-f0-9]{64}$/i', $stored)) {
        $ok = hash_equals(strtolower($stored), hash('sha256', $plain));
    } else {
        // Tương thích dữ liệu demo cũ đang lưu trực tiếp mật khẩu 123456.
        $ok = hash_equals($stored, $plain);
    }

    if ($ok && empty($info['algo'])) {
        $newHash = password_hash($plain, PASSWORD_DEFAULT);
        $sql = "UPDATE `$table` SET `mat_khau` = ? WHERE `$idColumn` = ?";
        $st = $pdo->prepare($sql);
        $st->execute([$newHash, $id]);
    }
    return $ok;
}
