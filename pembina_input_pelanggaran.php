<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

$taruna = $conn->query("SELECT id_taruna, nama, nit FROM data_taruna ORDER BY nama ASC");
$kategori = $conn->query("SELECT * FROM kategori_pelanggaran ORDER BY tingkat_pelanggaran ASC, poin ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Input Pelanggaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="p-8">
    <div class="max-w-3xl mx-auto bg-gray-900 border border-gray-700 rounded-2xl p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-white">Form Input Pelanggaran Taruna</h2>
            <a href="pembina_dashboard.php" class="text-gray-400 hover:text-white">Kembali</a>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="proses_pelanggaran.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Pilih Taruna</label>
                    <select name="id_taruna" required class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Cari / Pilih Taruna --</option>
                        <?php while($t = $taruna->fetch_assoc()): ?>
                            <option value="<?php echo $t['id_taruna']; ?>"><?php echo $t['nit'].' - '.$t['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Kategori Pelanggaran</label>
                    <select name="id_kategori" required class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Jenis --</option>
                        <?php while($k = $kategori->fetch_assoc()): ?>
                            <option value="<?php echo $k['id_kategori']; ?>"><?php echo "[+".$k['poin']." Poin] ".$k['nama_pelanggaran']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Tanggal Kejadian</label>
                    <input type="date" name="tanggal" required value="<?php echo date('Y-m-d'); ?>" class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Jam Kejadian</label>
                    <input type="time" name="jam" required value="<?php echo date('H:i'); ?>" class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Lokasi Kejadian</label>
                <input type="text" name="lokasi" required placeholder="Contoh: Barak A / Lapangan Apel" class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Deskripsi / Kronologi Singkat</label>
                <textarea name="deskripsi" rows="3" class="w-full bg-gray-800 border border-gray-600 rounded p-3 text-white" placeholder="Jelaskan secara singkat..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Upload Foto Bukti (Opsional, JPG/PNG)</label>
                <input type="file" name="foto_bukti" accept="image/*" class="w-full bg-gray-800 border border-gray-600 rounded p-2 text-gray-400">
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-4 rounded shadow-lg transition-all">
                SUBMIT PELANGGARAN & POTONG POIN
            </button>
        </form>
    </div>
</body>
</html>
