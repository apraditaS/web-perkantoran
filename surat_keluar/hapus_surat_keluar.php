<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $queryhapus = "DELETE FROM tb_suratkeluar WHERE id = '$id'";
    $hapusdata = $conn->query($queryhapus);

    if ($hapusdata) {
        echo "<script>alert('Data berhasil dihapus');window.location='data_surat_keluar.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>