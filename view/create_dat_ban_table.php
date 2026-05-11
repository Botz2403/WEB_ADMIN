<?php
include_once "../model/connect_db.php";
try {
    $conn = connectdb();
    $sql = "CREATE TABLE IF NOT EXISTS dat_ban (
        ma_dat_cho VARCHAR(50) PRIMARY KEY,
        ma_nguoi_dung VARCHAR(100) NOT NULL,
        ho_ten VARCHAR(100) NOT NULL,
        so_dien_thoai VARCHAR(20) NOT NULL,
        ma_ban VARCHAR(20) DEFAULT NULL,
        ma_chi_nhanh VARCHAR(20) DEFAULT 'B01',
        thoi_gian DATETIME NOT NULL,
        so_nguoi INT NOT NULL,
        khu_vuc VARCHAR(50) NOT NULL,
        ghi_chu TEXT DEFAULT NULL,
        trang_thai VARCHAR(50) DEFAULT 'PENDING',
        tien_dat_coc INT DEFAULT 0,
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->exec($sql);
    echo "Table 'dat_ban' created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
