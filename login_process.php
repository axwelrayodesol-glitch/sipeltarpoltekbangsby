<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = md5($_POST['password']); 

    // 1. Cek di tabel users (multi-role)
    $sql_admin = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result_admin = $conn->query($sql_admin);

    if ($result_admin->num_rows > 0) {
        $row = $result_admin->fetch_assoc();
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['nama'] = $row['nama_lengkap'];
        $_SESSION['role'] = $row['role'];
        
        // Catat aktivitas
        $conn->query("INSERT INTO riwayat_aktivitas (deskripsi, waktu) VALUES ('User ".$row['nama_lengkap']." (".$row['role'].") login ke sistem', NOW())");

        // Redirect sesuai role
        if($row['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else if($row['role'] == 'pembina') {
            header("Location: pembina_dashboard.php");
        } else {
            // Default fallback
            header("Location: admin_dashboard.php"); 
        }
        exit();
    }

    // 2. Cek di tabel taruna
    $sql_taruna = "SELECT * FROM data_taruna WHERE nit = '$username' AND password = '$password'";
    $result_taruna = $conn->query($sql_taruna);

    if ($result_taruna->num_rows > 0) {
        $row = $result_taruna->fetch_assoc();
        $_SESSION['id_taruna'] = $row['id_taruna'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['nit'] = $row['nit'];
        $_SESSION['role'] = 'taruna';

        header("Location: taruna_dashboard.php");
        exit();
    }

    // Jika gagal
    $_SESSION['error'] = "Username/NIT atau Password salah!";
    header("Location: index.php");
    exit();
}
?>
