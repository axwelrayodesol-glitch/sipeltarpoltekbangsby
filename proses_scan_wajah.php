<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'pembina' && $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    die("Akses Ditolak");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = (int)$_POST['id_taruna'];
    $nit = $conn->real_escape_string($_POST['nit']);
    
    $waktu_sekarang = date('Y-m-d H:i:s');
    $tanggal_sekarang = date('Y-m-d');
    
    // Cek apakah sudah absen hari ini
    $cek_absen = $conn->query("SELECT id_kedatangan FROM data_kedatangan WHERE id_taruna = $id_taruna AND DATE(waktu_datang) = '$tanggal_sekarang'");
    
    if($cek_absen->num_rows == 0) {
        // Insert Absen Kehadiran
        $sql = "INSERT INTO data_kedatangan (id_taruna, waktu_datang, status_kehadiran) VALUES ($id_taruna, '$waktu_sekarang', 'hadir')";
        if($conn->query($sql)) {
            // Catat log
            $nama_petugas = $_SESSION['nama'];
            $conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('AI Terminal: Taruna NIT $nit berhasil Auto-CheckIn (Dipantau $nama_petugas)', '$waktu_sekarang')");
            echo "SUCCESS: Check-In Berhasil";
        } else {
            echo "ERROR: " . $conn->error;
        }
    } else {
        echo "INFO: Taruna sudah check-in hari ini";
    }
}
?>
