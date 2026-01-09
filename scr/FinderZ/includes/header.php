<?php
// Kiểm tra session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinderZ - Tìm đồ thất lạc</title>
    
    <!-- Link Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-family: 'Merriweather', serif;
            font-weight: bold;
            font-size: 1.8rem;
            color: #4a3b1e !important;
        }
        .navbar-brand span { color: #e8cd64ff; }
        .navbar {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        .nav-link {
            font-weight: 600;
            color: #555 !important;
            margin: 0 8px;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .nav-link:hover { color: #d4ac0d !important; }
        .nav-link i { margin-right: 5px; color: #aaa; transition: 0.3s; }
        .nav-link:hover i { color: #d4ac0d; }

        .btn-dangtin {
            background-color: #f1c40f;
            color: #fff;
            font-weight: bold;
            border-radius: 50px;
            padding: 10px 25px;
            border: none;
            box-shadow: 0 4px 10px rgba(241, 196, 15, 0.3);
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-dangtin:hover {
            background-color: #d4ac0d;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(241, 196, 15, 0.4);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- LOGO -->
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-compass text-warning me-2"></i>Finder<span>Z</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- MENU CHÍNH -->
            <ul class="navbar-nav mx-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fa-solid fa-house"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?type=lost">
                        <i class="fa-solid fa-magnifying-glass-minus"></i> Đồ bị mất
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?type=found">
                        <i class="fa-solid fa-hand-holding-heart"></i> Đồ nhặt được
                    </a>
                </li>
            </ul>

            <!-- PHẦN USER -->
            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php 
                                if (!empty($_SESSION['full_name'])) {
                                    $display_name = $_SESSION['full_name'];
                                } elseif (!empty($_SESSION['username'])) {
                                    $display_name = $_SESSION['username'];
                                } else {
                                    $display_name = 'Thành viên';
                                }
                            ?>
                            <span class="text-dark">Xin chào, <strong><?php echo $display_name; ?></strong></span>
                        </a>
                        
                        <!-- MENU XỔ XUỐNG -->
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                            
                            <!-- [MỚI] CHỈ HIỆN VỚI ADMIN -->
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold text-warning" href="admin.php">
                                        <i class="fa-solid fa-wrench me-2"></i> Trang Quản Trị
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <!-- HẾT PHẦN ADMIN -->

                            <li><a class="dropdown-item py-2" href="profile.php"><i class="fa-solid fa-id-card me-2 text-muted"></i> Hồ sơ cá nhân</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
                        </ul>
                    </div>

                    <a href="post.php" class="btn-dangtin">
                        <i class="fa-solid fa-circle-plus me-1"></i> ĐĂNG TIN
                    </a>

                <?php else: ?>
                    <a href="login.php" class="text-decoration-none text-dark fw-bold me-3">
                        <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                    </a>
                    <a href="register.php" class="btn btn-outline-dark rounded-pill px-4">
                        <i class="fa-solid fa-user-plus me-1"></i> Đăng ký
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>