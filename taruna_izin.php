<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}
$id_taruna = $_SESSION['id_taruna'];

// Cek riwayat pengajuan izin
$riwayat = $conn->query("SELECT * FROM pengajuan_izin WHERE id_taruna = $id_taruna ORDER BY waktu_pengajuan DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Pengajuan Izin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-envelope-open-text mr-2 text-blue-500"></i> Pengajuan Izin</h2>
            <a href="taruna_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard</a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4"><i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4"><i class="fas fa-times mr-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Form Pengajuan -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Formulir Baru</h3>
                <form action="proses_izin.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Jenis Izin</label>
                        <select name="jenis_izin" required class="w-full bg-gray-800 border border-gray-600 rounded p-2 text-white">
                            <option value="Sakit">Sakit</option>
                            <option value="Keluar Asrama">Keluar Asrama (IB/Pesiar)</option>
                            <option value="Pulang">Pulang (Cuti)</option>
                            <option value="Kegiatan Luar">Kegiatan Luar</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Tgl Mulai</label>
                            <input type="date" name="tanggal_mulai" required class="w-full bg-gray-800 border border-gray-600 rounded p-2 text-white">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Tgl Selesai</label>
                            <input type="date" name="tanggal_selesai" required class="w-full bg-gray-800 border border-gray-600 rounded p-2 text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Alasan Lengkap</label>
                        <textarea name="alasan" required rows="3" class="w-full bg-gray-800 border border-gray-600 rounded p-2 text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Upload Dokumen (Surat Dokter/Undangan - PDF/JPG)</label>
                        <input type="file" name="dokumen" class="w-full bg-gray-800 border border-gray-600 rounded p-1 text-sm text-gray-400">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded transition-all">
                        Ajukan Izin
                    </button>
                </form>
            </div>

            <!-- Riwayat Pengajuan -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Riwayat Pengajuan</h3>
                <div class="space-y-4">
                    <?php if($riwayat->num_rows == 0): ?>
                        <p class="text-gray-500 text-sm">Belum ada riwayat pengajuan izin.</p>
                    <?php else: while($r = $riwayat->fetch_assoc()): ?>
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-white"><?php echo $r['jenis_izin']; ?></h4>
                                    <?php if($r['status_approval'] == 'Menunggu'): ?>
                                        <span class="bg-yellow-500/20 text-yellow-500 px-3 py-1 rounded-full text-xs font-bold uppercase"><i class="fas fa-clock mr-1"></i>Menunggu</span>
                                    <?php elseif($r['status_approval'] == 'Disetujui Pembina'): ?>
                                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-bold uppercase mb-2 block w-fit"><i class="fas fa-check-double mr-1"></i>Disetujui</span>
                                        <a href="cetak_surat_izin.php?id=<?php echo $r['id_izin']; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded text-[10px] font-bold uppercase transition-colors inline-block mt-1">
                                            <i class="fas fa-print mr-1"></i> Cetak Surat PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="bg-red-500/20 text-red-500 px-3 py-1 rounded-full text-xs font-bold uppercase"><i class="fas fa-times mr-1"></i>Ditolak</span>
                                    <?php endif; ?>
                            </div>
                            <p class="text-xs text-gray-400 mb-1"><i class="far fa-calendar-alt"></i> <?php echo $r['tanggal_mulai'] . ' s/d ' . $r['tanggal_selesai']; ?></p>
                            <p class="text-sm text-gray-300">"<?php echo $r['alasan']; ?>"</p>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
