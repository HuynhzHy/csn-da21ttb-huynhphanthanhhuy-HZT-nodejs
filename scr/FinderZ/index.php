<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// --- CẤU HÌNH PHÂN TRANG ---
$limit = 8; // Số bài viết trên mỗi trang
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- XỬ LÝ ĐIỀU KIỆN TÌM KIẾM ---
$where_sql = "WHERE 1=1";
$params = ""; // Chuỗi lưu tham số để gắn vào link phân trang (ví dụ: &keyword=vi&type=lost)

// 1. Lọc theo Loại tin (Mất / Nhặt)
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = mysqli_real_escape_string($conn, $_GET['type']);
    $where_sql .= " AND p.type = '$type'";
    $params .= "&type=$type";
    $current_type = $type;
} else {
    $current_type = '';
}

// 2. Lọc theo Từ khóa
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $where_sql .= " AND (p.title LIKE '%$keyword%' OR p.description LIKE '%$keyword%')";
    $params .= "&keyword=$keyword";
}

// 3. Lọc theo Danh mục
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $cat_filter = mysqli_real_escape_string($conn, $_GET['category']);
    $where_sql .= " AND p.category = '$cat_filter'";
    $params .= "&category=$cat_filter";
}

// 4. Lọc theo Địa điểm
if (isset($_GET['location']) && !empty($_GET['location'])) {
    $loc_filter = mysqli_real_escape_string($conn, $_GET['location']);
    $where_sql .= " AND p.address LIKE '%$loc_filter%'";
    $params .= "&location=$loc_filter";
}

// --- TRUY VẤN LẤY TỔNG SỐ BÀI (ĐỂ TÍNH SỐ TRANG) ---
$count_sql = "SELECT COUNT(*) as total FROM posts p $where_sql";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// --- TRUY VẤN LẤY DỮ LIỆU BÀI VIẾT (CÓ LIMIT) ---
$sql = "SELECT p.*, u.full_name, u.avatar 
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        $where_sql 
        ORDER BY p.created_at DESC 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
$cat_result = mysqli_query($conn, "SELECT * FROM categories");
?>

