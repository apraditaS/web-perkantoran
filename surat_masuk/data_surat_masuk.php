<?php
include '../koneksi.php';

$keyword = '';
$orderBy = " ORDER BY sm.tanggal DESC ";

if (isset($_GET['search'])) {
    $keyword = trim($_GET['search']);
}

// Buat query search jika keyword tidak kosong
if ($keyword != '') {
    $sql = $conn->prepare("
        SELECT sm.*, l.nama AS nama_tujuan, d.status_baca
        FROM tb_suratmasuk sm
        LEFT JOIN tb_login l ON sm.id_tujuan_disposisi = l.id
        LEFT JOIN (
            SELECT id_surat, status_baca
            FROM tb_disposisi
            WHERE id_disposisi IN (
                SELECT MAX(id_disposisi) FROM tb_disposisi GROUP BY id_surat
            )
        ) d ON sm.id = d.id_surat
        WHERE sm.no_surat LIKE ? OR sm.pengirim LIKE ? OR sm.perihal LIKE ? OR d.status_baca LIKE ?
        $orderBy
    ");
    $like_keyword = "%$keyword%";
    $sql->bind_param("ssss", $like_keyword, $like_keyword, $like_keyword, $like_keyword);
    $sql->execute();
    $tampilsurat = $sql->get_result();

    $jumlahdata = $conn->prepare("
        SELECT COUNT(*) as jum
        FROM tb_suratmasuk sm
        LEFT JOIN (
            SELECT id_surat, status_baca
            FROM tb_disposisi
            WHERE id_disposisi IN (
                SELECT MAX(id_disposisi) FROM tb_disposisi GROUP BY id_surat
            )
        ) d ON sm.id = d.id_surat
        WHERE sm.no_surat LIKE ? OR sm.pengirim LIKE ? OR sm.perihal LIKE ? OR d.status_baca LIKE ?
    ");
    $jumlahdata->bind_param("ssss", $like_keyword, $like_keyword, $like_keyword, $like_keyword);
    $jumlahdata->execute();
    $jumlah = $jumlahdata->get_result()->fetch_assoc()['jum'];
} else {
    // Jika tidak ada pencarian
    $tampilsurat = $conn->query("
        SELECT sm.*, l.nama AS nama_tujuan, d.status_baca
        FROM tb_suratmasuk sm
        LEFT JOIN tb_login l ON sm.id_tujuan_disposisi = l.id
        LEFT JOIN (
            SELECT id_surat, status_baca
            FROM tb_disposisi
            WHERE id_disposisi IN (
                SELECT MAX(id_disposisi) FROM tb_disposisi GROUP BY id_surat
            )
        ) d ON sm.id = d.id_surat
        $orderBy
    ");
    $jumlahdata = $conn->query("
        SELECT COUNT(*) as jum
        FROM tb_suratmasuk sm
        LEFT JOIN (
            SELECT id_surat, status_baca
            FROM tb_disposisi
            WHERE id_disposisi IN (
                SELECT MAX(id_disposisi) FROM tb_disposisi GROUP BY id_surat
            )
        ) d ON sm.id = d.id_surat
    ");
    $jumlah = $jumlahdata->fetch_assoc()['jum'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Surat Masuk</title>
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
            margin: 10px auto;
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

        .btn-tambah,
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

        .btn-tambah {
            background-color: #6e1414;
            color: white;
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
            color: #bfa181;
            margin-bottom: 50px;
        }

        /* Search form styling */
        .search-form {
            width: 95%;
            margin: 20px auto;
            text-align: center;
        }

        .search-form input[type="text"] {
            padding: 8px 12px;
            width: 300px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 8px 16px;
            background-color: #6e1414;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .reset-link {
            margin-left: 10px;
            color: #6e1414;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Container and layout styling */
        .container-flex {
            display: flex;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container-flex">
        <?php include 'sidebar.php'; ?>
        <?php include __DIR__ . '/../koneksi.php'; ?>

        <main class="content-wrapper">
            <table class="table-custom">
                <caption>
                    <h1>Data Surat Masuk</h1>
                    <div class="search-form">
                        <form action="" method="get">
                            <input type="text" name="search" placeholder="Cari No Surat, Pengirim, atau Perihal..."
                                value="<?php echo htmlspecialchars($keyword); ?>" />
                            <button type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <?php if ($keyword != ''): ?>
                                <a href="?" class="reset-link">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Surat</th>
                        <th>Tanggal</th>
                        <th>Pengirim</th>
                        <th>Perihal</th>
                        <th>File</th>
                        <th>Tujuan Disposisi</th>
                        <th>Status Baca</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = $tampilsurat->fetch_array()) {
                        echo "<tr>  
                            <td>{$no}</td>  
                            <td>{$row['no_surat']}</td>  
                            <td>{$row['tanggal']}</td>  
                            <td>{$row['pengirim']}</td>  
                            <td>{$row['perihal']}</td>  
                            <td><a href='../file/{$row['file_upload']}' download='{$row['file_upload']}'>Download</a></td>
                            <td>" . htmlspecialchars($row['nama_tujuan']) . "</td>
                            <td>" . htmlspecialchars($row['status_baca'] ?? '-') . "</td>  
                            <td>  
                                <a href='edit_surat_masuk.php?id={$row['id']}' class='btn-ubah' title='Edit Data'><i class='fas fa-edit'></i></a>  
                                <a href='hapus_surat_masuk.php?id={$row['id']}' onclick='return confirm(\"Yakin ingin menghapus data ini?\")' class='btn-hapus' title='Hapus Data'><i class='fas fa-trash'></i></a>  
                                <a href='lihat_surat_masuk.php?id={$row['id']}' class='btn-view' title='Lihat Detail'><i class='fas fa-eye'></i></a>  
                                <a href='disposisi_surat.php?id={$row['id']}' class='btn-tambah' title='Disposisi Surat'><i class='fas fa-share-square'></i></a>
                            </td>  
                        </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>

            <div class="total">Total Surat Masuk: <?php echo $jumlah; ?></div>
            <footer
                style="background-color: beige; color: maroon; text-align: left; padding: 15px 0; font-weight: bold;">
                &copy; <?php echo date('Y'); ?>  2025 By Apradita. All rights reserved.
            </footer>
        </main>
    </div>
</body>

</html>