USE `sipeltar_db`;

CREATE TABLE IF NOT EXISTS `laporan_fasilitas` (
  `id_laporan` int(11) NOT NULL AUTO_INCREMENT,
  `id_taruna` int(11) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('Menunggu','Diproses','Selesai') NOT NULL DEFAULT 'Menunggu',
  `waktu_lapor` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_laporan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
