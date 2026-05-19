<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = $_SESSION['id_taruna'];
    $jenis_izin = $conn->real_escape_string($_POST['jenis_izin']);
    $tgl_mulai = $conn->real_escape_string($_POST['tanggal_mulai']);
    $tgl_selesai = $conn->real_escape_string($_POST['tanggal_selesai']);
    $alasan = $conn->real_escape_string($_POST['alasan']);
    
    // Upload Dokumen
    $dokumen = NULL;
    if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] == 0) {
        $target_dir = "uploads/dokumen/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["dokumen"]["name"], PATHINFO_EXTENSION);
        $dokumen = "dokumen_".$id_taruna."_".time().".".$file_extension;
        move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_dir . $dokumen);
    }

    $sql = "INSERT INTO pengajuan_izin (id_taruna, jenis_izin, tanggal_mulai, tanggal_selesai, alasan, dokumen_pendukung) 
            VALUES ($id_taruna, '$jenis_izin', '$tgl_mulai', '$tgl_selesai', '$alasan', '$dokumen')";
    
    if($conn->query($sql)) {
        // Catat di Log
        $nama_taruna = $_SESSION['nama'];
        $conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('Taruna $nama_taruna mengajukan izin $jenis_izin', NOW())");

        $_SESSION['success'] = "Pengajuan izin berhasil dikirim dan menunggu persetujuan Pembina.";
    } else {
        $_SESSION['error'] = "Gagal mengajukan izin: " . $conn->error;
    }
    header("Location: taruna_izin.php");
    exit();
}
?>
