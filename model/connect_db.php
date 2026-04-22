<?php
    function connectdb(){
        static $conn = null;
        $servername = "localhost";
        $username = "root";
        $password = "";

        if($conn===null){
            try {
                $conn = new PDO("mysql:host=$servername;dbname=web_admin", $username, $password);
  
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn->exec("set names utf8mb4");
        
            } catch(PDOException $e) {
        
            }
        }
            return $conn;

    
    }


?>