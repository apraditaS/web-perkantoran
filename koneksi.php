<?php
$conn = new mysqli("localhost", "root", "", "db_uas_apradita");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->query("SET time_zone = '+07:00'");
?>