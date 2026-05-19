<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// 1. Data Kategori Status (Pie Chart)
$status_q = $conn->query("SELECT kategori_status, COUNT(*) as jumlah FROM data_taruna GROUP BY kategori_status");
$status_data = [
    'labels' => [],
    'data' => []
];
while($r = $status_q->fetch_assoc()) {
    $status_data['labels'][] = $r['kategori_status'];
    $status_data['data'][] = $r['jumlah'];
}

// 2. Perbandingan Poin Pelanggaran vs Prestasi (Top 10 Taruna Aktif)
$bar_q = $conn->query("SELECT nama, total_poin_pelanggaran, total_poin_prestasi FROM data_taruna WHERE total_poin_pelanggaran > 0 OR total_poin_prestasi > 0 ORDER BY (total_poin_pelanggaran + total_poin_prestasi) DESC LIMIT 10");
$bar_data = [
    'labels' => [],
    'pelanggaran' => [],
    'prestasi' => []
];
while($r = $bar_q->fetch_assoc()) {
    $bar_data['labels'][] = explode(' ', trim($r['nama']))[0]; // Ambil nama depan saja agar rapi di chart
    $bar_data['pelanggaran'][] = $r['total_poin_pelanggaran'];
    $bar_data['prestasi'][] = $r['total_poin_prestasi'];
}

header('Content-Type: application/json');
echo json_encode([
    'kategori' => $status_data,
    'perbandingan' => $bar_data
]);
?>
