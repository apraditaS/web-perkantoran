<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $queryhapus = "DELETE FROM tb_bukutamu WHERE id = '$id'";
    $hapusdata = $conn->query($queryhapus);

    if ($hapusdata) {
        echo "<script>alert('Data berhasil dihapus');window.location='data_buku_tamu.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>