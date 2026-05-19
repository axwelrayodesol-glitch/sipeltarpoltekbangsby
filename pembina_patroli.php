<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

$id_pembina = $_SESSION['id_user'];
$query = "SELECT * FROM histori_patroli ORDER BY waktu_patroli DESC LIMIT 20";
$patroli = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Histori Patroli</title>
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
                <a href="pembina_kamar.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-bed w-5"></i> <span class="font-medium">Monitoring Kamar</span>
                </a>
                <a href="pembina_menghadap.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-broadcast-tower w-5"></i> <span class="font-medium">Radar Menghadap</span>
                </a>
                <a href="pembina_patroli.php" class="flex items-center gap-3 bg-blue-600/20 text-blue-400 px-4 py-3 rounded-lg border border-blue-500/30 transition-all">
                    <i class="fas fa-shoe-prints w-5"></i> <span class="font-medium">Histori Patroli</span>
                </a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h2 class="text-2xl font-bold text-white mb-6"><i class="fas fa-shoe-prints text-blue-500 mr-2"></i> Log Histori Patroli Pengawasan</h2>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded mb-6 flex justify-between">
            <div><i class="fas fa-check-circle mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <button onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Form Input -->
            <div class="md:col-span-1 glass-card rounded-2xl p-6 h-fit sticky top-8">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2"><i class="fas fa-pen text-blue-400 mr-2"></i>Catat Patroli Baru</h3>
                <form action="proses_patroli.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Lokasi Patroli / Sidak</label>
                        <input type="text" name="lokasi" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="Misal: Barak B Lantai 2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Catatan / Temuan</label>
                        <textarea name="catatan" required rows="4" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="Misal: Ditemukan 2 Taruna belum tidur, kamar kotor..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded transition-all shadow-lg">
                        SIMPAN LOG PATROLI
                    </button>
                </form>
            </div>

            <!-- List Histori -->
            <div class="md:col-span-2">
                <div class="glass-card rounded-2xl p-6 relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-10 top-10 bottom-10 w-0.5 bg-gray-800 z-0"></div>
                    
                    <div class="space-y-6 relative z-10">
                        <?php if($patroli->num_rows == 0): ?>
                            <p class="text-gray-500 text-center py-8">Belum ada catatan histori patroli.</p>
                        <?php else: while($row = $patroli->fetch_assoc()): ?>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-blue-900 border-4 border-[#0b1120] flex items-center justify-center shrink-0 shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                                    <i class="fas fa-check text-blue-400 text-xs"></i>
                                </div>
                                <div class="flex-1 bg-gray-800/50 p-4 rounded-xl border border-gray-700/50 hover:border-gray-600 transition-colors">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-white"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> <?php echo $row['lokasi_patroli']; ?></h4>
                                        <span class="text-xs text-gray-500"><i class="far fa-clock"></i> <?php echo date('d M Y - H:i', strtotime($row['waktu_patroli'])); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-300">"<?php echo $row['catatan']; ?>"</p>
                                </div>
                            </div>
                        <?php endwhile; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
