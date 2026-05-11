<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include_once "../model/connect_db.php";

$id_hoa_don = $_GET['id'] ?? '';

if (empty($id_hoa_don)) {
    echo json_encode(["status" => "error", "message" => "Thiếu mã hóa đơn"]);
    exit();
}

try {
    $conn = connectdb();
    
    // 1. Lấy thông tin chung của hóa đơn
    $stmt = $conn->prepare("
        SELECT h.*, b.ten_ban 
        FROM hoa_don h
        LEFT JOIN ban_an b ON h.ma_ban = b.ma_ban
        WHERE h.id_hoa_don = ?
    ");
    $stmt->execute([$id_hoa_don]);
    $orderInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$orderInfo) {
        echo json_encode(["status" => "error", "message" => "Không tìm thấy hóa đơn"]);
        exit();
    }

    // 2. Lấy danh sách món ăn (chi tiết hóa đơn)
    $stmtDetail = $conn->prepare("
        SELECT c.*, m.ten_mon 
        FROM chi_tiet_hoa_don c
        LEFT JOIN mon_an m ON c.ma_mon_an = m.ma_mon_an
        WHERE c.id_hoa_don = ?
    ");
    $stmtDetail->execute([$id_hoa_don]);
    $itemsResult = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn hóa danh sách items khớp với class OrderItem trong Kotlin
    $items = [];
    foreach ($itemsResult as $row) {
        $items[] = [
            "menuItemId" => $row['ma_mon_an'],
            "name"       => $row['ten_mon'] ?? "Món không xác định",
            "qty"        => (int)$row['so_luong'],
            "price"      => (int)$row['gia_ban'],
            "note"       => $row['ghi_chu'] ?? "",
            "selectedModifiers" => []
        ];
    }

    // Map trạng thái đơn sang chuẩn Enum của Android
    $statusMap = [
        'cho_bep' => 'PREPARING',
        'da_xong' => 'READY',
        'hoan_thanh' => 'COMPLETED',
        'huy' => 'CANCELLED'
    ];
    $trang_thai = $orderInfo['trang_thai_don'];
    $statusStr = isset($statusMap[$trang_thai]) ? $statusMap[$trang_thai] : 'PENDING';

    // Tạo object Order JSON khớp với class Order trong Kotlin
    $orderData = [
        "id_hoa_don"     => $orderInfo['id_hoa_don'],
        "ma_ban"         => $orderInfo['ma_ban'],
        "ma_nhan_vien"   => $orderInfo['nhan_vien_id'] ?? "",
        "trang_thai_don" => $statusStr,
        "tong_tien"      => (int)$orderInfo['tong_tien'],
        "thoi_gian_tao"  => $orderInfo['thoi_gian_tao'] ?? date('Y-m-d H:i:s'),
        "tableName"      => $orderInfo['ten_ban'] ?? "Bàn không xác định",
        "items"          => $items,
        "discount"       => 0,
        "eta"            => "25 phút"
    ];

    echo json_encode([
        "status" => "success",
        "data"   => $orderData
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi truy xuất CSDL: " . $e->getMessage()
    ]);
}
?>
