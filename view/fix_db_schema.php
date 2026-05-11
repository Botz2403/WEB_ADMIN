<?php
include_once "../model/connect_db.php";
try {
    $conn = connectdb();
    
    // Kiểm tra xem cột uid_firebase đã tồn tại chưa
    $stmt = $conn->query("SHOW COLUMNS FROM hoa_don LIKE 'uid_firebase'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE hoa_don ADD COLUMN uid_firebase VARCHAR(128) AFTER ma_ban");
        echo "Successfully added uid_firebase column to hoa_don table.";
    } else {
        echo "Column uid_firebase already exists in hoa_don table.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
