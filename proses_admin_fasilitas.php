<?php
session_start();
require 'config.php';
require 'wa_gateway.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_laporan = $conn->real_escape_string($_POST['id_laporan']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Get taruna info for WhatsApp notification
    $query_taruna = $conn->query("SELECT t.nama, l.jenis FROM laporan_fasilitas l JOIN data_taruna t ON l.id_taruna = t.id_taruna WHERE l.id_laporan = '$id_laporan'");
    $t = $query_taruna->fetch_assoc();

    $sql = "UPDATE laporan_fasilitas SET status = '$status' WHERE id_laporan = '$id_laporan'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Status laporan berhasil diubah menjadi: " . $status;
        
        // --- WHATSAPP NOTIFICATION TRIGGER (Simulasi) ---
        // Jika sistem mencatat nomor Taruna, bisa dinotifikasi di sini:
        // $taruna_phone = "0812xxxx"; // Ambil dari database
        // $msg = "Halo " . $t['nama'] . ", status laporan kerusakan fasilitas (" . $t['jenis'] . ") Anda saat ini telah diupdate menjadi: *" . strtoupper($status) . "*.";
        // sendWA($taruna_phone, $msg);
        
    } else {
        $_SESSION['error'] = "Gagal mengubah status!";
    }
    header("Location: admin_fasilitas.php");
    exit();
}
?>
