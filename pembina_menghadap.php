<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

$query_aktif = "SELECT m.*, t.nama as nama_junior, t.nit FROM laporan_menghadap m JOIN data_taruna t ON m.id_junior = t.id_taruna WHERE m.status_menghadap = 'Sedang Menghadap' ORDER BY m.waktu_mulai DESC";
$aktif = $conn->query($query_aktif);

$query_selesai = "SELECT m.*, t.nama as nama_junior FROM laporan_menghadap m JOIN data_taruna t ON m.id_junior = t.id_taruna WHERE m.status_menghadap = 'Selesai' ORDER BY m.waktu_selesai DESC LIMIT 10";
$selesai = $conn->query($query_selesai);

// Hitung durasi
function getDurasi($waktu_mulai) {
    $now = new DateTime();
    $start = new DateTime($waktu_mulai);
    $diff = $start->diff($now);
    
    $str = '';
    if($diff->h > 0) $str .= $diff->h . " jam ";
    $str .= $diff->i . " mnt";
    
    // Warning jika lebih dari 1 jam
    $is_warning = ($diff->h > 0);
    return ['teks' => $str, 'warning' => $is_warning];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Radar Menghadap Senior</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .radar-ping { animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite; }
        @keyframes ping { 75%, 100% { transform: scale(1.5); opacity: 0; } }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 glass-card h-full flex flex-col hidden md:flex border-r border-gray-800">
        <div class="p-6 border-b border-gray-800 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                <i class="fas fa-user-shield text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-blue-400 tracking-wider">SIPELTAR</h1>
        </div>
        <div class="p-4">
            <nav class="space-y-2">
                <a href="pembina_dashboard.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-home w-5"></i> <span class="font-medium">Dashboard Utama</span>
                </a>
                <a href="pembina_kamar.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-bed w-5"></i> <span class="font-medium">Monitoring Kamar</span>
                </a>
                <a href="pembina_menghadap.php" class="flex items-center gap-3 bg-orange-600/20 text-orange-400 px-4 py-3 rounded-lg border border-orange-500/30 transition-all">
                    <i class="fas fa-broadcast-tower w-5"></i> <span class="font-medium">Radar Menghadap</span>
                </a>
                <a href="pembina_patroli.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-shoe-prints w-5"></i> <span class="font-medium">Histori Patroli</span>
                </a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-satellite-dish text-orange-500 mr-2"></i> Live Radar: Taruna Menghadap Senior</h2>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-orange-500 rounded-full radar-ping"></div>
                <span class="text-orange-400 text-sm font-bold tracking-wider">SYSTEM ONLINE</span>
            </div>
        </div>

        <!-- Grid Radar Aktif -->
        <h3 class="text-lg font-bold text-gray-400 mb-4 border-b border-gray-800 pb-2">Koneksi Radar Aktif Saat Ini</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
            <?php if($aktif->num_rows == 0): ?>
                <div class="col-span-2 glass-card p-8 rounded-2xl text-center border-dashed border-2 border-gray-700">
                    <i class="fas fa-shield-alt text-4xl text-gray-600 mb-3"></i>
                    <p class="text-gray-400 font-bold">KONDISI AMAN</p>
                    <p class="text-sm text-gray-500">Tidak ada Taruna yang terpantau sedang berada di kamar Senior.</p>
                </div>
            <?php else: while($row = $aktif->fetch_assoc()): 
                $durasi = getDurasi($row['waktu_mulai']);
            ?>
                <div class="glass-card p-6 rounded-2xl relative overflow-hidden <?php echo $durasi['warning'] ? 'border border-red-500/50 shadow-[0_0_30px_rgba(239,68,68,0.1)]' : 'border border-orange-500/20'; ?>">
                    
                    <?php if($durasi['warning']): ?>
                    <div class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg z-10 animate-pulse">
                        <i class="fas fa-exclamation-triangle"></i> OVERTIME
                    </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center border border-gray-600">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-sm"><?php echo $row['nama_junior']; ?></h4>
                                    <p class="text-[10px] text-gray-500">Junior (Pemohon)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-1 flex flex-col items-center justify-center px-4 relative">
                            <div class="h-0.5 w-full bg-gray-700 absolute top-1/2 -z-10"></div>
                            <div class="bg-gray-900 border border-orange-500 px-3 py-1 rounded-full text-xs text-orange-400 font-bold flex items-center gap-2">
                                <i class="fas fa-arrow-right"></i>
                                <span><?php echo $durasi['teks']; ?></span>
                            </div>
                        </div>

                        <div class="flex-1 text-right">
                            <div class="flex items-center gap-3 justify-end mb-3">
                                <div class="text-right">
                                    <h4 class="font-bold text-white text-sm"><?php echo $row['nama_senior']; ?></h4>
                                    <p class="text-[10px] text-gray-500">Senior (Tujuan)</p>
                                </div>
                                <div class="w-10 h-10 bg-orange-900/50 rounded-full flex items-center justify-center border border-orange-500/50">
                                    <i class="fas fa-user-graduate text-orange-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800/50 mt-4 p-4 rounded-xl border border-gray-700/50 text-sm">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <p class="text-[10px] text-gray-500 mb-0.5">LOKASI / KAMAR</p>
                                <p class="font-bold text-white"><i class="fas fa-map-marker-alt text-red-400 mr-1"></i> <?php echo $row['lokasi_kamar']; ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 mb-0.5">KEPERLUAN</p>
                                <p class="italic text-gray-300 truncate" title="<?php echo $row['keperluan']; ?>">"<?php echo $row['keperluan']; ?>"</p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($durasi['warning']): ?>
                    <div class="mt-4 flex gap-2">
                        <a href="pembina_input_pelanggaran.php" class="bg-red-600/20 hover:bg-red-600/40 text-red-400 text-xs font-bold px-4 py-2 rounded-lg border border-red-500/30 transition-colors w-full text-center">
                            <i class="fas fa-gavel mr-1"></i> Eksekusi Pelanggaran
                        </a>
                        <a href="pembina_patroli.php" class="bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 text-xs font-bold px-4 py-2 rounded-lg border border-blue-500/30 transition-colors w-full text-center">
                            <i class="fas fa-shoe-prints mr-1"></i> Mulai Patroli
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; endif; ?>
        </div>
        
        <!-- Riwayat Selesai -->
        <h3 class="text-lg font-bold text-gray-400 mb-4 border-b border-gray-800 pb-2">Riwayat Terselesaikan (Terbaru)</h3>
        <div class="glass-card rounded-xl p-4">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="text-xs uppercase bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-3">Junior</th>
                        <th class="px-4 py-3">Senior</th>
                        <th class="px-4 py-3">Waktu Mulai</th>
                        <th class="px-4 py-3">Waktu Selesai</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    <?php while($s = $selesai->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-800/30">
                        <td class="px-4 py-2 font-bold text-white"><?php echo $s['nama_junior']; ?></td>
                        <td class="px-4 py-2 text-gray-300"><?php echo $s['nama_senior']; ?></td>
                        <td class="px-4 py-2 text-xs"><?php echo date('H:i:s', strtotime($s['waktu_mulai'])); ?></td>
                        <td class="px-4 py-2 text-xs"><?php echo date('H:i:s', strtotime($s['waktu_selesai'])); ?></td>
                        <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/20 text-green-400">SELESAI</span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
