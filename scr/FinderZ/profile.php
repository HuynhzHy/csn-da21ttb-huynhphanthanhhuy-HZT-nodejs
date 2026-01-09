<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = ""; 

// 2. XỬ LÝ KHI BẤM LƯU
if (isset($_POST['btn_update_profile'])) {
    // Lấy dữ liệu và xử lý ký tự đặc biệt
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']); // Đã đổi thành address

    // Xử lý Upload Avatar
    $avatar_sql = ""; 
    if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
        $file_name = $_FILES['avatar']['name'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_err = $_FILES['avatar']['error'];

        if ($file_err == 0) {
            $new_name = "avatar_" . $user_id . "_" . time() . ".jpg"; 
            if (move_uploaded_file($file_tmp, "uploads/" . $new_name)) {
                $avatar_sql = ", avatar='$new_name'";
                
                // Cập nhật lại session avatar luôn để header hiển thị ngay
                $_SESSION['avatar'] = $new_name;
            } else {
                $msg = "<div class='alert alert-danger'>Lỗi tải ảnh lên server!</div>";
            }
        }
    }

    // Cập nhật vào Database (Đã thay zalo bằng address)
    // Dùng cú pháp SQL update an toàn
    $sql_update = "UPDATE users SET full_name='$full_name', phone='$phone', address='$address' $avatar_sql WHERE id='$user_id'";
    
    if (mysqli_query($conn, $sql_update)) {
        $msg = "<div class='alert alert-success'>✅ Cập nhật hồ sơ thành công!</div>";
        // Cập nhật lại tên hiển thị trên session
        $_SESSION['username'] = $full_name; 
    } else {
        $msg = "<div class='alert alert-danger'>Lỗi Database: " . mysqli_error($conn) . "</div>";
    }
}

// 3. LẤY THÔNG TIN USER HIỆN TẠI
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// HÀM HỖ TRỢ: Lấy dữ liệu an toàn (Chống lỗi Warning Undefined array key)
function get_val($data, $key) {
    return isset($data[$key]) ? $data[$key] : '';
}
?>

<style>
    .profile-header {
        background: linear-gradient(to right, #6f582b, #bca16b);
        height: 140px;
        border-radius: 15px 15px 0 0;
    }
    .avatar-wrapper {
        position: relative;
        margin-top: -70px;
        text-align: center;
    }
    .profile-avatar {
        width: 140px; height: 140px;
        border-radius: 50%;
        border: 5px solid #fff;
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        background: #fff;
    }
    .btn-save-profile {
        background-color: #6f582b; color: white;
        border-radius: 50px; padding: 10px 30px; font-weight: bold; border: none;
        transition: 0.3s;
    }
    .btn-save-profile:hover { background-color: #5a4622; transform: translateY(-2px); color: white; }
    .camera-icon {
        cursor: pointer; background: #e9ecef; padding: 5px 10px; 
        border-radius: 20px; font-size: 14px;
    }
</style>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <?php echo $msg; ?>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="profile-header"></div>

                <div class="card-body px-4 pb-5">
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <!-- ẢNH ĐẠI DIỆN -->
                        <div class="avatar-wrapper mb-4">
                            <?php 
                                $avatar_db = get_val($user, 'avatar');
                                $avatar_path = !empty($avatar_db) ? "uploads/" . $avatar_db : "uploads/default_avatar.png";
                            ?>
                            <img src="<?php echo $avatar_path; ?>" class="profile-avatar" id="previewter">
                            
                            <div class="mt-3">
                                <label class="camera-icon shadow-sm">
                                    <i class="bi bi-camera-fill"></i> Đổi ảnh
                                    <input type="file" name="avatar" style="display: none;" onchange="previewImage(this)">
                                </label>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <!-- Hiển thị username (Dùng hàm get_val để tránh lỗi warning nếu chưa có) -->
                            <h3 class="fw-bold">
                                <?php echo !empty(get_val($user, 'full_name')) ? get_val($user, 'full_name') : get_val($user, 'username'); ?>
                            </h3>
                            <p class="text-muted small">ID thành viên: #<?php echo get_val($user, 'id'); ?></p>
                        </div>

                        <hr class="mb-4">

                        <!-- FORM NHẬP LIỆU -->
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Họ và tên hiển thị:</label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="<?php echo get_val($user, 'full_name'); ?>" 
                                       placeholder="Ví dụ: Nguyễn Văn A">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại:</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?php echo get_val($user, 'phone'); ?>" 
                                       placeholder="Nhập SĐT liên hệ">
                            </div>

                            <!-- ĐÃ ĐỔI TỪ ZALO SANG ĐỊA CHỈ -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Địa chỉ:</label>
                                <input type="text" name="address" class="form-control" 
                                       value="<?php echo get_val($user, 'address'); ?>" 
                                       placeholder="Ví dụ: Phường 5, TP Trà Vinh">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Email:</label>
                                <input type="text" class="form-control bg-light" 
                                       value="<?php echo get_val($user, 'email'); ?>" readonly>
                                <small class="text-muted">Không thể thay đổi email.</small>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" name="btn_update_profile" class="btn-save-profile">
                                <i class="bi bi-check-circle-fill"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('previewter').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'includes/footer.php'; ?>