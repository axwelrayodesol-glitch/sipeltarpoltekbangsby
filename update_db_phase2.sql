USE `sipeltar_db`;

-- Tambahkan kolom prestasi ke data_taruna
ALTER TABLE `data_taruna` ADD COLUMN `total_poin_prestasi` INT(11) DEFAULT 0 AFTER `total_poin_pelanggaran`;

-- Tabel Pengajuan Izin
CREATE TABLE IF NOT EXISTS `pengajuan_izin` (
  `id_izin` int(11) NOT NULL AUTO_INCREMENT,
  `id_taruna` int(11) NOT NULL,
  `jenis_izin` enum('Sakit','Keluar Asrama','Pulang','Kegiatan Luar') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `status_approval` enum('Pending','Disetujui Pembina','Ditolak') DEFAULT 'Pending',
  `id_pembina_approver` int(11) DEFAULT NULL,
  `waktu_pengajuan` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_izin`),
  KEY `id_taruna` (`id_taruna`),
  CONSTRAINT `fk_izin_taruna` FOREIGN KEY (`id_taruna`) REFERENCES `data_taruna` (`id_taruna`) ON DELETE CASCADE
);

-- Tabel Master Kategori Prestasi
CREATE TABLE IF NOT EXISTS `data_prestasi` (
  `id_prestasi` int(11) NOT NULL AUTO_INCREMENT,
  `nama_prestasi` varchar(150) NOT NULL,
  `kategori` enum('Akademik','Olahraga','Organisasi','Lomba','Kedisiplinan') NOT NULL,
  `poin_reward` int(11) NOT NULL,
  PRIMARY KEY (`id_prestasi`)
);

-- Tabel Transaksi Riwayat Prestasi Taruna
CREATE TABLE IF NOT EXISTS `riwayat_prestasi` (
  `id_riwayat_prestasi` int(11) NOT NULL AUTO_INCREMENT,
  `id_taruna` int(11) NOT NULL,
  `id_prestasi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `id_pemberi` int(11) NOT NULL,
  `waktu_input` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_riwayat_prestasi`),
  KEY `id_taruna` (`id_taruna`),
  CONSTRAINT `fk_prestasi_taruna` FOREIGN KEY (`id_taruna`) REFERENCES `data_taruna` (`id_taruna`) ON DELETE CASCADE,
  CONSTRAINT `fk_prestasi_master` FOREIGN KEY (`id_prestasi`) REFERENCES `data_prestasi` (`id_prestasi`) ON DELETE CASCADE
);

-- Data Dummy Prestasi
INSERT INTO `data_prestasi` (`nama_prestasi`, `kategori`, `poin_reward`) VALUES
('Juara 1 Lomba Akademik Nasional', 'Akademik', 50),
('Komandan Peleton Terbaik', 'Kedisiplinan', 30),
('Juara Bela Diri Antar Akademi', 'Olahraga', 40),
('Ketua Senat Taruna', 'Organisasi', 25);
