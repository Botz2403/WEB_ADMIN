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
        $stmt = $conn->prepare("SELECT ma_nhan_vien, ho_ten, vai_tro FROM nhan_vien WHERE email = ? AND mat_khau = ?");
        $stmt->execute([$email, $password]);
        
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff) {
            // Map roles for the Mobile App
            $rawRole = strtolower($staff['vai_tro']);
            $appRole = 'staff'; // default
            
            if ($rawRole == 'bep' || $rawRole == 'kitchen') {
                $appRole = 'kitchen';
            } else if ($rawRole == 'admin' || $rawRole == 'nhanvien' || $rawRole == 'staff') {
                $appRole = 'staff';
            }

            echo json_encode([
                "status" => "success", 
                "message" => "Đăng nhập thành công",
                "data" => [
                    "ma_nhan_vien" => $staff['ma_nhan_vien'],
                    "ho_ten" => $staff['ho_ten'],
                    "vai_tro" => $appRole
                ]
            ]);
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
