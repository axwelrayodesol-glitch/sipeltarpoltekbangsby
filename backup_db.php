<?php
require 'config.php';

// Nama file database hasil export
$filename = "backup_sipeltar_" . date("Y-m-d_H-i-s") . ".sql";

// Header untuk force download file SQL
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Ambil semua nama tabel
$tables = array();
$result = $conn->query("SHOW TABLES");
while($row = $result->fetch_row()){
    $tables[] = $row[0];
}

$sqlScript = "";
$sqlScript .= "-- Database Backup for SIPELTAR\n";
$sqlScript .= "-- Waktu Backup: " . date("Y-m-d H:i:s") . "\n\n";

// Loop untuk setiap tabel
foreach($tables as $table){
    // Tambahkan DROP TABLE IF EXISTS
    $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";

    // Dapatkan struktur CREATE TABLE
    $row = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
    $sqlScript .= "\n".$row[1].";\n\n";

    // Ambil data (Isi) dari tabel
    $result = $conn->query("SELECT * FROM `$table`");
    $columnCount = $result->field_count;

    for($i = 0; $i < $columnCount; $i++){
        while($row = $result->fetch_row()){
            $sqlScript .= "INSERT INTO `$table` VALUES(";
            for($j = 0; $j < $columnCount; $j++){
                $row[$j] = $row[$j];

                if(isset($row[$j])){
                    $sqlScript .= '"' . $conn->real_escape_string($row[$j]) . '"';
                }else{
                    $sqlScript .= '""';
                }
                if($j < ($columnCount - 1)){
                    $sqlScript .= ',';
                }
            }
            $sqlScript .= ");\n";
        }
    }
    $sqlScript .= "\n";
}

echo $sqlScript;
?>
