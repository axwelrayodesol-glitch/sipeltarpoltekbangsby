<?php
session_start();
require 'config.php';

// Proteksi: Hanya Pembina, Admin, atau Petugas yang boleh melakukan Scan
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'pembina' && $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    $_SESSION['error'] = "Akses Ditolak: Hanya Petugas/Pembina yang dapat menggunakan Scanner ini.";
    header("Location: index.php");
    exit();
}

if (!isset($_GET['nit']) || empty($_GET['nit'])) {
    die("Error: NIT tidak ditemukan pada QR Code.");
}

$nit = $conn->real_escape_string($_GET['nit']);

// Cari data Taruna
$query_taruna = "SELECT id_taruna, nit, nama, jurusan, angkatan FROM data_taruna WHERE nit = '$nit'";
$res_taruna = $conn->query($query_taruna);

if ($res_taruna->num_rows == 0) {
    die("Error: Taruna dengan NIT $nit tidak ditemukan di database.");
}

$taruna = $res_taruna->fetch_assoc();
$id_taruna = $taruna['id_taruna'];
$waktu_sekarang = date('Y-m-d H:i:s');
$tanggal_sekarang = date('Y-m-d');
$id_pembina = $_SESSION['id_user'];
$nama_petugas = $_SESSION['nama'];

// 1. Cek apakah ada Izin Aktif (Status: Disetujui Pembina)
$query_izin = "SELECT id_izin, jenis_izin FROM pengajuan_izin WHERE id_taruna = $id_taruna AND status_approval = 'Disetujui Pembina'";
$res_izin = $conn->query($query_izin);
$pesan_izin = "Tidak ada izin aktif yang perlu diselesaikan.";

if ($res_izin->num_rows > 0) {
    // Selesaikan semua izin aktif
    while($izin = $res_izin->fetch_assoc()) {
        $id_izin = $izin['id_izin'];
        $conn->query("UPDATE pengajuan_izin SET status_approval = 'Selesai / Kembali' WHERE id_izin = $id_izin");
    }
    $pesan_izin = "Status Izin (LLWE/Pesiar) telah ditutup & diselesaikan.";
}

// 2. Lakukan Check-In (Absensi) Otomatis
$query_absen = "SELECT * FROM data_kedatangan WHERE id_taruna = $id_taruna AND DATE(waktu_datang) = '$tanggal_sekarang'";
$res_absen = $conn->query($query_absen);
$pesan_absen = "Taruna sudah melakukan Check-In hari ini.";

if ($res_absen->num_rows == 0) {
    // Insert Absen Baru
    // Anggap default status hadir (Bisa disesuaikan telat atau tidak berdasarkan jam)
    $conn->query("INSERT INTO data_kedatangan (id_taruna, waktu_datang, status_kehadiran) VALUES ($id_taruna, '$waktu_sekarang', 'hadir')");
    $pesan_absen = "Tercatat HADIR (Check-In) pada $waktu_sekarang.";
}

// Catat di Log
$conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('Petugas $nama_petugas melakukan Scan QR Lapor Kembali untuk Taruna NIT $nit', '$waktu_sekarang')");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Hasil Scan QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .success-pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 70% { box-shadow: 0 0 0 20px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
    </style>
</head>
<body class="p-4 md:p-8 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-gray-900 border border-green-500 rounded-3xl p-8 shadow-[0_0_50px_rgba(34,197,94,0.15)] text-center relative overflow-hidden">
        
        <!-- Decoration -->
        <div class="absolute top-0 left-0 w-full h-2 bg-green-500"></div>
        
        <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 success-pulse text-white text-4xl shadow-xl shadow-green-500/50">
            <i class="fas fa-check"></i>
        </div>
        
        <h2 class="text-3xl font-bold text-white mb-2">SCAN SUKSES!</h2>
        <p class="text-green-400 font-semibold mb-6 tracking-widest uppercase">TARUNA TELAH KEMBALI</p>
        
        <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 text-left mb-6 space-y-3">
            <div>
                <p class="text-xs text-gray-500">NAMA LENGKAP</p>
                <p class="text-lg font-bold text-white"><?php echo $taruna['nama']; ?></p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">NIT</p>
                    <p class="font-bold text-blue-400"><?php echo $taruna['nit']; ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">JURUSAN</p>
                    <p class="font-bold text-gray-300"><?php echo $taruna['jurusan']; ?></p>
                </div>
            </div>
        </div>

        <div class="space-y-2 text-left mb-8">
            <div class="flex items-start gap-3 bg-gray-800/50 p-3 rounded-lg border border-gray-700/50">
                <i class="fas fa-door-open text-blue-400 mt-0.5"></i>
                <div>
                    <p class="text-xs font-bold text-gray-300">STATUS IZIN</p>
                    <p class="text-xs text-gray-400"><?php echo $pesan_izin; ?></p>
                </div>
            </div>
            <div class="flex items-start gap-3 bg-gray-800/50 p-3 rounded-lg border border-gray-700/50">
                <i class="fas fa-fingerprint text-green-400 mt-0.5"></i>
                <div>
                    <p class="text-xs font-bold text-gray-300">ABSENSI KESATRIAN</p>
                    <p class="text-xs text-gray-400"><?php echo $pesan_absen; ?></p>
                </div>
            </div>
        </div>
        
        <?php 
            $dashboard_link = "admin_dashboard.php";
            if($_SESSION['role'] == 'pembina') $dashboard_link = "pembina_dashboard.php";
        ?>
        <a href="<?php echo $dashboard_link; ?>" class="block w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 rounded-xl transition-all border border-gray-600">
            KEMBALI KE DASHBOARD
        </a>
    </div>
</body>
</html>
