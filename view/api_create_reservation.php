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

$ma_nguoi_dung = $data['ma_nguoi_dung'] ?? '';
$ho_ten        = $data['ho_ten'] ?? '';
$so_dien_thoai = $data['so_dien_thoai'] ?? '';
$thoi_gian     = $data['thoi_gian'] ?? ''; // yyyy-MM-dd HH:mm
$so_nguoi      = (int)($data['so_nguoi'] ?? 0);
$khu_vuc       = $data['khu_vuc'] ?? 'Trong nhà';
$ghi_chu       = $data['ghi_chu'] ?? '';
$tien_dat_coc  = (int)($data['tien_dat_coc'] ?? 0);
$ma_chi_nhanh  = $data['ma_chi_nhanh'] ?? 'B01';

if (empty($ma_nguoi_dung) || empty($ho_ten) || empty($thoi_gian)) {
    echo json_encode(["status" => "error", "message" => "Thiếu thông tin đặt bàn bắt buộc (Người dùng, Họ tên, Thời gian)"]);
    exit();
}

try {
    $conn = connectdb();
    
    $ma_dat_cho = "RES-" . strtoupper(substr(uniqid(), -6)) . "-" . date('is');
    
    $sql = "INSERT INTO dat_ban (ma_dat_cho, ma_nguoi_dung, ho_ten, so_dien_thoai, thoi_gian, so_nguoi, khu_vuc, ghi_chu, tien_dat_coc, ma_chi_nhanh, trang_thai) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $ma_dat_cho, 
        $ma_nguoi_dung, 
        $ho_ten, 
        $so_dien_thoai, 
        $thoi_gian, 
        $so_nguoi, 
        $khu_vuc, 
        $ghi_chu, 
        $tien_dat_coc,
        $ma_chi_nhanh
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Đã gửi yêu cầu đặt bàn thành công",
        "ma_dat_cho" => $ma_dat_cho
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi hệ thống: " . $e->getMessage()
    ]);
}
?>
