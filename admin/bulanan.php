<?php
$conn = new mysqli("localhost", "root", "", "db_uas_apradita");

// Ambil jumlah surat masuk
$queryMasuk = "SELECT MONTH(tanggal) AS bulan, COUNT(*) AS jumlah FROM tb_suratmasuk GROUP BY MONTH(tanggal)";
$resultMasuk = $conn->query($queryMasuk);

// Ambil jumlah surat keluar
$queryKeluar = "SELECT MONTH(tanggal) AS bulan, COUNT(*) AS jumlah FROM tb_suratkeluar GROUP BY MONTH(tanggal)";
$resultKeluar = $conn->query($queryKeluar);

// Ambil jumlah buku tamu
$queryTamu = "SELECT MONTH(waktu_datang) AS bulan, COUNT(*) AS jumlah FROM tb_bukutamu GROUP BY MONTH(waktu_datang)";
$resultTamu = $conn->query($queryTamu);

// Siapkan array 0 untuk 12 bulan
$suratMasuk = array_fill(1, 12, 0);
$suratKeluar = array_fill(1, 12, 0);
$bukuTamu = array_fill(1, 12, 0);

// Isi data surat masuk
while ($row = $resultMasuk->fetch_assoc()) {
    $suratMasuk[(int) $row['bulan']] = (int) $row['jumlah'];
}

// Isi data surat keluar
while ($row = $resultKeluar->fetch_assoc()) {
    $suratKeluar[(int) $row['bulan']] = (int) $row['jumlah'];
}

while ($row = $resultTamu->fetch_assoc()){
    $bukuTamu[(int) $row['bulan']] = (int) $row['jumlah'];
}

// akan di kirim ke json
echo json_encode([
    "masuk" => array_values($suratMasuk),
    "keluar" => array_values($suratKeluar),
    "tamu" => array_values($bukuTamu)
], JSON_NUMERIC_CHECK);
?>