<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}
$id_taruna = $_SESSION['id_taruna'];

$riwayat = $conn->query("SELECT * FROM laporan_kamar WHERE id_taruna = $id_taruna ORDER BY waktu_lapor DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Laporan Kamar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="p-8">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-bed mr-2 text-indigo-500"></i> Laporan Kondisi Kamar</h2>
            <a href="taruna_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4"><i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4"><i class="fas fa-times mr-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Form Laporan -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Foto & Laporkan Kamar Pagi/Malam</h3>
                <form action="proses_kamar.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Nama Barak / Nomor Kamar</label>
                        <select name="nama_barak" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                            <option value="">-- Pilih Kamar --</option>
                            <optgroup label="Barak Echo Utara">
                                <option value="Echo Utara - Kamar 103">Kamar 103</option>
                                <option value="Echo Utara - Kamar 104">Kamar 104</option>
                                <option value="Echo Utara - Kamar 203">Kamar 203</option>
                                <option value="Echo Utara - Kamar 204">Kamar 204</option>
                            </optgroup>
                            <optgroup label="Barak Echo Selatan">
                                <option value="Echo Selatan - Kamar 101">Kamar 101</option>
                                <option value="Echo Selatan - Kamar 102">Kamar 102</option>
                                <option value="Echo Selatan - Kamar 201">Kamar 201</option>
                                <option value="Echo Selatan - Kamar 202">Kamar 202</option>
                            </optgroup>
                            <optgroup label="Barak Foxtrot">
                                <option value="Foxtrot - Kamar 101">Kamar 101 (Lt. 1)</option>
                                <option value="Foxtrot - Kamar 102">Kamar 102 (Lt. 1)</option>
                                <option value="Foxtrot - Kamar 103">Kamar 103 (Lt. 1)</option>
                                <option value="Foxtrot - Kamar 104">Kamar 104 (Lt. 1)</option>
                                <option value="Foxtrot - Kamar 105">Kamar 105 (Lt. 1)</option>
                                <option value="Foxtrot - Kamar 201">Kamar 201 (Lt. 2)</option>
                                <option value="Foxtrot - Kamar 202">Kamar 202 (Lt. 2)</option>
                                <option value="Foxtrot - Kamar 203">Kamar 203 (Lt. 2)</option>
                                <option value="Foxtrot - Kamar 204">Kamar 204 (Lt. 2)</option>
                                <option value="Foxtrot - Kamar 205">Kamar 205 (Lt. 2)</option>
                            </optgroup>
                            <optgroup label="Asrama Alpha">
                                <option value="Alpha - Kamar 101">Kamar 101</option>
                                <option value="Alpha - Kamar 102">Kamar 102</option>
                                <option value="Alpha - Kamar 201">Kamar 201</option>
                                <option value="Alpha - Kamar 202">Kamar 202</option>
                            </optgroup>
                            <optgroup label="Asrama Charlie">
                                <option value="Charlie - Kamar 101">Kamar 101</option>
                                <option value="Charlie - Kamar 102">Kamar 102</option>
                                <option value="Charlie - Kamar 201">Kamar 201</option>
                                <option value="Charlie - Kamar 202">Kamar 202</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Kondisi Saat Ini</label>
                        <select name="kondisi" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                            <option value="Sangat Bersih">Sangat Bersih (Siap Sidak)</option>
                            <option value="Bersih">Bersih (Standar)</option>
                            <option value="Kotor">Kotor / Berantakan</option>
                            <option value="Ada Kerusakan">Ada Kerusakan Fasilitas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Foto Kamar (Wajib)</label>
                        <!-- Capture image using mobile camera -->
                        <input type="file" name="foto" required accept="image/*" capture="environment" class="w-full bg-gray-800 border border-gray-700 rounded p-1 text-sm text-gray-400">
                        <p class="text-[10px] text-gray-500 mt-1">Gunakan kamera HP untuk memfoto seluruh ruangan kamar.</p>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded transition-all tracking-wider shadow-lg">
                        KIRIM LAPORAN KAMAR
                    </button>
                </form>
            </div>

            <!-- Riwayat -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Riwayat Laporan Anda</h3>
                <div class="space-y-4 overflow-y-auto max-h-[500px] pr-2 custom-scrollbar">
                    <?php if($riwayat->num_rows == 0): ?>
                        <p class="text-gray-500 text-sm">Belum ada laporan kamar.</p>
                    <?php else: while($r = $riwayat->fetch_assoc()): ?>
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 relative overflow-hidden group">
                            <?php if($r['foto']): ?>
                            <div class="absolute right-0 top-0 h-full w-24 opacity-20 group-hover:opacity-100 transition-opacity">
                                <img src="uploads/kamar/<?php echo $r['foto']; ?>" class="h-full w-full object-cover">
                            </div>
                            <?php endif; ?>
                            
                            <div class="relative z-10">
                                <h4 class="font-bold text-white text-sm mb-1"><?php echo $r['nama_barak']; ?></h4>
                                <?php 
                                    $sc = 'text-green-400 bg-green-400/20';
                                    if($r['kondisi'] == 'Kotor') $sc = 'text-orange-400 bg-orange-400/20';
                                    if($r['kondisi'] == 'Ada Kerusakan') $sc = 'text-red-400 bg-red-400/20';
                                ?>
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?php echo $sc; ?>"><?php echo $r['kondisi']; ?></span>
                                <p class="text-xs text-gray-400 mt-2"><i class="far fa-clock"></i> <?php echo $r['waktu_lapor']; ?></p>
                            </div>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
