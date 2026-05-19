<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'pembina' && $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

// Fetch semua data wajah dari DB untuk diload ke Javascript
$query = "SELECT id_taruna, nit, nama, face_descriptor FROM data_taruna WHERE face_descriptor IS NOT NULL AND face_descriptor != ''";
$result = $conn->query($query);
$registered_faces = [];

while($row = $result->fetch_assoc()) {
    $registered_faces[] = [
        'id' => $row['id_taruna'],
        'nit' => $row['nit'],
        'nama' => $row['nama'],
        'descriptor' => json_decode($row['face_descriptor'], true)
    ];
}
$faces_json = json_encode($registered_faces);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Terminal AI Face Scanner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Face API JS -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js"></script>
    
    <style>
        body { background-color: #000; color: #e2e8f0; font-family: 'Inter', sans-serif; overflow: hidden; }
        #video-container { position: relative; width: 100vw; height: 100vh; display: flex; justify-content: center; align-items: center; }
        video { position: absolute; object-fit: cover; width: 100%; height: 100%; z-index: 1; opacity: 0.8; }
        canvas { position: absolute; top: 0; left: 0; z-index: 2; width: 100%; height: 100%; object-fit: cover; }
        
        /* HUD Overlay */
        .hud { position: absolute; z-index: 10; pointer-events: none; }
        .hud-top { top: 0; left: 0; right: 0; padding: 2rem; background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent); }
        .hud-bottom { bottom: 0; left: 0; right: 0; padding: 2rem; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); text-align: center; }
        
        .scanning-reticle {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 300px; height: 300px; border: 2px dashed rgba(59, 130, 246, 0.5); z-index: 5;
            animation: spin 10s linear infinite; pointer-events: none;
        }
        @keyframes spin { 100% { transform: translate(-50%, -50%) rotate(360deg); } }
        
        #resultToast {
            position: absolute; bottom: 100px; left: 50%; transform: translateX(-50%);
            z-index: 20; transition: all 0.3s; opacity: 0;
        }
        .show-toast { opacity: 1 !important; transform: translate(-50%, -20px) !important; }
    </style>
