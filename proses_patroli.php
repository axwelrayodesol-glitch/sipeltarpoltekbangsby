<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembina') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pembina = $_SESSION['id_user'];
    $lokasi = $conn->real_escape_string($_POST['lokasi']);
    $catatan = $conn->real_escape_string($_POST['catatan']);

    $sql = "INSERT INTO histori_patroli (id_pembina, lokasi_patroli, catatan) VALUES ('$id_pembina', '$lokasi', '$catatan')";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Log patroli berhasil disimpan.";
    }

    header("Location: pembina_patroli.php");
    exit();
}
?>
