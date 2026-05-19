<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_csv'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    
    if ($_FILES["file_csv"]["size"] > 0) {
        $file_open = fopen($file, "r");
        $berhasil = 0;
        $gagal = 0;
        
        // Lewati baris pertama (Header) jika diperlukan, tapi kita cek formatnya langsung.
        $is_header = true;
        
        while (($data = fgetcsv($file_open, 1000, ",")) !== FALSE) {
            // Asumsi format kolom: Nama [0], NIT [1], Jurusan [2], Angkatan [3]
            // Sesuaikan jika urutan kolom Google Sheets Anda berbeda.
            
            if ($is_header) {
                // Skip baris pertama (biasanya judul kolom)
                $is_header = false;
                continue;
            }
            
            $nama = $conn->real_escape_string(trim($data[0] ?? ''));
            $nit = $conn->real_escape_string(trim($data[1] ?? ''));
            $jurusan = $conn->real_escape_string(trim($data[2] ?? ''));
            $angkatan = $conn->real_escape_string(trim($data[3] ?? ''));
            $password = md5('taruna123'); // Default password taruna
            
            if(empty($nama) || empty($nit)) continue; // Skip baris kosong

            // Cek duplikasi NIT
            $cek = $conn->query("SELECT id_taruna FROM data_taruna WHERE nit = '$nit'");
            if ($cek->num_rows == 0) {
                $sql = "INSERT INTO data_taruna (nama, nit, jurusan, angkatan, password, total_poin_pelanggaran, total_poin_prestasi, kategori_status) 
                        VALUES ('$nama', '$nit', '$jurusan', '$angkatan', '$password', 0, 0, 'Teladan')";
                if($conn->query($sql)){
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } else {
                $gagal++; // Hitung gagal jika duplikat
            }
        }
        fclose($file_open);
        
        $_SESSION['success'] = "Import Selesai! Berhasil: $berhasil data. Gagal/Duplikat/Dilewati: $gagal data.";
        header("Location: admin_users.php");
        exit();
    }
}
$_SESSION['error'] = "File tidak valid atau kosong.";
header("Location: admin_users.php");
exit();
?>
