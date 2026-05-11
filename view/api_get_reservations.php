<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include_once "../model/connect_db.php";

try {
    $conn = connectdb();
    
    $sql = "SELECT * FROM dat_ban ORDER BY thoi_gian DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($rows as $row) {
        $data[] = [
            "ma_dat_cho"    => $row['ma_dat_cho'],
            "ma_nguoi_dung" => $row['ma_nguoi_dung'],
            "ho_ten"        => $row['ho_ten'],
            "so_dien_thoai" => $row['so_dien_thoai'],
            "ma_ban"        => $row['ma_ban'],
            "ma_chi_nhanh"  => $row['ma_chi_nhanh'],
            "thoi_gian"     => $row['thoi_gian'],
            "so_nguoi"      => (int)$row['so_nguoi'],
            "khu_vuc"       => $row['khu_vuc'],
            "ghi_chu"       => $row['ghi_chu'],
            "trang_thai"    => $row['trang_thai'],
            "tien_dat_coc"  => (int)$row['tien_dat_coc']
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi CSDL hoặc Bảng dat_ban chưa tồn tại: " . $e->getMessage()
    ]);
}
?>
