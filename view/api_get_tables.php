<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include_once "../model/connect_db.php";

try {
    $conn = connectdb();
    
    // Lấy toàn bộ danh sách bàn ăn
    $stmt = $conn->prepare("SELECT ma_ban, ten_ban, khu_vuc, suc_chua, trang_thai FROM ban_an ORDER BY khu_vuc, ma_ban");
    $stmt->execute();
    
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn hóa dữ liệu JSON để khớp với Model bên Android Studio
    $formattedTables = array_map(function($table) {
        return [
            "ma_ban" => $table['ma_ban'],
            "ten_ban" => $table['ten_ban'],
            "so_cho_ngoi" => (int)$table['suc_chua'],
            "trang_thai" => $table['trang_thai'], // "trong", "co_khach", "dat_truoc", "bao_tri"
            
            // Map dữ liệu khu_vuc cho khớp với App Android
            "khu_vuc" => match($table['khu_vuc']) {
                "trong_nha" => "Trong nhà",
                "ngoai_troi" => "Ngoài trời",
                "vip" => "Phòng VIP",
                "bar" => "Quầy Bar",
                default => "Trong nhà"
            },
            
            // Tạm thời để trống ID đơn hàng. Sau này kết nối bảng Hoa_don thì query thêm.
            "currentOrderId" => "" 
        ];
    }, $tables);

    // Trả về JSON chuẩn
    echo json_encode([
        "status" => "success",
        "data" => $formattedTables
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi truy xuất CSDL: " . $e->getMessage()
    ]);
}
?>
