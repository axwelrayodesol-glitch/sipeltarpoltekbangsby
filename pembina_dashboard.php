<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

// Get recent violations
$query = "SELECT r.*, t.nama as nama_taruna, k.nama_pelanggaran, k.tingkat_pelanggaran, k.poin 
          FROM riwayat_pelanggaran r 
          JOIN data_taruna t ON r.id_taruna = t.id_taruna 
          JOIN kategori_pelanggaran k ON r.id_kategori = k.id_kategori 
          WHERE r.id_pelapor = '{$_SESSION['id_user']}'
          ORDER BY r.waktu_lapor DESC LIMIT 5";
$recent = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Pembina Dashboard</title>
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
    <aside class="w-64 glass-card h-full hidden md:flex flex-col border-r border-gray-800">
        <div class="p-6 border-b border-gray-800">
            <h1 class="text-xl font-bold text-blue-400">SIPELTAR <span class="text-xs text-white bg-red-500 px-2 py-1 rounded">PEMBINA</span></h1>
        </div>
        <nav class="p-4 space-y-2">
            <a href="pembina_dashboard.php" class="flex items-center gap-3 bg-blue-600/20 text-blue-400 px-4 py-3 rounded-lg border border-blue-500/30">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>
            <a href="pembina_input_pelanggaran.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-triangle w-5"></i> Input Pelanggaran
            </a>
            <a href="pembina_input_prestasi.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg">
                <i class="fas fa-medal w-5"></i> Input Prestasi
            </a>
            <a href="pembina_approval_izin.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg">
                <i class="fas fa-check-double w-5"></i> Approval Izin
            </a>
            <a href="admin_peta.php" class="flex items-center gap-3 text-emerald-400 hover:bg-emerald-900/50 hover:text-emerald-300 px-4 py-3 rounded-lg transition-all border border-emerald-500/20">
                <i class="fas fa-map w-5"></i> Denah Asrama
            </a>
            <a href="pembina_kamar.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg">
                <i class="fas fa-bed w-5"></i> Monitor Kamar
            </a>
            <a href="pembina_menghadap.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg">
                <i class="fas fa-broadcast-tower w-5"></i> Radar Menghadap
            </a>
            <a href="pembina_patroli.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg">
                <i class="fas fa-shoe-prints w-5"></i> Histori Patroli
            </a>
            <a href="scan_wajah.php" target="_blank" class="flex items-center gap-3 bg-red-600/20 text-red-400 hover:bg-red-600/40 border border-red-500/30 px-4 py-3 rounded-lg mt-4 font-bold shadow-[0_0_10px_rgba(239,68,68,0.2)]">
                <i class="fas fa-video w-5"></i> Terminal AI Wajah
            </a>
        </nav>
        <div class="mt-auto p-4 border-t border-gray-800">
            <p class="text-sm font-bold text-white mb-2"><i class="fas fa-user-shield mr-2"></i><?php echo $_SESSION['nama']; ?></p>
            <a href="logout.php" class="block text-center w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 py-2 rounded-lg text-sm border border-red-500/20">Logout</a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang, <?php echo $_SESSION['nama']; ?></h2>
        <p class="text-gray-400 mb-8">Dashboard Pemantauan & Penindakan Kedisiplinan Taruna</p>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="pembina_input_pelanggaran.php" class="glass-card p-6 rounded-2xl flex items-center justify-between hover:border-red-500 hover:bg-gray-800 transition-all group">
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Laporkan Pelanggaran</h3>
                    <p class="text-sm text-gray-400">Kurangi poin disiplin</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-times text-red-500 text-xl"></i>
                </div>
            </a>
            <a href="pembina_input_prestasi.php" class="glass-card p-6 rounded-2xl flex items-center justify-between hover:border-yellow-500 hover:bg-gray-800 transition-all group border-t-4 border-transparent">
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Input Prestasi</h3>
                    <p class="text-sm text-gray-400">Berikan reward poin</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-medal text-yellow-500 text-xl"></i>
                </div>
            </a>
            <a href="pembina_approval_izin.php" class="glass-card p-6 rounded-2xl flex items-center justify-between hover:border-blue-500 hover:bg-gray-800 transition-all group border-t-4 border-transparent">
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Approval Izin</h3>
                    <p class="text-sm text-gray-400">Validasi pengajuan</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-envelope-open-text text-blue-500 text-xl"></i>
                </div>
            </a>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Laporan Terbaru Anda</h3>
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="text-xs uppercase bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Taruna</th>
                        <th class="px-4 py-3">Pelanggaran</th>
                        <th class="px-4 py-3">Poin</th>
                        <th class="px-4 py-3">Tingkat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    <?php if($recent->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center py-4">Belum ada data.</td></tr>
                    <?php else: while($row = $recent->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-800/30">
                            <td class="px-4 py-3"><?php echo $row['tanggal'] . ' ' . $row['jam']; ?></td>
                            <td class="px-4 py-3 text-white font-semibold"><?php echo $row['nama_taruna']; ?></td>
                            <td class="px-4 py-3"><?php echo $row['nama_pelanggaran']; ?></td>
                            <td class="px-4 py-3 text-red-400 font-bold">+<?php echo $row['poin']; ?></td>
                            <td class="px-4 py-3">
                                <?php 
                                    $color = 'bg-yellow-500';
                                    if($row['tingkat_pelanggaran'] == 'Berat') $color = 'bg-orange-500';
                                    if($row['tingkat_pelanggaran'] == 'Sangat Berat') $color = 'bg-red-500';
                                ?>
                                <span class="<?php echo $color; ?>/20 text-<?php echo str_replace('bg-', '', $color); ?> px-2 py-1 rounded text-xs">
                                    <?php echo $row['tingkat_pelanggaran']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
