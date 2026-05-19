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
    <title>SIPELTAR - Analitik Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="admin_users.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-users w-5"></i>
                    <span class="font-medium">Manajemen User</span>
                </a>
                <a href="admin_users.php" class="flex items-center gap-3 text-gray-400 hover:bg-gray-800/50 hover:text-white px-4 py-3 rounded-lg transition-all">
                    <i class="fas fa-users w-5"></i>
                    <span class="font-medium">Manajemen User</span>
                </a>
                <a href="admin_laporan.php" class="flex items-center gap-3 bg-blue-600/20 text-blue-400 px-4 py-3 rounded-lg border border-blue-500/30 transition-all">
                    <i class="fas fa-file-export w-5"></i>
                    <span class="font-medium">Laporan & Rekap</span>
                </a>
            </nav>
        </div>
        <div class="mt-auto p-4 border-t border-gray-800">
            <a href="logout.php" class="block text-center w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm py-2 rounded-lg transition-all border border-red-500/20">
                Logout System
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-white"><i class="fas fa-chart-line text-blue-500 mr-2"></i> Analitik & Export Laporan</h2>
                <p class="text-gray-400 mt-1">Grafik visualisasi data kedisiplinan taruna</p>
            </div>
            <div>
                <a href="export_csv.php" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center gap-2 transition-all">
                    <i class="fas fa-file-excel"></i> Export Pelanggaran (CSV)
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Pie Chart -->
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 text-center">Status Kedisiplinan Taruna</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 text-center">Top 10 Taruna (Pelanggaran vs Prestasi)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>

    </main>

    <script>
        // Set default Chart.js styles to fit dark mode
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.font.family = 'Inter';

        fetch('api_charts.php')
            .then(res => res.json())
            .then(data => {
                
                // Color mapping for Status
                const pieColors = data.kategori.labels.map(label => {
                    if(label === 'Teladan') return '#22c55e'; // Green
                    if(label === 'Perhatian Khusus') return '#eab308'; // Yellow
                    if(label === 'Pembinaan') return '#f97316'; // Orange
                    return '#ef4444'; // Red (Kritis)
                });

                // Init Pie Chart
                new Chart(document.getElementById('pieChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.kategori.labels,
                        datasets: [{
                            data: data.kategori.data,
                            backgroundColor: pieColors,
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        cutout: '70%'
                    }
                });

                // Init Bar Chart (Pelanggaran vs Prestasi)
                new Chart(document.getElementById('barChart'), {
                    type: 'bar',
                    data: {
                        labels: data.perbandingan.labels,
                        datasets: [
                            {
                                label: 'Poin Pelanggaran',
                                data: data.perbandingan.pelanggaran,
                                backgroundColor: '#ef4444',
                                borderRadius: 4
                            },
                            {
                                label: 'Poin Prestasi',
                                data: data.perbandingan.prestasi,
                                backgroundColor: '#eab308',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(255, 255, 255, 0.1)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });

            })
            .catch(err => console.error("Error loading charts", err));
    </script>
</body>
</html>
