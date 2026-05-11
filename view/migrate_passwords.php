<?php
include_once "../model/connect_db.php";
try {
    $conn = connectdb();
    $stmt = $conn->query("SELECT ma_nhan_vien, mat_khau FROM nhan_vien");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updated = 0;
    foreach ($users as $user) {
        $id = $user['ma_nhan_vien'];
        $pass = $user['mat_khau'];
        
        // Nếu mật khẩu chưa phải là MD5 (độ dài khác 32)
        if (strlen($pass) != 32) {
            $hashed = md5($pass);
            $updateStmt = $conn->prepare("UPDATE nhan_vien SET mat_khau = ? WHERE ma_nhan_vien = ?");
            $updateStmt->execute([$hashed, $id]);
            $updated++;
        }
    }
    echo "Successfully updated $updated passwords to MD5.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
