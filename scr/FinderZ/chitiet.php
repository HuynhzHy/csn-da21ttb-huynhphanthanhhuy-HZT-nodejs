<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// 1. Kiểm tra ID trên URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$post_id = intval($_GET['id']);

// 2. Lấy thông tin bài viết KÈM thông tin người đăng (JOIN users)
$sql = "SELECT p.*, u.full_name, u.phone, u.avatar, u.address as user_address, u.email 
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = $post_id";

$result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Bài viết không tồn tại!</div></div>";
    include 'includes/footer.php';
    exit();
}
?>

<div class="container mt-5 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active">Chi tiết tin</li>
        </ol>
    </nav>

    <div class="row">
        <!-- CỘT TRÁI: THÔNG TIN BÀI VIẾT -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <?php if (!empty($post['image'])): ?>
                    <div class="text-center bg-light">
                        <img src="uploads/<?php echo $post['image']; ?>" class="img-fluid" style="max-height: 500px; object-fit: contain;">
                    </div>
                <?php endif; ?>

                <div class="card-body p-4">
                    <div class="mb-3">
                        <?php if ($post['type'] == 'lost'): ?>
                            <span class="badge bg-danger rounded-pill px-3 py-2">ĐỒ BỊ MẤT</span>
                        <?php else: ?>
                            <span class="badge bg-success rounded-pill px-3 py-2">ĐỒ NHẶT ĐƯỢC</span>
                        <?php endif; ?>
                        
                        <?php if ($post['status'] == 0): ?>
                            <span class="badge bg-secondary ms-2 rounded-pill px-3 py-2">Đã xong</span>
                        <?php endif; ?>
                    </div>

                    <h2 class="fw-bold" style="color: #6f582b;"><?php echo $post['title']; ?></h2>
                    <p class="text-muted"><i class="bi bi-clock"></i> Đăng ngày: <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></p>
                    
                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="bi bi-geo-alt text-danger"></i> Khu vực:</strong><br>
                            <?php echo $post['address']; ?>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="bi bi-tag text-primary"></i> Danh mục:</strong><br>
                            <?php echo $post['category']; ?>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-4">Mô tả chi tiết:</h5>
                    <p style="white-space: pre-line;"><?php echo $post['description']; ?></p>

                    <!-- Nút chỉnh sửa (Nếu là chủ bài viết) -->
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                        <div class="mt-4 pt-3 border-top">
                            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Sửa tin</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: THÔNG TIN LIÊN HỆ -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-warning text-dark fw-bold text-center py-3 rounded-top-4">
                    THÔNG TIN LIÊN HỆ
                </div>
                <div class="card-body text-center p-4">
                    <!-- Avatar người đăng -->
                    <img src="uploads/<?php echo !empty($post['avatar']) ? $post['avatar'] : 'default_avatar.png'; ?>" 
                         class="rounded-circle mb-3 shadow-sm border" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                    
                    <h5 class="fw-bold">
                        <a href="user.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark">
                            <?php echo !empty($post['full_name']) ? $post['full_name'] : 'Thành viên'; ?>
                        </a>
                    </h5>
                    
                    <hr>

                    <div class="d-grid gap-2">
                        <?php if (!empty($post['phone'])): ?>
                            <a href="tel:<?php echo $post['phone']; ?>" class="btn btn-success fw-bold py-2">
                                <i class="bi bi-telephone-fill"></i> Gọi: <?php echo $post['phone']; ?>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Chưa có SĐT</button>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($post['user_address'])): ?>
                        <div class="mt-3 text-start bg-light p-3 rounded">
                            <small class="text-muted d-block fw-bold">Địa chỉ liên hệ:</small>
                            <span><?php echo $post['user_address']; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="alert alert-warning mt-3 small">
                <i class="bi bi-shield-exclamation"></i> <strong>Lưu ý an toàn:</strong> Không chuyển tiền trước. Nên hẹn gặp ở nơi đông người.
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>