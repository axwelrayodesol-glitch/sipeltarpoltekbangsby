<?php
session_start();
require 'config.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipe = $_POST['tipe'];
    
    if ($tipe == 'taruna') {
        $nama = $conn->real_escape_string($_POST['nama']);
        $nit = $conn->real_escape_string($_POST['nit']);
        $jurusan = $conn->real_escape_string($_POST['jurusan']);
        $angkatan = $conn->real_escape_string($_POST['angkatan']);
        $password = md5($_POST['password']);
        
        // Cek NIT duplicate
        $cek = $conn->query("SELECT nit FROM data_taruna WHERE nit = '$nit'");
        if ($cek->num_rows > 0) {
            $_SESSION['error'] = "Gagal! NIT $nit sudah terdaftar.";
        } else {
            $sql = "INSERT INTO data_taruna (nama, nit, jurusan, angkatan, password, total_poin_pelanggaran, total_poin_prestasi, kategori_status) 
                    VALUES ('$nama', '$nit', '$jurusan', '$angkatan', '$password', 0, 0, 'Teladan')";
            if($conn->query($sql)){
                $_SESSION['success'] = "Taruna $nama berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Database Error: " . $conn->error;
            }
        }
    } 
    else if ($tipe == 'staff') {
        $nama = $conn->real_escape_string($_POST['nama']);
        $role = $conn->real_escape_string($_POST['role']);
        $username = $conn->real_escape_string($_POST['username']);
        $password = md5($_POST['password']);
        
        // Cek username duplicate
        $cek = $conn->query("SELECT username FROM users WHERE username = '$username'");
        if ($cek->num_rows > 0) {
            $_SESSION['error'] = "Gagal! Username $username sudah digunakan.";
        } else {
            $sql = "INSERT INTO users (nama_lengkap, role, username, password) 
                    VALUES ('$nama', '$role', '$username', '$password')";
            if($conn->query($sql)){
                $_SESSION['success'] = "Staff $nama berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Database Error: " . $conn->error;
            }
        }
    }
    
    header("Location: admin_users.php");
    exit();
}
?>