<style>
    .hero-section {
        background: linear-gradient(135deg, #57595B 0%, #6d4849ff 100%);
        padding: 80px 0;
        color: white;
        margin-bottom: 50px;
        border-radius: 0 0 50px 50px; /* Bo cong dưới nhìn mềm mại hơn */
    }
    .search-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .post-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
        border-radius: 15px;
        background: #fff;
    }
    .post-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .card-img-top {
        height: 220px;
        object-fit: cover;
        border-radius: 15px 15px 0 0;
    }
    .badge-lost { background-color: #e74c3c; color: white; padding: 5px 12px; }
    .badge-found { background-color: #27ae60; color: white; padding: 5px 12px;}
    
    /* Style cho form input đẹp hơn */
    .input-group-text { background: transparent; border-right: none; }
    .form-control-lg { border-left: none; }
    .form-control-lg:focus { box-shadow: none; border-color: #ced4da; }
    .search-input-group { border: 1px solid #ced4da; border-radius: 10px; overflow: hidden; display: flex; align-items: center; background: white;}

    /* Style Phân trang */
    .pagination .page-link { color: #333; border-radius: 50%; margin: 0 5px; width: 40px; height: 40px; text-align: center; line-height: 25px; border: none; }
    .pagination .page-item.active .page-link { background-color: #f1c40f; color: white; font-weight: bold; }
    .pagination .page-link:hover { background-color: #eee; }
</style>

<!-- KHUNG TÌM KIẾM -->
<div class="hero-section">
    <div class="container">
        <h1 class="text-center fw-bold mb-2" style="font-family: 'Merriweather', serif;">
            <i class="fa-solid fa-magnifying-glass-location me-2"></i>TÌM KIẾM ĐỒ THẤT LẠC
        </h1>
        <p class="text-center mb-5 opacity-75">Kết nối cộng đồng - Tìm lại yêu thương</p>
        
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="search-box">
                    <form action="index.php" method="GET" class="row g-3">
                        <?php if($current_type): ?>
                            <input type="hidden" name="type" value="<?php echo $current_type; ?>">
                        <?php endif; ?>

                        <!-- Ô 1: Từ khóa -->
                        <div class="col-md-4">
                            <div class="search-input-group p-1">
                                <span class="input-group-text text-muted ps-3"><i class="fa-solid fa-keyboard"></i></span>
                                <input type="text" name="keyword" class="form-control form-control-lg border-0" 
                                       placeholder="Tên đồ vật..."
                                       value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Ô 2: Danh mục -->
                        <div class="col-md-3">
                            <div class="search-input-group p-1">
                                <span class="input-group-text text-muted ps-3"><i class="fa-solid fa-layer-group"></i></span>
                                <select name="category" class="form-select form-select-lg border-0" style="cursor: pointer;">
                                    <option value="">Danh mục</option>
                                    <?php 
                                    if(mysqli_num_rows($cat_result) > 0) {
                                        while($c = mysqli_fetch_assoc($cat_result)) {
                                            $selected = (isset($_GET['category']) && $_GET['category'] == $c['name']) ? 'selected' : '';
                                            echo "<option value='".$c['name']."' $selected>".$c['name']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Ô 3: Địa điểm -->
                        <div class="col-md-3">
                            <div class="search-input-group p-1">
                                <span class="input-group-text text-muted ps-3"><i class="fa-solid fa-map-location-dot"></i></span>
                                <input type="text" name="location" class="form-control form-control-lg border-0" 
                                       placeholder="Khu vực..."
                                       value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Nút Tìm kiếm -->
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold text-white shadow-sm" style="border-radius: 10px;">
                                <i class="fa-solid fa-search me-1"></i> TÌM
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KẾT QUẢ -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h3 class="fw-bold text-dark">
            <?php 
                if($current_type == 'lost') echo '<span><i class="fa-solid fa-box-open text-danger me-2"></i>ĐỒ BỊ MẤT</span>';
                elseif($current_type == 'found') echo '<span><i class="fa-solid fa-gift text-success me-2"></i>ĐỒ NHẶT ĐƯỢC</span>';
                else echo '<span><i class="fa-solid fa-bolt text-warning me-2"></i>TIN MỚI NHẤT</span>';
            ?>
        </h3>
        <?php if(isset($_GET['keyword']) || isset($_GET['category'])): ?>
            <a href="index.php" class="btn btn-light btn-sm rounded-pill border">
                <i class="fa-solid fa-rotate-left me-1"></i> Xóa bộ lọc
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 post-card">
                        <div class="position-relative">
                            <?php 
                                $img_url = !empty($row['image']) ? "uploads/".$row['image'] : "https://via.placeholder.com/300x200?text=No+Image";
                            ?>
                            <img src="<?php echo $img_url; ?>" class="card-img-top" alt="...">
                            
                            <div class="position-absolute top-0 end-0 m-2">
                                <?php if($row['status'] == 0): ?>
                                    <span class="badge bg-secondary shadow-sm"><i class="fa-solid fa-check me-1"></i>Đã xong</span>
                                <?php elseif($row['type'] == 'lost'): ?>
                                    <span class="badge badge-lost shadow-sm">Tìm đồ</span>
                                <?php else: ?>
                                    <span class="badge badge-found shadow-sm">Nhặt được</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column pt-3">
                            <small class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-folder-open me-1"></i> <?php echo $row['category']; ?>
                            </small>
                            
                            <h5 class="card-title fw-bold text-truncate mb-2" title="<?php echo $row['title']; ?>">
                                <?php echo $row['title']; ?>
                            </h5>
                            
                            <p class="card-text small text-muted mb-3">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i> 
                                <?php echo substr($row['address'], 0, 30) . '...'; ?>
                            </p>
                            
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <a href="user.php?id=<?php echo $row['user_id']; ?>" class="text-decoration-none text-muted fw-bold">
                                        <i class="fa-solid fa-user me-1"></i> <?php echo $row['full_name']; ?>
                                    </a>
                                </small>
                                <a href="chitiet.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                                    Xem <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Nếu là Admin (role=1) hoặc chủ bài viết thì hiện nút sửa/xóa -->
                        <?php if(isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $row['user_id'] || (isset($_SESSION['role']) && $_SESSION['role'] == 1))): ?>
                            <div class="card-footer bg-white border-0 text-end py-2">
                                <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="text-primary me-3 text-decoration-none"><i class="fa-regular fa-pen-to-square"></i> Sửa</a>
                                <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="text-danger text-decoration-none" onclick="return confirm('Xóa tin này?')"><i class="fa-regular fa-trash-can"></i> Xóa</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-box-open display-1 text-muted opacity-25"></i>
                <h4 class="mt-3 text-muted">Không tìm thấy kết quả nào!</h4>
                <a href="index.php" class="btn btn-warning mt-2 rounded-pill px-4 text-white fw-bold">Xem tất cả</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- THANH PHÂN TRANG -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Nút Trước -->
            <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $params; ?>"><i class="fa-solid fa-chevron-left"></i></a>
            </li>
            
            <!-- Các số trang -->
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $params; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <!-- Nút Sau -->
            <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $params; ?>"><i class="fa-solid fa-chevron-right"></i></a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>