</head>
<body>
    
    <!-- Loading Screen -->
    <div id="loader" class="absolute inset-0 bg-gray-900 z-50 flex flex-col items-center justify-center">
        <i class="fas fa-satellite-dish text-blue-500 text-6xl mb-4 animate-pulse"></i>
        <h1 class="text-3xl font-bold text-white tracking-widest mb-2">SISTEM PENJAGAAN PINTAR</h1>
        <p class="text-blue-400 font-bold" id="loaderText">Menghidupkan Mesin Kecerdasan Buatan (AI)...</p>
        <p class="text-xs text-gray-500 mt-2">Mengunduh Modul Pengenalan Wajah (~5MB). Harap tunggu...</p>
    </div>

    <!-- Main Terminal -->
    <div id="video-container">
        <video id="video" autoplay muted playsinline></video>
        <div class="scanning-reticle"></div>
        
        <div class="hud hud-top flex justify-between items-start">
            <div>
                <h2 class="text-red-500 font-bold tracking-widest text-xl"><i class="fas fa-video"></i> POS JAGA UTAMA</h2>
                <p class="text-xs text-gray-400 font-mono">STATUS: MENCARI TARGET (AUTO CHECK-IN)</p>
            </div>
            <a href="admin_dashboard.php" class="bg-red-600/50 border border-red-500 text-white px-4 py-2 text-xs font-bold rounded pointer-events-auto hover:bg-red-600">
                <i class="fas fa-times mr-1"></i> MATIKAN RADAR
            </a>
        </div>
        
        <div id="resultToast" class="bg-green-900/90 border-2 border-green-500 p-6 rounded-2xl shadow-[0_0_50px_rgba(34,197,94,0.3)] backdrop-blur-md flex items-center gap-6">
            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white text-3xl">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <p class="text-sm text-green-400 font-bold tracking-widest uppercase">AKSES DITERIMA (CHECK-IN)</p>
                <h3 class="text-2xl font-bold text-white mt-1" id="resNama">Nama Taruna</h3>
                <p class="text-gray-400 text-sm mt-1">NIT: <span id="resNit" class="text-white font-bold">123</span></p>
            </div>
        </div>

        <div class="hud hud-bottom">
            <p class="text-gray-400 text-xs tracking-widest uppercase">Silakan arahkan wajah Taruna ke depan kamera</p>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const loader = document.getElementById('loader');
        const loaderText = document.getElementById('loaderText');
        
        // Data dari PHP
        const dbFaces = <?php echo $faces_json; ?>;
        let faceMatcher = null;
        let isProcessing = false; // Mencegah spam absen beruntun ke orang yang sama

        async function loadModels() {
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/';
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                loaderText.innerText = "Memuat Modul Geometri...";
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                loaderText.innerText = "Memuat Database Wajah...";
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                
                initFaceMatcher();
                startVideo();
            } catch (err) {
                console.error(err);
                alert("Error loading AI: " + err.message);
                loaderText.innerText = "Gagal memuat AI. Pastikan internet aktif.";
            }
        }

        function initFaceMatcher() {
            const labeledDescriptors = [];
            dbFaces.forEach(face => {
                if(face.descriptor && face.descriptor.length === 128) {
                    const descArray = new Float32Array(face.descriptor);
                    // Kita jadikan 'nit' sebagai label
                    labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(face.nit.toString(), [descArray]));
                }
            });
            
            if(labeledDescriptors.length > 0) {
                // 0.5 adalah threshold kecocokan. Makin kecil makin ketat.
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5); 
            } else {
                console.warn("Belum ada Taruna yang mendaftarkan wajah.");
            }
        }

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.error(err);
                    loaderText.innerText = "Akses kamera ditolak.";
                });
        }

        video.addEventListener('play', () => {
            loader.style.display = 'none';
            
            const container = document.getElementById('video-container');
            const canvas = faceapi.createCanvasFromMedia(video);
            container.append(canvas);
            
            const displaySize = { width: video.clientWidth, height: video.clientHeight };
            faceapi.matchDimensions(canvas, displaySize);

            setInterval(async () => {
                if(isProcessing) return; // Tunggu proses sebelumnya selesai

                // Deteksi wajah
                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                
                // Gambar kotak wajah
                faceapi.draw.drawDetections(canvas, resizedDetections);

                if (detections.length > 0 && faceMatcher) {
                    const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
                    
                    results.forEach((result, i) => {
                        const box = resizedDetections[i].detection.box;
                        const label = result.label;
                        
                        // Gambar Label di atas kotak wajah
                        const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                        drawBox.draw(canvas);

                        // Jika menemukan wajah yg terdaftar (bukan unknown)
                        if(label !== 'unknown') {
                            const matchedTaruna = dbFaces.find(t => t.nit == label);
                            if(matchedTaruna) {
                                processCheckIn(matchedTaruna);
                            }
                        }
                    });
                }
            }, 500); // scan setiap 0.5 detik
        });

        function processCheckIn(taruna) {
            isProcessing = true; // Kunci sistem agar tidak memproses wajah yg sama ratusan kali
            
            // Tampilkan UI sukses
            document.getElementById('resNama').innerText = taruna.nama;
            document.getElementById('resNit').innerText = taruna.nit;
            const toast = document.getElementById('resultToast');
            toast.classList.add('show-toast');

            // Kirim data ke backend via AJAX
            const formData = new FormData();
            formData.append('id_taruna', taruna.id);
            formData.append('nit', taruna.nit);

            fetch('proses_scan_wajah.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(response => {
                console.log(response); // Debug log (Sudah diabsen/dll)
                
                // Sembunyikan notif setelah 3 detik dan buka kunci sistem
                setTimeout(() => {
                    toast.classList.remove('show-toast');
                    isProcessing = false;
                }, 3000);
            })
            .catch(error => {
                console.error(error);
                isProcessing = false;
            });
        }

        loadModels();
    </script>
</body>
</html>
