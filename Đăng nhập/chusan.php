
<?php
require_once __DIR__ . '/ketnoi.php';

class ChuSan {
    private PDO $pdo;
    public function __construct() {
        $this->pdo = (new Database())->pdo;
    }

    public function dangNhap(string $gmail, string $mat_khau) {
        $st = $this->pdo->prepare(
            'SELECT ma_chu_san AS id, ten_chu_san AS HoTen, mat_khau
             FROM chu_san WHERE LOWER(gmail) = LOWER(?) LIMIT 1'
        );
        $st->execute([$gmail]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        if (!auth_password_verify_and_upgrade($this->pdo, 'chu_san', 'ma_chu_san', (int)$row['id'], $row['mat_khau'] ?? null, $mat_khau)) return false;
        return ['id' => (int)$row['id'], 'HoTen' => $row['HoTen']];
    }
}
