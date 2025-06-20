<?php
session_start();
include '../koneksi.php';

$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
$id_surat = isset($_GET['id_surat']) ? (int) $_GET['id_surat'] : 0;

// Pastikan id_surat dan user_id valid
if ($id_surat > 0 && $user_id > 0) {
    error_log("Mencoba memperbarui status baca untuk ID Surat: $id_surat, User ID: $user_id");

    $stmt = $conn->prepare("UPDATE tb_disposisi SET status_baca = 'Sudah Dibaca' WHERE id_surat = ? AND id_penerima = ?");
    $stmt->bind_param("ii", $id_surat, $user_id);

    if ($stmt->execute()) {
        error_log("Status baca berhasil diperbarui untuk ID Surat: $id_surat, User ID: $user_id");
    } else {
        error_log("Gagal memperbarui status baca: " . $stmt->error);
    }
    $stmt->close();
} else {
    error_log("ID Surat atau User ID tidak valid: ID Surat = $id_surat, User ID = $user_id");
}

// Cek apakah file ada
$filepath = '../file/' . $filename;
error_log("Mencari file di: " . $filepath); // Log path file
if (!file_exists($filepath)) {
    header("HTTP/1.0 404 Not Found");
    echo "File tidak ditemukan.";
    exit;
}

// Lanjutkan dengan pengiriman file
$fileinfo = pathinfo($filepath);
$ext = strtolower($fileinfo['extension']);

$contentTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'doc' => 'application/msword'
];

if (isset($contentTypes[$ext])) {
    header('Content-Type: ' . $contentTypes[$ext]);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    readfile($filepath);
    exit;
} else {
    // Untuk ekstensi yang tidak support, bisa disuruh download:
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    readfile($filepath);
    exit;
}
