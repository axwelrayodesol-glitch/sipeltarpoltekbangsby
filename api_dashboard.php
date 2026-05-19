<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$today = date('Y-m-d');

// Total Taruna
$total_q = $conn->query("SELECT COUNT(*) as total FROM data_taruna");
$total_taruna = $total_q->fetch_assoc()['total'];

// Hadir & Terlambat hari ini
$hadir_q = $conn->query("SELECT COUNT(*) as total FROM data_kedatangan WHERE DATE(waktu_datang) = '$today' AND status_kehadiran = 'hadir'");
$hadir = $hadir_q->fetch_assoc()['total'];

$terlambat_q = $conn->query("SELECT COUNT(*) as total FROM data_kedatangan WHERE DATE(waktu_datang) = '$today' AND status_kehadiran = 'terlambat'");
$terlambat = $terlambat_q->fetch_assoc()['total'];

$belum_datang = $total_taruna - ($hadir + $terlambat);

// Lokasi Check-in Hari Ini untuk Map
$map_q = $conn->query("SELECT k.latitude, k.longitude, t.nama, k.waktu_datang, k.status_kehadiran 
                       FROM data_kedatangan k 
                       JOIN data_taruna t ON k.id_taruna = t.id_taruna 
                       WHERE DATE(k.waktu_datang) = '$today' AND k.latitude IS NOT NULL");
$locations = [];
while($r = $map_q->fetch_assoc()) {
    $locations[] = $r;
}

// Aktivitas Terbaru (Log)
$log_q = $conn->query("SELECT * FROM riwayat_aktivitas ORDER BY waktu DESC LIMIT 5");
$activities = [];
while($r = $log_q->fetch_assoc()) {
    $activities[] = $r;
}

// Tabel Kedatangan Terbaru
$table_q = $conn->query("SELECT t.nama, t.nit, t.jurusan, k.waktu_datang, k.status_kehadiran, k.alamat_lokasi 
                         FROM data_kedatangan k 
                         JOIN data_taruna t ON k.id_taruna = t.id_taruna 
                         WHERE DATE(k.waktu_datang) = '$today' 
                         ORDER BY k.waktu_datang DESC LIMIT 10");
$recent_checkins = [];
while($r = $table_q->fetch_assoc()) {
    $recent_checkins[] = $r;
}

// Top Pelanggaran
$top_q = $conn->query("SELECT nama, nit, total_poin_pelanggaran, kategori_status FROM data_taruna WHERE total_poin_pelanggaran > 0 ORDER BY total_poin_pelanggaran DESC LIMIT 5");
$top_violations = [];
while($r = $top_q->fetch_assoc()) {
    $top_violations[] = $r;
}

// Trend pelanggaran 7 hari terakhir
$trend_q = $conn->query("
    SELECT DATE(waktu_lapor) as tgl, COUNT(*) as jumlah 
    FROM riwayat_pelanggaran 
    WHERE waktu_lapor >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
    GROUP BY DATE(waktu_lapor)
    ORDER BY tgl ASC
");
$trend_pelanggaran = [];
while($r = $trend_q->fetch_assoc()) {
    $trend_pelanggaran[] = $r;
}

// Distribusi Pelanggaran per Jurusan
$jurusan_q = $conn->query("
    SELECT t.jurusan, COUNT(r.id_riwayat) as jumlah 
    FROM riwayat_pelanggaran r
    JOIN data_taruna t ON r.id_taruna = t.id_taruna
    GROUP BY t.jurusan
");
$pie_jurusan = [];
while($r = $jurusan_q->fetch_assoc()) {
    $pie_jurusan[] = $r;
}

header('Content-Type: application/json');
echo json_encode([
    "stats" => [
        "total" => $total_taruna,
        "hadir" => $hadir,
        "terlambat" => $terlambat,
        "belum_datang" => $belum_datang
    ],
    "locations" => $locations,
    "activities" => $activities,
    "recent_checkins" => $recent_checkins,
    "top_violations" => $top_violations,
    "charts" => [
        "trend" => $trend_pelanggaran,
        "jurusan" => $pie_jurusan
    ]
]);
?>
