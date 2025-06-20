<?php
session_start();
include '../koneksi.php';

// Set timezone ke Asia/Jakarta di PHP
date_default_timezone_set('Asia/Jakarta');

// Ambil email user dari session
$emailUser = $_SESSION['email'] ?? null;

// Jika emailUser kosong, hentikan eksekusi dengan pesan
if (!$emailUser) {
    die("Error: User belum login, email tidak ditemukan.");
}

// Update kolom last_login untuk user yang sedang login menggunakan prepared statement
$waktuLogin = date('Y-m-d H:i:s');
$stmt_update = $conn->prepare("UPDATE tb_login SET last_login = ? WHERE email = ?");
if (!$stmt_update) {
    die("Prepare failed: " . $conn->error);
}
$stmt_update->bind_param("ss", $waktuLogin, $emailUser);
if (!$stmt_update->execute()) {
    die("Update last_login gagal: " . $stmt_update->error);
}
$stmt_update->close();

// Ambil data last_login milik user yang sedang login dengan prepared statement
$stmt_user = $conn->prepare("SELECT nama, last_login FROM tb_login WHERE email = ?");
if (!$stmt_user) {
    die("Prepare failed: " . $conn->error);
}
$stmt_user->bind_param("s", $emailUser);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user && $result_user->num_rows > 0) {
    $userInfo = $result_user->fetch_assoc();
} else {
    $userInfo = null;
}
$stmt_user->close();

// Query untuk menampilkan data login hari ini (tidak perlu parameter jadi bisa pakai query langsung)
$sql_select = "
    SELECT nama, email, last_login
    FROM tb_login
    WHERE DATE(last_login) = CURDATE()
    ORDER BY last_login DESC
";

$result = $conn->query($sql_select);
if (!$result) {
    die("Query gagal: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Login Hari Ini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            color: #333;
        }

        h2 {
            margin-top: 50px;
            color: #6e1414;
            text-align: center;
            font-size: 28px;
        }

        .table-custom {
            width: 95%;
            margin: 30px auto;
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

        .btn-kembali-container {
            margin: 20px;
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

        .user-last-login {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
            color: #fff;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
        }
    </style>
</head>

<body>

    <div class="btn-kembali-container">
        <a href="/uas_web_kelas11_apradita/admin/admin_page.php" class="btn-kembali" title="Kembali ke Dashboard">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2>Daftar Akun yang Login Hari Ini</h2>

    <table class="table-custom">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Waktu Login</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . date("d-m-Y H:i:s", strtotime($row['last_login'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Belum ada akun yang login hari ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>

</html>