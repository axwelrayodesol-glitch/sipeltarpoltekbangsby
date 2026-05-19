<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}
$id_taruna = $_SESSION['id_taruna'];

$riwayat = $conn->query("SELECT * FROM laporan_menghadap WHERE id_junior = $id_taruna ORDER BY waktu_mulai DESC LIMIT 10");

// Cek apakah ada sesi menghadap yang belum selesai
$cek_aktif = $conn->query("SELECT * FROM laporan_menghadap WHERE id_junior = $id_taruna AND status_menghadap = 'Sedang Menghadap'");
$aktif = $cek_aktif->num_rows > 0 ? $cek_aktif->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Izin Menghadap Senior</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .radar-ping { animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite; }
        @keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
    </style>
</head>
<body class="p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-walking mr-2 text-orange-500"></i> Laporan Menghadap Senior</h2>
            <a href="taruna_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4"><i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if($aktif): ?>
            <!-- Mode Sedang Menghadap -->
            <div class="bg-orange-900/40 border border-orange-500/50 rounded-2xl p-8 text-center relative overflow-hidden shadow-[0_0_50px_rgba(249,115,22,0.2)]">
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-orange-500/20 rounded-full radar-ping"></div>
                
                <h3 class="text-2xl font-bold text-orange-400 mb-2 relative z-10"><i class="fas fa-broadcast-tower mr-2"></i> RADAR AKTIF</h3>
                <p class="text-gray-300 relative z-10">Anda saat ini tercatat sedang berada di kamar/lokasi senior.</p>
                <div class="bg-gray-900 inline-block px-6 py-4 rounded-xl mt-4 mb-6 border border-gray-700 relative z-10 text-left">
                    <p class="text-sm text-gray-400">Menghadap: <span class="text-white font-bold"><?php echo $aktif['nama_senior']; ?></span></p>
                    <p class="text-sm text-gray-400">Lokasi: <span class="text-white font-bold"><?php echo $aktif['lokasi_kamar']; ?></span></p>
                    <p class="text-sm text-gray-400">Keperluan: <span class="text-white italic">"<?php echo $aktif['keperluan']; ?>"</span></p>
                    <p class="text-xs text-orange-500 mt-2"><i class="far fa-clock"></i> Dimulai sejak: <?php echo date('H:i:s', strtotime($aktif['waktu_mulai'])); ?></p>
                </div>
                
                <form action="proses_menghadap.php" method="POST" class="relative z-10">
                    <input type="hidden" name="action" value="selesai">
                    <input type="hidden" name="id_menghadap" value="<?php echo $aktif['id_menghadap']; ?>">
                    <button type="submit" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-transform hover:scale-105">
                        <i class="fas fa-check-double mr-2"></i> Selesai Menghadap & Kembali
                    </button>
                </form>
            </div>
        <?php else: ?>
            <!-- Form Lapor Baru -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl">
                <div class="bg-orange-500/10 border-l-4 border-orange-500 text-orange-400 p-4 rounded mb-6 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <b>Wajib Lapor:</b> Sebelum Anda berangkat menemui Senior ke kamarnya, Anda <b>wajib</b> mengisi form ini agar Pengasuh mengetahui keberadaan Anda secara *real-time*. Jika urusan sudah selesai, jangan lupa kembali ke halaman ini untuk menekan tombol <b>Selesai</b>.
                </div>
                
                <form action="proses_menghadap.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="mulai">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Nama Senior yang Memanggil</label>
                            <input type="text" name="nama_senior" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="Misal: Sermatutar Budi">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Lokasi / Nomor Kamar Senior</label>
                            <input type="text" name="lokasi_kamar" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="Misal: Barak A Kamar 2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Keperluan Menghadap</label>
                        <textarea name="keperluan" required rows="2" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="Misal: Laporan korve, pembinaan, dll"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded transition-all shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i> KIRIM LAPORAN SEKARANG
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Riwayat -->
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-400 mb-4 border-b border-gray-800 pb-2">Riwayat Menghadap Senior</h3>
            <div class="space-y-3">
                <?php while($r = $riwayat->fetch_assoc()): ?>
                    <div class="bg-gray-800/50 p-4 rounded border border-gray-700 flex justify-between items-center opacity-70 hover:opacity-100 transition-opacity">
                        <div>
                            <p class="font-bold text-white text-sm">Menghadap: <?php echo $r['nama_senior']; ?> <span class="text-xs text-gray-500 font-normal">(<?php echo $r['lokasi_kamar']; ?>)</span></p>
                            <p class="text-xs text-gray-400">Mulai: <?php echo date('H:i', strtotime($r['waktu_mulai'])); ?> | Selesai: <?php echo $r['waktu_selesai'] ? date('H:i', strtotime($r['waktu_selesai'])) : '-'; ?></p>
                            <p class="text-[11px] text-gray-500 mt-1 italic">"<?php echo $r['keperluan']; ?>"</p>
                        </div>
                        <div>
                            <?php if($r['status_menghadap'] == 'Selesai'): ?>
                                <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-1 rounded border border-green-500/50">SELESAI</span>
                            <?php else: ?>
                                <span class="text-[10px] bg-orange-500/20 text-orange-400 px-2 py-1 rounded border border-orange-500/50 animate-pulse">AKTIF</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
