CREATE DATABASE IF NOT EXISTS `sipeltar_db`;
USE `sipeltar_db`;

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  PRIMARY KEY (`id_user`)
);

CREATE TABLE `data_taruna` (
  `id_taruna` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `angkatan` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id_taruna`),
  UNIQUE KEY `nit` (`nit`)
);

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

CREATE TABLE `riwayat_aktivitas` (
  `id_aktivitas` int(11) NOT NULL AUTO_INCREMENT,
  `deskripsi` varchar(255) NOT NULL,
  `waktu` datetime NOT NULL,
  PRIMARY KEY (`id_aktivitas`)
);

-- Gunakan MD5 untuk kemudahan setup awal di XAMPP
INSERT INTO `users` (`username`, `password`, `role`, `nama_lengkap`) VALUES
('admin', MD5('admin123'), 'admin', 'Administrator Utama'),
('petugas1', MD5('petugas123'), 'petugas', 'Petugas Piket');

INSERT INTO `data_taruna` (`nama`, `nit`, `jurusan`, `angkatan`, `password`) VALUES
('Axel Taruna', '21234567', 'Teknika', '2021', MD5('taruna123')),
('Budi Santoso', '21234568', 'Nautika', '2021', MD5('taruna123')),
('Citra Lestari', '22234569', 'Transportasi', '2022', MD5('taruna123'));
