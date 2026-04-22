-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 06:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_nhap`
--

CREATE TABLE `chi_tiet_phieu_nhap` (
  `ma_phieu_nhap` int(11) NOT NULL,
  `ma_nguyen_lieu` int(11) NOT NULL,
  `so_luong` decimal(10,2) NOT NULL,
  `don_gia` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chi_tiet_phieu_nhap`
--

INSERT INTO `chi_tiet_phieu_nhap` (`ma_phieu_nhap`, `ma_nguyen_lieu`, `so_luong`, `don_gia`) VALUES
(1, 20, 1.00, 150000.00);

-- --------------------------------------------------------

--
-- Table structure for table `cong_thuc_mon_an`
--

CREATE TABLE `cong_thuc_mon_an` (
  `ma_mon_an` varchar(10) NOT NULL,
  `ma_nguyen_lieu` int(11) NOT NULL,
  `luong_tieu_hao` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cong_thuc_mon_an`
--

INSERT INTO `cong_thuc_mon_an` (`ma_mon_an`, `ma_nguyen_lieu`, `luong_tieu_hao`) VALUES
('M1', 1, 0.05),
('M1', 4, 0.20),
('M1', 13, 0.01),
('M10', 5, 0.05),
('M10', 13, 0.01),
('M11', 6, 0.15),
('M11', 13, 0.02),
('M12', 14, 0.02),
('M14', 6, 0.05),
('M14', 17, 0.05),
('M15', 6, 0.10),
('M16', 7, 0.15),
('M17', 2, 0.10),
('M17', 13, 0.02),
('M18', 1, 0.15),
('M18', 14, 0.01),
('M19', 13, 0.03),
('M2', 1, 0.05),
('M2', 13, 0.01),
('M20', 3, 0.20),
('M20', 5, 0.05),
('M20', 13, 0.01),
('M21', 6, 0.20),
('M21', 13, 0.02),
('M22', 5, 0.05),
('M22', 17, 0.05),
('M23', 1, 0.10),
('M23', 5, 0.10),
('M23', 13, 0.03),
('M24', 16, 0.05),
('M25', 10, 0.10),
('M26', 19, 1.00),
('M27', 9, 0.02),
('M28', 19, 1.00),
('M29', 16, 0.05),
('M3', 2, 0.15),
('M3', 3, 0.20),
('M3', 13, 0.01),
('M30', 15, 0.05),
('M31', 16, 0.05),
('M32', 8, 0.02),
('M33', 10, 0.30),
('M34', 9, 0.05),
('M34', 12, 0.20),
('M35', 11, 0.10),
('M36', 11, 0.05),
('M38', 18, 0.02),
('M38', 20, 0.01),
('M4', 8, 0.02),
('M4', 9, 0.10),
('M5', 6, 0.10),
('M5', 13, 0.01),
('M5', 17, 0.05),
('M6', 5, 0.03),
('M6', 6, 0.02),
('M6', 13, 0.01),
('M7', 20, 0.01),
('M8', 9, 0.05),
('M9', 5, 0.05),
('M9', 17, 0.01);

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id` int(11) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL,
  `loai_danh_muc` enum('thuc_don','kho_hang') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `loai_danh_muc`) VALUES
(1, 'Món chính', 'thuc_don'),
(2, 'Khai vị', 'thuc_don'),
(3, 'Đồ uống', 'thuc_don'),
(4, 'Tráng miệng', 'thuc_don'),
(5, 'Thực phẩm khô', 'kho_hang'),
(6, 'Thực phẩm tươi', 'kho_hang'),
(7, 'Gia vị', 'kho_hang');

-- --------------------------------------------------------

--
-- Table structure for table `kho_nguyen_lieu`
--

