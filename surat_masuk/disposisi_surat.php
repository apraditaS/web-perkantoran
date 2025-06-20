<?php
include '../koneksi.php';

// Cek apakah id surat dikirim via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID surat tidak ditemukan.";
    exit;
}

$id_surat = intval($_GET['id']);

// Ambil data surat masuk sesuai id
$sqlSurat = $conn->prepare("SELECT * FROM tb_suratmasuk WHERE id = ?");
$sqlSurat->bind_param("i", $id_surat);
$sqlSurat->execute();
$resultSurat = $sqlSurat->get_result();

if ($resultSurat->num_rows == 0) {
    echo "Surat tidak ditemukan.";
    exit;
}

$surat = $resultSurat->fetch_assoc();

// Ambil data tujuan disposisi dari tb_login
$sqlTujuan = $conn->query("SELECT id, nama FROM tb_login ORDER BY nama ASC");

// Proses simpan disposisi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penerima = intval($_POST['id_tujuan']);
    $catatan = trim($_POST['catatan']);
    $status_baca = 'Belum Dibaca'; // default status
    $tanggal_batas = date('Y-m-d'); // tanggal batas disposisi hari ini

    $stmtInsert = $conn->prepare("INSERT INTO tb_disposisi (id_surat, id_penerima, catatan, status_baca, tanggal_batas) VALUES (?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("issss", $id_surat, $id_penerima, $catatan, $status_baca, $tanggal_batas);

    if ($stmtInsert->execute()) {
        header("Location: data_surat_masuk.php?msg=Disposisi berhasil ditambahkan");
        exit;
    } else {
        $error = "Gagal menyimpan disposisi: " . $stmtInsert->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Disposisi Surat Masuk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* style tetap sama seperti sebelumnya */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #bfa181, #6e1414);
            color: #333;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #6e1414;
            text-align: center;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
        }

        select,
        textarea,
        input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        button {
            margin-top: 16px;
            background-color: #6e1414;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #541010;
        }

        .error {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6e1414;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .info-surat {
            background-color: #f6eee3;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #bfa181;
        }

        .info-surat strong {
            color: #6e1414;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Disposisi Surat Masuk</h1>

        <div class="info-surat">
            <p><strong>No Surat:</strong> <?php echo htmlspecialchars($surat['no_surat']); ?></p>
            <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($surat['tanggal']); ?></p>
            <p><strong>Pengirim:</strong> <?php echo htmlspecialchars($surat['pengirim']); ?></p>
            <p><strong>Perihal:</strong> <?php echo htmlspecialchars($surat['perihal']); ?></p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="id_tujuan">Tujuan Disposisi</label>
            <select name="id_tujuan" id="id_tujuan" required>
                <option value="">-- Pilih Tujuan --</option>
                <?php
                while ($rowTujuan = $sqlTujuan->fetch_assoc()) {
                    echo '<option value="' . $rowTujuan['id'] . '">' . htmlspecialchars($rowTujuan['nama']) . '</option>';
                }
                ?>
            </select>

            <label for="catatan">Catatan Disposisi</label>
            <textarea name="catatan" id="catatan" rows="4" placeholder="Catatan untuk disposisi..." required></textarea>

            <button type="submit"><i class="fas fa-share-square"></i> Kirim Disposisi</button>
        </form>

        <a href="data_surat_masuk.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Data Surat
            Masuk</a>
    </div>
</body>

</html>