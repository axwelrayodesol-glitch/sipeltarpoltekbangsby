USE `sipeltar_db`;

INSERT INTO `users` (`username`, `password`, `role`, `nama_lengkap`) VALUES
('darsono', MD5('pembina123'), 'pembina', 'Pengasuh Darsono'),
('idris', MD5('pembina123'), 'pembina', 'Pengasuh Idris'),
('iwan', MD5('pembina123'), 'pembina', 'Pengasuh Iwan'),
('agus', MD5('pembina123'), 'pembina', 'Pengasuh Agus'),
('mudiyono', MD5('pembina123'), 'pembina', 'Pengasuh Mudiyono'),
('silvi', MD5('pembina123'), 'pembina', 'Pengasuh Silvi'),
('lilik', MD5('pembina123'), 'pembina', 'Pengasuh Lilik');
