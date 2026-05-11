<?php
include_once "../model/connect_db.php";
try {
    $conn = connectdb();
    $stmt = $conn->query("DESCRIBE hoa_don");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
