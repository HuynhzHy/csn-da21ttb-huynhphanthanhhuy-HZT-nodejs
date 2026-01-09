<?php
session_start();
require_once 'config/db.php';

// 1. BẢO MẬT: CHẶN NGƯỜI LẠ VÀ KHÔNG PHẢI ADMIN TRUY CẬP
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    echo "<script>alert('Bạn không có quyền truy cập trang này!'); window.location.href='index.php';</script>";
    exit();
}

// 2. XỬ LÝ KHÓA TÀI KHOẢN (BAN USER)
if (isset($_POST['btn_lock_user'])) {
    $uid = intval($_POST['user_id_lock']);
    $duration = $_POST['duration']; 
    
    if ($uid != $_SESSION['user_id']) {
        if ($duration == 'forever') {
            $sql = "UPDATE users SET is_locked = 1, locked_until = NULL WHERE id = $uid";
        } else {
            $days = intval($duration);
            $unlock_date = date('Y-m-d H:i:s', strtotime("+$days days"));
            $sql = "UPDATE users SET is_locked = 1, locked_until = '$unlock_date' WHERE id = $uid";
        }
        mysqli_query($conn, $sql);
        echo "<script>alert('Đã khóa tài khoản thành công!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Không thể tự khóa chính mình!'); window.location.href='admin.php';</script>";
    }
}

// 3. XỬ LÝ MỞ KHÓA (UNBAN)
if (isset($_GET['unlock_user'])) {
    $uid = intval($_GET['unlock_user']);
    mysqli_query($conn, "UPDATE users SET is_locked = 0, locked_until = NULL WHERE id = $uid");
    echo "<script>alert('Đã mở khóa tài khoản!'); window.location.href='admin.php';</script>";
}

// 4. XỬ LÝ XÓA USER
if (isset($_GET['delete_user'])) {
    $uid = intval($_GET['delete_user']);
    if ($uid != $_SESSION['user_id']) {
        $query_posts = mysqli_query($conn, "SELECT image FROM posts WHERE user_id=$uid");
        while ($post = mysqli_fetch_assoc($query_posts)) {
            if (!empty($post['image']) && file_exists("uploads/".$post['image'])) {
                unlink("uploads/".$post['image']); 
            }
        }
        mysqli_query($conn, "DELETE FROM posts WHERE user_id=$uid"); 
        mysqli_query($conn, "DELETE FROM users WHERE id=$uid");
        echo "<script>alert('Đã xóa thành viên và toàn bộ bài viết!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Không thể tự xóa chính mình!'); window.location.href='admin.php';</script>";
    }
}

// 5. XỬ LÝ XÓA BÀI VIẾT
if (isset($_GET['delete_post'])) {
    $pid = intval($_GET['delete_post']);
    $query = mysqli_query($conn, "SELECT image FROM posts WHERE id=$pid");
    $data = mysqli_fetch_assoc($query);
    if (!empty($data['image']) && file_exists("uploads/".$data['image'])) {
        unlink("uploads/".$data['image']);
    }
    mysqli_query($conn, "DELETE FROM posts WHERE id=$pid"); 
    echo "<script>alert('Đã xóa bài viết!'); window.location.href='admin.php';</script>";
}

include 'includes/header.php';
?>

