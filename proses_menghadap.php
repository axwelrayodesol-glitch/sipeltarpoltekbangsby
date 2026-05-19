<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = $_SESSION['id_taruna'];
    $action = $_POST['action'];

    if ($action == 'mulai') {
        $nama_senior = $conn->real_escape_string($_POST['nama_senior']);
        $lokasi_kamar = $conn->real_escape_string($_POST['lokasi_kamar']);
        $keperluan = $conn->real_escape_string($_POST['keperluan']);

        $sql = "INSERT INTO laporan_menghadap (id_junior, nama_senior, lokasi_kamar, keperluan, status_menghadap) 
                VALUES ('$id_taruna', '$nama_senior', '$lokasi_kamar', '$keperluan', 'Sedang Menghadap')";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Radar aktif! Pengasuh memantau keberadaan Anda. Ingat untuk mematikan radar jika sudah selesai.";
        }
    } 
    else if ($action == 'selesai') {
        $id_menghadap = (int)$_POST['id_menghadap'];
        $waktu_selesai = date('Y-m-d H:i:s');
        
        $sql = "UPDATE laporan_menghadap SET status_menghadap = 'Selesai', waktu_selesai = '$waktu_selesai' WHERE id_menghadap = $id_menghadap AND id_junior = $id_taruna";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Sesi selesai. Anda telah keluar dari radar pantauan.";
        }
    }

    header("Location: taruna_menghadap.php");
    exit();
}
?>
