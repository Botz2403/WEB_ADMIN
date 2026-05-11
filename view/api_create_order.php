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

// Map fields from Kotlin CreateOrderRequest
$orderId = $data['id_hoa_don'] ?? '';
$tableId = $data['ma_ban'] ?? 'Online';
$staffId = $data['nhan_vien_id'] ?? null; // Khớp với DB
$userId = $data['ma_nguoi_dung'] ?? null; // UID của khách hàng
$items = $data['chi_tiet'] ?? []; // Kotlin sends 'chi_tiet'
$subtotal = $data['tong_tien'] ?? 0;

// Auto-generate order ID if not provided
if (empty($orderId)) {
    $orderId = "ORD" . date('YmdHis') . rand(100, 999);
}

if (empty($tableId) || empty($items)) {
    echo json_encode(["status" => "error", "message" => "Thiếu thông tin đơn hàng (Bàn hoặc Món ăn)"]);
    exit();
}

try {
    $conn = connectdb();
    $conn->beginTransaction();

    // 1. Tạo hóa đơn mới (Lưu cả uid_firebase để khách hàng xem lại đơn)
    $stmt = $conn->prepare("INSERT INTO hoa_don (id_hoa_don, ma_ban, uid_firebase, nhan_vien_id, trang_thai_don, tong_tien) VALUES (?, ?, ?, ?, 'cho_bep', ?)");
    $stmt->execute([$orderId, $tableId, $userId, $staffId, $subtotal]);

    // 2. Thêm chi tiết món ăn
    $stmtDetail = $conn->prepare("INSERT INTO chi_tiet_hoa_don (id_hoa_don, ma_mon_an, so_luong, gia_ban, ghi_chu) VALUES (?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        // Map fields from Kotlin OrderItemRequest
        $stmtDetail->execute([
            $orderId,
            $item['ma_mon_an'] ?? '',
            $item['so_luong'] ?? 0,
            $item['don_gia'] ?? 0,
            $item['ghi_chu'] ?? ''
        ]);
    }

    // 3. Cập nhật trạng thái bàn thành "Có khách" (nếu không phải đơn Online)
    if ($tableId !== 'Online') {
        $stmtUpdateTable = $conn->prepare("UPDATE ban_an SET trang_thai = 'co_khach' WHERE ma_ban = ?");
        $stmtUpdateTable->execute([$tableId]);
    }

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Đã gửi order thành công",
        "id_hoa_don" => $orderId
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
