<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Hapus dulu disposisi yang terkait
    $hapus_disposisi = $conn->query("DELETE FROM tb_disposisi WHERE id_surat = '$id'");

    if ($hapus_disposisi) {
        // Lalu hapus surat masuk-nya
        $hapus_surat = $conn->query("DELETE FROM tb_suratmasuk WHERE id = '$id'");

        if ($hapus_surat) {
            echo "<script>alert('Data berhasil dihapus');window.location='data_surat_masuk.php';</script>";
        } else {
            echo "Error hapus surat: " . $conn->error;
        }
    } else {
        echo "Error hapus disposisi: " . $conn->error;
    }
}
?>