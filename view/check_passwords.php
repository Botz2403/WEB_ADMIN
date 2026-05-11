<?php
include_once "../model/connect_db.php";
try {
    $conn = connectdb();
    $stmt = $conn->query("SELECT email, mat_khau FROM nhan_vien LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($users);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
