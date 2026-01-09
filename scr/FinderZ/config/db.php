<?php
// File: config/db.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lost_found";

$conn = new mysqli($host, $user, $pass, $dbname);

// Kiểm tra lỗi và đặt bảng mã UTF-8 để không lỗi font tiếng Việt
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>