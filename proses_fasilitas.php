<?php
session_start();
require 'config.php';
require 'wa_gateway.php'; // Integrasi WA

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = $_SESSION['id_taruna'];
    $jenis = $conn->real_escape_string($_POST['jenis']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $foto = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = time() . '_fasilitas.' . $ext;
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $newname);
            $foto = $newname;
        }
    }

    $sql = "INSERT INTO laporan_fasilitas (id_taruna, jenis, deskripsi, foto, status) 
            VALUES ('$id_taruna', '$jenis', '$deskripsi', '$foto', 'Menunggu')";
            
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Laporan kerusakan berhasil dikirim ke bagian logistik/sarpas.";
        
        // --- WHATSAPP NOTIFICATION TRIGGER (Simulasi) ---
        // Jika ada nomor Admin Sarpas, bisa dipanggil di sini:
        // $admin_phone = "08123456789"; 
        // $msg = "LAPORAN BARU MASUK!\nJenis: $jenis\nDetail: $deskripsi\nStatus: Menunggu Pengecekan.";
        // sendWA($admin_phone, $msg);
        
    } else {
        $_SESSION['error'] = "Terjadi kesalahan: " . $conn->error;
    }

    header("Location: taruna_fasilitas.php");
    exit();
}
?>
