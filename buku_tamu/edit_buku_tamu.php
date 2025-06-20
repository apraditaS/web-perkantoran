<?php
include "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID surat tidak ditemukan'); window.location='data_buku_tamu.php';</script>";
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM tb_bukutamu WHERE id='$id'");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); window.location='data_buku_tamu.php';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit data buku tamu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 60px auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
            border-left: 6px solid #6e1414;
        }

        h2 {
            color: #6e1414;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            color: #6e1414;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #6e1414;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #541010;
        }

        .caption-action {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .alert {
            margin-top: 20px;
            padding: 12px;
            background-color: #fdd;
            border-left: 5px solid #6e1414;
            color: #6e1414;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Ubah Data Tamu</h2>

        <form class="form-container" action="edit_buku_tamu.php?id=<?= $id; ?>" method="POST"
            enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Instansi</label>
                <input type="text" name="instansi" class="form-control" value="<?= $row['instansi'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tujuan</label>
                <input type="text" name="tujuan" class="form-control" value="<?= $row['tujuan'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal & Waktu Kedatangan</label>
                <input type="datetime-local" name="waktu_datang" class="form-control"
                    value="<?= $row['waktu_datang'] ?>" required>
            </div>

            <div class="caption-action">
                <button type="submit" name="tbsimpan" class="btn btn-ubah">Simpan Perubahan</button>
                <a href="data_buku_tamu.php" class="btn btn-kembali">Kembali</a>
            </div>

        </form>

        <?php
        if (isset($_POST['tbsimpan'])) {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $instansi = $_POST['instansi'];
            $tujuan = $_POST['tujuan'];
            $waktu_datang = $_POST['waktu_datang'];
            $queryubah = "UPDATE tb_bukutamu 
            SET nama='$nama', instansi='$instansi', tujuan='$tujuan', waktu_datang='$waktu_datang' 
            WHERE id='$id'";
            $hasil = mysqli_query($conn, $queryubah);

            if ($hasil) {
                echo "<script>alert('Data berhasil diubah'); window.location='data_buku_tamu.php';</script>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Gagal menyimpan perubahan.</div>";
            }
        }
        ?>
    </div>
</body>

</html>