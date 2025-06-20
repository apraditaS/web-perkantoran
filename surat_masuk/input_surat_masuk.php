<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Input Surat Masuk</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container-flex {
            display: flex;
            flex: 1;
            position: relative;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            box-sizing: border-box;
            margin-left: 400px;
        }


        .form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-left: 6px solid #6e1414;
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
            margin: 20px 0;
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
        input[type="file"],
        select {
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

        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar-collapsed .content-wrapper {
                margin-left: 0;
            }

            .form {
                padding: 20px;
            }

            td {
                display: block;
                width: 100%;
                padding: 8px 0;
            }
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
    <div class="container-flex">
        <?php include 'sidebar.php'; ?>
        <?php include __DIR__ . '/../koneksi.php'; ?>

        <main class="content-wrapper">
            <form method="post" enctype="multipart/form-data" class="form" autocomplete="off">
                <p class="title">Halaman Form Surat Masuk</p>
                <div class="form-group">
                    <table>
                        <tr>
                            <td>No Surat</td>
                            <td><input type="text" name="no_surat" required /></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td><input type="date" name="tanggal" required /></td>
                        </tr>
                        <tr>
                            <td>Pengirim</td>
                            <td>
                                <select name="id_bukutamu" required>
                                    <option value="">-- NamaTamu --</option>
                                    <?php
                                    $bukutamu = $conn->query("SELECT id, nama, instansi FROM tb_bukutamu ORDER BY nama");
                                    if (!$bukutamu) {
                                        echo '<option disabled>Gagal mengambil data</option>';
                                    } else {
                                        while ($bt = $bukutamu->fetch_assoc()) {
                                            echo '<option value="' . $bt['id'] . '">' . htmlspecialchars($bt['nama']) . ' - ' . htmlspecialchars($bt['instansi']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Perihal</td>
                            <td><input type="text" name="perihal" required /></td>
                        </tr>
                        <tr>
                            <td>File</td>
                            <td><input type="file" name="file_upload" required /></td>
                        </tr>
                        <tr>
                            <td>Tujuan Disposisi</td>
                            <td>
                                <select name="id_tujuan_disposisi" required>
                                    <option value="">-- Pilih Tujuan Disposisi --</option>
                                    <?php
                                    $query_tujuan = $conn->query("SELECT id, nama, role FROM tb_login WHERE role IN ('atasan', 'divisi') ORDER BY role, nama");
                                    if ($query_tujuan && $query_tujuan->num_rows > 0) {
                                        while ($row = $query_tujuan->fetch_assoc()) {
                                            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama']) . ' (' . $row['role'] . ')</option>';
                                        }
                                    } else {
                                        echo '<option value="">Tidak ada tujuan disposisi</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="tbsimpan" value="Simpan" /></td>
                        </tr>
                    </table>
                </div>
            </form>
        </main>
    </div>

    <footer>
        <p style="margin: 0;">&copy; 2025 By Apradita. All rights reserved.</p>
    </footer>
</body>

</html>

<?php
if (isset($_POST['tbsimpan'])) {
    $no_surat = $conn->real_escape_string($_POST['no_surat']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $id_bukutamu = (int) $_POST['id_bukutamu'];
    $perihal = $conn->real_escape_string($_POST['perihal']);
    $id_tujuan_disposisi = (int) $_POST['id_tujuan_disposisi'];

    $file_name = $_FILES['file_upload']['name'];
    $file_tmp = $_FILES['file_upload']['tmp_name'];
    $folder = "../file/";
    $file_path = $folder . basename($file_name);

    if (!empty($file_name)) {
        if (move_uploaded_file($file_tmp, $file_path)) {

            // Ambil nama pengirim dari tb_bukutamu
            $pengirim = '';
            $result_pengirim = $conn->query("SELECT nama FROM tb_bukutamu WHERE id = $id_bukutamu");
            if ($result_pengirim && $result_pengirim->num_rows > 0) {
                $pengirim = $conn->real_escape_string($result_pengirim->fetch_assoc()['nama']);
            }

            $querysimpan = "INSERT INTO tb_suratmasuk 
                (no_surat, tanggal, pengirim, perihal, file_upload, id_tujuan_disposisi, id_bukutamu)
                VALUES 
                ('$no_surat', '$tanggal', '$pengirim', '$perihal', '$file_name', '$id_tujuan_disposisi', '$id_bukutamu')";
            $simpandata = $conn->query($querysimpan);

            if ($simpandata) {
                $id_surat = $conn->insert_id;

                $isi_disposisi = "Mohon tindak lanjut surat ini.";
                $batas_waktu = date('Y-m-d', strtotime('+3 days'));
                $sifat = "Penting";
                $catatan = "Segera ditindaklanjuti";

                $query_disposisi = "INSERT INTO tb_disposisi 
                    (id_surat, id_penerima, isi_disposisi, status, tanggal_batas, catatan)
                    VALUES
                    ('$id_surat', '$id_tujuan_disposisi', '" . $conn->real_escape_string($isi_disposisi) . "', 'baru', '$batas_waktu', '" . $conn->real_escape_string($catatan) . "')";

                $insert_disp = $conn->query($query_disposisi);

                if ($insert_disp) {
                    echo "<script>alert('Data Surat dan Disposisi berhasil disimpan'); window.location='../admin/admin_page.php';</script>";
                } else {
                    echo "❌ Gagal simpan disposisi: " . $conn->error;
                }

            } else {
                echo "❌ Gagal simpan surat masuk: " . $conn->error;
            }

        } else {
            echo "❌ Gagal upload file. Pastikan folder '../file/' bisa ditulis.";
        }

    } else {
        echo "❌ File belum dipilih.";
    }
}
?>