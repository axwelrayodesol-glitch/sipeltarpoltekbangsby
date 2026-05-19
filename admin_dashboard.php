<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        body { background-color: #0b1120; color: #e2e8f0; }
        .glass-card {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }
        #map { height: 400px; z-index: 1; border-radius: 0.75rem; }
        
        /* Custom Scrollbar for table */
        .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 3px; }
        
        .pulse-live {
            animation: pulse-dot 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
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
                <a href="admin_dashboard.php" class="flex items-center gap-3 bg-blue-600/20 text-blue-400 px-4 py-3 rounded-lg border border-blue-500/30 transition-all">
                    <i class="fas fa-satellite-dish w-5"></i>
                    <span class="font-medium">Live Monitor</span>
                </a>
                <a href="admin_peta.php" class="flex items-center gap-3 text-emerald-400 hover:bg-emerald-900/50 hover:text-emerald-300 px-4 py-3 rounded-lg transition-all border border-emerald-500/20">
                    <i class="fas fa-map w-5"></i>
                    <span class="font-medium">Denah Asrama</span>
                </a>
                <a href="admin_users.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-users w-5"></i>
                    <span class="font-medium">Manajemen User</span>
                </a>
                <a href="admin_fasilitas.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-tools w-5"></i>
                    <span class="font-medium">Laporan Fasilitas</span>
                </a>
                <a href="admin_anonim.php" class="flex items-center gap-3 text-red-400 hover:bg-red-900/50 hover:text-red-300 px-4 py-3 rounded-lg transition-all border border-red-500/20">
                    <i class="fas fa-user-secret w-5"></i>
                    <span class="font-medium">Laporan Anonim</span>
                </a>
                <a href="admin_banding.php" class="flex items-center gap-3 text-yellow-400 hover:bg-yellow-900/50 hover:text-yellow-300 px-4 py-3 rounded-lg transition-all border border-yellow-500/20">
                    <i class="fas fa-balance-scale w-5"></i>
                    <span class="font-medium">Banding Hukuman</span>
                </a>
                <a href="admin_laporan.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-file-export w-5"></i>
                    <span class="font-medium">Laporan & Rekap</span>
                </a>
            </nav>
        </div>
        <div class="mt-auto p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-white"><?php echo $_SESSION['nama']; ?></p>
                    <p class="text-xs text-green-400 capitalize"><?php echo $_SESSION['role']; ?></p>
                </div>
            </div>
            <a href="logout.php" class="block text-center w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm py-2 rounded-lg transition-all border border-red-500/20">
                Logout System
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-[#0b1120] p-4 md:p-8 custom-scrollbar">
        
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    Command Center 
                    <span class="bg-red-500 text-white text-[10px] px-2 py-1 rounded-full uppercase tracking-wider flex items-center gap-1 pulse-live">
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div> LIVE
                    </span>
                </h2>
                <p class="text-sm text-gray-400 mt-1"><?php echo date('l, d F Y'); ?> | Monitoring Kedatangan Taruna</p>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card p-4 md:p-6 rounded-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-users text-5xl"></i></div>
                <p class="text-gray-400 text-sm font-semibold mb-1">Total Taruna</p>
                <h3 class="text-3xl font-bold text-white" id="stat-total">0</h3>
            </div>
            <div class="glass-card p-4 md:p-6 rounded-2xl border-b-4 border-green-500 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-check-circle text-5xl text-green-500"></i></div>
                <p class="text-gray-400 text-sm font-semibold mb-1">Hadir (Hari Ini)</p>
                <h3 class="text-3xl font-bold text-green-400" id="stat-hadir">0</h3>
            </div>
            <div class="glass-card p-4 md:p-6 rounded-2xl border-b-4 border-yellow-500 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-exclamation-triangle text-5xl text-yellow-500"></i></div>
                <p class="text-gray-400 text-sm font-semibold mb-1">Terlambat</p>
                <h3 class="text-3xl font-bold text-yellow-400" id="stat-terlambat">0</h3>
            </div>
            <div class="glass-card p-4 md:p-6 rounded-2xl border-b-4 border-red-500 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-times-circle text-5xl text-red-500"></i></div>
                <p class="text-gray-400 text-sm font-semibold mb-1">Belum Datang</p>
                <h3 class="text-3xl font-bold text-red-400" id="stat-belum">0</h3>
            </div>
        </div>

        <!-- Analytics Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="glass-card rounded-2xl p-4">
                <h3 class="text-lg font-bold text-white mb-4"><i class="fas fa-chart-line text-blue-400 mr-2"></i> Tren Pelanggaran (7 Hari)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-4">
                <h3 class="text-lg font-bold text-white mb-4"><i class="fas fa-chart-pie text-yellow-400 mr-2"></i> Distribusi per Jurusan</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="jurusanChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="glass-card rounded-2xl p-4 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-map-marked-alt text-blue-400 mr-2"></i> Radar GPS Realtime</h3>
                <span class="text-xs text-gray-500">Auto-update every 5s</span>
            </div>
            <div id="map" class="w-full border border-gray-700"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Table Section -->
            <div class="glass-card rounded-2xl p-6 md:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-list text-blue-400 mr-2"></i> Log Kedatangan Terbaru</h3>
                    <button class="bg-gray-800 hover:bg-gray-700 text-xs px-3 py-1.5 rounded transition-colors"><i class="fas fa-download mr-1"></i> Export PDF</button>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-sm text-gray-400">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 rounded-tl-lg">Nama / NIT</th>
                                <th class="px-4 py-3">Jurusan</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 rounded-tr-lg">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="divide-y divide-gray-800/50">
                            <!-- Data injected via JS -->
                            <tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Section -->
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-6"><i class="fas fa-history text-blue-400 mr-2"></i> Sistem Log</h3>
                <div class="relative border-l border-gray-700 ml-3 space-y-6" id="activity-list">
                    <!-- Data injected via JS -->
                </div>
            </div>
            
            <!-- Top Violations Section -->
            <div class="glass-card rounded-2xl p-6 md:col-span-3 border-t-4 border-red-500 mt-4">
                <h3 class="text-lg font-bold text-white mb-4"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Top 5 Taruna Perhatian Khusus</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4" id="top-violations-list">
                    <!-- Data injected via JS -->
                    <div class="text-gray-400 text-sm">Memuat data disiplin...</div>
                </div>
            </div>
        </div>

    </main>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize Charts
        Chart.defaults.color = '#94a3b8'; // text-slate-400
        Chart.defaults.borderColor = '#334155'; // border-slate-700
        
        let trendChart = new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Pelanggaran', data: [], borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.2)', tension: 0.4, fill: true, borderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        let jurusanChart = new Chart(document.getElementById('jurusanChart'), {
            type: 'doughnut',
            data: { labels: [], datasets: [{ data: [], backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
        });

        // Initialize Map
        const map = L.map('map').setView([-6.200000, 106.816666], 12); // Default to Jakarta
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        let markersGroup = L.layerGroup().addTo(map);
        
        // Custom icons
        const iconHadir = L.divIcon({ className: 'custom-icon', html: '<div style="background-color:#22c55e;width:15px;height:15px;border-radius:50%;border:2px solid white;box-shadow:0 0 10px #22c55e;"></div>' });
        const iconTerlambat = L.divIcon({ className: 'custom-icon', html: '<div style="background-color:#eab308;width:15px;height:15px;border-radius:50%;border:2px solid white;box-shadow:0 0 10px #eab308;"></div>' });

        function fetchDashboardData() {
            fetch('api_dashboard.php')
                .then(res => res.json())
                .then(data => {
                    if(data.error) return; // session expired etc

                    // Update Stats
                    document.getElementById('stat-total').innerText = data.stats.total;
                    document.getElementById('stat-hadir').innerText = data.stats.hadir;
                    document.getElementById('stat-terlambat').innerText = data.stats.terlambat;
                    document.getElementById('stat-belum').innerText = data.stats.belum_datang;

                    // Update Map Markers
                    markersGroup.clearLayers();
                    let bounds = [];
                    data.locations.forEach(loc => {
                        let icon = loc.status_kehadiran === 'hadir' ? iconHadir : iconTerlambat;
                        let marker = L.marker([loc.latitude, loc.longitude], {icon: icon})
                            .bindPopup(`<b>${loc.nama}</b><br>Status: ${loc.status_kehadiran}<br>Waktu: ${loc.waktu_datang}`);
                        markersGroup.addLayer(marker);
                        bounds.push([loc.latitude, loc.longitude]);
                    });
                    // Auto fit bounds if there are markers
                    if(bounds.length > 0 && !window.mapZoomedOnce) {
                        map.fitBounds(bounds, {padding: [50, 50]});
                        window.mapZoomedOnce = true;
                    }

                    // Update Charts
                    if(data.charts) {
                        trendChart.data.labels = data.charts.trend.map(i => i.tgl);
                        trendChart.data.datasets[0].data = data.charts.trend.map(i => i.jumlah);
                        trendChart.update();

                        jurusanChart.data.labels = data.charts.jurusan.map(i => i.jurusan);
                        jurusanChart.data.datasets[0].data = data.charts.jurusan.map(i => i.jumlah);
                        jurusanChart.update();
                    }

                    // Update Table
                    let tbody = document.getElementById('table-body');
                    tbody.innerHTML = '';
                    if(data.recent_checkins.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Belum ada data kedatangan hari ini.</td></tr>';
                    } else {
                        data.recent_checkins.forEach(row => {
                            let statusClass = row.status_kehadiran === 'hadir' ? 'text-green-400 bg-green-400/10' : 'text-yellow-400 bg-yellow-400/10';
                            let html = `
                                <tr class="hover:bg-gray-800/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <p class="font-bold text-white">${row.nama}</p>
                                        <p class="text-xs text-blue-400">${row.nit}</p>
                                    </td>
                                    <td class="px-4 py-3">${row.jurusan}</td>
                                    <td class="px-4 py-3 text-white">${row.waktu_datang.split(' ')[1]}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold ${statusClass} uppercase">
                                            ${row.status_kehadiran}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs w-1/3 truncate" title="${row.alamat_lokasi}">
                                        <i class="fas fa-map-marker-alt text-gray-500 mr-1"></i> ${row.alamat_lokasi}
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += html;
                        });
                    }

                    // Update Activities
                    let alist = document.getElementById('activity-list');
                    alist.innerHTML = '';
                    data.activities.forEach(act => {
                        let html = `
                            <div class="relative pl-6">
                                <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-[6.5px] top-1.5 ring-4 ring-[#0f172a]"></div>
                                <p class="text-xs text-blue-400 mb-1">${act.waktu}</p>
                                <p class="text-sm text-gray-300">${act.deskripsi}</p>
                            </div>
                        `;
                        alist.innerHTML += html;
                    });
                    
                    // Update Top Violations
                    let tlist = document.getElementById('top-violations-list');
                    tlist.innerHTML = '';
                    if(!data.top_violations || data.top_violations.length === 0) {
                        tlist.innerHTML = '<div class="col-span-5 text-green-400 text-sm text-center">Belum ada data taruna bermasalah.</div>';
                    } else {
                        data.top_violations.forEach(tar => {
                            let statusColor = 'text-yellow-500';
                            if(tar.kategori_status === 'Kritis') statusColor = 'text-red-500';
                            else if(tar.kategori_status === 'Pembinaan') statusColor = 'text-orange-500';
                            
                            let html = `
                                <div class="bg-gray-800/50 p-4 rounded-xl border border-gray-700 text-center relative overflow-hidden group hover:border-red-500 transition-colors">
                                    <div class="w-12 h-12 bg-red-500/20 rounded-full mx-auto flex items-center justify-center mb-3">
                                        <i class="fas fa-user-times ${statusColor} text-xl"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm truncate">${tar.nama}</h4>
                                    <p class="text-xs text-gray-400 mb-2">${tar.nit}</p>
                                    <div class="text-2xl font-black text-red-500">${tar.total_poin_pelanggaran} <span class="text-xs text-gray-500 font-normal">Poin</span></div>
                                    <div class="mt-2 text-[10px] uppercase font-bold px-2 py-1 bg-gray-900 rounded ${statusColor}">${tar.kategori_status}</div>
                                </div>
                            `;
                            tlist.innerHTML += html;
                        });
                    }
                })
                .catch(err => console.error("Error fetching data:", err));
        }

        // Fetch immediately then every 5 seconds
        fetchDashboardData();
        setInterval(fetchDashboardData, 5000);
    </script>
</body>
</html>
