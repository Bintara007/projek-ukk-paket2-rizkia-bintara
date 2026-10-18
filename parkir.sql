-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2026 at 03:38 PM
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
-- Database: `parkir`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_area_parkir`
--

CREATE TABLE `tb_area_parkir` (
  `id_area` int(11) NOT NULL,
  `nama_area` varchar(50) NOT NULL,
  `kapasitas` int(11) NOT NULL DEFAULT 0,
  `terisi` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_area_parkir`
--

INSERT INTO `tb_area_parkir` (`id_area`, `nama_area`, `kapasitas`, `terisi`) VALUES
(1, 'Area A', 50, 10),
(2, 'Area B', 40, 15),
(3, 'Area C', 30, 8),
(4, 'Area D', 25, 13),
(7, 'VIP', 20, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_histori`
--

CREATE TABLE `tb_histori` (
  `id_histori` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_parkir` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_keluar` datetime NOT NULL,
  `durasi_jam` int(11) NOT NULL DEFAULT 1,
  `biaya_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tanggal_histori` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_histori`
--

INSERT INTO `tb_histori` (`id_histori`, `id_transaksi`, `id_parkir`, `id_user`, `waktu_masuk`, `waktu_keluar`, `durasi_jam`, `biaya_total`, `tanggal_histori`) VALUES
(3, 2, 2, 2, '2026-09-21 09:53:00', '2026-09-22 00:57:00', 1, 10000.00, '2026-09-21 23:59:59'),
(4, 1, 1, 2, '2026-09-14 08:33:00', '2026-09-14 08:33:00', 1, 10000.00, '2026-09-22 00:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `tb_log_aktivitas`
--

CREATE TABLE `tb_log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `aktivitas` text NOT NULL,
  `waktu_aktivitas` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_log_aktivitas`
--

INSERT INTO `tb_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu_aktivitas`) VALUES
(1, 1, 'Login ke sistem', '2026-09-08 06:50:00'),
(2, 2, 'Mencatat kendaraan masuk', '2026-09-08 07:00:00'),
(3, 3, 'Mencatat kendaraan keluar', '2026-09-08 09:30:00'),
(4, 4, 'Melihat laporan transaksi', '2026-09-08 10:00:00'),
(5, 5, 'Mencatat kendaraan masuk', '2026-09-08 10:15:00'),
(7, 2, 'Login ke sistem', '2026-09-17 08:04:21'),
(8, 2, 'Logout dari sistem', '2026-09-17 08:04:25'),
(9, 1, 'Login ke sistem', '2026-09-17 08:04:33'),
(10, 1, 'Logout dari sistem', '2026-09-17 08:05:08'),
(11, 4, 'Login ke sistem', '2026-09-17 08:05:24'),
(12, 4, 'Logout dari sistem', '2026-09-17 08:05:50'),
(13, 2, 'Login ke sistem', '2026-09-17 08:05:56'),
(14, 2, 'Logout dari sistem', '2026-09-17 08:14:33'),
(15, 1, 'Login ke sistem', '2026-09-21 11:57:15'),
(16, 1, 'Login ke sistem', '2026-09-21 12:49:42'),
(17, 1, 'Logout dari sistem', '2026-09-21 12:54:20'),
(18, 2, 'Login ke sistem', '2026-09-21 12:54:28'),
(19, 2, 'Logout dari sistem', '2026-09-21 12:55:38'),
(20, 4, 'Login ke sistem', '2026-09-21 12:55:48'),
(21, 4, 'Logout dari sistem', '2026-09-21 13:30:18'),
(22, 1, 'Login ke sistem', '2026-09-21 14:03:30'),
(23, 1, 'Logout dari sistem', '2026-09-21 14:23:27'),
(24, 4, 'Login ke sistem', '2026-09-21 14:23:39'),
(25, 4, 'Logout dari sistem', '2026-09-21 14:44:24'),
(26, 1, 'Login ke sistem', '2026-09-21 14:44:36'),
(27, 1, 'Logout dari sistem', '2026-09-21 14:46:35'),
(28, 2, 'Login ke sistem', '2026-09-21 14:47:01'),
(29, 2, 'Logout dari sistem', '2026-09-21 14:47:32'),
(30, 1, 'Login ke sistem', '2026-09-21 14:47:41'),
(31, 1, 'Logout dari sistem', '2026-09-21 14:49:50'),
(32, 2, 'Login ke sistem', '2026-09-21 14:49:55'),
(33, 2, 'Login ke sistem', '2026-09-21 17:16:30'),
(34, 2, 'Login ke sistem', '2026-09-22 05:57:52'),
(35, 2, 'Logout dari sistem', '2026-09-22 05:58:47'),
(36, 1, 'Login ke sistem', '2026-09-22 05:59:03'),
(37, 1, 'Logout dari sistem', '2026-09-22 08:14:24'),
(38, 4, 'Login ke sistem', '2026-09-22 08:14:26'),
(39, 4, 'Logout dari sistem', '2026-09-22 08:14:49'),
(40, 2, 'Login ke sistem', '2026-09-22 08:15:03'),
(41, 2, 'Logout dari sistem', '2026-09-22 08:18:41'),
(42, 1, 'Login ke sistem', '2026-09-22 08:18:49'),
(43, 1, 'Menambahkan user: aseppp', '2026-09-22 08:19:14'),
(44, 1, 'Mengubah data user: aseppp', '2026-09-22 08:19:21'),
(45, 1, 'Menambahkan kendaraan parkir: D 1098 KM', '2026-09-22 08:22:55'),
(46, 1, 'Logout dari sistem', '2026-09-22 08:23:47'),
(47, 2, 'Login ke sistem', '2026-09-22 08:23:54'),
(48, 2, 'Menambahkan member: fandi onichan', '2026-09-22 08:24:20'),
(49, 2, 'Menambahkan kendaraan parkir: D 1999 KM (motor)', '2026-09-22 08:26:42'),
(50, 2, 'Logout dari sistem', '2026-09-22 08:26:59'),
(51, 1, 'Login ke sistem', '2026-09-22 08:27:09'),
(52, 1, 'Menambahkan user: asp dengan role petugas', '2026-09-22 08:27:28'),
(53, 1, 'Logout dari sistem', '2026-09-22 08:27:44'),
(54, 2, 'Login ke sistem', '2026-09-22 08:27:51'),
(55, 2, 'Menambahkan kendaraan parkir: D 1459 KM (motor)', '2026-09-22 08:36:08'),
(56, 2, 'Menambahkan kendaraan parkir: D 1119 KM (mobil)', '2026-09-22 08:47:05'),
(57, 2, 'Menambahkan kendaraan parkir: A 1119 KM (mobil)', '2026-09-22 08:48:37'),
(58, 1, 'Login ke sistem', '2026-09-22 15:03:32'),
(59, 1, 'Menambahkan area parkir: VIP', '2026-09-22 15:03:59'),
(60, 1, 'Logout dari sistem', '2026-09-22 15:05:14'),
(61, 4, 'Login ke sistem', '2026-09-22 15:05:23'),
(62, 4, 'Logout dari sistem', '2026-09-22 15:05:55'),
(63, 2, 'Login ke sistem', '2026-09-22 15:06:04'),
(64, 2, 'Menambahkan kendaraan parkir: E 5468 LM (mobil)', '2026-09-22 15:06:47'),
(65, 2, 'Logout dari sistem', '2026-09-22 15:07:26'),
(66, 1, 'Login ke sistem', '2026-09-22 15:07:36'),
(67, 2, 'Login ke sistem', '2026-09-22 20:42:06'),
(68, 2, 'Logout dari sistem', '2026-09-22 20:43:25'),
(69, 1, 'Login ke sistem', '2026-09-22 20:43:31'),
(70, 1, 'Logout dari sistem', '2026-09-22 20:45:23'),
(71, 2, 'Login ke sistem', '2026-09-22 20:45:29'),
(72, 2, 'Logout dari sistem', '2026-09-22 20:46:47'),
(73, 1, 'Login ke sistem', '2026-09-22 20:46:55'),
(74, 1, 'Logout dari sistem', '2026-09-22 20:47:01'),
(75, 4, 'Login ke sistem', '2026-09-22 20:47:03'),
(76, 4, 'Logout dari sistem', '2026-09-22 20:54:48'),
(77, 1, 'Login ke sistem', '2026-09-22 20:54:54'),
(78, 1, 'Logout dari sistem', '2026-09-22 20:58:38'),
(79, 4, 'Login ke sistem', '2026-09-22 20:58:40'),
(80, 4, 'Logout dari sistem', '2026-09-22 21:05:14'),
(81, 1, 'Login ke sistem', '2026-09-22 21:05:21'),
(82, 1, 'Logout dari sistem', '2026-09-22 21:10:06'),
(83, 2, 'Login ke sistem', '2026-09-22 21:10:11'),
(84, 2, 'Logout dari sistem', '2026-09-22 21:10:55'),
(85, 4, 'Login ke sistem', '2026-09-22 21:10:57'),
(86, 4, 'Logout dari sistem', '2026-09-22 21:11:41'),
(87, 4, 'Login ke sistem', '2026-09-22 21:12:31'),
(88, 4, 'Logout dari sistem', '2026-09-22 21:20:29'),
(89, 4, 'Login ke sistem', '2026-09-23 07:43:48'),
(90, 4, 'Logout dari sistem', '2026-09-23 07:43:53'),
(91, 2, 'Login ke sistem', '2026-09-23 07:46:12'),
(92, 2, 'Logout dari sistem', '2026-09-23 07:47:36'),
(93, 1, 'Login ke sistem', '2026-09-23 07:47:44'),
(94, 1, 'Logout dari sistem', '2026-09-23 07:59:03'),
(95, 3, 'Login ke sistem', '2026-09-23 07:59:10'),
(96, 3, 'Menambahkan kendaraan masuk parkir D 5538 OL - Area VIP', '2026-09-23 08:26:30'),
(97, 3, 'Logout dari sistem', '2026-09-23 08:27:42'),
(98, 4, 'Login ke sistem', '2026-09-23 08:35:19'),
(99, 4, 'Logout dari sistem', '2026-09-23 08:35:32'),
(100, 3, 'Login ke sistem', '2026-09-23 08:35:39'),
(101, 4, 'Login ke sistem', '2026-09-23 13:55:40'),
(102, 4, 'Logout dari sistem', '2026-09-23 13:56:00'),
(103, 1, 'Login ke sistem', '2026-09-23 14:06:59'),
(104, 1, 'Mengubah data user: p3', '2026-09-23 14:07:18'),
(105, 1, 'Login ke sistem', '2026-09-23 14:09:53'),
(106, 4, 'Login ke sistem', '2026-09-23 14:18:39'),
(107, 4, 'Logout dari sistem', '2026-09-23 14:18:42'),
(108, 1, 'Login ke sistem', '2026-09-23 14:18:49'),
(109, 1, 'Logout dari sistem', '2026-09-23 14:41:28'),
(110, 2, 'Login ke sistem', '2026-09-23 14:48:45'),
(111, 2, 'Menambahkan kendaraan masuk parkir D 5789 HK - Area Area D', '2026-09-23 14:49:11'),
(112, 1, 'Login ke sistem', '2026-09-24 12:55:35'),
(113, 1, 'Mengubah data user: p1', '2026-09-24 12:58:56'),
(114, 1, 'Logout dari sistem', '2026-09-24 12:59:25'),
(115, 2, 'Login ke sistem', '2026-09-24 12:59:50'),
(116, 2, 'Logout dari sistem', '2026-09-24 13:00:32'),
(117, 1, 'Login ke sistem', '2026-09-24 13:00:40'),
(118, 1, 'Mengubah data user: p3', '2026-09-24 13:01:58'),
(119, 1, 'Login ke sistem', '2026-09-24 13:03:14'),
(120, 2, 'Login ke sistem', '2026-09-28 09:01:20'),
(121, 1, 'Login ke sistem', '2026-09-28 09:02:01'),
(122, 1, 'Mengubah area parkir: VIP', '2026-09-28 09:04:14'),
(123, 4, 'Login ke sistem', '2026-09-29 06:32:40'),
(124, 4, 'Login ke sistem', '2026-09-29 06:33:07'),
(125, 1, 'Login ke sistem', '2026-09-30 10:28:50'),
(126, 2, 'Login ke sistem', '2026-09-30 10:32:25'),
(127, 2, 'Login ke sistem', '2026-09-30 13:09:46'),
(128, 2, 'Menambahkan kendaraan masuk parkir D 8790 KM - Area VIP', '2026-09-30 13:10:32'),
(129, 4, 'Login ke sistem', '2026-09-30 13:11:56'),
(130, 1, 'Login ke sistem', '2026-09-30 13:12:47'),
(131, 1, 'Menambahkan user: egi dengan role petugas', '2026-09-30 13:13:21'),
(132, 1, 'Mengubah data user: bintang', '2026-09-30 13:13:36'),
(133, 1, 'Mengubah data user: bintang', '2026-09-30 13:14:16'),
(134, 1, 'Mengubah data user: bintang', '2026-09-30 13:14:40'),
(135, 1, 'Menambahkan member: Fadhil', '2026-09-30 13:15:09'),
(136, 1, 'Mengubah member: Fadhil', '2026-09-30 13:15:52'),
(137, 1, 'Mengubah member: Andi Saputra', '2026-09-30 13:16:38'),
(138, 1, 'Menambahkan area parkir: Area Z', '2026-09-30 13:17:22'),
(139, 1, 'Mengubah area parkir: Area Z', '2026-09-30 13:17:36'),
(140, 1, 'Mengubah tarif kendaraan: bus - Rp 75.000', '2026-09-30 13:19:15'),
(141, 4, 'Login ke sistem', '2026-09-30 13:19:48'),
(142, 1, 'Login ke sistem', '2026-10-06 19:04:13'),
(143, 4, 'Login ke sistem', '2026-10-06 19:06:49'),
(144, 1, 'Login ke sistem', '2026-10-06 19:12:02'),
(145, 1, 'Menambahkan user: gus dengan role petugas', '2026-10-06 19:18:08'),
(146, 4, 'Login ke sistem', '2026-10-07 06:32:09'),
(147, 1, 'Login ke sistem', '2026-10-18 20:15:02'),
(148, 4, 'Login ke sistem', '2026-10-18 20:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `tb_member`
--

CREATE TABLE `tb_member` (
  `id_member` int(11) NOT NULL,
  `nama_member` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tanggal_daftar` date NOT NULL,
  `status_aktif` enum('aktif','suspended') NOT NULL DEFAULT 'aktif',
  `id_user` int(11) DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_member`
--

INSERT INTO `tb_member` (`id_member`, `nama_member`, `jenis_kelamin`, `no_hp`, `tanggal_daftar`, `status_aktif`, `id_user`, `id_area`) VALUES
(1, 'Andi Saputra', 'L', '081234567801', '2026-09-01', 'suspended', 2, 1),
(2, 'Budi Santoso', 'L', '081234567802', '2026-09-01', 'aktif', 2, 2),
(3, 'Citra Lestari', 'P', '081234567803', '2026-09-02', 'aktif', 3, 3),
(4, 'Dewi Anggraini', 'P', '081234567804', '2026-09-03', 'aktif', 3, 4),
(5, 'Eko Pratama', 'L', '081234567805', '2026-09-04', 'aktif', 5, 2),
(8, 'fandi onichan', 'P', '081234567805', '2026-09-22', 'aktif', 2, 3),
(9, 'Fadhil', 'L', '', '2026-09-30', 'suspended', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `tb_parkir`
--

CREATE TABLE `tb_parkir` (
  `id_parkir` int(11) NOT NULL,
  `id_member` int(11) DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL,
  `plat_nomor` varchar(15) NOT NULL,
  `warna_kendaraan` varchar(30) DEFAULT NULL,
  `jenis_kendaraan` enum('motor','mobil') NOT NULL,
  `id_tarif` int(11) NOT NULL,
  `waktu_masuk` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user_masuk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_parkir`
--

INSERT INTO `tb_parkir` (`id_parkir`, `id_member`, `id_area`, `plat_nomor`, `warna_kendaraan`, `jenis_kendaraan`, `id_tarif`, `waktu_masuk`, `id_user_masuk`) VALUES
(1, 1, 1, 'D 1298 KM', 'Putih', 'motor', 1, '2026-09-14 08:33:00', 3),
(2, 1, 1, 'D 1233 dg', 'Putih', 'motor', 2, '2026-09-21 09:53:00', 1),
(5, 3, 3, 'D 1297 hu', 'Putih', '', 4, '2026-09-22 01:33:00', 2),
(6, 4, 4, 'D 1098 KM', 'Pink', '', 4, '2026-09-22 03:22:55', 1),
(7, 8, 3, 'D 1999 KM', 'Pink', 'motor', 1, '2026-09-22 03:26:42', 2),
(8, 1, 1, 'D 1459 KM', 'Merah', 'motor', 1, '2026-09-22 08:36:08', 2),
(9, 1, 1, 'D 1119 KM', 'Merah', 'mobil', 2, '2026-09-22 08:47:05', 2),
(10, 3, 3, 'A 1119 KM', 'Hitam', 'mobil', 2, '2026-09-22 08:48:37', 2),
(11, 8, 3, 'E 5468 LM', 'Hitam', 'mobil', 2, '2026-09-22 15:06:47', 2),
(12, 4, 7, 'D 5538 OL', 'Biru', 'mobil', 2, '2026-09-23 08:26:30', 3),
(13, 4, 4, 'D 5789 HK', 'Putih', 'mobil', 2, '2026-09-23 14:49:11', 2),
(14, 5, 7, 'D 8790 KM', 'Hitam', 'motor', 1, '2026-09-30 13:10:32', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_tarif`
--

CREATE TABLE `tb_tarif` (
  `id_tarif` int(11) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','mini bus','bus') NOT NULL,
  `tarif_per_jam` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_tarif`
--

INSERT INTO `tb_tarif` (`id_tarif`, `jenis_kendaraan`, `tarif_per_jam`) VALUES
(1, 'motor', 3000.00),
(2, 'mobil', 5000.00),
(3, 'mini bus', 6000.00),
(4, 'bus', 75000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_parkir` int(11) NOT NULL,
  `waktu_keluar` datetime NOT NULL DEFAULT current_timestamp(),
  `durasi_jam` int(11) NOT NULL,
  `biaya_total` decimal(10,2) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_parkir`, `waktu_keluar`, `durasi_jam`, `biaya_total`, `id_user`) VALUES
(1, 1, '2026-09-14 08:33:00', 1, 10000.00, 2),
(2, 2, '2026-09-22 00:57:00', 1, 10000.00, 2),
(4, 5, '2026-09-22 01:33:37', 1, 10000.00, 1),
(5, 6, '2026-09-22 03:28:00', 1, 10000.00, 2),
(6, 8, '2026-09-22 08:40:31', 1, 3000.00, 2),
(7, 7, '2026-09-22 08:41:59', 6, 18000.00, 2),
(8, 9, '2026-09-22 08:47:08', 1, 5000.00, 2),
(9, 10, '2026-09-22 08:48:40', 1, 5000.00, 2),
(10, 11, '2026-09-23 07:52:06', 17, 85000.00, 1),
(11, 12, '2026-09-23 08:36:27', 1, 5000.00, 3),
(12, 13, '2026-09-23 14:49:40', 1, 5000.00, 2),
(13, 14, '2026-09-30 13:10:41', 1, 3000.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','owner') NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_aktif`) VALUES
(1, 'Admin Utama', 'admin', 'admin123', 'admin', 1),
(2, 'Petugas Satu', 'p1', 'p1', 'petugas', 1),
(3, 'Petugas Dua', 'p2', 'p2', 'petugas', 1),
(4, 'Owner Parkir', 'owner', 'owner123', 'owner', 1),
(5, 'Petugas Tiga', 'p3', 'p3', 'petugas', 0),
(12, 'Egi Bintang', 'bintang', '$2y$10$nXtG6XIb/9sPDDqQ1mHknuqiRUAnUPS9xZ5uhpvpw00TtcbWUSV/q', 'petugas', 0),
(13, 'AGus', 'gus', '$2y$10$JN.iQVSExYFu9HgO7tdZnOdgQOtmued9Lll1wUksELioDc5FWt1Za', 'petugas', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  ADD PRIMARY KEY (`id_area`);

--
-- Indexes for table `tb_histori`
--
ALTER TABLE `tb_histori`
  ADD PRIMARY KEY (`id_histori`),
  ADD UNIQUE KEY `uq_histori_transaksi` (`id_transaksi`),
  ADD KEY `idx_histori_parkir` (`id_parkir`),
  ADD KEY `idx_histori_user` (`id_user`),
  ADD KEY `idx_histori_keluar` (`waktu_keluar`);

--
-- Indexes for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `fk_log_user` (`id_user`);

--
-- Indexes for table `tb_member`
--
ALTER TABLE `tb_member`
  ADD PRIMARY KEY (`id_member`),
  ADD KEY `fk_member_user` (`id_user`),
  ADD KEY `fk_member_area` (`id_area`);

--
-- Indexes for table `tb_parkir`
--
ALTER TABLE `tb_parkir`
  ADD PRIMARY KEY (`id_parkir`),
  ADD KEY `fk_parkir_member` (`id_member`),
  ADD KEY `fk_parkir_tarif` (`id_tarif`),
  ADD KEY `fk_parkir_user_masuk` (`id_user_masuk`),
  ADD KEY `fk_parkir_area` (`id_area`);

--
-- Indexes for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `id_parkir` (`id_parkir`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_histori`
--
ALTER TABLE `tb_histori`
  MODIFY `id_histori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `tb_member`
--
ALTER TABLE `tb_member`
  MODIFY `id_member` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_parkir`
--
ALTER TABLE `tb_parkir`
  MODIFY `id_parkir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_histori`
--
ALTER TABLE `tb_histori`
  ADD CONSTRAINT `fk_histori_parkir` FOREIGN KEY (`id_parkir`) REFERENCES `tb_parkir` (`id_parkir`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_histori_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_histori_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON UPDATE CASCADE;

--
-- Constraints for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_member`
--
ALTER TABLE `tb_member`
  ADD CONSTRAINT `fk_member_area` FOREIGN KEY (`id_area`) REFERENCES `tb_area_parkir` (`id_area`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_member_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tb_parkir`
--
ALTER TABLE `tb_parkir`
  ADD CONSTRAINT `fk_parkir_area` FOREIGN KEY (`id_area`) REFERENCES `tb_area_parkir` (`id_area`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_parkir_member` FOREIGN KEY (`id_member`) REFERENCES `tb_member` (`id_member`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_parkir_tarif` FOREIGN KEY (`id_tarif`) REFERENCES `tb_tarif` (`id_tarif`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_parkir_user_masuk` FOREIGN KEY (`id_user_masuk`) REFERENCES `tb_user` (`id_user`) ON UPDATE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `fk_transaksi_parkir` FOREIGN KEY (`id_parkir`) REFERENCES `tb_parkir` (`id_parkir`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user_keluar` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
