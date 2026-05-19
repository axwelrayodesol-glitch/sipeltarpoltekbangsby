<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

// Ambil semua laporan
$query = "SELECT l.*, t.nama as nama_taruna, t.nit, t.angkatan 
          FROM laporan_fasilitas l 
          JOIN data_taruna t ON l.id_taruna = t.id_taruna 
          ORDER BY l.waktu_lapor DESC";
$laporan = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Manajemen Laporan Fasilitas</title>
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
                <i class="fas fa-shield-alt text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-blue-400 tracking-wider">SIPELTAR</h1>
        </div>
        <div class="p-4">
            <nav class="space-y-2">
                <a href="admin_dashboard.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-satellite-dish w-5"></i> <span class="font-medium">Live Monitor</span>
                </a>
                <a href="admin_users.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-users w-5"></i> <span class="font-medium">Manajemen User</span>
                </a>
                <a href="admin_fasilitas.php" class="flex items-center gap-3 bg-blue-600/20 text-blue-400 px-4 py-3 rounded-lg border border-blue-500/30 transition-all">
                    <i class="fas fa-tools w-5"></i> <span class="font-medium">Laporan Fasilitas</span>
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
        <h2 class="text-2xl font-bold text-white mb-6"><i class="fas fa-wrench text-yellow-500 mr-2"></i> Manajemen Laporan Kerusakan Fasilitas</h2>

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
                            <th class="px-4 py-3 rounded-tl-lg">Waktu Lapor</th>
                            <th class="px-4 py-3">Pelapor (Taruna)</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Deskripsi Kerusakan</th>
                            <th class="px-4 py-3">Bukti</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 rounded-tr-lg">Aksi (Update)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        <?php if($laporan->num_rows == 0): ?>
                            <tr><td colspan="7" class="text-center py-8">Belum ada laporan kerusakan.</td></tr>
                        <?php else: while($row = $laporan->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-4 py-4 text-xs"><?php echo $row['waktu_lapor']; ?></td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-white"><?php echo $row['nama_taruna']; ?></p>
                                    <p class="text-xs text-blue-400">NIT: <?php echo $row['nit']; ?></p>
                                </td>
                                <td class="px-4 py-4 font-semibold"><?php echo $row['jenis']; ?></td>
                                <td class="px-4 py-4 w-1/4">
                                    <p class="text-xs italic bg-gray-800 p-2 rounded">"<?php echo $row['deskripsi']; ?>"</p>
                                </td>
                                <td class="px-4 py-4">
                                    <?php if($row['foto']): ?>
                                        <a href="uploads/<?php echo $row['foto']; ?>" target="_blank" class="text-blue-400 hover:underline text-xs"><i class="fas fa-image"></i> Lihat Foto</a>
                                    <?php else: ?>
                                        <span class="text-gray-600 text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <?php 
                                        $sc = 'text-yellow-400 bg-yellow-400/20';
                                        if($row['status'] == 'Diproses') $sc = 'text-blue-400 bg-blue-400/20';
                                        if($row['status'] == 'Selesai') $sc = 'text-green-400 bg-green-400/20';
                                    ?>
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?php echo $sc; ?>"><?php echo $row['status']; ?></span>
                                </td>
                                <td class="px-4 py-4">
                                    <form action="proses_admin_fasilitas.php" method="POST" class="flex flex-col gap-1">
                                        <input type="hidden" name="id_laporan" value="<?php echo $row['id_laporan']; ?>">
                                        <select name="status" class="bg-gray-700 text-white text-xs p-1 rounded border border-gray-600 outline-none w-full mb-1">
                                            <option value="Menunggu" <?php if($row['status']=='Menunggu') echo 'selected'; ?>>Menunggu</option>
                                            <option value="Diproses" <?php if($row['status']=='Diproses') echo 'selected'; ?>>Sedang Diproses</option>
                                            <option value="Selesai" <?php if($row['status']=='Selesai') echo 'selected'; ?>>Selesai Diperbaiki</option>
                                        </select>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-xs px-2 py-1 rounded w-full">Update</button>
                                    </form>
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
