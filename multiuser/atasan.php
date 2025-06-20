<?php
include '../koneksi.php';
session_start();

$_SESSION['login_success'] = true;

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login_out/login.php");
  exit;
}


$id_atasan = $_SESSION['user_id'];

// Prepared statement untuk query disposisi
$query_disposisi = "
   SELECT 
    d.id_disposisi,
    d.status,
    d.status_baca,
    s.no_surat,
    s.tanggal,
    s.pengirim,
    s.perihal,
    s.file_upload,
    s.id AS id_surat
    FROM 
        tb_disposisi d
    INNER JOIN 
        tb_suratmasuk s ON d.id_surat = s.id
    WHERE 
        d.id_penerima = ?
    ORDER BY s.tanggal DESC
";

$stmtDisposisi = $conn->prepare($query_disposisi);
$stmtDisposisi->bind_param("i", $id_atasan);
$stmtDisposisi->execute();
$data_disposisi = $stmtDisposisi->get_result();

// Query jumlah disposisi per bulan (prepared)
$queryPerBulan = "
  SELECT MONTH(s.tanggal) as bulan, COUNT(*) as jumlah
  FROM tb_disposisi d
  JOIN tb_suratmasuk s ON d.id_surat = s.id
  WHERE d.id_penerima = ? AND YEAR(s.tanggal) = YEAR(CURDATE())
  GROUP BY MONTH(s.tanggal)
";
$stmtPerBulan = $conn->prepare($queryPerBulan);
$stmtPerBulan->bind_param("i", $id_atasan);
$stmtPerBulan->execute();
$resultPerBulan = $stmtPerBulan->get_result();

$jumlahPerBulan = array_fill(1, 12, 0);
while ($row = $resultPerBulan->fetch_assoc()) {
  $bulan = (int) $row['bulan'];
  $jumlah = (int) $row['jumlah'];
  $jumlahPerBulan[$bulan] = $jumlah;
}

// Query status baca (prepared)
$queryStatusBaca = "
  SELECT 
    SUM(CASE WHEN status_baca = 'Sudah Dibaca' THEN 1 ELSE 0 END) as sudah,
    SUM(CASE WHEN status_baca = 'Belum Dibaca' THEN 1 ELSE 0 END) as belum
  FROM tb_disposisi 
  WHERE id_penerima = ?
";
$stmtStatusBaca = $conn->prepare($queryStatusBaca);
$stmtStatusBaca->bind_param("i", $id_atasan);
$stmtStatusBaca->execute();
$resultStatusBaca = $stmtStatusBaca->get_result();
$dataStatusBaca = $resultStatusBaca->fetch_assoc();

