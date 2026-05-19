<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

$taruna = $conn->query("SELECT * FROM data_taruna ORDER BY angkatan DESC, nama ASC");
$users = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY role ASC, nama_lengkap ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Manajemen User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5); }
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
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-4 font-semibold">Menu Utama</p>
            <nav class="space-y-2">
                <a href="admin_dashboard.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-satellite-dish w-5"></i>
                    <span class="font-medium">Live Monitor</span>
                </a>
                <a href="admin_users.php" class="flex items-center gap-3 bg-blue-600/20 text-blue-400 px-4 py-3 rounded-lg border border-blue-500/30 transition-all">
                    <i class="fas fa-users w-5"></i>
                    <span class="font-medium">Manajemen User</span>
                </a>
                <a href="admin_laporan.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-file-export w-5"></i>
                    <span class="font-medium">Laporan & Rekap</span>
                </a>
            </nav>
        </div>
        <div class="mt-auto p-4 border-t border-gray-800">
            <a href="logout.php" class="block text-center w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm py-2 rounded-lg transition-all border border-red-500/20">Logout System</a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h2 class="text-2xl font-bold text-white mb-6"><i class="fas fa-users-cog text-blue-500 mr-2"></i> Manajemen User & Taruna</h2>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded mb-6 flex justify-between">
            <div><i class="fas fa-check-circle mr-2"></i><?php echo $_SESSION['success']; ?></div>
            <button onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <?php unset($_SESSION['success']); endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded mb-6 flex justify-between">
            <div><i class="fas fa-exclamation-triangle mr-2"></i><?php echo $_SESSION['error']; ?></div>
            <button onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            
            <!-- TARUNA SECTION -->
            <div>
                <div class="glass-card rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-700 pb-2">Tambah Taruna Baru</h3>
                    <form action="proses_tambah_user.php" method="POST" class="space-y-4">
                        <input type="hidden" name="tipe" value="taruna">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">NIT (Nomor Induk)</label>
                                <input type="text" name="nit" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Jurusan</label>
                                <select name="jurusan" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white text-[10px]">
                                    <option value="TEKNIK LISTRIK BANDARA">TEKNIK LISTRIK BANDARA</option>
                                    <option value="TEKNIK NAVIGASI UDARA">TEKNIK NAVIGASI UDARA</option>
                                    <option value="TEKNIK PESAWAT UDARA">TEKNIK PESAWAT UDARA</option>
                                    <option value="LALU LINTAS UDARA">LALU LINTAS UDARA</option>
                                    <option value="MANAJEMEN TRANSPORTASI UDARA">MANAJEMEN TRANSPORTASI UDARA</option>
                                    <option value="TEKNIK BANGUNAN DAN LANDASAN">TEKNIK BANGUNAN DAN LANDASAN</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Angkatan</label>
                                <input type="text" name="angkatan" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs text-gray-400 mb-1">Password (Otomatis MD5)</label>
                                <input type="password" name="password" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="••••••••">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 rounded">Tambahkan Taruna</button>
                    </form>
                    
                    <div class="mt-6 pt-4 border-t border-gray-700">
                        <h4 class="text-sm font-bold text-white mb-2"><i class="fas fa-file-csv text-green-500 mr-2"></i>Import Massal (Dari Google Sheets / CSV)</h4>
                        <p class="text-xs text-gray-400 mb-3">Download Spreadsheet Anda sebagai file <b>.csv</b>, lalu upload ke sini. Format kolom: Nama, NIT, Jurusan, Angkatan.</p>
                        <form action="import_taruna.php" method="POST" enctype="multipart/form-data" class="flex gap-2">
                            <input type="file" name="file_csv" accept=".csv" required class="flex-1 bg-gray-800 border border-gray-700 rounded p-1 text-sm text-gray-400">
                            <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold px-4 py-1 rounded text-sm transition-colors">Import CSV</button>
                        </form>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Daftar Taruna (Total: <?php echo $taruna->num_rows; ?>)</h3>
                    <div class="overflow-y-auto max-h-96 pr-2">
                        <?php while($t = $taruna->fetch_assoc()): ?>
                            <div class="bg-gray-800/50 border border-gray-700 p-3 rounded-lg mb-3 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-white text-sm"><?php echo $t['nama']; ?> <span class="text-blue-400 text-xs">(<?php echo $t['nit']; ?>)</span></p>
                                    <p class="text-xs text-gray-400"><?php echo $t['jurusan']; ?> | Angkatan <?php echo $t['angkatan']; ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-1 rounded">Pelanggaran: <?php echo $t['total_poin_pelanggaran']; ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- STAFF/PEMBINA SECTION -->
            <div>
                <div class="glass-card rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-700 pb-2">Tambah Staff / Pembina Baru</h3>
                    <form action="proses_tambah_user.php" method="POST" class="space-y-4">
                        <input type="hidden" name="tipe" value="staff">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Role / Jabatan</label>
                                <select name="role" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                                    <option value="pembina">Pembina / Pengasuh</option>
                                    <option value="komandan">Komandan</option>
                                    <option value="instruktur">Instruktur</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Username Login</label>
                                <input type="text" name="username" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Password (Otomatis MD5)</label>
                                <input type="password" name="password" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="••••••••">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-2 rounded">Tambahkan Staff</button>
                    </form>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Daftar Staff & Pembina (Total: <?php echo $users->num_rows; ?>)</h3>
                    <div class="overflow-y-auto max-h-96 pr-2">
                        <?php while($u = $users->fetch_assoc()): ?>
                            <div class="bg-gray-800/50 border border-gray-700 p-3 rounded-lg mb-3 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-white text-sm"><?php echo $u['nama_lengkap']; ?></p>
                                    <p class="text-xs text-gray-400">Username: <span class="text-blue-400"><?php echo $u['username']; ?></span></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold bg-gray-700 text-gray-300 px-2 py-1 rounded uppercase"><?php echo $u['role']; ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
