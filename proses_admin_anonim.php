<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_laporan = $conn->real_escape_string($_POST['id_laporan']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE laporan_anonim SET status = '$status' WHERE id_laporan = '$id_laporan'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Status penyelidikan laporan rahasia berhasil diupdate.";
    }
    header("Location: admin_anonim.php");
    exit();
}
?>
