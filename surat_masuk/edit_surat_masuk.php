<?php
include "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID surat tidak ditemukan'); window.location='data_surat_masuk.php';</script>";
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM tb_suratmasuk WHERE id='$id'");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); window.location='data_surat_masuk.php';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit data surat masuk</title>
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
        <h2 class="text-center mb-4">Ubah Data Surat Masuk</h2>

        <form class="form-container" action="edit_surat_masuk.php?id=<?= $id; ?>" method="POST"
            enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <div class="mb-3">
                <label class="form-label">No Surat</label>
                <input type="text" name="no_surat" class="form-control"
                    value="<?= htmlspecialchars($row['no_surat']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($row['tanggal']) ?>"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Pengirim</label>
                <input type="text" name="pengirim" class="form-control"
                    value="<?= htmlspecialchars($row['pengirim']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Perihal</label>
                <input type="text" name="perihal" class="form-control" value="<?= htmlspecialchars($row['perihal']) ?>"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">File</label>
                <p>File sebelumnya: <a href="../file/<?= htmlspecialchars($row['file_upload']) ?>"
                        target="_blank"><?= htmlspecialchars($row['file_upload']) ?></a></p>
                <input type="file" name="file_upload" class="form-control">
                <small style="color:#6e1414;">*Kosongkan jika tidak ingin mengganti file</small>
            </div>

            <div class="caption-action">
                <button type="submit" name="tbsimpan" class="btn btn-ubah">Simpan Perubahan</button>
                <a href="data_surat_masuk.php" class="btn btn-kembali">Kembali</a>
            </div>

        </form>

        <?php
        if (isset($_POST['tbsimpan'])) {
            $id = $_POST['id'];
            $no_surat = mysqli_real_escape_string($conn, $_POST['no_surat']);
            $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
            $pengirim = mysqli_real_escape_string($conn, $_POST['pengirim']);
            $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);

            // Cek apakah ada file baru diupload
            if (!empty($_FILES['file_upload']['name'])) {
                $fileName = $_FILES['file_upload']['name'];
                $tmpName = $_FILES['file_upload']['tmp_name'];
                $uploadDir = '../file/';
                $uploadPath = $uploadDir . basename($fileName);

                if (move_uploaded_file($tmpName, $uploadPath)) {
                    // Update dengan file baru
                    $queryubah = "UPDATE tb_suratmasuk SET 
                                    no_surat='$no_surat', 
                                    tanggal='$tanggal', 
                                    pengirim='$pengirim', 
                                    perihal='$perihal', 
                                    file_upload='$fileName' 
                                  WHERE id='$id'";
                } else {
                    echo "<div class='alert'>Gagal mengupload file.</div>";
                    exit;
                }
            } else {
                // Update tanpa ganti file_upload
                $queryubah = "UPDATE tb_suratmasuk SET 
                                no_surat='$no_surat', 
                                tanggal='$tanggal', 
                                pengirim='$pengirim', 
                                perihal='$perihal' 
                              WHERE id='$id'";
            }

            $hasil = mysqli_query($conn, $queryubah);

            if ($hasil) {
                echo "<script>alert('Data berhasil diubah'); window.location='data_surat_masuk.php';</script>";
            } else {
                echo "<div class='alert'>Gagal menyimpan perubahan.</div>";
            }
        }
        ?>
    </div>
</body>

</html>