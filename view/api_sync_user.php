<?php
// Tắt cảnh báo lỗi để không làm hỏng JSON trả về
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../model/connect_db.php";

// Đọc dữ liệu JSON từ request (Android gửi lên)
$data = json_decode(file_get_contents("php://input"), true);

// Nếu không phải JSON, thử lấy từ $_POST
if (!$data) {
    $data = $_POST;
}

// Kiểm tra xem có đủ dữ liệu bắt buộc không (ít nhất phải có uid)
if (!isset($data['uid_firebase']) || empty($data['uid_firebase'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu UID Firebase'
    ]);
    exit();
}

$uid_firebase = $data['uid_firebase'];
$ho_ten = isset($data['ho_ten']) ? $data['ho_ten'] : 'Khách hàng';
$email = isset($data['email']) ? $data['email'] : '';
$so_dien_thoai = isset($data['so_dien_thoai']) ? $data['so_dien_thoai'] : null;

try {
    $conn = connectdb();
    
    // Câu lệnh INSERT ON DUPLICATE KEY UPDATE: 
    // Nếu chưa có thì thêm mới, nếu có rồi thì cập nhật thông tin mới nhất
    $sql = "INSERT INTO khach_hang (uid_firebase, ho_ten, email, so_dien_thoai, hang_thanh_vien) 
            VALUES (:uid, :name, :email, :phone, 'standard')
            ON DUPLICATE KEY UPDATE 
            ho_ten = :name_update, 
            email = :email_update, 
            so_dien_thoai = COALESCE(:phone_update, so_dien_thoai)"; // Chỉ cập nhật sđt nếu có sđt mới
            
    $stmt = $conn->prepare($sql);
    
    // Ràng buộc tham số
    $stmt->bindParam(':uid', $uid_firebase);
    $stmt->bindParam(':name', $ho_ten);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $so_dien_thoai);
    
    // Tham số cho phần UPDATE
    $stmt->bindParam(':name_update', $ho_ten);
    $stmt->bindParam(':email_update', $email);
    $stmt->bindParam(':phone_update', $so_dien_thoai);
    
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đồng bộ khách hàng thành công'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi đồng bộ: ' . $e->getMessage()
    ]);
}
exit();
?>
