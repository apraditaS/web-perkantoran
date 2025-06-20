<?php
include '../koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id_disposisi = $_GET['id'];

// Update status_baca
$stmt = $conn->prepare("UPDATE tb_disposisi SET status_baca = 'Sudah Dibaca' WHERE id_disposisi = ?");
$stmt->bind_param("i", $id_disposisi);
$stmt->execute();

// Ambil file surat
$query = "
    SELECT s.file_upload
    FROM tb_disposisi d
    JOIN tb_suratmasuk s ON d.id_surat = s.id
    WHERE d.id_disposisi = ?
";
$stmt2 = $conn->prepare($query);
$stmt2->bind_param("i", $id_disposisi);
$stmt2->execute();
$result = $stmt2->get_result();
$data = $result->fetch_assoc();

if ($data) {
    $file = "../file/" . $data['file_upload'];
    if (file_exists($file)) {
        header("Location: $file");
        exit;
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "Data tidak ditemukan.";
}
?>