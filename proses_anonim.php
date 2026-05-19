<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = $_SESSION['id_taruna'];
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $foto = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = time() . '_anonim.' . $ext;
            if (!is_dir('uploads/anonim')) mkdir('uploads/anonim', 0777, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/anonim/' . $newname);
            $foto = $newname;
        }
    }

    $sql = "INSERT INTO laporan_anonim (id_taruna_pelapor, judul_laporan, deskripsi, foto_bukti, status) 
            VALUES ('$id_taruna', '$judul', '$deskripsi', '$foto', 'Menunggu')";
            
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Laporan berhasil dikirim secara anonim dan aman!";
    }

    header("Location: taruna_anonim.php");
    exit();
}
?>
