<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = $_SESSION['id_taruna'];
    $id_riwayat = $conn->real_escape_string($_POST['id_riwayat']);
    $alasan = $conn->real_escape_string($_POST['alasan']);

    // Cek apakah sudah pernah banding
    $cek = $conn->query("SELECT id_banding FROM banding_hukuman WHERE id_riwayat_pelanggaran = '$id_riwayat'");
    
    if ($cek->num_rows == 0) {
        $sql = "INSERT INTO banding_hukuman (id_riwayat_pelanggaran, id_taruna, alasan_pembelaan) 
                VALUES ('$id_riwayat', '$id_taruna', '$alasan')";
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Pledoi / Banding berhasil diajukan. Menunggu keputusan Komandan.";
        } else {
            $_SESSION['error'] = "Gagal mengajukan banding.";
        }
    } else {
        $_SESSION['error'] = "Anda sudah pernah mengajukan banding untuk pelanggaran ini.";
    }

    header("Location: taruna_banding.php");
    exit();
}
?>
