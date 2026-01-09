<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// 1. Kiểm tra ID người dùng trên URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Không tìm thấy người dùng!'); window.location.href='index.php';</script>";
    exit();
}

$user_id = intval($_GET['id']);

// 2. Lấy thông tin người dùng (ĐÃ XÓA 'username' ĐỂ TRÁNH LỖI)
$sql_user = "SELECT full_name, avatar, email, phone, address, created_at FROM users WHERE id = $user_id";
$result_user = mysqli_query($conn, $sql_user);

if (!$result_user) {
    die("Lỗi truy vấn SQL: " . mysqli_error($conn));
}

$user_info = mysqli_fetch_assoc($result_user);

if (!$user_info) {
    echo "<div class='container mt-5 text-center'><h3>Người dùng không tồn tại!</h3></div>";
    include 'includes/footer.php';
    exit();
}

// 3. Lấy danh sách bài viết
$sql_posts = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$result_posts = mysqli_query($conn, $sql_posts);
?>

<style>
    /* 1. MÀU ẢNH BÌA MỚI (Sang trọng hơn) */
    .user-cover {
        /* Bạn có thể đổi màu ở đây. Ví dụ: */
        /* Màu Xám Đen (Hiện tại): linear-gradient(to right, #232526, #414345); */
        /* Màu Xanh Đậm: linear-gradient(to right, #0f2027, #203a43, #2c5364); */
        /* Màu Tím Mộng Mơ: linear-gradient(to right, #654ea3, #eaafc8); */
        
        background: linear-gradient(to right, #232526, #414345); 
        height: 250px;
        border-radius: 0 0 50px 50px;
        margin-bottom: 100px;
        position: relative;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .user-avatar-large {
        width: 180px; height: 180px;
        border-radius: 50%;
        border: 6px solid #fff;
        object-fit: cover;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: -90px;
        background: white;
    }

    /* 2. KHUNG THÔNG TIN LIÊN HỆ (Đóng khung đẹp) */
    .info-card {
        background: white;
        border: 1px solid #eee;
        border-radius: 15px;
        padding: 15px 25px;
        min-width: 200px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
        display: inline-block;
        margin: 10px;
    }
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #f1c40f; /* Viền vàng khi di chuột vào */
    }
    .info-icon {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }

    /* Card bài viết */
    .post-card {
        transition: transform 0.3s;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .post-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    .card-img-top { height: 200px; object-fit: cover; }
    .badge-lost { background-color: #e74c3c; color: white; padding: 5px 12px; }
    .badge-found { background-color: #27ae60; color: white; padding: 5px 12px;}
</style>

<!-- HEADER PROFILE -->
<div class="user-cover">
    <?php 
        $avatar_url = !empty($user_info['avatar']) ? "uploads/".$user_info['avatar'] : "uploads/default_avatar.png";
    ?>
    <img src="<?php echo $avatar_url; ?>" class="user-avatar-large">
</div>

<div class="container text-center mb-5">
    <h2 class="fw-bold mb-1 display-6">
        <?php echo !empty($user_info['full_name']) ? $user_info['full_name'] : 'Thành viên'; ?>
    </h2>
    
    <p class="text-muted mb-4">
        <i class="fa-regular fa-clock me-1"></i> Tham gia từ: <?php echo date('d/m/Y', strtotime($user_info['created_at'])); ?>
    </p>
    
    <!-- KHUNG THÔNG TIN LIÊN HỆ -->
    <div class="d-flex justify-content-center flex-wrap">
        
        <!-- Ô Số điện thoại -->
        <?php if(!empty($user_info['phone'])): ?>
            <div class="info-card">
                <i class="fa-solid fa-phone info-icon text-success"></i>
                <div class="text-muted small text-uppercase fw-bold">Điện thoại</div>
                <div class="fw-bold fs-5 text-dark mt-1"><?php echo $user_info['phone']; ?></div>
            </div>
        <?php endif; ?>
        
        <!-- Ô Địa chỉ -->
        <?php if(!empty($user_info['address'])): ?>
            <div class="info-card">
                <i class="fa-solid fa-map-location-dot info-icon text-danger"></i>
                <div class="text-muted small text-uppercase fw-bold">Khu vực</div>
                <div class="fw-bold fs-5 text-dark mt-1"><?php echo $user_info['address']; ?></div>
            </div>
        <?php endif; ?>

        <!-- Ô Thống kê tin -->
        <div class="info-card">
            <i class="fa-solid fa-layer-group info-icon text-primary"></i>
            <div class="text-muted small text-uppercase fw-bold">Hoạt động</div>
            <div class="fw-bold fs-5 text-dark mt-1"><?php echo mysqli_num_rows($result_posts); ?> tin đăng</div>
        </div>

    </div>
</div>

<!-- DANH SÁCH BÀI ĐĂNG -->
<div class="container mb-5">
    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
        <h4 class="fw-bold text-uppercase m-0">
            <i class="fa-solid fa-newspaper me-2 text-warning"></i>Danh sách tin đăng
        </h4>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result_posts) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result_posts)): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 post-card">
                        <div class="position-relative">
                            <?php 
                                $img_url = !empty($row['image']) ? "uploads/".$row['image'] : "https://via.placeholder.com/300x200?text=No+Image";
                            ?>
                            <img src="<?php echo $img_url; ?>" class="card-img-top">
                            
                            <div class="position-absolute top-0 end-0 m-2">
                                <?php if($row['status'] == 0): ?>
                                    <span class="badge bg-secondary shadow-sm"><i class="fa-solid fa-check me-1"></i>Đã tìm được</span>
                                <?php elseif($row['type'] == 'lost'): ?>
                                    <span class="badge badge-lost shadow-sm">Tìm đồ</span>
                                <?php else: ?>
                                    <span class="badge badge-found shadow-sm">Nhặt được</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                                <i class="fa-solid fa-folder-open me-1"></i> <?php echo $row['category']; ?>
                            </small>
                            <h6 class="card-title fw-bold mt-2 text-truncate"><?php echo $row['title']; ?></h6>
                            <p class="card-text small text-muted">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i> 
                                <?php echo substr($row['address'], 0, 25) . '...'; ?>
                            </p>
                            <a href="chitiet.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-dark w-100 rounded-pill mt-2 fw-bold">
                                Xem chi tiết <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="fa-regular fa-folder-open display-4 mb-3 opacity-25"></i>
                <p>Thành viên này chưa đăng tin nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>