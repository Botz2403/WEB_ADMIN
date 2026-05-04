<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

include_once "../model/connect_db.php";

try {
    $conn = connectdb();
    
    $sql = "SELECT 
                ma_mon_an, 
                ten_mon, 
                gia_ban, 
                ma_danh_muc, 
                duong_dan_anh, 
                mo_ta_ngan, 
                trang_thai 
            FROM mon_an"; 
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as &$item) {
        $item['gia_ban'] = (int)$item['gia_ban'];
        $item['trang_thai'] = (int)$item['trang_thai'];
    }

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([]);
}
exit();
?>
