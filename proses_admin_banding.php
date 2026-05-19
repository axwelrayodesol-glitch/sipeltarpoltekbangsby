<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_banding = (int)$_POST['id_banding'];
    $id_taruna = (int)$_POST['id_taruna'];
    $id_riwayat = (int)$_POST['id_riwayat'];
    $poin_dikembalikan = (int)$_POST['poin'];
    $keputusan = $conn->real_escape_string($_POST['keputusan']); // Diterima / Ditolak

    // Update status banding
    $sql_banding = "UPDATE banding_hukuman SET status_banding = '$keputusan' WHERE id_banding = $id_banding";
    if ($conn->query($sql_banding)) {
        
        if ($keputusan == 'Diterima') {
            // REFUND POIN: Kurangi poin pelanggaran taruna
            $res_taruna = $conn->query("SELECT total_poin_pelanggaran FROM data_taruna WHERE id_taruna = $id_taruna");
            $poin_sekarang = $res_taruna->fetch_assoc()['total_poin_pelanggaran'];
            
            $poin_baru = $poin_sekarang - $poin_dikembalikan;
            if($poin_baru < 0) $poin_baru = 0; // Poin pelanggaran tidak bisa minus
            
            // Hitung ulang status
            $kategori_status = 'Teladan';
            if($poin_baru > 20 && $poin_baru <= 50) $kategori_status = 'Perhatian Khusus';
            else if($poin_baru > 50 && $poin_baru <= 99) $kategori_status = 'Pembinaan';
            else if($poin_baru >= 100) $kategori_status = 'Kritis';
            
            // Eksekusi pengembalian poin
            $conn->query("UPDATE data_taruna SET total_poin_pelanggaran = $poin_baru, kategori_status = '$kategori_status' WHERE id_taruna = $id_taruna");
            
            // Hapus riwayat pelanggaran agar benar-benar bersih (Opsional: bisa juga hanya ditandai "Dibatalkan")
            // $conn->query("DELETE FROM riwayat_pelanggaran WHERE id_riwayat = $id_riwayat"); 
            // Kita biarkan riwayat pelanggaran tetap ada (karena foreign key ON DELETE CASCADE bisa menghapus laporan bandingnya juga jika riwayat dihapus), tapi kita beri tanda bahwa poinnya sudah direfund via tabel banding.

            $_SESSION['success'] = "Banding DITERIMA. Poin sebesar ($poin_dikembalikan) telah dikembalikan (Refund) ke Taruna.";
        } else {
            $_SESSION['success'] = "Banding DITOLAK. Hukuman tetap berlaku.";
        }
    } else {
        $_SESSION['error'] = "Gagal memproses keputusan.";
    }

    header("Location: admin_banding.php");
    exit();
}
?>
