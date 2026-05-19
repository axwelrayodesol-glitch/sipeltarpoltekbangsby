<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = $_SESSION['id_taruna'];
    $nama_barak = $conn->real_escape_string($_POST['nama_barak']);
    $kondisi = $conn->real_escape_string($_POST['kondisi']);
    $foto = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = time() . '_kamar_' . $id_taruna . '.' . $ext;
            if (!is_dir('uploads/kamar')) mkdir('uploads/kamar', 0777, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/kamar/' . $newname);
            $foto = $newname;
        }
    }

    if(!empty($foto)){
        $sql = "INSERT INTO laporan_kamar (id_taruna, nama_barak, kondisi, foto) 
                VALUES ('$id_taruna', '$nama_barak', '$kondisi', '$foto')";
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Laporan kondisi kamar berhasil dikirim ke Pengasuh.";
        } else {
            $_SESSION['error'] = "Gagal menyimpan ke database.";
        }
    } else {
        $_SESSION['error'] = "Wajib melampirkan foto kamar.";
    }

    header("Location: taruna_kamar.php");
    exit();
}
?>
