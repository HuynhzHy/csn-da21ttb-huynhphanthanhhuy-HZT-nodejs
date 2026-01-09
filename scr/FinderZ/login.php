<?php
session_start();
require_once 'config/db.php';

// Xử lý khi bấm nút Đăng nhập
if (isset($_POST['btn_login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Lấy thông tin user dựa trên email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Kiểm tra mật khẩu
        if (password_verify($password, $row['password'])) {
            
            // --- KIỂM TRA TRẠNG THÁI KHÓA (BAN) ---
            $is_banned = false; // Mặc định là không bị khóa

            if ($row['is_locked'] == 1) {
                // Trường hợp 1: Khóa vĩnh viễn (locked_until là NULL)
                if ($row['locked_until'] == NULL) {
                    $is_banned = true;
                    $error = "❌ Tài khoản của bạn đã bị KHÓA VĨNH VIỄN do vi phạm quy định!";
                } 
                // Trường hợp 2: Khóa có thời hạn
                else {
                    $unlock_time = strtotime($row['locked_until']);
                    $current_time = time();

                    if ($unlock_time > $current_time) {
                        // Vẫn đang trong thời gian khóa
                        $is_banned = true;
                        $formatted_time = date('H:i d/m/Y', $unlock_time);
                        $error = "⚠️ Tài khoản đang bị tạm khóa.<br>Thời gian mở lại: <b>$formatted_time</b>";
                    } else {
                        // Đã hết thời gian khóa -> Tự động mở khóa
                        // Cập nhật lại Database để lần sau không phải check nữa (Optional)
                        mysqli_query($conn, "UPDATE users SET is_locked = 0, locked_until = NULL WHERE id = " . $row['id']);
                        $is_banned = false;
                    }
                }
            }
            // ---------------------------------------

            // NẾU KHÔNG BỊ KHÓA THÌ TIẾN HÀNH ĐĂNG NHẬP
            if (!$is_banned) {
                // Lưu session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['avatar'] = $row['avatar'];
                $_SESSION['role'] = $row['role']; 
                
                // Chuyển hướng
                if ($row['role'] == 1) {
                    header("Location: admin.php"); // Admin vào trang quản trị
                } else {
                    header("Location: index.php"); // User thường vào trang chủ
                }
                exit();
            }

        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Email này chưa đăng ký!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- CSS Sticky Footer (Đẩy footer xuống đáy nếu nội dung ngắn) -->
<style>
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1; display: flex; align-items: center; }
</style>

<div class="main-content">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center w-100">
            <div class="col-md-5">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h3 class="text-center fw-bold mb-4" style="color: #6f582b;">ĐĂNG NHẬP</h3>
                        
                        <!-- Hiển thị thông báo lỗi (nếu có) -->
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger text-center shadow-sm">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <input type="email" name="email" class="form-control" required placeholder="Nhập email...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mật khẩu:</label>
                                <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu...">
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="btn_login" class="btn btn-warning fw-bold text-dark">
                                    ĐĂNG NHẬP
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small>Chưa có tài khoản? <a href="register.php" class="fw-bold text-decoration-none" style="color: #6f582b;">Đăng ký ngay</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>