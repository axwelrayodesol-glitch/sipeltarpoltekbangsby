<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}
$id_taruna = $_SESSION['id_taruna'];

$riwayat = $conn->query("SELECT * FROM laporan_anonim WHERE id_taruna_pelapor = $id_taruna ORDER BY waktu_lapor DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Laporan Anonim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .hacker-bg { background: linear-gradient(180deg, #111827 0%, #000000 100%); }
    </style>
</head>
<body class="p-8 hacker-bg">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-red-500"><i class="fas fa-user-secret mr-2"></i> Whistleblower (Laporan Anonim)</h2>
            <a href="taruna_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors border border-gray-700"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
        </div>

        <div class="bg-red-500/10 border-l-4 border-red-500 text-red-400 p-4 rounded mb-6 text-sm">
            <i class="fas fa-shield-alt mr-2"></i> <b>Privasi Terjamin:</b> Identitas Anda saat mengirim laporan ini 100% dirahasiakan oleh sistem. Admin/Komandan tidak akan bisa melihat siapa yang mengirim laporan. Gunakan fitur ini secara bijak!
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4"><i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Form Laporan -->
            <div class="bg-gray-900 border border-red-900/50 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2"><i class="fas fa-bullhorn text-red-400 mr-2"></i>Buat Laporan Rahasia</h3>
                <form action="proses_anonim.php" method="POST" enctype="multipart/form-data" class="space-y-4 relative z-10">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Judul Laporan</label>
                        <input type="text" name="judul" required class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="Misal: Ditemukan Taruna X Merokok di Toilet">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Kronologi / Deskripsi Kejadian</label>
                        <textarea name="deskripsi" required rows="5" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" placeholder="Ceritakan detail kejadian selengkap-lengkapnya secara anonim..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Bukti Foto (Rahasia)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full bg-gray-800 border border-gray-700 rounded p-1 text-sm text-gray-400">
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded transition-all tracking-wider shadow-lg shadow-red-500/20">
                        KIRIM LAPORAN ANONIM
                    </button>
                </form>
            </div>

            <!-- Riwayat -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Kotak Laporan Terkirim (Milik Anda)</h3>
                <div class="space-y-4 overflow-y-auto max-h-[500px] pr-2 custom-scrollbar">
                    <?php if($riwayat->num_rows == 0): ?>
                        <p class="text-gray-500 text-sm">Belum ada laporan rahasia yang dikirim.</p>
                    <?php else: while($r = $riwayat->fetch_assoc()): ?>
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-white text-sm"><?php echo $r['judul_laporan']; ?></h4>
                                <?php 
                                    $sc = 'text-yellow-400 bg-yellow-400/20';
                                    if($r['status'] == 'Ditindaklanjuti') $sc = 'text-blue-400 bg-blue-400/20';
                                    if($r['status'] == 'Selesai') $sc = 'text-green-400 bg-green-400/20';
                                ?>
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?php echo $sc; ?>"><?php echo $r['status']; ?></span>
                            </div>
                            <p class="text-xs text-gray-400 mb-1"><i class="far fa-clock"></i> <?php echo $r['waktu_lapor']; ?></p>
                            <p class="text-sm text-gray-300">"<?php echo $r['deskripsi']; ?>"</p>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
