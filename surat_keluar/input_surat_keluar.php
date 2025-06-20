<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Surat Keluar</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            color: #333;
            height: 100vh;
            display: flex;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(6px);
            border-left: 6px solid #6e1414;
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
        }

        .title {
            font-size: 24px;
            color: #6e1414;
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }

        table {
            width: 100%;
        }

        td {
            padding: 10px;
            vertical-align: top;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #6e1414;
            color: #fff;
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #541010;
        }


        footer {
            background-color: Beige;
            color: maroon;
            text-align: left;
            padding: 10px 0;
            width: 100%;
            position: absolute;
            bottom: 0;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <main class="content-wrapper">
        <!-- Form -->
        <form method="post" enctype="multipart/form-data" class="form" autocomplete="off">
            <p class="title">Halaman Form Surat Keluar</p>
            <div class="form-group">
                <table>
                    <tr>
                        <td>No Surat</td>
                        <td><input type="text" name="no_surat" required></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td><input type="date" name="tanggal" required></td>
                    </tr>
                    <tr>
                        <td>Tujuan</td>
                        <td><input type="text" name="tujuan" required></td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td><input type="text" name="perihal" required></td>
                    </tr>
                    <tr>
                        <td>File</td>
                        <td><input type="file" name="file_upload" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" name="tbsimpan" value="Simpan"></td>
                    </tr>
                </table>
            </div>
        </form>
    </main>
    <footer>
        <p style="margin: 0;">&copy; 2025 By Apradita. All rights reserved.</p>
    </footer>
</body>

</html>

<?php
include __DIR__ . '/../koneksi.php';

if (isset($_POST['tbsimpan'])) {
    $no_surat = $_POST['no_surat'];
    $tanggal = $_POST['tanggal'];
    $tujuan = $_POST['tujuan'];
    $perihal = $_POST['perihal'];

    $file_name = $_FILES['file_upload']['name'];
    $file_tmp = $_FILES['file_upload']['tmp_name'];
    $folder = "../file/";

    if (move_uploaded_file($file_tmp, $folder . $file_name)) {
        $querysimpan = "INSERT INTO tb_suratkeluar (no_surat, tanggal, tujuan, perihal, file_upload)
                        VALUES ('$no_surat', '$tanggal', '$tujuan', '$perihal', '$file_name')";
        $simpandata = $conn->query($querysimpan);

        if ($simpandata) {
            echo "<script>alert('Data Berhasil disimpan');window.location='../admin/admin_page.php'</script>";
        } else {
            echo "Error simpan ke database: " . $conn->error;
        }
    } else {
        echo "Gagal upload file. Pastikan folder 'file/' sudah dibuat dan bisa ditulis.";
    }
}
?>