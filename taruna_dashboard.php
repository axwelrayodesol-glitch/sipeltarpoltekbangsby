<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

$id_taruna = $_SESSION['id_taruna'];

// Cek apakah sudah absen hari ini
$today = date('Y-m-d');
$cek_absen = $conn->query("SELECT * FROM data_kedatangan WHERE id_taruna = $id_taruna AND DATE(waktu_datang) = '$today'");
$sudah_absen = $cek_absen->num_rows > 0;
$data_absen = $sudah_absen ? $cek_absen->fetch_assoc() : null;

// Ambil info taruna terbaru (Poin)
$info = $conn->query("SELECT total_poin_pelanggaran, total_poin_prestasi, kategori_status FROM data_taruna WHERE id_taruna = $id_taruna")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Taruna - SIPELTAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { navy: { 800: '#0f172a', 900: '#0b1120' } }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0f172a; color: white; }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        /* Ripple animation for button */
        .ripple {
            animation: ripple 2s infinite;
        }
        @keyframes ripple {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

    <!-- Navbar -->
    <nav class="glass-card rounded-2xl p-4 flex justify-between items-center mb-8 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center font-bold">
                <?php echo substr($_SESSION['nama'], 0, 1); ?>
            </div>
            <div>
                <h2 class="font-bold text-lg"><?php echo $_SESSION['nama']; ?></h2>
                <p class="text-xs text-blue-400">NIT: <?php echo $_SESSION['nit']; ?></p>
            </div>
        </div>
        <a href="logout.php" class="bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </nav>

    <!-- Main Content -->
    <div class="max-w-2xl mx-auto space-y-6">
        
        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-xl"></i>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        </div>
        <?php endif; ?>

        <!-- Discipline Stats Card -->
        <div class="grid grid-cols-3 gap-4">
            <div class="glass-card rounded-2xl p-6 text-center border-t-4 border-red-500 hover:scale-105 transition-transform">
                <p class="text-sm text-gray-400 mb-1">Poin Pelanggaran</p>
                <h2 class="text-4xl font-bold text-red-400"><?php echo $info['total_poin_pelanggaran']; ?></h2>
            </div>
            <div class="glass-card rounded-2xl p-6 text-center border-t-4 border-yellow-500 hover:scale-105 transition-transform">
                <p class="text-sm text-gray-400 mb-1">Poin Prestasi</p>
                <h2 class="text-4xl font-bold text-yellow-400"><?php echo isset($info['total_poin_prestasi']) ? $info['total_poin_prestasi'] : 0; ?></h2>
            </div>
            <?php 
                $status_color = 'text-green-400';
                if($info['kategori_status'] == 'Perhatian Khusus') $status_color = 'text-yellow-400';
                if($info['kategori_status'] == 'Pembinaan') $status_color = 'text-orange-400';
                if($info['kategori_status'] == 'Kritis') $status_color = 'text-red-500';
            ?>
            <div class="glass-card rounded-2xl p-6 text-center border-t-4 border-gray-600">
                <p class="text-sm text-gray-400 mb-1">Status Disiplin</p>
                <h2 class="text-lg md:text-xl mt-2 font-bold <?php echo $status_color; ?> leading-tight"><?php echo strtoupper($info['kategori_status']); ?></h2>
            </div>
        </div>

        <!-- E-KTA QR Code Card -->
        <div class="glass-card rounded-3xl p-6 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6 border-l-4 border-blue-500 shadow-xl">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="inline-flex items-center gap-2 bg-blue-900/50 text-blue-400 px-3 py-1 rounded-full text-xs font-bold mb-3 border border-blue-500/30">
                    <i class="fas fa-id-badge"></i> E-KTA DIGITAL
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Pindai Untuk Lapor Kembali</h3>
                <p class="text-sm text-gray-400">
                    Tunjukkan QR Code ini kepada Petugas Jaga / Pengasuh saat Anda kembali ke asrama setelah melaksanakan Izin, Pesiar, LWE, atau LLWE.
                </p>
                <div class="mt-4 flex flex-wrap gap-2 justify-center md:justify-start">
                    <span class="text-[10px] bg-gray-800 border border-gray-700 px-2 py-1 rounded text-gray-400"><i class="fas fa-bolt text-yellow-500 mr-1"></i> Auto Check-In</span>
                    <span class="text-[10px] bg-gray-800 border border-gray-700 px-2 py-1 rounded text-gray-400"><i class="fas fa-bolt text-green-500 mr-1"></i> Auto Selesai Izin</span>
                </div>
                
                <a href="taruna_wajah.php" class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-lg transition-colors text-sm border border-indigo-500/50 shadow-[0_0_15px_rgba(79,70,229,0.3)]">
                    <i class="fas fa-camera-retro mr-1"></i> Daftar Wajah AI
                </a>
            </div>
            
            <div class="shrink-0 bg-white p-3 rounded-2xl shadow-lg relative group">
                <!-- Gunakan localhost IP atau domain asli saat produksi. Di sini kita pakai URL relative/lokal asumsi untuk keperluan demo -->
                <?php 
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                    $domain = $_SERVER['HTTP_HOST'];
                    // Asumsi path folder adalah /sipeltar
                    $base_url = $protocol . "://" . $domain . "/sipeltar";
                    $qr_data = urlencode($base_url . "/scan_kembali.php?nit=" . $_SESSION['nit']);
                ?>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $qr_data; ?>" alt="QR Code Taruna" class="w-32 h-32 md:w-40 md:h-40 rounded-lg group-hover:scale-105 transition-transform">
                <div class="absolute inset-0 border-4 border-blue-500 rounded-2xl pointer-events-none opacity-50"></div>
                <!-- Frame effect -->
                <div class="absolute -top-1 -left-1 w-4 h-4 border-t-4 border-l-4 border-blue-600 rounded-tl-lg"></div>
                <div class="absolute -top-1 -right-1 w-4 h-4 border-t-4 border-r-4 border-blue-600 rounded-tr-lg"></div>
                <div class="absolute -bottom-1 -left-1 w-4 h-4 border-b-4 border-l-4 border-blue-600 rounded-bl-lg"></div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 border-b-4 border-r-4 border-blue-600 rounded-br-lg"></div>
            </div>
        </div>
        
        <!-- Peringatan Testing Localhost -->
        <div class="bg-blue-900/30 border border-blue-500/50 rounded-xl p-4 text-sm text-blue-300 text-center shadow-lg">
            <i class="fas fa-info-circle mr-2"></i> <b>Info Testing (Localhost):</b> HP Anda tidak akan bisa membuka link "localhost" jika di-scan langsung. Untuk menguji sistem scanner di laptop ini, klik tombol di bawah (Pastikan Anda login sebagai Admin/Pembina terlebih dahulu di tab lain):<br>
            <a href="scan_kembali.php?nit=<?php echo $_SESSION['nit']; ?>" target="_blank" class="inline-block mt-3 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded font-bold transition-colors">
                <i class="fas fa-external-link-alt mr-1"></i> Simulasi Buka Hasil Scan
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Checkin Card -->
            <div class="glass-card rounded-3xl p-8 text-center relative overflow-hidden flex flex-col justify-center items-center h-full">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500 rounded-full blur-3xl opacity-10"></div>
            
            <h3 class="text-xl font-bold text-gray-300 mb-2">Status Kedatangan Hari Ini</h3>
            <p class="text-gray-500 text-sm mb-8"><?php echo date('l, d F Y'); ?></p>

            <?php if ($sudah_absen): ?>
                <div class="inline-block bg-navy-900 border border-gray-700 rounded-full p-8 mb-6">
                    <i class="fas fa-check text-6xl text-green-400"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Sudah Check-In</h2>
                <p class="text-gray-400">Waktu: <span class="text-blue-400"><?php echo date('H:i:s', strtotime($data_absen['waktu_datang'])); ?></span></p>
                <div class="mt-4 inline-block px-4 py-1 rounded-full text-sm font-semibold <?php echo $data_absen['status_kehadiran'] == 'hadir' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'; ?>">
                    Status: <?php echo strtoupper($data_absen['status_kehadiran']); ?>
                </div>
            <?php else: ?>
                <form id="checkinForm" action="checkin_process.php" method="POST" class="mt-4">
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">
                    
                    <button type="button" id="btnCheckin" onclick="getLocation()" 
                        class="ripple bg-blue-600 hover:bg-blue-500 text-white w-40 h-40 rounded-full shadow-lg shadow-blue-500/50 flex flex-col items-center justify-center mx-auto transition-all transform hover:scale-105 cursor-pointer">
                        <i class="fas fa-fingerprint text-5xl mb-2"></i>
                        <span class="font-bold tracking-widest text-sm">CHECK-IN</span>
                    </button>
                    <p id="statusMsg" class="mt-6 text-sm text-gray-400"><i class="fas fa-map-marker-alt text-blue-400 mr-1"></i> Sistem akan mengambil lokasi GPS Anda</p>
                </form>
            <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-center gap-4">
                <h3 class="text-xl font-bold text-white mb-2 text-center">Menu Cepat</h3>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="taruna_izin.php" class="bg-gray-800/80 hover:bg-gray-700 p-4 rounded-xl flex flex-col items-center justify-center border border-gray-700 transition-all group">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-envelope-open-text text-blue-400 text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-white">Ajukan Izin</span>
                    </a>
                    
                    <a href="taruna_fasilitas.php" class="bg-gray-800/80 hover:bg-gray-700 p-4 rounded-xl flex flex-col items-center justify-center border border-gray-700 transition-all group">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-tools text-yellow-500 text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-white">Fasilitas</span>
                    </a>
                    
                    <a href="taruna_banding.php" class="bg-gray-800/80 hover:bg-gray-700 p-4 rounded-xl flex flex-col items-center justify-center border border-red-900 transition-all group">
                        <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-balance-scale text-red-500 text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-white">Banding</span>
                    </a>

                    <a href="taruna_anonim.php" class="bg-gray-800/80 hover:bg-gray-700 p-4 rounded-xl flex flex-col items-center justify-center border border-gray-700 transition-all group">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-secret text-purple-400 text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-white">Anonim</span>
                    </a>

                    <a href="taruna_kamar.php" class="bg-gray-800/80 hover:bg-gray-700 p-4 rounded-xl flex flex-col items-center justify-center border border-gray-700 transition-all group">
                        <div class="w-12 h-12 bg-indigo-500/20 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-bed text-indigo-400 text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-white">Cek Kamar</span>
                    </a>

                    <a href="taruna_menghadap.php" class="bg-gray-800/80 hover:bg-gray-700 p-4 rounded-xl flex flex-col items-center justify-center border border-orange-500/30 transition-all group shadow-[0_0_15px_rgba(249,115,22,0.1)]">
                        <div class="w-12 h-12 bg-orange-500/20 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-walking text-orange-400 text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-white">Menghadap</span>
                    </a>
                </div>
            </div>
        </div>
        
    </div>

    <script>
        function getLocation() {
            const btn = document.getElementById('btnCheckin');
            const statusMsg = document.getElementById('statusMsg');
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-4xl mb-2"></i><span class="font-bold text-sm">PROSES...</span>';
            btn.classList.remove('ripple');
            btn.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                statusMsg.innerHTML = "Geolocation tidak didukung oleh browser ini.";
                resetButton();
            }
        }

        function showPosition(position) {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lng').value = position.coords.longitude;
            document.getElementById('statusMsg').innerHTML = "Lokasi ditemukan! Mengirim data...";
            document.getElementById('checkinForm').submit();
        }

        function showError(error) {
            const statusMsg = document.getElementById('statusMsg');
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    statusMsg.innerHTML = "Anda menolak permintaan Geolocation. Mohon izinkan akses lokasi.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    statusMsg.innerHTML = "Informasi lokasi tidak tersedia.";
                    break;
                case error.TIMEOUT:
                    statusMsg.innerHTML = "Waktu permintaan lokasi habis.";
                    break;
                case error.UNKNOWN_ERROR:
                    statusMsg.innerHTML = "Terjadi kesalahan yang tidak diketahui.";
                    break;
            }
            resetButton();
        }

        function resetButton() {
            const btn = document.getElementById('btnCheckin');
            btn.innerHTML = '<i class="fas fa-fingerprint text-5xl mb-2"></i><span class="font-bold tracking-widest text-sm">CHECK-IN</span>';
            btn.classList.add('ripple');
            btn.disabled = false;
        }
    </script>
</body>
</html>
