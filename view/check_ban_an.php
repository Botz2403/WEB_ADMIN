<?php
include_once "../model/connect_db.php";
try {
    $conn = connectdb();
    $stmt = $conn->query("DESCRIBE ban_an");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($cols);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
