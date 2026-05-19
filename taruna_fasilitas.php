<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}
$id_taruna = $_SESSION['id_taruna'];

$riwayat = $conn->query("SELECT * FROM laporan_fasilitas WHERE id_taruna = $id_taruna ORDER BY waktu_lapor DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Lapor Fasilitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="p-8">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-tools mr-2 text-yellow-500"></i> Laporan Kerusakan Fasilitas</h2>
            <a href="taruna_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4"><i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4"><i class="fas fa-times mr-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Form Pengajuan -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl h-fit relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-yellow-500 rounded-full blur-3xl opacity-10"></div>
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2 relative z-10">Buat Laporan Baru</h3>
                <form action="proses_fasilitas.php" method="POST" enctype="multipart/form-data" class="space-y-4 relative z-10">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Kategori Kerusakan</label>
                        <select name="jenis" required class="w-full bg-gray-800 border border-gray-600 rounded p-2 text-white">
                            <option value="Lampu/Kelistrikan">Lampu / Kelistrikan</option>
                            <option value="Fasilitas Asrama">Fasilitas Asrama (Kasur, Lemari, Toilet)</option>
                            <option value="Peralatan Hilang">Peralatan Hilang</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Deskripsi Detail</label>
                        <textarea name="deskripsi" required rows="4" class="w-full bg-gray-800 border border-gray-600 rounded p-2 text-white" placeholder="Sebutkan lokasi pasti (misal: Barak B, Kamar 4, Lampu depan putus)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Foto Bukti Kerusakan (Opsional tapi disarankan)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full bg-gray-800 border border-gray-600 rounded p-1 text-sm text-gray-400">
                    </div>
                    <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded transition-all">
                        Kirim Laporan
                    </button>
                </form>
            </div>

            <!-- Riwayat -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Riwayat Laporan Anda</h3>
                <div class="space-y-4 overflow-y-auto max-h-[500px] pr-2 custom-scrollbar">
                    <?php if($riwayat->num_rows == 0): ?>
                        <p class="text-gray-500 text-sm">Belum ada riwayat pelaporan fasilitas.</p>
                    <?php else: while($r = $riwayat->fetch_assoc()): ?>
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 relative overflow-hidden group">
                            <?php if($r['foto']): ?>
                            <div class="absolute right-0 top-0 h-full w-24 opacity-20 group-hover:opacity-100 transition-opacity">
                                <img src="uploads/<?php echo $r['foto']; ?>" class="h-full w-full object-cover">
                            </div>
                            <?php endif; ?>
                            
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-white text-sm"><?php echo $r['jenis']; ?></h4>
                                    <?php 
                                        $sc = 'text-yellow-400 bg-yellow-400/20';
                                        if($r['status'] == 'Diproses') $sc = 'text-blue-400 bg-blue-400/20';
                                        if($r['status'] == 'Selesai') $sc = 'text-green-400 bg-green-400/20';
                                    ?>
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?php echo $sc; ?>"><?php echo $r['status']; ?></span>
                                </div>
                                <p class="text-xs text-gray-400 mb-1"><i class="far fa-clock"></i> <?php echo $r['waktu_lapor']; ?></p>
                                <p class="text-sm text-gray-300 w-3/4">"<?php echo $r['deskripsi']; ?>"</p>
                            </div>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
