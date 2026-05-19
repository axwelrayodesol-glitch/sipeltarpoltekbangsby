<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

// Fetch pending permissions
$pending = $conn->query("SELECT p.*, t.nama, t.nit FROM pengajuan_izin p JOIN data_taruna t ON p.id_taruna = t.id_taruna WHERE p.status_approval = 'Pending' ORDER BY p.waktu_pengajuan ASC");

// Fetch history
$history = $conn->query("SELECT p.*, t.nama, t.nit, u.nama_lengkap as approver FROM pengajuan_izin p JOIN data_taruna t ON p.id_taruna = t.id_taruna LEFT JOIN users u ON p.id_pembina_approver = u.id_user WHERE p.status_approval != 'Pending' ORDER BY p.waktu_pengajuan DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPELTAR - Approval Izin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-check-double text-blue-500 mr-2"></i> Approval Pengajuan Izin</h2>
            <a href="pembina_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">Kembali ke Dashboard</a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded mb-6">
            <i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Pending List -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Menunggu Persetujuan</h3>
                <div class="space-y-4">
                    <?php if($pending->num_rows == 0): ?>
                        <p class="text-gray-500 text-sm">Tidak ada pengajuan yang perlu di-approve.</p>
                    <?php else: while($p = $pending->fetch_assoc()): ?>
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-bold text-white"><?php echo $p['nama']; ?> <span class="text-xs text-blue-400">(<?php echo $p['nit']; ?>)</span></h4>
                                    <p class="text-sm font-semibold text-yellow-500"><?php echo $p['jenis_izin']; ?></p>
                                </div>
                                <?php if($p['dokumen_pendukung']): ?>
                                    <a href="uploads/dokumen/<?php echo $p['dokumen_pendukung']; ?>" target="_blank" class="text-xs bg-blue-600/20 text-blue-400 px-2 py-1 rounded"><i class="fas fa-paperclip"></i> Dokumen</a>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-gray-400 mb-2"><i class="far fa-calendar-alt"></i> <?php echo $p['tanggal_mulai'] . ' s/d ' . $p['tanggal_selesai']; ?></p>
                            <p class="text-sm text-gray-300 italic mb-4">"<?php echo $p['alasan']; ?>"</p>
                            
                            <div class="flex gap-2">
                                <form action="proses_approval.php" method="POST" class="flex-1">
                                    <input type="hidden" name="id_izin" value="<?php echo $p['id_izin']; ?>">
                                    <input type="hidden" name="status" value="Disetujui Pembina">
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-2 rounded text-sm transition-colors">SETUJUI</button>
                                </form>
                                <form action="proses_approval.php" method="POST" class="flex-1">
                                    <input type="hidden" name="id_izin" value="<?php echo $p['id_izin']; ?>">
                                    <input type="hidden" name="status" value="Ditolak">
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-2 rounded text-sm transition-colors">TOLAK</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>

            <!-- History List -->
            <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-2">Riwayat Approval</h3>
                <div class="space-y-4">
                    <?php if($history->num_rows == 0): ?>
                        <p class="text-gray-500 text-sm">Belum ada riwayat.</p>
                    <?php else: while($h = $history->fetch_assoc()): ?>
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 opacity-70">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-white text-sm"><?php echo $h['nama']; ?></h4>
                                <?php 
                                    $color = $h['status_approval'] == 'Disetujui Pembina' ? 'text-green-400 bg-green-400/10' : 'text-red-400 bg-red-400/10';
                                ?>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded <?php echo $color; ?>"><?php echo $h['status_approval']; ?></span>
                            </div>
                            <p class="text-xs text-gray-400 mb-1"><?php echo $h['jenis_izin']; ?> | <?php echo $h['tanggal_mulai']; ?></p>
                            <p class="text-[10px] text-gray-500">Oleh: <?php echo $h['approver']; ?></p>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
