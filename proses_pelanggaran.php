<?php
session_start();
require 'config.php';
require 'wa_gateway.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = (int)$_POST['id_taruna'];
    $id_kategori = (int)$_POST['id_kategori'];
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $jam = $conn->real_escape_string($_POST['jam']);
    $lokasi = $conn->real_escape_string($_POST['lokasi']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $id_pelapor = $_SESSION['id_user'];
    
    // Upload File Handling
    $foto_bukti = NULL;
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] == 0) {
        $target_dir = "uploads/bukti/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["foto_bukti"]["name"], PATHINFO_EXTENSION);
        $foto_bukti = "bukti_".$id_taruna."_".time().".".$file_extension;
        move_uploaded_file($_FILES["foto_bukti"]["tmp_name"], $target_dir . $foto_bukti);
    }

    // 1. Insert ke Riwayat
    $sql_riwayat = "INSERT INTO riwayat_pelanggaran (id_taruna, id_kategori, tanggal, jam, deskripsi, foto_bukti, lokasi_kejadian, id_pelapor) 
                    VALUES ($id_taruna, $id_kategori, '$tanggal', '$jam', '$deskripsi', '$foto_bukti', '$lokasi', $id_pelapor)";
    
    if($conn->query($sql_riwayat)) {
        // 2. Dapatkan Poin dari Kategori
        $res_kat = $conn->query("SELECT poin FROM kategori_pelanggaran WHERE id_kategori = $id_kategori");
        $poin_baru = $res_kat->fetch_assoc()['poin'];

        // 3. Update Data Taruna (Tambah Poin dan Tentukan Kategori)
        $res_taruna = $conn->query("SELECT total_poin_pelanggaran FROM data_taruna WHERE id_taruna = $id_taruna");
        $poin_sekarang = $res_taruna->fetch_assoc()['total_poin_pelanggaran'];
        
        $total_poin = $poin_sekarang + $poin_baru;
        
        $kategori_status = 'Teladan';
        if($total_poin > 20 && $total_poin <= 50) $kategori_status = 'Perhatian Khusus';
        else if($total_poin > 50 && $total_poin <= 99) $kategori_status = 'Pembinaan';
        else if($total_poin >= 100) $kategori_status = 'Kritis';

        $conn->query("UPDATE data_taruna SET total_poin_pelanggaran = $total_poin, kategori_status = '$kategori_status' WHERE id_taruna = $id_taruna");
        
        // --- WHATSAPP NOTIFICATION TRIGGER (Simulasi) ---
        // $nama_taruna = $res_taruna->fetch_assoc()['nama']; // perlu select nama juga
        $res_nama = $conn->query("SELECT nama FROM data_taruna WHERE id_taruna = $id_taruna");
        $nama_taruna = $res_nama->fetch_assoc()['nama'];
        
        // Ambil nama pelanggaran
        $res_nama_pel = $conn->query("SELECT nama_pelanggaran FROM kategori_pelanggaran WHERE id_kategori = $id_kategori");
        $nama_pel = $res_nama_pel->fetch_assoc()['nama_pelanggaran'];

        // Format pesan peringatan (Misal dikirim ke ortu/wali atau taruna)
        $wa_msg = "🚨 *PERINGATAN KEDISIPLINAN SIPELTAR* 🚨\n\n";
        $wa_msg .= "Taruna a.n. *$nama_taruna* baru saja melakukan pelanggaran:\n";
        $wa_msg .= "Jenis: *$nama_pel*\n";
        $wa_msg .= "Poin Sanksi: *+$poin_baru*\n";
        $wa_msg .= "Total Poin Saat Ini: *$total_poin* ($kategori_status)\n\n";
        $wa_msg .= "Harap perhatikan tingkat kedisiplinan. Terima kasih.";
        
        // Asumsi kolom nomor HP belum ada, kita tembak dummy atau ambil dari setting
        $dummy_phone = "081234567890"; 
        sendWA($dummy_phone, $wa_msg);
        // ------------------------------------------------

        // Catat di Log
        $nama_pelapor = $_SESSION['nama'];
        $conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('$nama_pelapor menginput pelanggaran untuk taruna ID $id_taruna', NOW())");

        $_SESSION['success'] = "Data pelanggaran berhasil disimpan dan Poin telah diakumulasi!";
        header("Location: pembina_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menyimpan data: " . $conn->error;
        header("Location: pembina_input_pelanggaran.php");
        exit();
    }
}
?>