$sudahDibaca = $dataStatusBaca['sudah'] ?? 0;
$belumDibaca = $dataStatusBaca['belum'] ?? 0;


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Halaman Atasan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #3b0a0a;
      color: #f3e6e6;
      margin: 0;
      padding: 20px;
    }

    .notification {
      position: fixed;
      top: 20px;
      /* Geser sedikit dari atas */
      left: 57%;
      transform: translateX(-50%);
      /* Tengah horizontal saja */
      background-color: #6e1414;
      /* Maroon */
      color: #fdf7e4;
      /* Beige terang */
      border: 2px solid #bfa181;
      /* Gold/Beige */
      padding: 16px 24px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      font-size: 16px;
      font-weight: bold;
      z-index: 9999;
      opacity: 1;
      transition: opacity 0.5s ease;
      display: none;
      cursor: pointer;
      text-align: center;
    }

    .notification:hover {
      transform: translateX(-50%) scale(1.03);
    }

    .navbar {
      background-color: #D5B893;
      padding: 12px 24px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.7);
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #f9f5f5;
      margin-bottom: 20px;
    }

    .navbar h1 {
      color: #6F4D38;
    }

    .logo-img {
      height: 40px;
      margin-right: 25px;
    }

    .welcome-text {
      font-size: 22px;
      font-weight: 600;
    }

    .main-layout {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 24px;
    }

    .left-column {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .card {
      background-color: #7F1F0E;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(101, 0, 0, 0.6);
      color: #D5B893;
    }

    .center-box {
      height: 200px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .content-center h3 {
      margin: 0;
      font-weight: 600;
      font-size: 1.3rem;
    }

    .content-center h1 {
      margin: 8px 0 0 0;
      font-size: 4rem;
      font-weight: 700;
      color: #6F4D38;
    }

    .file-link {
      color: #6F4D38;
      text-decoration: underline;
      cursor: pointer;
    }

    .file-link:hover {
      color: white;
    }

    .status-badge {
      padding: 6px 14px;
      border-radius: 9999px;
      font-size: 13px;
      font-weight: 600;
      display: inline-block;
      color: #641818;
      background-color: #6F4D38;
      user-select: none;
    }

    .status-proses {
      background-color: #6F4D38;
      color: #D5B893;
    }

    .status-selesai {
      background-color: #6F4D38;
      color: #D5B893;
    }

    .search-box {
      position: relative;
      width: 280px;
      margin-bottom: 15px;
    }

    .search-box input {
      width: 100%;
      margin-left: 300px;
      padding: 10px 12px 10px 40px;
      border-radius: 12px;
      border: none;
      background-color: #7a2a2a;
      color: #f3e6e6;
      font-size: 14px;
    }

    .search-box input::placeholder {
      color: white;
    }

    .search-box svg {
      position: absolute;
      margin-left: 300px;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      width: 20px;
      height: 20px;
      fill: #6F4D38;
    }

    canvas {
      width: 100% !important;
      height: 220px !important;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 8px;
      font-size: 14px;
      color: #f3e6e6;
      background-color: transparent;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(101, 0, 0, 0.6);
    }

    .table-header h2 {
      margin: 0 0 15px 0;
      font-weight: 700;
      color: #D5B893;
      text-align: center;
    }

    th {
      background-color: #c29666;
      font-weight: 700;
      padding: 14px 20px;
      text-align: left;
      color: #f3e6e6;
      user-select: none;
      border-bottom: 2px solid #500000;
    }

    td {
      background-color: #D5B893;
      color: #6F4D38;
      padding: 14px 20px;
      vertical-align: middle;
      transition: background-color 0.3s ease;
      border-left: 1px solid #500000;
      border-right: 1px solid #500000;
    }

    tr {
      border-radius: 12px;
    }

    tr:hover td {
      background-color: #8b2c2c;
      color: white;
      cursor: pointer;
    }

    tr:last-child td {
      border-bottom-left-radius: 12px;
      border-bottom-right-radius: 12px;
    }

    tr:first-child td {
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    table::-webkit-scrollbar {
      height: 8px;
    }

    table::-webkit-scrollbar-thumb {
      background-color: #7a2a2a;
      border-radius: 12px;
    }

    table::-webkit-scrollbar-track {
      background-color: #3b0a0a;
    }

    .btn-logout {
      background-color: #7a2a2a;
      color: #f3e6e6;
      border: none;
      padding: 8px 16px;
      font-weight: 600;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      box-shadow: 0 3px 8px rgba(122, 42, 42, 0.7);
    }

    .btn-logout:hover {
      background-color: #a23939;
      box-shadow: 0 4px 12px rgba(162, 57, 57, 0.9);
    }

    footer {
      background-color: #6F4D38;
      color: #D5B893;
      padding: 14px 20px;
      text-align: center;
      font-size: 13px;
      font-weight: 500;
      border-top: 1px solid #c29666;
      margin-top: 40px;
      border-radius: 0 0 8px 8px;
      box-shadow: inset 0 1px 0 #a77f5b;
    }

    footer p {
      margin: 0;
      font-style: italic;
      letter-spacing: 0.3px;
    }
  </style>
</head>

<body>
  <?php if (isset($_SESSION['login_success']) && $_SESSION['login_success']): ?>
    <div class="notification" id="loginNotification">
      Login berhasil!
    </div>
    <?php unset($_SESSION['login_success']); // Hapus session setelah ditampilkan ?>
  <?php endif; ?>
  <script>
    // Menampilkan notifikasi
    const notification = document.getElementById('loginNotification');
    if (notification) {
      notification.style.display = 'block';
      setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
          notification.style.display = 'none';
        }, 500); // Waktu untuk menghilangkan notifikasi
      }, 5000); // Tampilkan selama 5 detik
      // Menghilangkan notifikasi saat diklik
      notification.addEventListener('click', () => {
        notification.style.opacity = '0';
        setTimeout(() => {
          notification.style.display = 'none';
        }, 500);
      });
    }
  </script>
  <header class="navbar">
    <img src="../images/logo-smk.png" alt="Logo" class="logo-img" />
    <div class="welcome-text">Selamat Datang Bapak Devi</div>
    <form action="../login_out/logout.php" method="post" style="margin-left:auto;">
      <button type="submit" class="btn-logout">Logout</button>
    </form>
  </header>

  <!-- Notif -->
  <div id="notif" style="
  display:none;
  position: fixed;
  top: 20px;
  right: 20px;
  background-color: #ef4444;
  color: white;
  padding: 15px 25px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.7);
  font-weight: 600;
  z-index: 1000;
  cursor: pointer;
