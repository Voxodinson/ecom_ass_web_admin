<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "ecom_web_assignment";

    try {
        $con = new PDO(
            "mysql:host=$host;dbname=$database",
            $username, 
            $password
        );
        
        $con->setAttribute(
            PDO::ATTR_ERRMODE, 
            PDO::ERRMODE_EXCEPTION
        );

    } catch (PDOException $e) {
        die(
            "Connection failed: " . 
            $e->getMessage());
    }
?>
