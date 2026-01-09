<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='login.php';</script>";
    exit();
}

// 2. KIỂM TRA ID HỢP LỆ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$id = $_GET['id'];
$user_id_dang_nhap = $_SESSION['user_id'];

// 3. LẤY THÔNG TIN & KIỂM TRA QUYỀN CHÍNH CHỦ
$sql = "SELECT * FROM posts WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] == 1;

if (!$row || ($row['user_id'] != $user_id_dang_nhap && !$is_admin)) {
    echo "<script>alert('Bạn không có quyền sửa bài này!'); window.location.href='index.php';</script>";
    exit();
}

// Lấy danh mục
$sql_cat = "SELECT * FROM categories";
$result_cat = mysqli_query($conn, $sql_cat);
?>

<!-- STYLE RIÊNG ĐỂ ĐỒNG BỘ MÀU VÀNG -->
<style>
    /* Màu nền vàng chủ đạo */
    .bg-custom-yellow {
        background-color: #f1c40f !important;
        color: white;
    }
    /* Nút lưu màu vàng */
    .btn-custom-yellow {
        background-color: #f1c40f;
        color: white;
        border: none;
        transition: 0.3s;
    }
    .btn-custom-yellow:hover {
        background-color: #d4ac0d; /* Vàng đậm hơn khi di chuột */
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(241, 196, 15, 0.4);
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                
                <!-- 1. ĐÃ ĐỔI THANH TIÊU ĐỀ THÀNH MÀU VÀNG -->
                <div class="card-header bg-custom-yellow text-center py-3 rounded-top-4">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> CHỈNH SỬA TIN</h4>
                </div>

                <div class="card-body p-4">
                    <form action="xuly_suatin.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái hiện tại:</label>
                            <select name="status" class="form-select">
                                <option value="1" <?php if($row['status'] == 1) echo 'selected'; ?>>🔥 Đang tìm / Chưa trả lại</option>
                                <option value="0" <?php if($row['status'] == 0) echo 'selected'; ?>>✅ ĐÃ XONG (Đã tìm thấy / Đã trả)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề:</label>
                            <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Địa điểm:</label>
                                <input type="text" name="address" class="form-control" value="<?php echo $row['address']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại tin:</label>
                                <select name="type" class="form-select">
                                    <option value="lost" <?php if($row['type'] == 'lost') echo 'selected'; ?>>Đồ bị mất</option>
                                    <option value="found" <?php if($row['type'] == 'found') echo 'selected'; ?>>Đồ nhặt được</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Danh mục:</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php
                                if (mysqli_num_rows($result_cat) > 0) {
                                    while ($cat = mysqli_fetch_assoc($result_cat)) {
                                        // Sử dụng tên để so sánh và lưu
                                        $is_selected = ($cat['name'] == $row['category']) ? 'selected' : ''; 
                                        echo "<option value='" . $cat['name'] . "' $is_selected>" . $cat['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả:</label>
                            <textarea name="description" class="form-control" rows="5"><?php echo $row['description']; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Thay đổi hình ảnh (Không bắt buộc):</label>
                            <input type="file" name="images[]" class="form-control" multiple>
                            
                            <?php if(!empty($row['image'])): ?>
                                <div class="mt-2 p-2 border rounded bg-light d-inline-block">
                                    <small class="d-block text-muted mb-1">Ảnh hiện tại:</small>
                                    <img src="uploads/<?php echo $row['image']; ?>" style="height: 100px; border-radius: 5px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php" class="btn btn-secondary rounded-pill px-4">Hủy bỏ</a>
                            
                            <!-- 2. ĐÃ ĐỔI NÚT LƯU THÀNH MÀU VÀNG -->
                            <button type="submit" name="btn_save" class="btn btn-custom-yellow rounded-pill px-4 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>