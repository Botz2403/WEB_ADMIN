<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

include_once "../model/connect_db.php";

try {
    $conn = connectdb();
    
    $sql = "SELECT 
                mon_an.ma_mon_an, 
                mon_an.ten_mon, 
                mon_an.gia_ban, 
                mon_an.ma_danh_muc, 
                danh_muc.ten_danh_muc, 
                mon_an.duong_dan_anh, 
                mon_an.mo_ta_ngan, 
                mon_an.trang_thai 
            FROM mon_an 
            LEFT JOIN danh_muc ON mon_an.ma_danh_muc = danh_muc.id"; 
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trong api_menu.php, trước khi echo json_encode($result);
foreach ($result as &$item) {
    $item['gia_ban'] = (int)$item['gia_ban'];
    $item['trang_thai'] = (int)$item['trang_thai'];
    
    // Thêm dòng này để nối link ảnh
    if (!empty($item['duong_dan_anh'])) {
        $item['duong_dan_anh'] = "http://10.0.2.2/web_admin/view/" . $item['duong_dan_anh'];
       // $item['duong_dan_anh'] = "http://192.168.2.147/web_admin/view/" . $item['duong_dan_anh'];
    }
}

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([]);
}
exit();
?>