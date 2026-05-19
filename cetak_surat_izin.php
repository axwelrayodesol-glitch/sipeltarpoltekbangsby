<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID Izin tidak ditemukan.");
}

$id_izin = $conn->real_escape_string($_GET['id']);
$query = "SELECT i.*, t.nama, t.nit, t.jurusan, t.angkatan 
          FROM pengajuan_izin i 
          JOIN data_taruna t ON i.id_taruna = t.id_taruna 
          WHERE i.id_izin = '$id_izin' AND i.status_approval = 'Disetujui Pembina'";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("Data izin tidak ditemukan atau belum disetujui.");
}

$data = $result->fetch_assoc();

// Format Tanggal Indo
function tgl_indo($tanggal){
	$bulan = array (
		1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
	);
	$pecahkan = explode('-', $tanggal);
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Khusus - <?php echo $data['nama']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', Times, serif; background-color: #f3f4f6; }
        .page { 
            width: 210mm; min-height: 297mm; 
            padding: 20mm; margin: 10mm auto; 
            background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        @media print {
            body { background-color: white; }
            .page { margin: 0; box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
        }
        .kop-surat { border-bottom: 4px solid black; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-surat .double-line { border-bottom: 1px solid black; margin-top: 2px; }
    </style>
</head>
<body>
    
    <div class="text-center mt-5 mb-5 no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-lg">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak / Simpan sebagai PDF
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow-lg ml-2">Tutup</button>
    </div>

    <div class="page">
        <!-- Kop Surat -->
        <div class="kop-surat text-center relative">
            <h1 class="text-xl font-bold uppercase tracking-widest">KEMENTERIAN PENDIDIKAN DAN KEDISIPLINAN</h1>
            <h2 class="text-2xl font-black uppercase">AKADEMI TARUNA SIPELTAR</h2>
            <p class="text-sm mt-1">Jl. Pendidikan No. 1, Cyber City, Indonesia 12345</p>
            <p class="text-xs">Telp: (021) 1234567 | Email: info@sipeltar.ac.id | Web: www.sipeltar.ac.id</p>
            <div class="double-line"></div>
        </div>

        <!-- Judul -->
        <div class="text-center mb-8">
            <h3 class="font-bold underline text-lg uppercase">SURAT IZIN KHUSUS TARUNA</h3>
            <p class="text-sm">Nomor: B/<?php echo str_pad($data['id_izin'], 4, '0', STR_PAD_LEFT); ?>/SIP/<?php echo date('Y'); ?></p>
        </div>

        <!-- Isi Surat -->
        <div class="text-justify leading-relaxed">
            <p class="mb-4">Diberikan izin khusus meninggalkan ksatrian / asrama kepada Taruna di bawah ini:</p>
            
            <table class="w-full mb-4 ml-4">
                <tr><td class="w-32 py-1">Nama</td><td class="w-4">:</td><td class="font-bold uppercase"><?php echo $data['nama']; ?></td></tr>
                <tr><td class="py-1">NIT</td><td>:</td><td><?php echo $data['nit']; ?></td></tr>
                <tr><td class="py-1">Jurusan/Angkatan</td><td>:</td><td><?php echo $data['jurusan'] . ' / ' . $data['angkatan']; ?></td></tr>
            </table>

            <p class="mb-2">Untuk keperluan <b><?php echo strtoupper($data['jenis_izin']); ?></b>, dengan rincian penjelasan sebagai berikut:</p>
            <p class="mb-4 p-3 bg-gray-100 italic border border-gray-300">"<?php echo $data['alasan']; ?>"</p>

            <table class="w-full mb-6 ml-4">
                <tr><td class="w-32 py-1">Tanggal Mulai</td><td class="w-4">:</td><td class="font-bold"><?php echo tgl_indo($data['tanggal_mulai']); ?></td></tr>
                <tr><td class="py-1">Tanggal Selesai</td><td>:</td><td class="font-bold"><?php echo tgl_indo($data['tanggal_selesai']); ?></td></tr>
                <tr><td class="py-1">Status Dokumen</td><td>:</td><td class="font-bold">Terverifikasi & Disetujui secara Elektronik</td></tr>
            </table>

            <p class="mb-8">Surat izin ini berlaku selama tanggal yang disebutkan di atas. Kepada pihak terkait (Petugas Jaga/Keamanan) dimohon maklum dan dapat memberikan bantuan seperlunya. Apabila taruna tersebut terlambat kembali ke asrama tanpa alasan yang sah, akan dikenakan sanksi disiplin sesuai peraturan yang berlaku.</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="flex justify-end mt-12">
            <div class="text-center w-64">
                <p>Cyber City, <?php echo tgl_indo(date('Y-m-d')); ?></p>
                <p class="font-bold mb-16">a.n. Komandan / Pembina Asrama</p>
                
                <!-- Stempel Digital -->
                <div class="absolute w-24 h-24 border-4 border-red-500 rounded-full opacity-50 right-20 transform -translate-y-12 flex items-center justify-center rotate-[-15deg]">
                    <span class="text-red-500 font-bold text-[10px] text-center leading-none">APPROVED<br>SIPELTAR<br>SYSTEM</span>
                </div>

                <p class="font-bold underline uppercase">Telah Disetujui</p>
                <p class="text-sm">Sistem Informasi SIPELTAR</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-20 text-[10px] text-gray-500 border-t border-gray-300 pt-2 text-center">
            Dokumen ini dicetak secara otomatis dari Sistem Pendataan Elektronik Taruna (SIPELTAR). Validitas dokumen dapat dicek melalui database pusat.
        </div>
    </div>

    <script>
        // Auto Print when opened (optional, uncomment if desired)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
