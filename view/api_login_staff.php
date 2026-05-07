<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include_once "../model/connect_db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = md5($data['password']); // Mã hóa MD5 để khớp với Database

    try {
        $conn = connectdb();
        $stmt = $conn->prepare("SELECT ma_nhan_vien, ho_ten, vai_tro, chi_nhanh, trang_thai FROM nhan_vien WHERE email = ? AND mat_khau = ?");
        $stmt->execute([$email, $password]);
        
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff) {
            if ($staff['trang_thai'] === 'inactive') {
                echo json_encode(["status" => "error", "message" => "Tài khoản của bạn đã bị khóa."]);
            } else {
                echo json_encode([
                    "status" => "success", 
                    "message" => "Đăng nhập thành công",
                    "data" => [
                        "id" => $staff['ma_nhan_vien'],
                        "name" => $staff['ho_ten'],
                        "role" => $staff['vai_tro'],
                        "branch" => $staff['chi_nhanh']
                    ]
                ]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Sai email hoặc mật khẩu nhân viên."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Lỗi máy chủ: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu email hoặc password."]);
}
?>
