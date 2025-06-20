<?php
include "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID surat tidak ditemukan'); window.location='data_surat_keluar.php';</script>";
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM tb_suratkeluar WHERE id='$id'");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); window.location='data_surat_keluar.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit data surat keluar</title>
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
        <h2 class="text-center mb-4">Ubah Data Surat Keluar</h2>

        <form class="form-container" action="edit_surat_keluar.php?id=<?= $id; ?>" method="POST"
            enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="file_lama" value="<?= $row['file_upload'] ?>">

            <div class="mb-3">
                <label class="form-label">No Surat</label>
                <input type="text" name="no_surat" class="form-control" value="<?= $row['no_surat'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tujuan</label>
                <input type="text" name="tujuan" class="form-control" value="<?= $row['tujuan'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Perihal</label>
                <input type="text" name="perihal" class="form-control" value="<?= $row['perihal'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">File</label>
                <p>File sebelumnya:
                    <a href="../uploads/<?= $row['file_upload'] ?>" target="_blank">
                        <?= $row['file_upload'] ?>
                    </a>
                </p>
                <input type="file" name="file_upload" class="form-control">
            </div>

            <div class="caption-action">
                <button type="submit" name="tbsimpan" class="btn btn-ubah">Simpan Perubahan</button>
                <a href="data_surat_keluar.php" class="btn btn-kembali">Kembali</a>
            </div>
        </form>

        <?php
        if (isset($_POST['tbsimpan'])) {
            $id = $_POST['id'];
            $no_surat = $_POST['no_surat'];
            $tanggal = $_POST['tanggal'];
            $tujuan = $_POST['tujuan'];
            $perihal = $_POST['perihal'];
            $fileLama = $_POST['file_lama'];

            $fileName = $_FILES['file_upload']['name'];
            $tmpName = $_FILES['file_upload']['tmp_name'];
            $error = $_FILES['file_upload']['error'];

            if ($error === 0 && !empty($fileName)) {
                $uploadDir = '../file/';
                $uploadPath = $uploadDir . basename($fileName);

                if (move_uploaded_file($tmpName, $uploadPath)) {
                    // Hapus file lama (opsional)
                    if (file_exists($uploadDir . $fileLama)) {
                        unlink($uploadDir . $fileLama);
                    }
                    $namaFileFinal = $fileName;
                } else {
                    echo "<div class='alert'>Gagal mengupload file.</div>";
                    exit;
                }
            } else {
                $namaFileFinal = $fileLama;
            }

            $queryubah = "UPDATE tb_suratkeluar SET 
                            no_surat='$no_surat', 
                            tanggal='$tanggal', 
                            tujuan='$tujuan', 
                            perihal='$perihal', 
                            file_upload='$namaFileFinal' 
                          WHERE id='$id'";

            $hasil = mysqli_query($conn, $queryubah);

            if ($hasil) {
                echo "<script>alert('Data berhasil diubah'); window.location='data_surat_keluar.php';</script>";
            } else {
                echo "<div class='alert'>Gagal menyimpan perubahan.</div>";
            }
        }
        ?>
    </div>
</body>

</html>