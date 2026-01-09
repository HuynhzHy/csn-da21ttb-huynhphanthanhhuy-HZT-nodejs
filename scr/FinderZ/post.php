<?php
session_start();
require_once 'config/db.php';

// 1. QUAN TRỌNG: KIỂM TRA ĐĂNG NHẬP (Phải đặt trên cùng)
// Nếu chưa đăng nhập thì chuyển hướng ngay lập tức, trước khi hiện bất kỳ HTML nào
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT "ĐĂNG TIN NGAY"
if (isset($_POST['btn_post'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['type']; // lost hoặc found
    $category_id = $_POST['category_id']; // Lưu vào cột category (hoặc category_id tùy DB)
    
    // Nếu trong DB cột category lưu tên (chữ) chứ không phải ID, thì bạn cần query lấy tên ra
    // Ở đây mình giả định lưu ID hoặc Tên tùy bạn chỉnh, code này lưu giá trị value của option
    
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = 1; // Mặc định là đang tìm (1)

    // Xử lý hình ảnh
    $image = "";
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_ext, $allowed)) {
            $new_name = time() . "_" . $file_name;
            $upload_path = "uploads/" . $new_name;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image = $new_name;
            }
        }
    }

    // Insert vào Database
    // Lưu ý: Kiểm tra tên cột 'category' hay 'cat_id' trong DB của bạn để sửa lại cho đúng
    $sql = "INSERT INTO posts (user_id, title, type, category, address, description, image, status, created_at) 
            VALUES ('$user_id', '$title', '$type', '$category_id', '$address', '$description', '$image', '$status', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Đăng tin thành công!'); window.location.href='index.php';</script>";
    } else {
        $error = "Lỗi: " . mysqli_error($conn);
    }
}

// 3. LẤY DANH MỤC ĐỂ HIỂN THỊ RA FORM
$sql_cat = "SELECT * FROM categories";
$result_cat = mysqli_query($conn, $sql_cat);

?>

<!-- BÂY GIỜ MỚI ĐƯỢC INCLUDE HEADER (VÌ ĐÃ XỬ LÝ PHP XONG) -->
<?php include 'includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-warning text-dark text-center py-3 rounded-top-4">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square"></i> ĐĂNG TIN TÌM ĐỒ</h4>
                </div>
                
                <div class="card-body p-4">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề tin:</label>
                            <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Tìm chó lạc, Nhặt được ví...">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại tin:</label>
                                <select name="type" class="form-select">
                                    <option value="lost">Đồ bị mất (Tôi đang tìm)</option>
                                    <option value="found">Đồ nhặt được (Tôi muốn trả)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục:</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php 
                                    if(mysqli_num_rows($result_cat) > 0){
                                        while($cat = mysqli_fetch_assoc($result_cat)){
                                            // Lưu ý: value là cái sẽ được lưu vào DB. 
                                            // Nếu DB cột category lưu tên -> để value="$cat[name]"
                                            // Nếu DB cột category lưu ID -> để value="$cat[id]"
                                            echo "<option value='".$cat['name']."'>".$cat['name']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa điểm (Mất / Nhặt được):</label>
                            <input type="text" name="address" class="form-control" required placeholder="Ví dụ: Công viên Cầu Giấy, Hà Nội">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả chi tiết:</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Mô tả đặc điểm, màu sắc, thời gian..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Hình ảnh (Nếu có):</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="btn_post" class="btn btn-primary fw-bold py-2" style="background-color: #6f582b; border: none;">
                                <i class="bi bi-send-fill"></i> ĐĂNG TIN NGAY
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>