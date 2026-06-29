-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2026 at 03:00 PM
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
-- Database: `db_peminjaman_lab`
--

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id_alat` int(11) NOT NULL,
  `kode_alat` char(6) NOT NULL,
  `nama_alat` text NOT NULL,
  `spesifikasi` text NOT NULL,
  `kategori_alat` enum('Perawatan','Pengujian','Kelistrikan','Pneumatik') NOT NULL,
  `stok_total` int(5) NOT NULL,
  `stok_tersedia` int(5) NOT NULL,
  `kondisi_alat` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `foto_alat` varchar(255) DEFAULT 'default_alat.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id_alat`, `kode_alat`, `nama_alat`, `spesifikasi`, `kategori_alat`, `stok_total`, `stok_tersedia`, `kondisi_alat`, `foto_alat`) VALUES
(1, 'PRW001', 'Kompressor SWAN', 'Max. Pressure 8 - 9 kg/cm2', 'Perawatan', 1, 1, 'baik', 'kompresor.jpg'),
(2, 'ALT002', 'Jangka Sorong Mitutoyo', 'Range 0 - 300 mm (0,05 mm)', 'Perawatan', 6, 6, 'baik', 'jangka_sorong.jpg'),
(3, 'ALT003', 'Dial Indicator with Magnetic Base', 'Mitutoyo 0 - 10 mm (0,01 mm)', 'Perawatan', 5, 5, 'baik', 'dial_indicator.jpg'),
(4, 'ALT004', 'Impact Testing Machine (Pukul Takik)', 'Capacity : 100 kg-cm', 'Perawatan', 1, 1, 'baik', 'impact_test.jpg'),
(6, 'PNM001', 'Multitester Digital', 'Mengukur besaran dasar seperti tegangan, arus, dan hambatan', 'Pneumatik', 5, 5, 'baik', 'alat-1782703262.jpg'),
(7, 'KLS001', 'Insulation Tester', 'Alat uji kelistrikan yang berfungsi mengukur nilai resistansi isolasi pada kabel, transformator, motor, dan panel listrik untuk mencegah kebocoran arus dan korsleting', 'Kelistrikan', 2, 2, 'baik', 'alat-1782703631.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id_detail` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `id_alat` int(11) NOT NULL,
  `jumlah_pinjam` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id_detail`, `id_peminjaman`, `id_alat`, `jumlah_pinjam`) VALUES
(2, 2, 6, 2),
(3, 3, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `kode_peminjaman` char(18) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_pinjam` datetime NOT NULL,
  `tanggal_kembali_rencana` datetime NOT NULL,
  `tujuan_keperluan` text NOT NULL,
  `status_peminjaman` enum('pending','disetujui','ditolak','pending_kembali','selesai') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `kode_peminjaman`, `id_user`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `tujuan_keperluan`, `status_peminjaman`) VALUES
(1, 'PMJ-20260624195810', 1, '2026-06-25 00:57:00', '2026-07-02 00:57:00', 'Praktikum', 'selesai'),
(2, 'PMJ-20260629105041', 9, '2026-06-29 15:50:00', '2026-07-06 15:50:00', 'Mau Praktikum', 'selesai'),
(3, 'PMJ-20260629115745', 9, '2026-06-29 16:57:00', '2026-07-06 16:57:00', 'praktikum', 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `tanggal_kembali_asli` datetime NOT NULL,
  `id_petugas` int(11) NOT NULL,
  `kondisi_kembali` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `keterangan_tambahan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_peminjaman`, `tanggal_kembali_asli`, `id_petugas`, `kondisi_kembali`, `keterangan_tambahan`) VALUES
