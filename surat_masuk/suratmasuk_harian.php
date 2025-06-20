<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Surat Masuk Hari Ini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            color: #333;
        }

        .table-custom {
            width: 95%;
            margin: 50px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            overflow: hidden;
        }

        .table-custom th,
        .table-custom td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        .table-custom thead {
            background-color: #6e1414;
            color: white;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: #f6eee3;
        }

        .btn-ubah,
        .btn-hapus,
        .btn-view {
            text-decoration: none;
            padding: 8px 14px;
            margin: 3px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
            transition: background 0.3s ease;
        }

        .btn-ubah {
            background-color: #bfa181;
            color: white;
        }

        .btn-hapus {
            background-color: #a83232;
            color: white;
        }

        .btn-view {
            background-color: #bfa181;
            color: white;
        }

        caption h1 {
            margin: 20px 0 10px;
            font-size: 28px;
            color: #6e1414;
        }

        .total {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #6e1414;
            margin: 30px 0;
        }

        .btn-kembali-container {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 999;
        }

        .btn-kembali {
            background-color: #6e1414;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .btn-kembali i {
            margin-right: 8px;
        }

        .btn-kembali:hover {
            background-color: #541010;
        }
    </style>
</head>

<body>
    <script>
        history.pushState(null, null, location.href);

        window.onpopstate = function () {
            location.href = '../admin/admin_page.php';
        };
    </script>
    <div class="btn-kembali-container">
        <a href="/uas_web_kelas11_apradita/admin/admin_page.php" class="btn-kembali" title="Kembali ke Dashboard">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <table class="table-custom">
        <caption>
            <h1>Data Surat Masuk Hari Ini</h1>
        </caption>
        <thead>
            <tr>
                <th>No</th>
                <th>No Surat</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Perihal</th>
                <th>File</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <?php
        include '../koneksi.php';

        // Ganti dengan id user yang sedang login
        $id_user = 1;

        // Query ambil data surat hari ini, urut berdasarkan tanggal terbaru
        $query = $conn->query("
    SELECT sm.*
    FROM tb_suratmasuk sm
    WHERE sm.tanggal = CURDATE()
    ORDER BY sm.tanggal DESC
");
        $jumlah = $query->num_rows;
        $no = 1;

        echo "<tbody>";
        if ($jumlah > 0) {
            while ($row = $query->fetch_array()) {
                echo "<tr>
            <td>{$no}</td>
            <td>{$row['no_surat']}</td>
            <td>{$row['tanggal']}</td>
            <td>{$row['pengirim']}</td>
            <td>{$row['perihal']}</td>
            <td><a href='../file/{$row['file_upload']}' download='{$row['file_upload']}'>Download</a></td>
            <td>
                <a href='hapus_surat_masuk.php?id={$row['id']}' class='btn-hapus' title='Hapus Data'><i class='fas fa-trash-alt'></i></a>
            </td>
        </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='7'>Tidak ada surat masuk hari ini.</td></tr>";
        }
        echo "</tbody>";
        ?>

    </table>

    <div class="total">
        Total Surat Masuk Hari Ini: <?php echo $jumlah; ?>
    </div>
</body>

</html>