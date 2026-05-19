<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_izin = (int)$_POST['id_izin'];
    $status = $conn->real_escape_string($_POST['status']);
    $id_pembina = $_SESSION['id_user'];
    
    $sql = "UPDATE pengajuan_izin SET status_approval = '$status', id_pembina_approver = $id_pembina WHERE id_izin = $id_izin";
    
    if($conn->query($sql)) {
        // Catat di Log
        $nama_pembina = $_SESSION['nama'];
        $conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('$nama_pembina melakukan $status pada pengajuan izin ID $id_izin', NOW())");
        
        $_SESSION['success'] = "Status pengajuan izin berhasil diubah menjadi: " . $status;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan: " . $conn->error;
    }
    header("Location: pembina_approval_izin.php");
    exit();
}
?>
