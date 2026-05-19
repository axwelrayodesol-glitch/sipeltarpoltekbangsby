<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas' && $_SESSION['role'] != 'pembina')) {
    header("Location: index.php");
    exit();
}

// Array of predefined rooms
$rooms = [
    'Barak Echo Utara' => [
        'Echo Utara - Kamar 103', 'Echo Utara - Kamar 104', 
        'Echo Utara - Kamar 203', 'Echo Utara - Kamar 204'
    ],
    'Barak Echo Selatan' => [
        'Echo Selatan - Kamar 101', 'Echo Selatan - Kamar 102', 
        'Echo Selatan - Kamar 201', 'Echo Selatan - Kamar 202'
    ],
    'Barak Foxtrot' => [
        'Foxtrot - Kamar 101', 'Foxtrot - Kamar 102', 'Foxtrot - Kamar 103', 'Foxtrot - Kamar 104', 'Foxtrot - Kamar 105',
        'Foxtrot - Kamar 201', 'Foxtrot - Kamar 202', 'Foxtrot - Kamar 203', 'Foxtrot - Kamar 204', 'Foxtrot - Kamar 205'
    ],
    'Asrama Alpha' => [
        'Alpha - Kamar 101', 'Alpha - Kamar 102', 'Alpha - Kamar 201', 'Alpha - Kamar 202'
    ],
    'Asrama Charlie' => [
        'Charlie - Kamar 101', 'Charlie - Kamar 102', 'Charlie - Kamar 201', 'Charlie - Kamar 202'
    ]
];

// Fetch the LATEST condition for each room
// We use a subquery to get the max waktu_lapor for each nama_barak
$query = "SELECT l1.nama_barak, l1.kondisi, l1.waktu_lapor, l1.foto, t.nama as pelapor 
          FROM laporan_kamar l1 
          JOIN (SELECT nama_barak, MAX(waktu_lapor) as max_waktu FROM laporan_kamar GROUP BY nama_barak) l2 
          ON l1.nama_barak = l2.nama_barak AND l1.waktu_lapor = l2.max_waktu
          JOIN data_taruna t ON l1.id_taruna = t.id_taruna";
$result = $conn->query($query);
$room_status = [];

if($result) {
    while($row = $result->fetch_assoc()) {
        $room_status[$row['nama_barak']] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Denah Asrama Interaktif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .blueprint-bg {
            background-color: #0b1120;
            background-image: linear-gradient(rgba(59, 130, 246, 0.2) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.2) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .room-box { transition: all 0.3s ease; }
        .room-box:hover { transform: scale(1.05); z-index: 10; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5); }
        .blink-red { animation: blink-red 2s infinite; }
        @keyframes blink-red {
            0% { box-shadow: 0 0 5px rgba(239, 68, 68, 0.5); }
            50% { box-shadow: 0 0 20px rgba(239, 68, 68, 1); border-color: rgba(239, 68, 68, 1); }
            100% { box-shadow: 0 0 5px rgba(239, 68, 68, 0.5); }
        }
    </style>
</head>
<body class="p-4 md:p-8 blueprint-bg min-h-screen">
    
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-gray-900/80 backdrop-blur p-6 rounded-2xl border border-blue-500/30 shadow-2xl">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <i class="fas fa-map text-blue-500"></i> PETA DENAH ASRAMA (LIVE)
                </h2>
                <p class="text-sm text-gray-400 mt-1">Status kebersihan dan kerusakan fasilitas asrama secara Real-Time.</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-4">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-green-500 rounded-full"></div> <span class="text-xs text-gray-300">Aman / Bersih</span></div>
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div> <span class="text-xs text-gray-300">Kotor / Rusak</span></div>
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-gray-600 rounded-full"></div> <span class="text-xs text-gray-300">Belum Ada Laporan</span></div>
            </div>
        </div>

        <!-- Blueprint Area -->
        <div class="space-y-12">
            <?php foreach($rooms as $floor_name => $room_list): ?>
            
            <div class="relative">
                <div class="absolute -left-4 top-0 bottom-0 w-2 bg-blue-500/50 rounded-full"></div>
                <h3 class="text-xl font-bold text-white mb-6 uppercase tracking-widest pl-4"><?php echo $floor_name; ?></h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 pl-4">
                    <?php 
                    foreach($room_list as $room): 
                        // Default Status
                        $status_color = "bg-gray-800/80 border-gray-600";
                        $icon = "fa-bed text-gray-500";
                        $kondisi = "Belum Lapor Hari Ini";
                        $pelapor = "-";
                        $waktu = "-";
                        $is_blink = false;

                        if(isset($room_status[$room])) {
                            $data = $room_status[$room];
                            $kondisi = $data['kondisi'];
                            $pelapor = $data['pelapor'];
                            $waktu = date('d/m/y H:i', strtotime($data['waktu_lapor']));
                            
                            if($kondisi == 'Sangat Bersih' || $kondisi == 'Bersih') {
                                $status_color = "bg-green-900/40 border-green-500";
                                $icon = "fa-check-circle text-green-400";
                            } else {
                                $status_color = "bg-red-900/40 border-red-500";
                                $icon = "fa-exclamation-triangle text-red-500";
                                $is_blink = true;
                            }
                        }
                    ?>
                    
                    <div class="room-box border-2 rounded-xl p-5 relative overflow-hidden flex flex-col justify-between h-48 <?php echo $status_color; ?> <?php echo $is_blink ? 'blink-red' : ''; ?>">
                        <?php if(isset($data['foto']) && $data['foto'] != ''): ?>
                            <div class="absolute right-0 top-0 bottom-0 w-1/2 opacity-20">
                                <img src="uploads/kamar/<?php echo $data['foto']; ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endif; ?>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <h4 class="font-black text-xl text-white"><?php $parts = explode(" - ", $room); echo isset($parts[1]) ? $parts[1] : $room; ?></h4>
                            <i class="fas <?php echo $icon; ?> text-2xl"></i>
                        </div>
                        
                        <div class="relative z-10 mt-auto">
                            <div class="inline-block px-2 py-1 bg-black/50 rounded text-xs font-bold text-white mb-2 uppercase"><?php echo $kondisi; ?></div>
                            <p class="text-[10px] text-gray-400"><i class="fas fa-user text-blue-400 mr-1"></i> <?php echo $pelapor; ?></p>
                            <p class="text-[10px] text-gray-400"><i class="fas fa-clock text-blue-400 mr-1"></i> <?php echo $waktu; ?></p>
                            
                            <?php if(isset($data['foto']) && $data['foto'] != ''): ?>
                            <a href="uploads/kamar/<?php echo $data['foto']; ?>" target="_blank" class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-camera"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>
        
    </div>

</body>
</html>
