<?php
session_start();
require_once 'config/db.php';

// Kiểm tra xem người dùng có bấm nút Lưu không
if (isset($_POST['btn_save'])) {
    
    // 1. LẤY DỮ LIỆU ĐẦU VÀO
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id']; 

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $status = $_POST['status']; 
    $type = $_POST['type']; 
    
    // Lấy ID danh mục từ form
    $category_val = $_POST['category_id']; 

    // 2. KIỂM TRA QUYỀN SỞ HỮU
    $check_sql = "SELECT * FROM posts WHERE id='$id' AND user_id='$user_id'";
    $check_query = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_query) == 0) {
        die("Lỗi: Bạn không có quyền chỉnh sửa bài viết này.");
    }
    
    $old_data = mysqli_fetch_assoc($check_query);

    // 3. XỬ LÝ HÌNH ẢNH (Nếu có chọn ảnh mới)
    $image_update_sql = ""; 

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $file_name = $_FILES['images']['name'][0]; // Lấy file đầu tiên
        $file_tmp = $_FILES['images']['tmp_name'][0];
        $file_error = $_FILES['images']['error'][0];

        if ($file_error == 0) {
            $new_file_name = time() . "_" . $file_name;
            $upload_path = "uploads/" . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_update_sql = ", image='$new_file_name'";

                // Xóa ảnh cũ nếu có
                $old_image_path = "uploads/" . $old_data['image'];
                if (!empty($old_data['image']) && file_exists($old_image_path)) {
                    unlink($old_image_path); 
                }
            }
        }
    }

    // 4. CẬP NHẬT VÀO DATABASE
    // SỬA QUAN TRỌNG: Cột trong DB tên là 'category', không phải 'category_id'
    $sql = "UPDATE posts SET 
            title = '$title', 
            address = '$address', 
            description = '$description', 
            status = '$status',
            type = '$type', 
            category = '$category_val' 
            $image_update_sql 
            WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Cập nhật tin thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi Database: " . mysqli_error($conn);
    }

} else {
    header("Location: index.php");
}
?>