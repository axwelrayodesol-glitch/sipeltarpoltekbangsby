CREATE DATABASE IF NOT EXISTS `sipeltar_db`;
USE `sipeltar_db`;

-- Menghapus tabel lama jika ada agar bersih (HATI-HATI JIKA DI PRODUCTION)
DROP TABLE IF EXISTS `riwayat_pelanggaran`;
DROP TABLE IF EXISTS `kategori_pelanggaran`;
DROP TABLE IF EXISTS `data_kedatangan`;
DROP TABLE IF EXISTS `riwayat_aktivitas`;
DROP TABLE IF EXISTS `data_taruna`;
DROP TABLE IF EXISTS `users`;

-- Tabel Multi-Role Users (Admin, Pembina, dll)
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pembina','instruktur','komandan','wali') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  PRIMARY KEY (`id_user`)
);

-- Tabel Taruna (Sekarang dengan Poin Pelanggaran dan Status Kategori)
CREATE TABLE `data_taruna` (
  `id_taruna` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `angkatan` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `total_poin_pelanggaran` int(11) DEFAULT 0,
  `kategori_status` enum('Teladan','Perhatian Khusus','Pembinaan','Kritis') DEFAULT 'Teladan',
  PRIMARY KEY (`id_taruna`),
  UNIQUE KEY `nit` (`nit`)
);

-- Tabel Master Kategori Pelanggaran beserta Bobot Poinnya
CREATE TABLE `kategori_pelanggaran` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `tingkat_pelanggaran` enum('Ringan','Sedang','Berat','Sangat Berat') NOT NULL,
  `poin` int(11) NOT NULL,
  PRIMARY KEY (`id_kategori`)
);

-- Tabel Transaksi Laporan Pelanggaran
CREATE TABLE `riwayat_pelanggaran` (
  `id_riwayat` int(11) NOT NULL AUTO_INCREMENT,
  `id_taruna` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `lokasi_kejadian` varchar(255) DEFAULT NULL,
  `id_pelapor` int(11) NOT NULL, -- FK ke users (Misal: Pembina)
  `waktu_lapor` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_riwayat`),
  KEY `id_taruna` (`id_taruna`),
  KEY `id_kategori` (`id_kategori`),
  KEY `id_pelapor` (`id_pelapor`),
  CONSTRAINT `fk_pel_taruna` FOREIGN KEY (`id_taruna`) REFERENCES `data_taruna` (`id_taruna`) ON DELETE CASCADE,
  CONSTRAINT `fk_pel_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_pelanggaran` (`id_kategori`) ON DELETE CASCADE,
  CONSTRAINT `fk_pel_user` FOREIGN KEY (`id_pelapor`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
);

-- Tabel Kedatangan (Masih dipertahankan dari V1)
CREATE TABLE `data_kedatangan` (
  `id_kedatangan` int(11) NOT NULL AUTO_INCREMENT,
  `id_taruna` int(11) NOT NULL,
  `waktu_datang` datetime NOT NULL,
  `status_kehadiran` enum('hadir','terlambat','belum_datang') NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `alamat_lokasi` text DEFAULT NULL,
  PRIMARY KEY (`id_kedatangan`),
  KEY `id_taruna` (`id_taruna`),
  CONSTRAINT `fk_taruna` FOREIGN KEY (`id_taruna`) REFERENCES `data_taruna` (`id_taruna`) ON DELETE CASCADE
);

-- Tabel Aktivitas Sistem (Log)
CREATE TABLE `riwayat_aktivitas` (
  `id_aktivitas` int(11) NOT NULL AUTO_INCREMENT,
  `deskripsi` varchar(255) NOT NULL,
  `waktu` datetime NOT NULL,
  PRIMARY KEY (`id_aktivitas`)
);

-- DUMMY DATA SETUP --
INSERT INTO `users` (`username`, `password`, `role`, `nama_lengkap`) VALUES
('admin', MD5('admin123'), 'admin', 'Administrator Pusat'),
('pembina1', MD5('pembina123'), 'pembina', 'Kapten Budi (Pembina Asrama)'),
('komandan', MD5('komandan123'), 'komandan', 'Kolonel Sugeng (Komandan Resimen)');

INSERT INTO `data_taruna` (`nama`, `nit`, `jurusan`, `angkatan`, `password`, `total_poin_pelanggaran`, `kategori_status`) VALUES
('Axel Taruna', '21234567', 'Teknika', '2021', MD5('taruna123'), 0, 'Teladan'),
('Budi Santoso', '21234568', 'Nautika', '2021', MD5('taruna123'), 0, 'Teladan'),
('Citra Lestari', '22234569', 'Transportasi', '2022', MD5('taruna123'), 0, 'Teladan');

INSERT INTO `kategori_pelanggaran` (`nama_pelanggaran`, `tingkat_pelanggaran`, `poin`) VALUES
('Terlambat apel pagi/sore', 'Ringan', 5),
('Tidak memakai atribut seragam lengkap', 'Sedang', 10),
('Kamar asrama berantakan', 'Ringan', 5),
('Keluar area kampus tanpa izin (Keluar Ksatrian)', 'Berat', 50),
('Berkelahi atau membuat onar', 'Sangat Berat', 100),
('Alpha / Tidak hadir tanpa keterangan', 'Sedang', 25);
