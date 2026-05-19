<?php
require 'config.php';

$jurusans = [
    "TEKNIK LISTRIK BANDARA",
    "TEKNIK NAVIGASI UDARA",
    "TEKNIK PESAWAT UDARA",
    "LALU LINTAS UDARA",
    "MANAJEMEN TRANSPORTASI UDARA",
    "TEKNIK BANGUNAN DAN LANDASAN"
];

// Ambil semua id taruna yang ada di database saat ini
$result = $conn->query("SELECT id_taruna FROM data_taruna");
$count = 0;

if($result) {
    while($row = $result->fetch_assoc()) {
        $id = $row['id_taruna'];
        // Pilih jurusan kebandarudaraan secara acak dari daftar di atas
        $random_jurusan = $jurusans[array_rand($jurusans)];
        
        // Timpa data jurusan yang lama (Teknika/Nautika)
        $conn->query("UPDATE data_taruna SET jurusan = '$random_jurusan' WHERE id_taruna = $id");
        $count++;
    }
}

echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
echo "<h1 style='color:green;'>⚙️ DATABASE BERHASIL DIPERBARUI!</h1>";
echo "<p>Sebanyak <b>$count</b> data Taruna lama telah diganti jurusannya secara acak ke jurusan Penerbangan/Bandara.</p>";
echo "<br><br><a href='admin_dashboard.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Kembali ke Dashboard</a>";
echo "</div>";
?>
