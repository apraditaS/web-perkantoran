<?php
include '../koneksi.php';

$keyword = '';
$query = '';
$stmt = null;
$tampilsurat = null;

// Cek apakah user melakukan pencarian
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $keyword = trim($_GET['search']);
    $like_keyword = "%$keyword%";
    $query = "SELECT * FROM tb_suratkeluar WHERE no_surat LIKE ? OR tujuan LIKE ? OR perihal LIKE ? ORDER BY tanggal DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $like_keyword, $like_keyword, $like_keyword);
    $stmt->execute();
    $tampilsurat = $stmt->get_result();

    // Hitung jumlah hasil pencarian
    $count_stmt = $conn->prepare("SELECT COUNT(*) as jum FROM tb_suratkeluar WHERE no_surat LIKE ? OR tujuan LIKE ? OR perihal LIKE ?");
    $count_stmt->bind_param("sss", $like_keyword, $like_keyword, $like_keyword);
    $count_stmt->execute();
    $jumlah = $count_stmt->get_result()->fetch_assoc()['jum'];
} else {
    // Jika tidak ada pencarian, tampilkan semua data diurutkan berdasarkan tanggal terbaru
    $query = "SELECT * FROM tb_suratkeluar ORDER BY tanggal DESC";
    $tampilsurat = $conn->query($query);
    $jumlah = $conn->query("SELECT COUNT(*) as jum FROM tb_suratkeluar")->fetch_assoc()['jum'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Surat Keluar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* [Gaya CSS tetap seperti sebelumnya] */
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
            color: #6e1414;
            margin-bottom: 50px;
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
                    <h1>Data Surat Keluar</h1>
                    <div style="width: 95%; margin: 20px auto; text-align: center;">
                        <form action="" method="get">
                            <input type="text" name="search" placeholder="Cari No Surat, Tujuan, atau Perihal..."
                                value="<?php echo htmlspecialchars($keyword); ?>"
                                style="padding: 8px 12px; width: 300px; border-radius: 6px; border: 1px solid #ccc;">
                            <button type="submit"
                                style="padding: 8px 16px; background-color: #6e1414; color: white; border: none; border-radius: 6px; cursor: pointer;"><i
                                    class="fas fa-search"></i> Cari</button>
                            <?php if ($keyword != ''): ?>
                                <a href="?"
                                    style="margin-left: 10px; color: #6e1414; font-weight: bold; text-decoration: underline;">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Surat</th>
                        <th>Tanggal</th>
                        <th>Tujuan</th>
                        <th>Perihal</th>
                        <th>File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = $tampilsurat->fetch_assoc()) {
                        echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['no_surat']}</td>
                    <td>{$row['tanggal']}</td>
                    <td>{$row['tujuan']}</td>
                    <td>{$row['perihal']}</td>
                    <td><a href='../file/{$row['file_upload']}' download='{$row['file_upload']}'>Download</a></td>
                    <td>
                        <a href='edit_surat_keluar.php?id={$row['id']}' class='btn-ubah' title='Edit Data'><i class='fas fa-edit'></i></a>
                        <a href='hapus_surat_keluar.php?id={$row['id']}' class='btn-hapus' title='Hapus Data'><i class='fas fa-trash-alt'></i></a>
                        <a href='lihat_surat_keluar.php?id={$row['id']}' class='btn-view' title='Lihat Data'><i class='fas fa-eye'></i></a>
                    </td>
                </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>

            <div class="total">
                Data Surat Keluar (Total: <?php echo $jumlah; ?>)
            </div>
            <footer
                style="background-color: beige; color: maroon; text-align: left; padding: 15px 0; font-weight: bold;">
                &copy; <?php echo date('Y'); ?> 2025 By Apradita. All rights reserved.
            </footer>
        </main>
    </div>
</body>

</html>