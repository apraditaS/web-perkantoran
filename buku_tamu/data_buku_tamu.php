<?php
include '../koneksi.php';

$keyword = '';
if (isset($_GET['search'])) {
    $keyword = trim($_GET['search']);
}

if ($keyword != '') {
    $stmt = $conn->prepare("SELECT * FROM tb_bukutamu WHERE nama LIKE ? OR instansi LIKE ? OR tujuan LIKE ? ORDER BY waktu_datang DESC");
    $like_keyword = "%$keyword%";
    $stmt->bind_param("sss", $like_keyword, $like_keyword, $like_keyword);
    $stmt->execute();
    $tampilbuku = $stmt->get_result();

    $stmt2 = $conn->prepare("SELECT COUNT(*) as jum FROM tb_bukutamu WHERE nama LIKE ? OR instansi LIKE ? OR tujuan LIKE ?");
    $stmt2->bind_param("sss", $like_keyword, $like_keyword, $like_keyword);
    $stmt2->execute();
    $jumlah = $stmt2->get_result()->fetch_assoc()['jum'];
} else {
    $tampilbuku = $conn->query("SELECT * FROM tb_bukutamu ORDER BY waktu_datang DESC");
    $jumlahdata = $conn->query("SELECT COUNT(*) as jum FROM tb_bukutamu");
    $jumlah = $jumlahdata->fetch_assoc()['jum'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Buku Tamu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Base Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            color: #333;
            min-height: 100vh;
        }

        /* Layout */
        .container-flex {
            display: flex;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            padding: 15px;
            margin-left: 45px;
            /* Sidebar width */
            transition: margin-left 0.3s;
        }

        /* Table Container */
        .table-container {
            background: transparent;
            border-radius: 8px;
            box-shadow: none;
            padding: 15px;
            margin: 15px auto;
            margin-left: 100px;
            margin-right: 100px;
        }

        /* Table Styles */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table-custom th,
        .table-custom td {
            padding: 10px 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .table-custom thead {
            background-color: #6e1414;
            color: white;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: #f8f5f0;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            color: #6e1414;
            font-size: 24px;
            margin-bottom: 15px;
        }

        /* Search Form */
        .search-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 300px;
            max-width: 100%;
        }

        .search-button {
            padding: 8px 15px;
            background-color: #6e1414;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-reset {
            color: #6e1414;
            font-weight: bold;
            align-self: center;
        }

        /* Action Buttons */
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .btn {
            padding: 6px 10px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-ubah {
            background-color: #bfa181;
        }

        .btn-hapus {
            background-color: #a83232;
        }

        .btn-view {
            background-color: #bfa181;
        }

        /* Total Counter */
        .total {
            text-align: center;
            color: #6e1414;
            font-weight: bold;
            margin: 15px 0;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
                padding: 10px;
            }

            .table-custom th,
            .table-custom td {
                padding: 8px 10px;
                font-size: 14px;
            }

            .search-form {
                flex-direction: column;
                align-items: center;
            }

            .search-input {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .table-custom thead {
                display: none;
            }

            .table-custom tr {
                display: block;
                margin-bottom: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .table-custom td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 8px;
                border-bottom: 1px solid #eee;
            }

            .table-custom td::before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 15px;
                color: #6e1414;
            }

            .btn-group {
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="container-flex">
        <?php include 'sidebar.php'; ?>

        <main class="content-wrapper">
            <div class="table-container">
                <div class="page-header">
                    <h1>Data Tamu</h1>

                    <form class="search-form" action="" method="get">
                        <input type="text" name="search" class="search-input"
                            placeholder="Cari Nama, Instansi, atau Tujuan..."
                            value="<?php echo htmlspecialchars($keyword); ?>" />
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <?php if ($keyword != ''): ?>
                            <a href="?" class="search-reset">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Tujuan</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $tampilbuku->fetch_assoc()) {
                            echo "<tr>  
                                <td data-label='No'>$no</td>  
                                <td data-label='Nama'>" . htmlspecialchars($row['nama']) . "</td>  
                                <td data-label='Instansi'>" . htmlspecialchars($row['instansi']) . "</td>  
                                <td data-label='Tujuan'>" . htmlspecialchars($row['tujuan']) . "</td>  
                                <td data-label='Waktu'>" . htmlspecialchars($row['waktu_datang']) . "</td>  
                                <td data-label='Aksi'>
                                    <div class='btn-group'>
                                        <a href='edit_buku_tamu.php?id=" . $row['id'] . "' class='btn btn-ubah' title='Edit'><i class='fas fa-edit'></i></a>  
                                        <a href='hapus_buku_tamu.php?id=" . $row['id'] . "' class='btn btn-hapus' title='Hapus'><i class='fas fa-trash-alt'></i></a>  
                                        <a href='lihat_buku_tamu.php?id=" . $row['id'] . "' class='btn btn-view' title='Lihat'><i class='fas fa-eye'></i></a>
                                    </div>
                                </td>  
                            </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>

                <div class="total">
                    Total Data: <?php echo $jumlah; ?>
                </div>
            </div>
            <footer
                style="background-color: beige; color: maroon; text-align: left; padding: 15px 0; font-weight: bold;">
                &copy; <?php echo date('Y'); ?> 2025 By Apradita. All rights reserved.
            </footer>
        </main>
    </div>

    <script>
        // Add data-label attributes for responsive table
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth <= 576) {
                const headers = document.querySelectorAll('.table-custom th');
                const rows = document.querySelectorAll('.table-custom tbody tr');

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    cells.forEach((cell, index) => {
                        if (headers[index]) {
                            cell.setAttribute('data-label', headers[index].textContent);
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>