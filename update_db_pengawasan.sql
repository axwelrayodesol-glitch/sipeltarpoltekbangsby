USE `sipeltar_db`;

CREATE TABLE IF NOT EXISTS `laporan_kamar` (
  `id_laporan` int(11) NOT NULL AUTO_INCREMENT,
  `id_taruna` int(11) NOT NULL,
  `nama_barak` varchar(50) NOT NULL,
  `kondisi` enum('Sangat Bersih','Bersih','Kotor','Ada Kerusakan') NOT NULL DEFAULT 'Bersih',
  `foto` varchar(255) NOT NULL,
  `waktu_lapor` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_laporan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `histori_patroli` (
  `id_patroli` int(11) NOT NULL AUTO_INCREMENT,
  `id_pembina` int(11) NOT NULL,
  `lokasi_patroli` varchar(100) NOT NULL,
  `catatan` text NOT NULL,
  `waktu_patroli` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_patroli`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `laporan_menghadap` (
  `id_menghadap` int(11) NOT NULL AUTO_INCREMENT,
  `id_junior` int(11) NOT NULL,
  `nama_senior` varchar(100) NOT NULL,
  `lokasi_kamar` varchar(100) NOT NULL,
  `keperluan` text NOT NULL,
  `waktu_mulai` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_selesai` datetime DEFAULT NULL,
  `status_menghadap` enum('Sedang Menghadap','Selesai') NOT NULL DEFAULT 'Sedang Menghadap',
  PRIMARY KEY (`id_menghadap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