(1, 2, '2026-06-29 11:43:34', 8, 'baik', ''),
(2, 3, '2026-06-29 11:58:09', 8, 'baik', 'Bagus.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nomor_induk` bigint(10) UNSIGNED ZEROFILL NOT NULL,
  `nama_lengkap` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `program_studi` enum('Teknik Mesin','Perawatan dan Perbaikan Mesin') NOT NULL DEFAULT 'Teknik Mesin',
  `kelas` varchar(50) DEFAULT NULL,
  `role` enum('mahasiswa','dosen','staff_admin','kepala_lab') NOT NULL DEFAULT 'mahasiswa',
  `foto_profil` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nomor_induk`, `nama_lengkap`, `email`, `password`, `program_studi`, `kelas`, `role`, `foto_profil`) VALUES
(1, 3202402099, 'Nayla Shafiqi', 'difaacreamyyy@gmail.com', '$2y$10$tzNPPVTtB3BvKiY2P0JGReOPbTQnarvl//xusOwxIIS7cYQJRkWlW', 'Teknik Mesin', 'TM-4A', 'mahasiswa', 'user_1_1782325563.jpg'),
(2, 3202402032, 'Dani Albaghafi', 'eacloncy@gmail.com', '$2y$10$f8viQ5q/E8UNMS6gL1UaY./6lPzklG6qn7SfjpaSkgBROeXP7p9Sa', 'Teknik Mesin', NULL, 'mahasiswa', 'default.jpg'),
(3, 0015038501, 'Gita Sastika, S.T., M.T', 'alexxandra850@gmail.com', '$2y$10$RbnuL58hffzlBYhxZ49BO.YSImvkmbKRRsIXChPKwr1gYb5jKEdMS', 'Teknik Mesin', NULL, 'staff_admin', 'default.jpg'),
(4, 0019781201, 'Ruby, S.T., M.Kom', 'hanadelgaf@gmail.com', '$2y$10$W9TFvTTDn7Wi21fVHXCaQO3fWhKPE4hmalrFAaRk8w7q4zdhHk.hy', 'Teknik Mesin', NULL, 'staff_admin', 'default.jpg'),
(5, 3202402067, 'Ribka Gunawan', 'kalibrasiieh@gmail.com', '$2y$10$Qq0w8FSsRkX3KOfvNwEJPeePk2EI55jV2Yw9IcD/ONAaquqODNLh.', 'Teknik Mesin', NULL, 'mahasiswa', 'default.jpg'),
(6, 0019920824, 'Agun Gunawan', 'piinoy12@gmail.com', '$2y$10$y/O0ZvH/AevsH2N/R8xJS.4niyjb4qu9Us5EptWqteaa5P9NiYC4m', 'Teknik Mesin', NULL, 'staff_admin', 'default.jpg'),
(7, 0715088302, 'Farel Gundala, S.T ., M.Kom', 'oliviadawyen@gmail.com', '$2y$10$s1s6kcaLpVxKYaG7Fhnu2ursBzgiSmJB9Q2rNSjupRqKJP2IpcWba', 'Teknik Mesin', NULL, 'staff_admin', 'default.jpg'),
(8, 0201018701, 'Demian Al Ghifari, S.T', 'demiaqn@gmail.com', '$2y$10$QhfxJd4y6FGvKRguiWSN2OxC.1pibLKsEoP90/Eb6.3XdKb2ihYKK', 'Teknik Mesin', NULL, 'staff_admin', 'default.jpg'),
(9, 0125076902, 'Irwansyah', 'aleaaa985@gmail.com', '$2y$10$11M8Pzwob9MK1McbxHEwvebT1ItCcX7E.583KO.kZeL7M/nyaflK.', 'Teknik Mesin', NULL, 'dosen', 'default.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id_alat`),
  ADD UNIQUE KEY `unik_kode_alat` (`kode_alat`);

--
-- Indexes for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_peminjaman` (`id_peminjaman`),
  ADD KEY `fk_detail_alat` (`id_alat`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `fk_peminjaman_user` (`id_user`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `fk_pengembalian_pinjam` (`id_peminjaman`),
  ADD KEY `fk_pengembalian_petugas` (`id_petugas`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `unik_nomor_induk` (`nomor_induk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id_alat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `fk_detail_alat` FOREIGN KEY (`id_alat`) REFERENCES `alat` (`id_alat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_peminjaman` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_peminjaman_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `fk_pengembalian_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pengembalian_pinjam` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