CREATE TABLE `kho_nguyen_lieu` (
  `id` int(11) NOT NULL,
  `ten_nguyen_lieu` varchar(100) NOT NULL,
  `don_vi_tinh` varchar(20) DEFAULT NULL,
  `so_luong_ton` decimal(10,2) DEFAULT 0.00,
  `nguong_bao_dong` decimal(10,2) DEFAULT 0.00,
  `gia_von_nhap` decimal(10,2) DEFAULT 0.00,
  `ma_danh_muc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `kho_nguyen_lieu`
--

INSERT INTO `kho_nguyen_lieu` (`id`, `ten_nguyen_lieu`, `don_vi_tinh`, `so_luong_ton`, `nguong_bao_dong`, `gia_von_nhap`, `ma_danh_muc`) VALUES
(1, 'Bò thăn loại 1', 'kg', 15.00, 3.00, 280000.00, 6),
(2, 'Sườn heo non', 'kg', 20.00, 5.00, 180000.00, 6),
(3, 'Gạo tấm thơm', 'kg', 50.00, 10.00, 22000.00, 5),
(4, 'Bánh phở tươi', 'kg', 25.00, 5.00, 15000.00, 6),
(5, 'Tôm sú tươi', 'kg', 10.00, 2.00, 320000.00, 6),
(6, 'Ức gà phi lê', 'kg', 12.00, 3.00, 95000.00, 6),
(7, 'Sụn gà ta', 'kg', 8.00, 2.00, 140000.00, 6),
(8, 'Cà phê hạt Robusta', 'kg', 10.00, 2.00, 220000.00, 5),
(9, 'Sữa đặc (Lon 380g)', 'lon', 48.00, 12.00, 18500.00, 5),
(10, 'Cam sành loại 1', 'kg', 30.00, 10.00, 35000.00, 6),
(11, 'Chanh leo', 'kg', 15.00, 5.00, 40000.00, 6),
(12, 'Bơ sáp Đắk Lắk', 'kg', 10.00, 3.00, 65000.00, 6),
(13, 'Nước mắm truyền thống', 'lít', 20.00, 5.00, 60000.00, 7),
(14, 'Bơ lạt động vật', 'kg', 5.00, 1.00, 240000.00, 6),
(15, 'Trân châu đen', 'gói', 20.00, 5.00, 35000.00, 5),
(16, 'Nước cốt dừa (Hộp)', 'hộp', 24.00, 6.00, 25000.00, 5),
(17, 'Bột mì đa dụng', 'kg', 20.00, 5.00, 22000.00, 5),
(18, 'Hạt sen khô', 'kg', 5.00, 1.00, 180000.00, 5),
(19, 'Dừa xiêm tươi', 'cái', 40.00, 10.00, 15000.00, 6),
(20, 'Trà lá Bảo Lộc', 'kg', 6.00, 1.00, 150000.00, 5);

-- --------------------------------------------------------

--
-- Table structure for table `mon_an`
--

CREATE TABLE `mon_an` (
  `ma_mon_an` varchar(10) NOT NULL,
  `ten_mon` varchar(100) NOT NULL,
  `gia_ban` decimal(10,2) NOT NULL,
  `ma_danh_muc` int(11) DEFAULT NULL,
  `duong_dan_anh` varchar(255) DEFAULT NULL,
  `mo_ta_ngan` text DEFAULT NULL,
  `trang_thai` enum('dang_ban','tam_dung') DEFAULT 'dang_ban'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mon_an`
--

INSERT INTO `mon_an` (`ma_mon_an`, `ten_mon`, `gia_ban`, `ma_danh_muc`, `duong_dan_anh`, `mo_ta_ngan`, `trang_thai`) VALUES
('M1', 'Phở Bò Đặc Biệt', 65000.00, 1, 'uploads/69e897e4c4626.webp', 'Phở bò tái chín với nước hầm 12 tiếng, thơm ngon đậm đà.', 'dang_ban'),
('M10', 'Nộm Ngó Sen Tôm Thịt', 60000.00, 1, 'uploads/69e89d943bb31.webp', 'Vị chua ngọt hài hòa, giòn sần sật của ngó sen.', 'dang_ban'),
('M11', 'Cánh Gà Chiên Nước Mắm', 70000.00, 2, 'uploads/69e89ddcafaa0.webp', 'Cánh gà chiên giòn thấm đẫm sốt nước mắm tỏi ớt.', 'dang_ban'),
('M12', 'Khoai Tây Chiên Bơ Tỏi', 35000.00, 2, 'uploads/69e89e2ccb0e6.webp', 'Khoai tây vàng giòn, thơm nồng hương bơ tỏi.', 'dang_ban'),
('M13', 'Đậu Hũ Lướt Ván', 30000.00, 2, 'uploads/69e89e738e412.webp', 'Đậu hũ mềm mịn, lớp vỏ ngoài dai nhẹ, ăn kèm mắm tôm.', 'dang_ban'),
('M14', 'Bánh Gối', 35000.00, 2, 'uploads/69e89eb0b55f1.webp', 'Nhân thịt băm, mộc nhĩ và trứng cút gói trong lớp vỏ bánh chiên vàng.', 'dang_ban'),
('M15', 'Salad Ức Gà', 50000.00, 2, 'uploads/69e89eee30f32.webp', 'Rau xanh tươi mát kết hợp với ức gà áp chảo và sốt mè rang.', 'dang_ban'),
('M16', 'Sụn Gà Rang Muối', 65000.00, 2, 'uploads/69e89f205b251.webp', 'Sụn gà giòn sần sật, đậm đà vị muối sả.', 'dang_ban'),
('M17', 'Bún Chả Hà Nội', 70000.00, 1, 'uploads/69e89f5ec8096.webp', 'Chả nướng thơm lừng ăn kèm nước chấm chua ngọt.', 'dang_ban'),
('M18', 'Bò Lúc Lắc', 150000.00, 1, 'uploads/69e89f956014a.webp', 'Thịt bò thăn mềm xào với ớt chuông và hành tây.', 'dang_ban'),
('M19', 'Cá Lóc Kho Tộ', 100000.00, 1, 'uploads/69e89fc57f18d.webp', 'Cá lóc kho đậm đà trong tộ đất, ăn kèm với cơm trắng nóng hổi.', 'dang_ban'),
('M2', 'Bún Bò Huế', 58000.00, 1, 'uploads/69e898c35db5c.webp', 'Bún bò cay đặc trưng Huế với chả lụa và huyết', 'dang_ban'),
('M20', 'Cơm Rang Hải Sản', 70000.00, 1, 'uploads/69e8a0072eb72.webp', 'Hạt cơm tơi xốp xào cùng tôm, mực và trứng.', 'dang_ban'),
('M21', 'Gà Kho Sả Ớt', 70000.00, 1, 'uploads/69e8a0395f789.webp', 'Thịt gà dai ngon, thơm mùi sả và vị cay nồng của ớt.', 'dang_ban'),
('M22', 'Mì Xào Giòn Hải Sản', 80000.00, 1, 'uploads/69e8a06d1ed1b.webp', 'Mì chiên giòn tan phủ sốt hải sản và rau củ.', 'dang_ban'),
('M23', 'Lẩu Thái Chua Cay', 300000.00, 1, 'uploads/69e8a0a077dc2.webp', 'Nước lẩu cay nồng, đa dạng đồ nhúng từ hải sản đến thịt bò.', 'dang_ban'),
('M24', 'Chè Thái', 30000.00, 3, 'uploads/69e8a0d826a8e.webp', 'Sự kết hợp mát lạnh của nhiều loại trái cây và nước cốt dừa.', 'dang_ban'),
('M25', 'Trái Cây Đĩa', 45000.00, 4, 'uploads/69e8a1199e17d.webp', 'Các loại trái cây tươi ngon theo mùa.', 'dang_ban'),
('M26', 'Kem Trái Dừa', 55000.00, 4, 'uploads/69e8a1471ef7c.webp', 'Kem dừa mát lạnh đựng trong quả dừa xiêm tươi.', 'dang_ban'),
('M27', 'Sữa Chua Nếp Cẩm', 30000.00, 4, 'uploads/69e8a175a9681.webp', 'Sữa chua dẻo mịn ăn kèm nếp cẩm thơm bùi.', 'dang_ban'),
('M28', 'Rau Câu Dừa', 25000.00, 4, 'uploads/69e8a1cc49962.webp', 'Thạch dừa thanh mát, giải nhiệt hiệu quả.', 'dang_ban'),
('M29', 'Chè Bưởi', 25000.00, 4, 'uploads/69e8a1f7edb34.webp', 'Cùi bưởi giòn sần sật, đỗ xanh thơm bùi và nước cốt dừa béo ngậy.', 'dang_ban'),
('M3', 'Cơm Tấm Sườn Nướng', 75000.00, 1, 'uploads/69e8991f1c0b5.webp', 'Cơm tấm với sườn nướng, bì, chả, trứng ốp la.', 'dang_ban'),
('M30', 'Tàu Hũ Trân Châu', 30000.00, 4, 'uploads/69e8a22db89c5.webp', 'Tàu hũ mềm mượt kết hợp trân châu đường đen dai giòn.', 'dang_ban'),
('M31', 'Chuối Nướng Cốt Dừa', 35000.00, 4, 'uploads/69e8a285ebf50.webp', 'Chuối nướng thơm phức chan nước cốt dừa béo thơm.', 'dang_ban'),
('M32', 'Cà Phê Đen Đá', 30000.00, 3, 'uploads/69e8a2b8564b8.webp', 'Cà phê nguyên chất đậm vị dành cho người sành điệu.', 'dang_ban'),
('M33', 'Nước Cam Ép', 35000.00, 1, 'uploads/69e8a2f96e955.webp', 'Nước cam tươi nguyên chất giàu vitamin C.', 'dang_ban'),
('M34', 'Sinh Tố Bơ', 40000.00, 3, 'uploads/69e8a32d9e0bc.webp', 'Bơ sáp béo ngậy xay mịn cùng sữa tươi.', 'dang_ban'),
('M35', 'Nước Chanh Leo', 30000.00, 3, 'uploads/69e8a35c78993.webp', 'Vị chua thanh mát, giải khát tức thì.', 'dang_ban'),
('M36', 'Soda Chanh Đường', 25000.00, 3, 'uploads/69e8a39762525.webp', 'Thức uống sảng khoái với soda, chanh tươi và đường.', 'dang_ban'),
('M37', 'Nước Ép Thơm', 35000.00, 3, 'uploads/69e8a3de1207d.webp', 'Nước ép dứa thơm ngọt, thanh lọc cơ thể.', 'dang_ban'),
('M38', 'Trà Sen Vàng', 40000.00, 3, 'uploads/69e8a418714e3.webp', 'Trà thanh nhẹ kết hợp hạt sen bùi và thạch rau câu.', 'dang_ban'),
('M4', 'Cà Phê Sữa Đá', 30000.00, 3, 'uploads/69e899b190e27.webp', 'Cà phê phin truyền thống với sữa đặc.', 'dang_ban'),
('M5', 'Chả Giò Rán', 70000.00, 2, 'uploads/69e89a2237c54.webp', 'CHả giò giòn rụm nhân thịt và rau củ.', 'dang_ban'),
('M6', 'Gỏi cuốn Tôm Thịt', 65000.00, 2, 'uploads/69e89a72f34e2.webp', 'Gỏi cuốn tươi với tôm, thịt heo, rau sống và bún.', 'dang_ban'),
('M7', 'Trà Đá Chanh', 40000.00, 3, 'uploads/69e89ac063f8a.webp', 'Trà đá chanh tươi mát lạnh', 'dang_ban'),
('M8', 'Bánh Flan Caramel', 35000.00, 4, 'uploads/69e89b16b8489.webp', 'Bánh flan mịn với caramel thơm.', 'dang_ban'),
('M9', 'Soup Hải sản', 45000.00, 2, 'uploads/69e89d5774b83.webp', 'Súp sánh mịn với tôm, mực và thanh cua tươi ngon.', 'dang_ban');

-- --------------------------------------------------------

--
-- Table structure for table `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `id` int(11) NOT NULL,
  `ten_ncc` varchar(100) NOT NULL,
  `dien_thoai` varchar(15) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`id`, `ten_ncc`, `dien_thoai`, `dia_chi`) VALUES
(1, 'Công ty Thực phẩm Sạch GreenFood', '0901234567', '123 Đường Lê Lợi, Đà Nẵng'),
(2, 'Tổng kho Gia vị Miền Trung', '0912345678', '45 Khu công nghiệp Hòa Cầm, Đà Nẵng'),
(3, 'Nhà máy Nước giải khát & Bia', '0987654321', 'Số 8 Đại lộ Độc Lập, Bình Dương'),
(4, 'Trang trại Rau củ Đà Lạt Fresh', '02633888999', 'Phường 7, TP. Đà Lạt, Lâm Đồng'),
(5, 'Hải sản Tươi sống Biển Đông', '0905999888', 'Cảng cá Thọ Quang, Sơn Trà, Đà Nẵng');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap_kho`
--

CREATE TABLE `phieu_nhap_kho` (
  `id` int(11) NOT NULL,
  `ngay_nhap` datetime DEFAULT current_timestamp(),
  `ma_ncc` int(11) DEFAULT NULL,
  `tong_tien` decimal(15,2) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_nhap_kho`
--

INSERT INTO `phieu_nhap_kho` (`id`, `ngay_nhap`, `ma_ncc`, `tong_tien`, `ghi_chu`) VALUES
(1, '2026-04-22 19:45:39', 1, 150000.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_xuat_dieu_chinh`
--

CREATE TABLE `phieu_xuat_dieu_chinh` (
  `id` int(11) NOT NULL,
  `ma_nguyen_lieu` int(11) NOT NULL,
  `loai_thao_tac` enum('nhap','xuat','dieu_chinh','hao_hut') NOT NULL,
  `so_luong` decimal(10,2) NOT NULL,
  `ly_do` varchar(255) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `ngay_thao_tac` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_xuat_dieu_chinh`
--

INSERT INTO `phieu_xuat_dieu_chinh` (`id`, `ma_nguyen_lieu`, `loai_thao_tac`, `so_luong`, `ly_do`, `ghi_chu`, `ngay_thao_tac`) VALUES
(1, 20, 'nhap', 1.00, 'Nhập kho – phiếu #1', NULL, '2026-04-22 19:45:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD PRIMARY KEY (`ma_phieu_nhap`,`ma_nguyen_lieu`),
  ADD KEY `ma_nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Indexes for table `cong_thuc_mon_an`
--
ALTER TABLE `cong_thuc_mon_an`
  ADD PRIMARY KEY (`ma_mon_an`,`ma_nguyen_lieu`),
  ADD KEY `ma_nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Indexes for table `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kho_nguyen_lieu`
--
ALTER TABLE `kho_nguyen_lieu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_danh_muc` (`ma_danh_muc`);

--
-- Indexes for table `mon_an`
--
ALTER TABLE `mon_an`
  ADD PRIMARY KEY (`ma_mon_an`),
  ADD KEY `ma_danh_muc` (`ma_danh_muc`);

--
-- Indexes for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phieu_nhap_kho`
--
ALTER TABLE `phieu_nhap_kho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_ncc` (`ma_ncc`);

--
-- Indexes for table `phieu_xuat_dieu_chinh`
--
ALTER TABLE `phieu_xuat_dieu_chinh`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kho_nguyen_lieu`
--
ALTER TABLE `kho_nguyen_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `phieu_nhap_kho`
--
ALTER TABLE `phieu_nhap_kho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phieu_xuat_dieu_chinh`
--
ALTER TABLE `phieu_xuat_dieu_chinh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_1` FOREIGN KEY (`ma_phieu_nhap`) REFERENCES `phieu_nhap_kho` (`id`),
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_2` FOREIGN KEY (`ma_nguyen_lieu`) REFERENCES `kho_nguyen_lieu` (`id`);

--
-- Constraints for table `cong_thuc_mon_an`
--
ALTER TABLE `cong_thuc_mon_an`
  ADD CONSTRAINT `cong_thuc_mon_an_ibfk_1` FOREIGN KEY (`ma_mon_an`) REFERENCES `mon_an` (`ma_mon_an`),
  ADD CONSTRAINT `cong_thuc_mon_an_ibfk_2` FOREIGN KEY (`ma_nguyen_lieu`) REFERENCES `kho_nguyen_lieu` (`id`);

--
-- Constraints for table `kho_nguyen_lieu`
--
ALTER TABLE `kho_nguyen_lieu`
  ADD CONSTRAINT `kho_nguyen_lieu_ibfk_1` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc` (`id`);

--
-- Constraints for table `mon_an`
--
ALTER TABLE `mon_an`
  ADD CONSTRAINT `mon_an_ibfk_1` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc` (`id`);

--
-- Constraints for table `phieu_nhap_kho`
--
ALTER TABLE `phieu_nhap_kho`
  ADD CONSTRAINT `phieu_nhap_kho_ibfk_1` FOREIGN KEY (`ma_ncc`) REFERENCES `nha_cung_cap` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
