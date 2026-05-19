<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}
$id_taruna = $_SESSION['id_taruna'];
$info = $conn->query("SELECT face_descriptor FROM data_taruna WHERE id_taruna = $id_taruna")->fetch_assoc();
$sudah_daftar = !empty($info['face_descriptor']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Registrasi Wajah AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Face API JS -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js"></script>
    
    <style>
        body { background-color: #0b1120; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        #video-container { position: relative; display: flex; justify-content: center; align-items: center; border-radius: 1rem; overflow: hidden; background: #000; min-height: 300px; }
        canvas { position: absolute; top: 0; left: 0; }
        video { object-fit: cover; width: 100%; height: 100%; }
        .scanning-line {
            position: absolute; width: 100%; height: 2px; background: #3b82f6; box-shadow: 0 0 10px #3b82f6;
            animation: scan 2s linear infinite; opacity: 0.5; z-index: 10;
        }
        @keyframes scan { 0% { top: 0%; } 100% { top: 100%; } }
    </style>
</head>
<body class="p-8">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-camera-retro mr-2 text-blue-500"></i> Setor Wajah Digital (AI)</h2>
            <a href="taruna_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4"><i class="fas fa-check mr-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if($sudah_daftar): ?>
        <div class="bg-green-900/30 border border-green-500/50 rounded-2xl p-6 text-center shadow-[0_0_20px_rgba(34,197,94,0.1)]">
            <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">Wajah Anda Telah Terdaftar</h3>
            <p class="text-gray-400 text-sm">DNA Digital wajah Anda sudah tersimpan di sistem. Anda sudah bisa melakukan Check-In otomatis di Pos Penjagaan tanpa menggunakan HP atau kartu.</p>
            <p class="text-xs text-yellow-500 mt-4 italic">Tekan tombol di bawah hanya jika Anda ingin memperbarui/mengulang scan wajah.</p>
        </div>
        <?php endif; ?>

        <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-xl relative">
            <div id="loader" class="absolute inset-0 bg-gray-900/90 z-50 flex flex-col items-center justify-center rounded-2xl">
                <i class="fas fa-brain text-blue-500 text-4xl mb-4 animate-bounce"></i>
                <p class="text-blue-400 font-bold" id="loaderText">Memuat Model Kecerdasan Buatan...</p>
                <p class="text-xs text-gray-500 mt-2">Pastikan internet Anda stabil (Mengunduh ~5MB file AI)</p>
            </div>

            <div class="mb-4 bg-blue-500/10 border-l-4 border-blue-500 text-blue-400 p-4 rounded text-sm">
                <i class="fas fa-info-circle mr-2"></i> <b>Instruksi:</b> Pastikan pencahayaan terang, tidak memakai topi/masker. Posisikan wajah Anda tepat di tengah kamera, lalu tunggu sampai kotak biru mendeteksi wajah Anda.
            </div>

            <div id="video-container" class="mb-6 w-full max-w-lg mx-auto">
                <video id="video" autoplay muted playsinline></video>
                <div class="scanning-line" id="scanLine" style="display:none;"></div>
            </div>
            
            <form action="proses_wajah.php" method="POST" id="faceForm" class="hidden text-center">
                <input type="hidden" name="descriptor" id="descriptorInput">
                <p class="text-green-400 font-bold mb-4" id="successMsg"><i class="fas fa-check-circle"></i> Wajah Terdeteksi Sempurna!</p>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-transform hover:scale-105">
                    SIMPAN DNA WAJAH SAYA
                </button>
            </form>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const loader = document.getElementById('loader');
        const loaderText = document.getElementById('loaderText');
        const form = document.getElementById('faceForm');
        const scanLine = document.getElementById('scanLine');
        let currentStream;

        // Load models from CDN
        async function loadModels() {
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/';
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                loaderText.innerText = "Memuat Landmark...";
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                loaderText.innerText = "Memuat Recognition...";
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                
                loaderText.innerText = "Mengaktifkan Kamera...";
                startVideo();
            } catch (err) {
                console.error(err);
                alert("Error loading AI: " + err.message);
                loaderText.innerText = "Gagal memuat AI. Pastikan internet aktif.";
                loaderText.classList.replace('text-blue-400', 'text-red-400');
            }
        }

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(stream => {
                    currentStream = stream;
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.error(err);
                    loaderText.innerText = "Kamera tidak diizinkan atau tidak ditemukan.";
                    loaderText.classList.replace('text-blue-400', 'text-red-400');
                });
        }

        video.addEventListener('play', () => {
            loader.style.display = 'none';
            scanLine.style.display = 'block';
            
            const container = document.getElementById('video-container');
            const canvas = faceapi.createCanvasFromMedia(video);
            container.append(canvas);
            
            const displaySize = { width: video.videoWidth || 500, height: video.videoHeight || 375 };
            // Kalo video blm ada ukuran, kita pake fallback width container
            if(!video.videoWidth) {
                 displaySize.width = container.clientWidth;
                 displaySize.height = container.clientHeight;
            }
            faceapi.matchDimensions(canvas, displaySize);

            // Scan interval
            const scanInterval = setInterval(async () => {
                const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                
                if (detection) {
                    const resizedDetections = faceapi.resizeResults(detection, displaySize);
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    
                    // Wajah sukses terbaca sempurna
                    if(detection.descriptor) {
                        clearInterval(scanInterval);
                        scanLine.style.display = 'none';
                        
                        // Konversi Float32Array ke JSON string
                        const descriptorArray = Array.from(detection.descriptor);
                        document.getElementById('descriptorInput').value = JSON.stringify(descriptorArray);
                        
                        // Hentikan kamera agar gambar freeze
                        if(currentStream) {
                            currentStream.getTracks().forEach(track => track.stop());
                        }
                        
                        form.classList.remove('hidden');
                    }
                }
            }, 300);
        });

        // Mulai eksekusi
        loadModels();
    </script>
</body>
</html>
