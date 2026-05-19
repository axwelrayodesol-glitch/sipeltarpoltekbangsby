<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

$filename = "Laporan_Pelanggaran_SIPELTAR_" . date('Ymd') . ".csv";

// Set Header untuk mendownload file CSV
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$filename");
header("Content-Type: application/csv; "); 

// Buka memori untuk menulis CSV
$file = fopen('php://output', 'w');

// Header Kolom CSV
$header = array("ID Riwayat", "Nama Taruna", "NIT", "Kategori Pelanggaran", "Poin", "Tanggal", "Jam", "Lokasi", "Pelapor");
fputcsv($file, $header);

// Query Data
$query = "SELECT r.id_riwayat, t.nama, t.nit, k.nama_pelanggaran, k.poin, r.tanggal, r.jam, r.lokasi_kejadian, u.nama_lengkap 
          FROM riwayat_pelanggaran r
          JOIN data_taruna t ON r.id_taruna = t.id_taruna
          JOIN kategori_pelanggaran k ON r.id_kategori = k.id_kategori
          JOIN users u ON r.id_pelapor = u.id_user
          ORDER BY r.waktu_lapor DESC";
          
$result = $conn->query($query);
while($row = $result->fetch_assoc()){
    $lineData = array(
        $row['id_riwayat'], 
        $row['nama'], 
        $row['nit'], 
        $row['nama_pelanggaran'], 
        $row['poin'], 
        $row['tanggal'], 
        $row['jam'], 
        $row['lokasi_kejadian'], 
        $row['nama_lengkap']
    );
    fputcsv($file, $lineData);
}
fclose($file);
exit;
?>