<style>
    .admin-header {
        background: linear-gradient(135deg, #e2cf93ff 0%, #FF9800 100%);
        padding: 30px 0;
        margin-bottom: 30px;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .table-container {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-bottom: 40px; /* Tạo khoảng cách dưới mỗi bảng */
    }
    .avatar-sm {
        width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px;
    }
</style>

<!-- HEADER ADMIN -->
<div class="admin-header text-center">
    <h3 class="fw-bold text-white mb-0"><i class="fa-solid fa-user-shield me-2"></i>QUẢN TRỊ HỆ THỐNG</h3>
    <p class="text-white-50 mt-1 mb-0">Xin chào Admin, <?php echo $_SESSION['full_name']; ?></p>
</div>

<div class="container mb-5">
    
    <!-- (ĐÃ XÓA PHẦN THỐNG KÊ NHANH Ở ĐÂY) -->

    <!-- 1. QUẢN LÝ THÀNH VIÊN -->
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-3 border-start border-4 border-primary ps-3 text-primary">
                <i class="fa-solid fa-users-gear me-2"></i>Danh sách Thành viên
            </h4>
            <div class="table-container table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Thành viên</th>
                            <th>Trạng thái</th> 
                            <th>Vai trò</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                        while ($u = mysqli_fetch_assoc($users)): 
                            $u_avatar = !empty($u['avatar']) ? "uploads/".$u['avatar'] : "uploads/default_avatar.png";
                            
                            // Check khóa
                            $is_locked = $u['is_locked'];
                            $lock_msg = "";
                            if ($is_locked) {
                                if ($u['locked_until'] == NULL) {
                                    $lock_msg = "Vĩnh viễn";
                                } else {
                                    if (strtotime($u['locked_until']) < time()) {
                                        $is_locked = 0; 
                                    } else {
                                        $lock_msg = date('d/m', strtotime($u['locked_until']));
                                    }
                                }
                            }
                        ?>
                        <tr class="<?php echo $is_locked ? 'table-danger' : ''; ?>">
                            <td>#<?php echo $u['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $u_avatar; ?>" class="avatar-sm border">
                                    <div>
                                        <div class="fw-bold"><?php echo $u['full_name']; ?></div>
                                        <small class="text-muted"><?php echo $u['email']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if($is_locked): ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-lock"></i> Khóa (<?php echo $lock_msg; ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-check"></i> Tốt</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($u['role'] == 1): ?>
                                    <span class="badge bg-warning text-dark rounded-pill">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill">Member</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if($u['role'] != 1): ?>
                                    <?php if($is_locked): ?>
                                        <a href="admin.php?unlock_user=<?php echo $u['id']; ?>" class="btn btn-sm btn-success me-1" onclick="return confirm('Mở khóa?')"><i class="fa-solid fa-lock-open"></i></a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-warning me-1" onclick="openLockModal(<?php echo $u['id']; ?>, '<?php echo $u['full_name']; ?>')"><i class="fa-solid fa-lock"></i></button>
                                    <?php endif; ?>

                                    <a href="admin.php?delete_user=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa vĩnh viễn?')"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. QUẢN LÝ BÀI VIẾT -->
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-3 border-start border-4 border-success ps-3 text-success">
                <i class="fa-solid fa-newspaper me-2"></i>Danh sách Bài viết
            </h4>
            <div class="table-container table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th style="width: 40%;">Tiêu đề</th>
                            <th>Người đăng</th>
                            <th>Loại</th>
                            <th>Ngày đăng</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $posts = mysqli_query($conn, "SELECT p.*, u.full_name FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
                        while ($p = mysqli_fetch_assoc($posts)): ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td>
                                <a href="chitiet.php?id=<?php echo $p['id']; ?>" target="_blank" class="text-decoration-none fw-bold text-dark">
                                    <?php echo $p['title']; ?> <i class="fa-solid fa-arrow-up-right-from-square small text-muted"></i>
                                </a>
                            </td>
                            <td><?php echo $p['full_name']; ?></td>
                            <td>
                                <?php if($p['type'] == 'lost'): ?>
                                    <span class="badge bg-danger">Mất</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Nhặt</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                            <td class="text-end">
                                <a href="admin.php?delete_post=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger text-white" onclick="return confirm('Xóa bài này?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- MODAL KHÓA TÀI KHOẢN -->
<div class="modal fade" id="lockUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin.php">
          <div class="modal-header bg-warning">
            <h5 class="modal-title fw-bold"><i class="fa-solid fa-lock"></i> Khóa tài khoản</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id_lock" id="user_id_lock">
            <p>Bạn đang khóa tài khoản: <strong id="user_name_lock" class="text-primary"></strong></p>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Thời gian khóa:</label>
                <select name="duration" class="form-select">
                    <option value="1">1 Ngày</option>
                    <option value="3">3 Ngày</option>
                    <option value="7">1 Tuần</option>
                    <option value="forever" selected>Vĩnh viễn</option>
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" name="btn_lock_user" class="btn btn-danger fw-bold">Xác nhận Khóa</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    function openLockModal(id, name) {
        document.getElementById('user_id_lock').value = id;
        document.getElementById('user_name_lock').innerText = name;
        var myModal = new bootstrap.Modal(document.getElementById('lockUserModal'));
        myModal.show();
    }
</script>

<?php include 'includes/footer.php'; ?>