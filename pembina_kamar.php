<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

$query = "SELECT l.*, t.nama, t.nit FROM laporan_kamar l JOIN data_taruna t ON l.id_taruna = t.id_taruna ORDER BY l.waktu_lapor DESC";
$laporan = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Monitoring Kamar Taruna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
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
                <a href="pembina_kamar.php" class="flex items-center gap-3 bg-indigo-600/20 text-indigo-400 px-4 py-3 rounded-lg border border-indigo-500/30 transition-all">
                    <i class="fas fa-bed w-5"></i> <span class="font-medium">Monitoring Kamar</span>
                </a>
                <a href="pembina_menghadap.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-broadcast-tower w-5"></i> <span class="font-medium">Radar Menghadap</span>
                </a>
                <a href="pembina_patroli.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-shoe-prints w-5"></i> <span class="font-medium">Histori Patroli</span>
                </a>
            </nav>
        </div>
        <div class="mt-auto p-4 border-t border-gray-800">
            <a href="logout.php" class="block text-center w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm py-2 rounded-lg transition-all border border-red-500/20">Logout System</a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h2 class="text-2xl font-bold text-white mb-6"><i class="fas fa-bed text-indigo-500 mr-2"></i> Laporan Kondisi Kamar Taruna</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if($laporan->num_rows == 0): ?>
                <div class="col-span-full text-center py-8 text-gray-500">Belum ada laporan kamar yang masuk hari ini.</div>
            <?php else: while($row = $laporan->fetch_assoc()): ?>
                <div class="glass-card rounded-2xl overflow-hidden group">
                    <div class="h-48 overflow-hidden relative">
                        <?php if($row['foto']): ?>
                            <img src="uploads/kamar/<?php echo $row['foto']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <!-- Overlay detail -->
                            <a href="uploads/kamar/<?php echo $row['foto']; ?>" target="_blank" class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-white bg-indigo-600 px-3 py-1 rounded-full text-sm font-bold"><i class="fas fa-expand mr-1"></i> Perbesar</span>
                            </a>
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-800 flex items-center justify-center text-gray-500"><i class="fas fa-image text-3xl"></i></div>
                        <?php endif; ?>
                        
                        <!-- Badge Kondisi -->
                        <div class="absolute top-2 right-2">
                            <?php 
                                $sc = 'text-green-400 bg-green-900/80 border border-green-500/50';
                                if($row['kondisi'] == 'Kotor') $sc = 'text-orange-400 bg-orange-900/80 border border-orange-500/50';
                                if($row['kondisi'] == 'Ada Kerusakan') $sc = 'text-red-400 bg-red-900/80 border border-red-500/50';
                            ?>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase backdrop-blur-sm <?php echo $sc; ?> shadow-lg"><?php echo $row['kondisi']; ?></span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-white"><?php echo $row['nama_barak']; ?></h3>
                                <p class="text-xs text-indigo-400">Piket: <?php echo $row['nama']; ?></p>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-2 border-t border-gray-800 pt-2"><i class="far fa-clock"></i> Dilaporkan: <?php echo $row['waktu_lapor']; ?></p>
                    </div>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </main>
</body>
</html>
