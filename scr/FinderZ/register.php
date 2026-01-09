<?php
session_start();
// 1. Kết nối Database (Đường dẫn chuẩn)
require_once 'config/db.php';

// Chỉ chạy code xử lý khi người dùng bấm nút "btn_register"
if (isset($_POST['btn_register'])) {
    // Lấy dữ liệu từ form và làm sạch (tránh hack SQL)
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']); // Tên đúng là 'phone'
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra mật khẩu nhập lại
    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra xem email đã tồn tại chưa
        $check_sql = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email này đã được sử dụng!";
        } else {
            // Mã hóa mật khẩu an toàn
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Thêm vào Database (Mặc định avatar và address sẽ null hoặc theo default của DB)
            $sql = "INSERT INTO users (full_name, email, phone, password, avatar) 
                    VALUES ('$full_name', '$email', '$phone', '$hashed_password', 'default_avatar.png')";
            
            if (mysqli_query($conn, $sql)) {
                // Đăng ký thành công -> Chuyển qua trang đăng nhập
                echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='login.php';</script>";
            } else {
                $error = "Lỗi hệ thống: " . mysqli_error($conn);
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- CSS giúp đẩy Footer xuống đáy (Sticky Footer) -->
<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .main-content {
        flex: 1;
        display: flex;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
    }
</style>

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4">
                    <!-- Header Form -->
                    <div class="card-header bg-dark text-white text-center py-3 rounded-top-4">
                        <h4 class="mb-0 fw-bold">ĐĂNG KÝ TÀI KHOẢN</h4>
                    </div>

                    <div class="card-body p-4">
                        
                        <!-- Hiển thị lỗi nếu có -->
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Họ và tên:</label>
                                <input type="text" name="full_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Số điện thoại:</label>
                                <input type="text" name="phone" class="form-control" required placeholder="Nhập SĐT liên hệ">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Mật khẩu:</label>
                                    <input type="password" name="password" class="form-control" required placeholder="Tạo mật khẩu">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nhập lại mật khẩu:</label>
                                    <input type="password" name="confirm_password" class="form-control" required placeholder="Xác nhận lại">
                                </div>
                            </div>

                            <div class="d-grid mt-3">
                                <button type="submit" name="btn_register" class="btn btn-warning fw-bold text-white shadow-sm" style="background-color: #f1c40f; border: none; color: black !important;">
                                    ĐĂNG KÝ NGAY
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small>Đã có tài khoản? <a href="login.php" class="fw-bold text-decoration-none" style="color: #6f582b;">Đăng nhập tại đây</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>