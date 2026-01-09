<?php
session_start();
require_once 'config/db.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='login.php';</script>";
    exit();
}

// 2. KIỂM TRA ID HỢP LỆ
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // 3. KIỂM TRA QUYỀN SỞ HỮU (Admin hoặc Chủ bài viết)
    // Nếu là Admin (role=1) thì được xóa tất cả. Nếu là User thường thì chỉ xóa bài của mình.
    if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $check_sql = "SELECT * FROM posts WHERE id = $id";
    } else {
        $check_sql = "SELECT * FROM posts WHERE id = $id AND user_id = $user_id";
    }

    $result = mysqli_query($conn, $check_sql);
    $post = mysqli_fetch_assoc($result);

    if ($post) {
        // --- XÓA ẢNH TRÊN SERVER TRƯỚC (NẾU CÓ) ---
        if (!empty($post['image'])) {
            $image_path = "uploads/" . $post['image'];
            if (file_exists($image_path)) {
                unlink($image_path); // Hàm unlink dùng để xóa file
            }
        }

        // --- XÓA DỮ LIỆU TRONG DATABASE ---
        $delete_sql = "DELETE FROM posts WHERE id = $id";
        if (mysqli_query($conn, $delete_sql)) {
            // Xóa xong quay lại trang trước đó (hoặc trang chủ)
            echo "<script>alert('Đã xóa bài viết thành công!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Lỗi Database: Không thể xóa!'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Bạn không có quyền xóa bài này hoặc bài viết không tồn tại!'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>