<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}
$id_taruna = $_SESSION['id_taruna'];

// Ambil riwayat pelanggaran Taruna
$query = "SELECT r.*, k.nama_pelanggaran, k.poin 
          FROM riwayat_pelanggaran r 
          JOIN kategori_pelanggaran k ON r.id_kategori = k.id_kategori 
          WHERE r.id_taruna = $id_taruna 
          ORDER BY r.waktu_input DESC";
$pelanggaran = $conn->query($query);

// Cek status banding masing-masing pelanggaran
function getStatusBanding($id_riwayat, $conn) {
    $cek = $conn->query("SELECT status_banding FROM banding_hukuman WHERE id_riwayat_pelanggaran = $id_riwayat");
    if($cek->num_rows > 0) return $cek->fetch_assoc()['status_banding'];
    return null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Banding Hukuman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="p-8">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-balance-scale mr-2 text-yellow-500"></i> Riwayat Pelanggaran & Banding</h2>
            <a href="taruna_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4 border border-green-500"><i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4 border border-red-500"><i class="fas fa-times mr-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl">
            <div class="space-y-4">
                <?php if($pelanggaran->num_rows == 0): ?>
                    <p class="text-gray-500 text-center py-8">Anda belum memiliki catatan pelanggaran. Pertahankan!</p>
                <?php else: while($p = $pelanggaran->fetch_assoc()): 
                    $status_banding = getStatusBanding($p['id_riwayat'], $conn);
                ?>
                    <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex-1">
                            <h4 class="font-bold text-red-400 text-lg"><?php echo $p['nama_pelanggaran']; ?> <span class="bg-red-900/50 text-red-300 px-2 py-0.5 rounded text-xs ml-2">+<?php echo $p['poin']; ?> Poin</span></h4>
                            <p class="text-xs text-gray-400 mb-2"><i class="far fa-calendar"></i> Kejadian: <?php echo $p['tanggal']; ?> | <i class="fas fa-map-marker-alt"></i> <?php echo $p['lokasi_kejadian']; ?></p>
                            <p class="text-sm text-gray-300 italic border-l-2 border-gray-600 pl-2">"<?php echo $p['deskripsi']; ?>"</p>
                        </div>
                        
                        <div class="md:w-64 w-full">
                            <?php if(!$status_banding): ?>
                                <!-- Form Banding -->
                                <form action="proses_banding.php" method="POST" class="flex flex-col gap-2">
                                    <input type="hidden" name="id_riwayat" value="<?php echo $p['id_riwayat']; ?>">
                                    <textarea name="alasan" required rows="2" class="w-full bg-gray-900 border border-gray-600 rounded p-2 text-xs text-white" placeholder="Alasan pembelaan Anda..."></textarea>
                                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-white font-bold py-1 px-4 rounded text-xs transition-all w-full">
                                        <i class="fas fa-gavel mr-1"></i> Ajukan Banding
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- Status Banding -->
                                <div class="bg-gray-900 border border-gray-700 p-3 rounded text-center">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">Status Banding:</p>
                                    <?php if($status_banding == 'Menunggu'): ?>
                                        <p class="font-bold text-yellow-500 text-sm"><i class="fas fa-clock mr-1"></i> Menunggu Keputusan</p>
                                    <?php elseif($status_banding == 'Diterima'): ?>
                                        <p class="font-bold text-green-500 text-sm"><i class="fas fa-check-circle mr-1"></i> Diterima (Poin Dikembalikan)</p>
                                    <?php elseif($status_banding == 'Ditolak'): ?>
                                        <p class="font-bold text-red-500 text-sm"><i class="fas fa-times-circle mr-1"></i> Ditolak</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
