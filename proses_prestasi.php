<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = (int)$_POST['id_taruna'];
    $id_prestasi = (int)$_POST['id_prestasi'];
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $id_pemberi = $_SESSION['id_user'];
    
    // 1. Insert ke Riwayat Prestasi
    $sql_riwayat = "INSERT INTO riwayat_prestasi (id_taruna, id_prestasi, tanggal, deskripsi, id_pemberi) 
                    VALUES ($id_taruna, $id_prestasi, '$tanggal', '$deskripsi', $id_pemberi)";
    
    if($conn->query($sql_riwayat)) {
        // 2. Dapatkan Poin dari Master Prestasi
        $res_kat = $conn->query("SELECT poin_reward FROM data_prestasi WHERE id_prestasi = $id_prestasi");
        $poin_baru = $res_kat->fetch_assoc()['poin_reward'];

        // 3. Tambahkan poin ke total_poin_prestasi di data_taruna
        // Jika kolom total_poin_prestasi belum ada, akan error, tapi kita sudah buat di update_db_phase2.sql
        $conn->query("UPDATE data_taruna SET total_poin_prestasi = total_poin_prestasi + $poin_baru WHERE id_taruna = $id_taruna");
        
        // Catat di Log
        $nama_pembina = $_SESSION['nama'];
        $conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('$nama_pembina memberikan reward/prestasi untuk taruna ID $id_taruna', NOW())");

        $_SESSION['success'] = "Data Prestasi berhasil disimpan! Taruna mendapatkan +$poin_baru poin reward.";
        header("Location: pembina_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menyimpan data: " . $conn->error;
        header("Location: pembina_input_prestasi.php");
        exit();
    }
}
?>
