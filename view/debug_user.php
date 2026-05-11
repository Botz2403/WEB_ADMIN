<?php
include_once "../model/connect_db.php";
try {
    $conn = connectdb();
    $stmt = $conn->prepare("SELECT * FROM nhan_vien WHERE email = 'ltri0116@gmail.com'");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
