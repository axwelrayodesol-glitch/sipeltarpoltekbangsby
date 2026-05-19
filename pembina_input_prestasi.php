<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

$taruna = $conn->query("SELECT id_taruna, nama, nit FROM data_taruna ORDER BY nama ASC");
$prestasi = $conn->query("SELECT * FROM data_prestasi ORDER BY poin_reward DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Input Prestasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="p-8">
    <div class="max-w-3xl mx-auto bg-gray-900 border border-gray-700 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-yellow-500 rounded-full blur-3xl opacity-10"></div>
        
        <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4 relative z-10">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-medal text-yellow-500 mr-2"></i> Form Input Prestasi (Reward)</h2>
            <a href="pembina_dashboard.php" class="text-gray-400 hover:text-white"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4 relative z-10"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="proses_prestasi.php" method="POST" class="space-y-6 relative z-10">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Pilih Taruna Berprestasi</label>
                    <select name="id_taruna" required class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white focus:ring-2 focus:ring-yellow-500">
                        <option value="">-- Cari / Pilih Taruna --</option>
                        <?php while($t = $taruna->fetch_assoc()): ?>
                            <option value="<?php echo $t['id_taruna']; ?>"><?php echo $t['nit'].' - '.$t['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Prestasi</label>
                    <select name="id_prestasi" required class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white focus:ring-2 focus:ring-yellow-500">
                        <option value="">-- Pilih Penghargaan --</option>
                        <?php while($p = $prestasi->fetch_assoc()): ?>
                            <option value="<?php echo $p['id_prestasi']; ?>"><?php echo "[+".$p['poin_reward']." Poin] ".$p['nama_prestasi']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Tanggal Pemberian / Sertifikat</label>
                <input type="date" name="tanggal" required value="<?php echo date('Y-m-d'); ?>" class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white focus:ring-2 focus:ring-yellow-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Deskripsi / Keterangan (Opsional)</label>
                <textarea name="deskripsi" rows="3" class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white focus:ring-2 focus:ring-yellow-500" placeholder="Juara 1 Lomba Web Design Tingkat Nasional..."></textarea>
            </div>

            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-500 text-black font-bold py-3 px-4 rounded shadow-lg shadow-yellow-500/30 transition-all transform hover:scale-[1.02]">
                <i class="fas fa-award mr-2"></i> BERIKAN REWARD & TAMBAH POIN PRESTASI
            </button>
        </form>
    </div>
</body>
</html>
