<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once "../model/connect_db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
    exit();
}

$orderId = $data['id'] ?? '';
$tableId = $data['tableId'] ?? '';
$items = $data['items'] ?? [];
$subtotal = $data['subtotal'] ?? 0;

if (empty($orderId) || empty($tableId) || empty($items)) {
    echo json_encode(["status" => "error", "message" => "Thiếu thông tin đơn hàng"]);
    exit();
}

try {
    $conn = connectdb();
    $conn->beginTransaction();

    // 1. Tạo hóa đơn mới
    $stmt = $conn->prepare("INSERT INTO hoa_don (id_hoa_don, ma_ban, trang_thai_don, tong_tien) VALUES (?, ?, 'cho_bep', ?)");
    $stmt->execute([$orderId, $tableId, $subtotal]);

    // 2. Thêm chi tiết món ăn
    $stmtDetail = $conn->prepare("INSERT INTO chi_tiet_hoa_don (id_hoa_don, ma_mon_an, so_luong, gia_ban, ghi_chu) VALUES (?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmtDetail->execute([
            $orderId,
            $item['menuItemId'],
            $item['qty'],
            $item['price'],
            $item['note'] ?? ''
        ]);
    }

    // 3. Cập nhật trạng thái bàn thành "Có khách" (co_khach)
    $stmtUpdateTable = $conn->prepare("UPDATE ban_an SET trang_thai = 'co_khach' WHERE ma_ban = ?");
    $stmtUpdateTable->execute([$tableId]);

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Đã gửi order xuống bếp thành công"
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi CSDL: " . $e->getMessage()
    ]);
}
?>
