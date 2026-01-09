<?php
session_start();
require_once 'config/db.php';

// 1. CHẶN KHÔNG CHO NGƯỜI CHƯA ĐĂNG NHẬP VÀO
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Bạn vui lòng đăng nhập để đăng tin!'); 
            window.location.href='login.php';
          </script>";
    exit();
}

// 2. XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT "ĐĂNG TIN"
if (isset($_POST['btn_dangtin'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['type']; // 'lost' hoặc 'found'
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $lost_date = $_POST['lost_date'];

    // --- XỬ LÝ UPLOAD ẢNH ---
    $image_name = ""; // Mặc định là rỗng
    
    // Kiểm tra xem có chọn ảnh không
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/"; // Thư mục chứa ảnh
        
        // Tạo tên file mới: Thời gian hiện tại + Tên gốc (để tránh trùng tên)
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        // Di chuyển file từ bộ nhớ tạm vào thư mục uploads
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // --- LƯU VÀO DATABASE ---
    $sql = "INSERT INTO posts (user_id, title, type, address, description, lost_date, image, status, created_at) 
            VALUES ('$user_id', '$title', '$type', '$address', '$description', '$lost_date', '$image_name', 1, NOW())";

    if (mysqli_query($conn, $sql)) {
        // Lấy ID bài vừa đăng xong
        $new_id = mysqli_insert_id($conn);
        // Chuyển hướng người dùng sang trang xem bài viết (post.php)
        echo "<script>
                alert('Đăng tin thành công!'); 
                window.location.href='post.php?id=$new_id';
              </script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-warning text-white text-center py-3">
                    <h3 class="fw-bold mb-0 text-uppercase"><i class="bi bi-pencil-square"></i> Đăng tin tìm đồ</h3>
                </div>
                <div class="card-body p-4">
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề ngắn gọn <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="VD: Tìm ví da màu đen rơi tại..." required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại tin</label>
                                <select name="type" class="form-select">
                                    <option value="lost">🛑 Đồ bị mất (Đang tìm)</option>
                                    <option value="found">🍀 Nhặt được đồ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời gian</label>
                                <input type="date" name="lost_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa điểm (Mất / Nhặt) <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" placeholder="VD: Số 10 đường ABC..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình ảnh mô tả</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text text-muted">Nên chọn ảnh rõ nét để mọi người dễ nhận diện.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Mô tả đặc điểm, màu sắc, giấy tờ bên trong (nếu có)..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="btn_submit" class="btn btn-primary btn-lg fw-bold">
                                ĐĂNG TIN NGAY
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>