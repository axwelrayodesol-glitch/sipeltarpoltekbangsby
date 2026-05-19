USE `sipeltar_db`;
ALTER TABLE `data_taruna` ADD COLUMN IF NOT EXISTS `face_descriptor` TEXT DEFAULT NULL;