">
    📩 Ada surat disposisi baru yang belum dibaca!
  </div>

  <!-- Carrd 1 -->
  <div class="main-layout">
    <div class="left-column">
      <div class="card"><canvas id="donutChart"></canvas></div>
      <div class="card"><canvas id="barChart"></canvas></div>
    </div>

    <!-- Card 2 Table -->
    <div class="card">
      <div class="table-header">
        <h2>Data Disposisi</h2>
        <div class="search-box">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M15.5 14h-.79l-.28-.27a6.471 6.471 0 001.48-5.34C15.09 5.59 12.5 3 9.25 3S3.41 5.59 3.41 8.89s2.59 5.89 5.84 5.89a6.471 6.471 0 005.34-1.48l.27.28v.79l5 5L20.49 19l-5-5zm-6.25 0C7.01 14 5 11.99 5 9.5S7.01 5 9.25 5 13.5 7.01 13.5 9.5 11.49 14 9.25 14z" />
          </svg>
          <input type="text" id="searchInput" placeholder="Cari disposisi..." />
        </div>
      </div>
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>No Surat</th>
            <th>Tanggal</th>
            <th>Pengirim</th>
            <th>Perihal</th>
            <th>Status</th>
            <th>Status Baca</th>
            <th>File</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data_disposisi->num_rows > 0): ?>
            <?php $no = 1;
            while ($row = $data_disposisi->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['no_surat']) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['pengirim']) ?></td>
                <td><?= htmlspecialchars($row['perihal']) ?></td>
                <td>
                  <span
                    class="status-badge <?= strtolower($row['status']) === 'selesai' ? 'status-selesai' : 'status-proses' ?>">
                    <?= htmlspecialchars($row['status']) ?>
                  </span>
                </td>
                <td>
                  <?php
                  $statusClass = ($row['status_baca'] === 'Sudah Dibaca') ? 'sudah' : 'belum';
                  ?>
                  <span class="status-baca <?= $statusClass ?>">
                    <?= htmlspecialchars($row['status_baca']) ?>
                  </span>
                </td>
                <td>
                  <a href="lihat_file.php?file=<?= htmlspecialchars($row['file_upload']) ?>&id_surat=<?= isset($row['id_surat']) ? $row['id_surat'] : 'NOTFOUND' ?>"
                    target="_blank" class="file-link">Lihat File</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">Belum ada data disposisi.</td>
            </tr>
          <?php endif; ?>
        </tbody>

      </table>
    </div>
  </div>

  <script>
    const donutCtx = document.getElementById("donutChart").getContext("2d");
    new Chart(donutCtx, {
      type: "doughnut",
      data: {
        labels: ["Sudah Dibaca", "Belum Dibaca"],
        datasets: [{
          label: "Status Baca",
          data: [<?= $sudahDibaca ?>, <?= $belumDibaca ?>],
          backgroundColor: ["#d1c791", "#7f8330"],
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: {
              color: "#D5B893"
            }
          }
        }
      },
    });


    const barCtx = document.getElementById("barChart").getContext("2d");
    new Chart(barCtx, {
      type: "bar",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Juli", "Agustus", "Sep", "Okt", "Nov", "Des"],
        datasets: [{
          label: "Jumlah Disposisi per Bulan",
          data: [<?= implode(",", $jumlahPerBulan) ?>],
          backgroundColor: "#BFB692",
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: {
              color: "#D5B893"
            },
            grid: {
              color: "#D5B893"
            }
          },
          y: {
            ticks: {
              color: "#D5B893"
            },
            grid: {
              color: "#D5B893"
            }
          }
        },
        plugins: {
          legend: {
            labels: {
              color: "#D5B893"
            }
          }
        }
      },
    });


    // Search
    document.getElementById("searchInput").addEventListener("keyup", function () {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll("table tbody tr");

      rows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchValue) ? "" : "none";
      });
    });

    window.onload = function () {
      const belumDibaca = <?= (int) $belumDibaca ?>;
      const notif = document.getElementById('notif');

      if (belumDibaca > 0) {
        notif.style.display = 'block';

        // Klik notifikasi akan sembunyikan notif dan bisa redirect ke halaman disposisi misalnya
        notif.addEventListener('click', () => {
          notif.style.display = 'none';
          window.location.href = ""; // isi dengan link ke halaman disposisi atau detail
        });

        // sembunyikan otomatis setelah 5 detik
        setTimeout(() => {
          notif.style.display = 'none';
        }, 5000);
      }
    };

  </script>
  <footer>
    <p style="margin: 0;">&copy; 2025 By Apradita. All rights reserved.</p>
  </footer>
</body>
</html>