<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include_once "../model/connect_db.php";

$userId = $_GET['user_id'] ?? '';

if (empty($userId)) {
    echo json_encode(["status" => "error", "message" => "Thiếu mã người dùng"]);
    exit();
}

try {
    $conn = connectdb();
    
    // 1. Lấy danh sách hóa đơn của khách hàng
    $stmt = $conn->prepare("
        SELECT h.*, b.ten_ban 
        FROM hoa_don h
        LEFT JOIN ban_an b ON h.ma_ban = b.ma_ban
        WHERE h.uid_firebase = ?
        ORDER BY h.thoi_gian_tao DESC
    ");
    $stmt->execute([$userId]);
    $ordersResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orders = [];
    foreach ($ordersResult as $orderInfo) {
        // Map trạng thái sang chuẩn Enum của Android
        $statusMap = [
            'cho_bep' => 'PREPARING',
            'da_xong' => 'READY',
            'hoan_thanh' => 'COMPLETED',
            'huy' => 'CANCELLED'
        ];
        $trang_thai = $orderInfo['trang_thai_don'];
        $statusStr = isset($statusMap[$trang_thai]) ? $statusMap[$trang_thai] : 'PENDING';

        // Lấy items cho mỗi đơn hàng (tùy chọn: có thể join để nhanh hơn, nhưng đây là cách đơn giản)
        $stmtDetail = $conn->prepare("
            SELECT c.*, m.ten_mon 
            FROM chi_tiet_hoa_don c
            LEFT JOIN mon_an m ON c.ma_mon_an = m.ma_mon_an
            WHERE c.id_hoa_don = ?
        ");
        $stmtDetail->execute([$orderInfo['id_hoa_don']]);
        $itemsResult = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

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

        $orders[] = [
            "id_hoa_don"     => $orderInfo['id_hoa_don'],
            "ma_ban"         => $orderInfo['ma_ban'],
            "nhan_vien_id"   => $orderInfo['nhan_vien_id'] ?? null,
            "trang_thai_don" => $statusStr,
            "tong_tien"      => (int)$orderInfo['tong_tien'],
            "thoi_gian_tao"  => $orderInfo['thoi_gian_tao'],
            "tableName"      => $orderInfo['ten_ban'] ?? "Đơn hàng Online",
            "items"          => $items,
            "discount"       => 0,
            "eta"            => "25 phút"
        ];
    }

    echo json_encode([
        "status" => "success",
        "data"   => $orders
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi truy xuất CSDL: " . $e->getMessage()
    ]);
}
?>
