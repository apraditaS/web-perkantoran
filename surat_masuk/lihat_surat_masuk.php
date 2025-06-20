<?php
include '../koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan!";
    exit;
}

$id = intval($_GET['id']);
$query = $conn->query("SELECT * FROM tb_suratmasuk WHERE id = $id");

if ($query->num_rows == 0) {
    echo "Data tidak ditemukan!";
    exit;
}

$data = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Surat Keluar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(6px);
            border-radius: 12px;
            padding: 30px 40px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-left: 6px solid #6e1414;
        }

        h2 {
            text-align: center;
            color: #6e1414;
            margin-bottom: 25px;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 10px 5px;
            vertical-align: top;
        }

        td:first-child {
            font-weight: bold;
            width: 35%;
            color: #6e1414;
        }

        td:last-child {
            word-break: break-word;
        }

        .btn-back {
            display: block;
            margin-top: 30px;
            text-decoration: none;
            background-color: #6e1414;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #541010;
        }

        @media (max-width: 500px) {
            .container {
                padding: 20px;
            }

            td:first-child {
                width: 100%;
                display: block;
                margin-bottom: 5px;
            }

            td {
                display: block;
                width: 100%;
            }

            table,
            tr {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Detail Surat Keluar</h2>
        <table>
            <tr>
                <td>No. Surat</td>
                <td><?= $data['no_surat'] ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td><?= $data['tanggal'] ?></td>
            </tr>
            <tr>
                <td>Pengirim</td>
                <td><?= $data['pengirim'] ?></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td><?= $data['perihal'] ?></td>
            </tr>
            <tr>
                <td>File</td>
                <td>
                    <?php if (!empty($data['file_upload'])): ?>
                        <a href="../file/<?= $data['file_upload'] ?>" target="_blank">Lihat File</a>
                    <?php else: ?>
                        Tidak ada file
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <a href="data_surat_masuk.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>

</html>