<?php
header('Content-Type: application/json');

// Konfigurasi database
$host = 'localhost';
$db = 'db_uas_apradita';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';

// Ambil data berdasarkan tahun dan bulan
if ($action === 'get') {
    $year = $_GET['year'] ?? '';
    $month = $_GET['month'] ?? '';

    if (!$year || !$month) {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM tb_kalender_acara WHERE YEAR(tanggal_acara) = ? AND MONTH(tanggal_acara) = ?");
    $stmt->execute([$year, $month]);
    $events = $stmt->fetchAll();

    $filtered = [];
    foreach ($events as $ev) {
        $date = $ev['tanggal_acara'];
        if (!isset($filtered[$date])) {
            $filtered[$date] = [];
        }
        $filtered[$date][] = $ev;
    }

    echo json_encode($filtered);
    exit;
}

// Ambil data berdasarkan tanggal
if ($action === 'getByDate') {
    $date = $_GET['date'] ?? '';
    if (!$date) {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM tb_kalender_acara WHERE tanggal_acara = ?");
    $stmt->execute([$date]);
    $events = $stmt->fetchAll();

    echo json_encode($events);
    exit;
}

// Tambah data acara
if ($action === 'add') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Input tidak ditemukan atau bukan JSON']);
        exit;
    }

    $tanggal = $input['tanggal_acara'] ?? '';
    $waktu = $input['waktu'] ?? '';
    $nama = $input['nama_acara'] ?? '';
    $jenis = $input['jenis_acara'] ?? '';
    $desc = $input['deskripsi'] ?? '';

    if (!$tanggal || !$waktu || !$nama || !$jenis) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap. Pastikan semua field diisi.']);
        exit;
    }

    $sql = "INSERT INTO tb_kalender_acara (tanggal_acara, waktu, nama_acara, jenis_acara, deskripsi) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([$tanggal, $waktu, $nama, $jenis, $desc]);
        echo json_encode(['success' => true, 'message' => 'Acara berhasil ditambahkan']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Hapus acara
if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM tb_kalender_acara WHERE id = ?");
    try {
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Acara berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Acara tidak ditemukan']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
    }
    exit;
}

// Update acara
if ($action === 'update') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Debug logging (opsional, untuk mengecek input yang dikirim)
    file_put_contents('log_update.txt', print_r($input, true));

    $id = intval($input['id'] ?? 0);
    $tanggal = $input['tanggal_acara'] ?? '';
    $waktu = $input['waktu'] ?? '';
    $nama = $input['nama_acara'] ?? '';
    $jenis = $input['jenis_acara'] ?? '';
    $desc = $input['deskripsi'] ?? '';

    if ($id <= 0 || !$tanggal || !$waktu || !$nama || !$jenis) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap atau ID tidak valid']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE tb_kalender_acara SET tanggal_acara = ?, waktu = ?, nama_acara = ?, jenis_acara = ?, deskripsi = ? WHERE id = ?");
    try {
        $stmt->execute([$tanggal, $waktu, $nama, $jenis, $desc, $id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Acara berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada perubahan data atau ID tidak ditemukan']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data: ' . $e->getMessage()]);
    }

    exit;
}

// Jika action tidak dikenali
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
