<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['btn_change_pass'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $user_id = $_SESSION['user_id'];

    // Lấy mật khẩu hiện tại trong DB
    $sql = "SELECT password FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if (password_verify($old_pass, $row['password'])) {
        if ($new_pass === $confirm_pass) {
            // Mã hóa mật khẩu mới
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            
            if (mysqli_query($conn, $update_sql)) {
                echo "<script>alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'); window.location.href='logout.php';</script>";
            }
        } else {
            $error = "Mật khẩu xác nhận không khớp!";
        }
    } else {
        $error = "Mật khẩu cũ không đúng!";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-key me-2"></i> ĐỔI MẬT KHẨU</h5>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu cũ:</label>
                            <input type="password" name="old_pass" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu mới:</label>
                            <input type="password" name="new_pass" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nhập lại mật khẩu mới:</label>
                            <input type="password" name="confirm_pass" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="btn_change_pass" class="btn btn-primary fw-bold">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>