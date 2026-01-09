<?php
session_start();
require_once 'config/db.php';

// Kiểm tra nút submit có được nhấn không
if (isset($_POST['btn_submit'])) {

    // 1. Lấy ID người đăng
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        // Nếu chưa đăng nhập thì chặn luôn
        echo "<script>alert('Bạn phải đăng nhập để đăng tin!'); window.location.href='login.php';</script>";
        exit();
    }

    // 2. Lấy dữ liệu từ Form
    $type        = $_POST['type'];
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $category    = mysqli_real_escape_string($conn, $_POST['cat']); // Lưu ý: name="cat" hay "category" phải khớp với form
    $address     = mysqli_real_escape_string($conn, $_POST['address']);
    $lost_date   = isset($_POST['lost_date']) ? $_POST['lost_date'] : date('Y-m-d');
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // 3. Insert bài viết vào database
    $sql = "INSERT INTO posts (user_id, type, title, category, address, lost_date, description)
            VALUES ('$user_id', '$type', '$title', '$category', '$address', '$lost_date', '$description')";

    if (mysqli_query($conn, $sql)) {
        $post_id = mysqli_insert_id($conn); // Lấy ID bài vừa tạo
        
        // 4. XỬ LÝ UPLOAD ẢNH (Nếu có)
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $target_dir = "uploads/";
            $countfiles = count($_FILES['images']['name']);
            
            for($i = 0; $i < $countfiles; $i++){
                $filename = $_FILES['images']['name'][$i];
                if(!empty($filename)){
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_name = "post_" . $post_id . "_" . time() . "_" . $i . "." . $ext;
                    $target_file = $target_dir . $new_name;
                    
                    if(move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)){
                        // Lưu vào bảng post_images (nếu có dùng bảng này)
                        $sql_img = "INSERT INTO post_images (post_id, image_path) VALUES ('$post_id', '$new_name')";
                        mysqli_query($conn, $sql_img);

                        // Cập nhật ảnh đại diện cho bài viết (lấy ảnh đầu tiên)
                        if($i == 0){
                            $update_main = "UPDATE posts SET image = '$new_name' WHERE id = $post_id";
                            mysqli_query($conn, $update_main);
                        }
                    }
                }
            }
        }

        // --- QUAN TRỌNG: THÔNG BÁO THÀNH CÔNG RỒI MỚI VỀ TRANG CHỦ ---
        echo "<script>
            alert('🎉 Đăng tin thành công! Hy vọng bạn sớm tìm được đồ.');
            window.location.href = 'index.php';
        </script>";
        exit();

    } else {
        echo "Lỗi SQL: " . mysqli_error($conn);
    }
} else {
    // Nếu truy cập trực tiếp file này mà không bấm nút
    header("Location: index.php");
    exit();
}
?>