<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

$query = "SELECT b.*, t.nama, t.nit, r.tanggal, r.deskripsi as deskripsi_pelanggaran, k.nama_pelanggaran, k.poin 
          FROM banding_hukuman b 
          JOIN data_taruna t ON b.id_taruna = t.id_taruna 
          JOIN riwayat_pelanggaran r ON b.id_riwayat_pelanggaran = r.id_riwayat 
          JOIN kategori_pelanggaran k ON r.id_kategori = k.id_kategori 
          ORDER BY b.waktu_pengajuan DESC";
$banding = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Manajemen Banding Hukuman</title>
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
            <div class="w-8 h-8 bg-yellow-600 rounded flex items-center justify-center">
                <i class="fas fa-balance-scale text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-yellow-500 tracking-wider">SIPELTAR</h1>
        </div>
        <div class="p-4">
            <nav class="space-y-2">
                <a href="admin_dashboard.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-satellite-dish w-5"></i> <span class="font-medium">Live Monitor</span>
                </a>
                <a href="admin_banding.php" class="flex items-center gap-3 bg-yellow-600/20 text-yellow-500 px-4 py-3 rounded-lg border border-yellow-500/30 transition-all">
                    <i class="fas fa-balance-scale w-5"></i> <span class="font-medium">Banding Hukuman</span>
                </a>
                <a href="admin_laporan.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-file-export w-5"></i> <span class="font-medium">Laporan & Rekap</span>
                </a>
            </nav>
        </div>
        <div class="mt-auto p-4 border-t border-gray-800">
            <a href="logout.php" class="block text-center w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm py-2 rounded-lg transition-all border border-red-500/20">Logout System</a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h2 class="text-2xl font-bold text-white mb-6"><i class="fas fa-gavel text-yellow-500 mr-2"></i> Sidang Banding / Pledoi Taruna</h2>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded mb-6 flex justify-between">
            <div><i class="fas fa-check-circle mr-2"></i><?php echo $_SESSION['success']; ?></div>
            <button onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <div class="glass-card rounded-2xl p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="text-xs uppercase bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">Waktu Pengajuan</th>
                            <th class="px-4 py-3">Taruna Pemohon</th>
                            <th class="px-4 py-3">Kasus Pelanggaran (Poin)</th>
                            <th class="px-4 py-3">Alasan Pembelaan (Pledoi)</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 rounded-tr-lg">Keputusan Komandan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        <?php if($banding->num_rows == 0): ?>
                            <tr><td colspan="6" class="text-center py-8">Belum ada pengajuan banding.</td></tr>
                        <?php else: while($row = $banding->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-4 py-4 text-xs"><?php echo $row['waktu_pengajuan']; ?></td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-white"><?php echo $row['nama']; ?></p>
                                    <p class="text-xs text-blue-400">NIT: <?php echo $row['nit']; ?></p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-red-400"><?php echo $row['nama_pelanggaran']; ?> (+<?php echo $row['poin']; ?>)</p>
                                    <p class="text-xs text-gray-500">"<?php echo $row['deskripsi_pelanggaran']; ?>"</p>
                                </td>
                                <td class="px-4 py-4 w-1/4">
                                    <p class="text-xs italic bg-gray-800 p-2 rounded border-l-2 border-yellow-500">"<?php echo $row['alasan_pembelaan']; ?>"</p>
                                </td>
                                <td class="px-4 py-4">
                                    <?php 
                                        $sc = 'text-yellow-400 bg-yellow-400/20';
                                        if($row['status_banding'] == 'Diterima') $sc = 'text-green-400 bg-green-400/20';
                                        if($row['status_banding'] == 'Ditolak') $sc = 'text-red-400 bg-red-400/20';
                                    ?>
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?php echo $sc; ?>"><?php echo $row['status_banding']; ?></span>
                                </td>
                                <td class="px-4 py-4">
                                    <?php if($row['status_banding'] == 'Menunggu'): ?>
                                    <form action="proses_admin_banding.php" method="POST" class="flex gap-2">
                                        <input type="hidden" name="id_banding" value="<?php echo $row['id_banding']; ?>">
                                        <input type="hidden" name="id_taruna" value="<?php echo $row['id_taruna']; ?>">
                                        <input type="hidden" name="id_riwayat" value="<?php echo $row['id_riwayat_pelanggaran']; ?>">
                                        <input type="hidden" name="poin" value="<?php echo $row['poin']; ?>">
                                        
                                        <button type="submit" name="keputusan" value="Diterima" class="bg-green-600 hover:bg-green-500 text-white text-[10px] font-bold px-2 py-1.5 rounded flex-1"><i class="fas fa-check mr-1"></i> Terima (Refund Poin)</button>
                                        <button type="submit" name="keputusan" value="Ditolak" class="bg-red-600 hover:bg-red-500 text-white text-[10px] font-bold px-2 py-1.5 rounded flex-1"><i class="fas fa-times mr-1"></i> Tolak Banding</button>
                                    </form>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-500">Sudah Diputuskan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
