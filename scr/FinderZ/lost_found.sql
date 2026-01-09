-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 09, 2026 lúc 05:31 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `lost_found`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Giấy tờ tùy thân'),
(2, 'Thiết bị điện tử'),
(3, 'Ví tiền'),
(5, 'Chìa khóa'),
(6, 'Khác'),
(7, 'Đồng hồ'),
(8, 'Túi xách'),
(9, 'Trang sức'),
(10, 'Thiết bị điện tử'),
(11, 'Giấy tờ tùy thân');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL COMMENT 'lost hoặc found',
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `lost_date` date NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `type`, `title`, `category`, `address`, `lost_date`, `image`, `description`, `status`, `created_at`) VALUES
(11, 10, 'lost', 'Chùm chìa khóa', 'Chìa khóa', 'Số 19C Hoàng Diệu, Phường Điện Biên, Quận Ba Đình, Hà Nội', '0000-00-00', '1766163796_tải xuống (8).jpg', '', 1, '2025-12-19 17:03:16'),
(12, 12, 'lost', 'IPhone 13', 'Thiết bị điện tử', 'Đường C11, Trà Vinh', '0000-00-00', '1766163985_tải xuống (4).jpg', '', 1, '2025-12-19 17:04:35'),
(13, 13, 'lost', 'Đồng hồ', 'Đồng hồ', 'Thiểm Tây, Trung Quốc', '0000-00-00', '1766164133_dong-ho-bo-tui-918x1024.jpg', '', 1, '2025-12-19 17:08:53'),
(14, 13, 'lost', 'Ngọc tỉ', 'Giấy tờ tùy thân', 'Thiểm Tây, Trung Quốc', '0000-00-00', '1766164201_tải xuống (1).jpg', '', 1, '2025-12-19 17:10:01'),
(15, 11, 'found', 'Chìa khóa vàng', 'Chìa khóa', 'Ba Đình, Hà Nội', '0000-00-00', '1766164309_chiakhoavang9999.jpg', '', 1, '2025-12-19 17:11:49'),
(16, 14, 'lost', 'Laptop', 'Thiết bị điện tử', 'Nam Kinh, Giang Tô, Trung Quốc', '0000-00-00', '1766164403_images (7).jpg', '', 1, '2025-12-19 17:13:23'),
(17, 15, 'found', 'Túi Luôn Vui Tươi', 'Túi xách', 'Palais des Tuileries, Paris, Pháp', '0000-00-00', '1766164637_images (8).jpg', '', 1, '2025-12-19 17:16:17'),
(18, 16, 'lost', 'Nỏ Thần', 'Trang sức', 'Đông Anh, Hà Nội', '0000-00-00', '1766191565_images (4).jpg', '', 1, '2025-12-20 00:45:11'),
(19, 16, 'found', 'Vòng vàng', 'Trang sức', 'Đông Anh, Hà Nội', '0000-00-00', '1766191640_20230810_221029-01.jpg', '', 1, '2025-12-20 00:47:20'),
(20, 17, 'lost', 'Dao mổ heo', 'Khác', 'Nam Kinh, Giang Tô, Trung Quốc', '0000-00-00', '1766192204_images (6).jpg', '', 1, '2025-12-20 00:52:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `post_images`
--

CREATE TABLE `post_images` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT 'default_avatar.png',
  `role` int(11) DEFAULT 0,
  `is_locked` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone_number`, `password`, `phone`, `address`, `created_at`, `avatar`, `role`, `is_locked`, `locked_until`) VALUES
(10, 'Huỳnh Huy', 'huy@gmail.com', NULL, '$2y$10$ox42O2k7hYuUx10Ih1GAN.mbwlxCmW.utoR6OqMfi8Wa6XcbvRv9e', '0942451122', NULL, '2025-12-19 14:00:07', 'default_avatar.png', 1, 0, NULL),
(11, 'King Trần', 'King113@gmail.com', NULL, '$2y$10$m5k72.MRR4hB6OCqbxTQGe3jylv8712VjSBYeunWfUx1bdWwVjNhy', '0812131241', 'Đông Anh, Hà Nội, Việt Nam', '2025-12-19 14:01:56', 'avatar_11_1766152956.jpg', 0, 0, NULL),
(12, 'Chu lão Bát', 'Chuong8@gmail.com', NULL, '$2y$10$VKe1Om/DF.zIQIRfhZW32OoRoOFBKxGSrE1y2voZkCs0Y7151jPuu', '0161106223', '', '2025-12-19 17:04:07', 'avatar_12_1766163999.jpg', 0, 0, NULL),
(13, 'Chính Ca', 'trieuchinh@gmail.com', NULL, '$2y$10$eGCcOa6GeVSRSCzZ0DY4M.go.nSc/wH1ybnWjIHfOcj7fN1CYoeHu', '0315121231', '', '2025-12-19 17:07:06', 'avatar_13_1766164175.jpg', 0, 0, NULL),
(14, 'Mạnh Đức', 'taothao@gmail.com', NULL, '$2y$10$45BM8Isy7eTuEujE827io.IxoB8Paf1owl728FXpo6dGpsieDMZLq', '0315123131', '', '2025-12-19 17:12:47', 'avatar_14_1766164471.jpg', 0, 0, NULL),
(15, 'Mã Lệ Cư Lý', 'mariecurie@gmail.com', NULL, '$2y$10$wPLV3fngJD4yb/3F9kMFFeya4M3298epP6equSejtKg93/O.oP2Uq', '0161106223', '', '2025-12-19 17:15:27', 'avatar_15_1766164650.jpg', 0, 0, NULL),
(16, 'ThucPhan', 'PhaN@gmail.com', NULL, '$2y$10$hFEikj4Olj0lpCxZMWg0I.v4S2XQwb8Db/p7ybQZg0OqCJQAldnPi', '0942612122', '', '2025-12-20 00:43:42', 'avatar_16_1766191466.jpg', 0, 0, NULL),
(17, 'Dực Đức', 'truongphi@gmail.com', NULL, '$2y$10$mLABkdq2Vazf4/cxIgA1WOr7CBcI00Z/1ZsIM5LjhmqiEoOtuhAjW', '0315113331', '', '2025-12-20 00:48:22', 'avatar_17_1766192221.jpg', 0, 0, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `post_images`
--
ALTER TABLE `post_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `post_images`
--
ALTER TABLE `post_images`
  ADD CONSTRAINT `post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
