
<?php
require_once __DIR__ . '/ketnoi.php';

class KhachHang {
    private PDO $pdo;
    public function __construct() {
        $this->pdo = (new Database())->pdo;
    }

    public function tonTai(string $gmail, string $sdt): bool {
        $sql = 'SELECT idKH FROM khachhang WHERE LOWER(gmail) = LOWER(?) OR LOWER(Email) = LOWER(?) OR SDT = ? LIMIT 1';
        $st = $this->pdo->prepare($sql);
        $st->execute([$gmail, $gmail, $sdt]);
        return (bool)$st->fetchColumn();
    }

    public function themMoi(string $hoten, string $gmail, string $mat_khau, string $sdt, string $diachi): void {
        $hash = password_hash($mat_khau, PASSWORD_DEFAULT);
        $tenDangNhap = substr((string)strstr($gmail, '@', true), 0, 30);
        $sql = 'INSERT INTO khachhang (HoTen, ten_dang_nhap, gmail, mat_khau, SDT, DiaChi, Email)
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $st = $this->pdo->prepare($sql);
        $st->execute([$hoten, $tenDangNhap, $gmail, $hash, $sdt, $diachi === '' ? null : $diachi, $gmail]);
    }

    public function dangNhap(string $gmail, string $mat_khau) {
        $st = $this->pdo->prepare(
            'SELECT idKH, HoTen, mat_khau FROM khachhang
             WHERE LOWER(gmail) = LOWER(?) OR LOWER(Email) = LOWER(?) LIMIT 1'
        );
        $st->execute([$gmail, $gmail]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        if (!auth_password_verify_and_upgrade($this->pdo, 'khachhang', 'idKH', (int)$row['idKH'], $row['mat_khau'] ?? null, $mat_khau)) return false;
        return ['idKH' => (int)$row['idKH'], 'HoTen' => $row['HoTen']];
    }

    public function dem(): int {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM khachhang')->fetchColumn();
    }
}
