<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taruna') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_taruna = $_SESSION['id_taruna'];
    $descriptor = $conn->real_escape_string($_POST['descriptor']);

    if (!empty($descriptor)) {
        $sql = "UPDATE data_taruna SET face_descriptor = '$descriptor' WHERE id_taruna = $id_taruna";
        if ($conn->query($sql)) {
            $_SESSION['success'] = "DNA Wajah Digital berhasil direkam secara permanen.";
        } else {
            $_SESSION['error'] = "Gagal menyimpan data ke database.";
        }
    }

    header("Location: taruna_wajah.php");
    exit();
}
?>
