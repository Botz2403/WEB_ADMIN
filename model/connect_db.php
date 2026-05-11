<?php
function connectdb(){
    static $conn = null;
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $port = 3307; // Đảm bảo port này đúng với XAMPP của bạn (thử đổi sang 3306 nếu vẫn lỗi)

    if($conn === null){
        try {
            $conn = new PDO(
                "mysql:host=$servername;port=$port;dbname=web_admin;charset=utf8mb4",
                $username,
                $password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // KHÔNG dùng die ở đây, để file menu.php xử lý lỗi
            return null;
        }
    }
    return $conn;
}
?>