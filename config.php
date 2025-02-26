<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "ecom_web_assignment";

$con = new mysqli($host, $username, $password, $database);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

?>