<?php
   function insert_menu($ma_mon_an, $ten_mon, $gia_ban, $ma_danh_muc, $duong_dan_anh, $mo_ta_ngan, $trang_thai) {
    $conn = connectdb();
    $sql = "INSERT INTO mon_an (ma_mon_an, ten_mon, gia_ban, ma_danh_muc, duong_dan_anh, mo_ta_ngan, trang_thai) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$ma_mon_an, $ten_mon, $gia_ban, $ma_danh_muc, $duong_dan_anh, $mo_ta_ngan, $trang_thai]);
    }
    function get_all_menu(){
    $conn = connectdb();
    // Sử dụng INNER JOIN để lấy thêm cột ten_danh_muc từ bảng danh_muc
    $sql = "SELECT mon_an.*, danh_muc.ten_danh_muc 
            FROM mon_an 
            INNER JOIN danh_muc ON mon_an.ma_danh_muc = danh_muc.id 
            ORDER BY mon_an.ma_mon_an DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
    }

   
    function update_menu($ma_mon_an, $ten_mon, $gia_ban, $ma_danh_muc, $duong_dan_anh, $mo_ta_ngan, $trang_thai) {
        $conn = connectdb();
        
        if ($duong_dan_anh != "") {
            $sql = "UPDATE mon_an SET ten_mon='$ten_mon', gia_ban='$gia_ban', ma_danh_muc='$ma_danh_muc', 
                    duong_dan_anh='$duong_dan_anh', mo_ta_ngan='$mo_ta_ngan', trang_thai='$trang_thai' 
                    WHERE ma_mon_an='$ma_mon_an'";
        } else {
            $sql = "UPDATE mon_an SET ten_mon='$ten_mon', gia_ban='$gia_ban', ma_danh_muc='$ma_danh_muc', 
                    mo_ta_ngan='$mo_ta_ngan', trang_thai='$trang_thai' 
                    WHERE ma_mon_an='$ma_mon_an'";
        }
        $conn->exec($sql);
    }
    function delete_menu($ma_mon_an){
        $conn = connectdb();
        $sql="DELETE FROM mon_an where ma_mon_an='$ma_mon_an'";    
        $conn->exec($sql);
    
    }
?>