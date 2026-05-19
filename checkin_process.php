<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_taruna'])) {
    $id_taruna = $_SESSION['id_taruna'];
    $lat = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $lng = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    
    // Validasi lokasi wajib ada
    if (empty($lat) || empty($lng)) {
        $_SESSION['error'] = "Gagal mengambil lokasi GPS. Silakan coba lagi.";
        header("Location: taruna_dashboard.php");
        exit();
    }

    $waktu_datang = date('Y-m-d H:i:s');
    $jam = date('H:i:s');
    
    // Aturan: Masuk sebelum 07:00 dianggap Hadir, setelahnya Terlambat
    $batas_hadir = "07:00:00";
    $status = ($jam <= $batas_hadir) ? 'hadir' : 'terlambat';

    // Reverse Geocoding Sederhana via Nominatim (Opsional, tapi diminta "Menampilkan alamat")
    // Note: Karena PHP berjalan di backend, kita panggil API Nominatim. 
    // Nominatim butuh User-Agent yang valid.
    $alamat_lokasi = "Lokasi tidak diketahui (Tidak dapat resolve map)";
    
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: SIPELTAR/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== FALSE) {
        $data = json_decode($response, true);
        if (isset($data['display_name'])) {
            $alamat_lokasi = $conn->real_escape_string($data['display_name']);
        }
    }

    $sql = "INSERT INTO data_kedatangan (id_taruna, waktu_datang, status_kehadiran, latitude, longitude, alamat_lokasi) 
            VALUES ('$id_taruna', '$waktu_datang', '$status', '$lat', '$lng', '$alamat_lokasi')";
    
    if ($conn->query($sql) === TRUE) {
        $conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('Taruna ".$_SESSION['nama']." melakukan check-in ($status)', '$waktu_datang')");
        $_SESSION['success'] = "Check-in berhasil! Status: " . strtoupper($status);
    } else {
        $_SESSION['error'] = "Terjadi kesalahan database: " . $conn->error;
    }

    header("Location: taruna_dashboard.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
