<?php
require_once __DIR__ . '/../koneksi.php';
session_start();

$_SESSION['login_success'] = true; // Set session ini setelah login berhasil

// Cegah cache supaya tombol back browser gak bisa reload halaman lama
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  // Kalau belum login atau bukan admin, redirect ke halaman login
  header("Location: ../login_out/login.php");
  exit;
}

// Cek role admin
if ($_SESSION['user_role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Hitung jumlah user yang pernah login 
$query = "SELECT COUNT(*) AS jumlah_login FROM tb_login WHERE DATE(last_login) = CURDATE()";
$result = $conn->query($query);
$data = $result->fetch_assoc();
$jumlah_login = $data['jumlah_login'];


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="styleadmin.css" />
  <script type="text/javascript" src="app.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout" />
  <style>
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

    .welcome-box {
      background-color: rgba(215, 190, 168, 0.15);
      border-radius: 8px;
      padding: 20px;
      margin: 20px 0;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .welcome-header {
      font-size: 1.5rem;
      color: var(--accent-clr);
      text-align: left;
      margin: 0;
    }

    .container h2 {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 2rem;
      color: var(--accent-clr);
      text-align: center;
      margin-top: 2px;
      margin-bottom: 2rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      border-bottom: 3px solid var(--accent-clr);
      padding-bottom: 0.3rem;
      max-width: 320px;
      margin-left: auto;
      margin-right: auto;
    }

    .chart-container {
      background-color: rgba(215, 190, 168, 0.15);
      border-radius: 12px;
      padding: 20px 15px 25px 15px;
      box-shadow: 0 6px 12px rgba(110, 20, 20, 0.15);
      max-width: 800px;
      margin: 0 auto 2rem auto;
    }

    .chart-container canvas {
      display: block;
      max-width: 100%;
      border-radius: 12px;
    }

    @media (max-width: 500px) {
      .chart-container h2 {
        font-size: 1.5rem;
        max-width: 260px;
      }
    }
  </style>
</head>

<body>
  <nav id="sidebar">
    <ul>

      <li>
        <img src="../images/logo-smk.png" class="logo-img" />

        <button onclick="toggleSidebar()" id="toggle-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
            <path
              d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z" />
          </svg>
        </button>
      </li>

      <li>
        <div class="sidebar-profile">
          <span class="admin-text">admin</span>
          <img src="../images/admin-pict.png" alt="Admin Profile" class="profile-pic" />
        </div>
      </li>

      <li class="active">
        <a href="#dashboard">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
            <path
              d="M520-640v-160q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v160q0 17-11.5 28.5T800-600H560q-17 0-28.5-11.5T520-640ZM120-480v-320q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v320q0 17-11.5 28.5T400-440H160q-17 0-28.5-11.5T120-480Zm400 320v-320q0-17 11.5-28.5T560-520h240q17 0 28.5 11.5T840-480v320q0 17-11.5 28.5T800-120H560q-17 0-28.5-11.5T520-160Zm-400 0v-160q0-17 11.5-28.5T160-360h240q17 0 28.5 11.5T440-320v160q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Zm80-360h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z" />
          </svg>
          <span>Dashboard</span>
        </a>
      </li>

      <li>
        <button onclick="toggleSubMenu(this)" class="dropdown-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
            <path
              d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h207q16 0 30.5 6t25.5 17l57 57h320q33 0 56.5 23.5T880-640v400q0 33-23.5 56.5T800-160H160Zm0-80h640v-400H447l-80-80H160v480Zm0 0v-480 480Zm400-160v40q0 17 11.5 28.5T600-320q17 0 28.5-11.5T640-360v-40h40q17 0 28.5-11.5T720-440q0-17-11.5-28.5T680-480h-40v-40q0-17-11.5-28.5T600-560q-17 0-28.5 11.5T560-520v40h-40q-17 0-28.5 11.5T480-440q0 17 11.5 28.5T520-400h40Z" />
          </svg>
          <span>Input</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
            <path
              d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z" />
          </svg>
        </button>
        <ul class="sub-menu">
          <div>
            <li><a href="../surat_masuk/input_surat_masuk.php">Surat Masuk</a></li>
            <li><a href="../surat_keluar/input_surat_keluar.php">Surat Keluar</a></li>
            <li><a href="../buku_tamu/input_buku_tamu.php">Buku Tamu</a></li>
          </div>
        </ul>
      </li>

      <li>
        <button onclick="toggleSubMenu(this)" class="dropdown-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
            <path
              d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
          </svg>
          <span>Data</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
            <path
              d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z" />
          </svg>
        </button>
        <ul class="sub-menu">
          <div>
            <li><a href="../surat_masuk/data_surat_masuk.php">Data Surat Masuk</a></li>
            <li><a href="../surat_keluar/data_surat_keluar.php">Data Surat Keluar</a></li>
            <li><a href="../buku_tamu/data_buku_tamu.php">Data Tamu</a></li>
          </div>
        </ul>
      </li>

      <li>
        <a href="galeri_surat.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#e8eaed"
            aria-hidden="true">
            <path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
          </svg>
          <span>Galeri File</span>
        </a>
      </li>

      <li>
        <a href="../kalender/kalender.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#e8eaed" viewBox="0 0 24 24">
            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 
        2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 
        2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zm0-13H5V6h14v1z" />
          </svg>
          <span>Kalender</span>
        </a>
      </li>

      <li>
        <a href="#" class="sidebar-link logout-link"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#e8eaed"
            aria-hidden="true">
            <path d="M16 13v-2H7V8l-5 4 5 4v-3h9zm3-10H5c-1.1 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.9 2 2 2h14
               c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
          </svg>
          <span>Logout</span>
        </a>
        <form id="logout-form" action="../login_out/logout.php" method="post" style="display: none;"></form>
      </li>

    </ul>

  </nav>

  <?php
  include '../koneksi.php';

  //data harian
  $tampilsuratmasuk = $conn->query("SELECT * FROM tb_suratmasuk WHERE DATE(tanggal) = CURDATE()");
  $jumlahdata = $conn->query("SELECT COUNT(*) as jum FROM tb_suratmasuk WHERE DATE(tanggal) = CURDATE()");
  $jumlah = $jumlahdata->fetch_assoc()['jum'];

  $tampilsuratkeluar = $conn->query("SELECT * FROM tb_suratkeluar WHERE DATE(tanggal) = CURDATE()");
  $jumlahdata2 = $conn->query("SELECT COUNT(*) as jum FROM tb_suratkeluar WHERE DATE(tanggal) = CURDATE()");
  $jumlah2 = $jumlahdata2->fetch_assoc()['jum'];

  $tampilbuku = $conn->query("SELECT * FROM tb_bukutamu WHERE DATE(waktu_datang) = CURDATE()");
  $jumlahdata3 = $conn->query("SELECT COUNT(*) as jum FROM tb_bukutamu WHERE DATE(waktu_datang) = CURDATE()");
  $jumlah3 = $jumlahdata3->fetch_assoc()['jum'];

  // Hitung jumlah login hari ini menggunakan prepared statement
  $stmt_jumlah = $conn->prepare("SELECT COUNT(*) as jum FROM tb_login WHERE DATE(last_login) = CURDATE()");
  if (!$stmt_jumlah) {
    die("Prepare gagal: " . $conn->error);
  }
  $stmt_jumlah->execute();
  $result_jumlah = $stmt_jumlah->get_result();
  $jumlahLogin = 0;
  if ($result_jumlah && $row = $result_jumlah->fetch_assoc()) {
    $jumlahLogin = $row['jum'];
  }
  $stmt_jumlah->close();

  $no = 1;
  ?>

  <main>
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
    <div class="welcome-box">
      <h1 class="welcome-header">Selamat Datang Admin</h1>
    </div>

    <!-- container 1 -->
    <div class="container" id="dashboard">
      <h2>Data Harian</h2>
      <div class="dashboard">
        <div class="card">
          <h3>Jumlah Tamu Hari Ini</h3>
          <div class="count"><?php echo htmlspecialchars($jumlah3); ?></div>
          <a href="../buku_tamu/bukutamu_harian.php" aria-label="Lihat detail Tamu">
            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
              <circle cx="12" cy="12" r="2.5" />
            </svg>
          </a>
        </div>

        <div class="card">
          <h3>Surat Masuk Hari Ini</h3>
          <div class="count"><?php echo htmlspecialchars($jumlah); ?></div>
          <a href="../surat_masuk/suratmasuk_harian.php" aria-label="Lihat detail Surat Masuk">
            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
              <circle cx="12" cy="12" r="2.5" />
            </svg>
          </a>
        </div>

        <div class="card">
          <h3>Surat Keluar Hari Ini</h3>
          <div class="count"><?php echo htmlspecialchars($jumlah2); ?></div>
          <a href="../surat_keluar/suratkeluar_harian.php" aria-label="Lihat detail Surat Keluar">
            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
              <circle cx="12" cy="12" r="2.5" />
            </svg>
          </a>
        </div>
      </div>
    </div>

    <div class="container" id="dashboard">
      <div class="dashboard">
        <div class="card">
          <h3>Jumlah Login Hari ini</h3>
          <div class="count"><?php echo htmlspecialchars($jumlahLogin); ?></div>
          <a href="../login_out/detail_login.php" aria-label="Lihat detail Login">
            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
              <circle cx="12" cy="12" r="2.5" />
            </svg>
          </a>
        </div>
      </div>
    </div>

    <!-- Container 3 -->
    <div class="container">
      <h2>Data Bulanan</h2>
      <canvas id="myChart"></canvas>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        const ctx = document.getElementById("myChart").getContext("2d");

        // ambil data 
        fetch("bulanan.php")
          // Ubah jadi JSON
          .then((response) => response.json())
          .then((data) => {
            createChart(data, 'bar');
          });

        function createChart(chartData, type) {
          new Chart(ctx, {
            type: type,
            data: {
              labels: [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
              ],
              datasets: [
                {
                  label: "Jumlah Surat Masuk",
                  data: chartData.masuk,
                  backgroundColor: "rgb(203, 163, 92)",
                  borderColor: "rgb(117, 78, 26)",
                  borderWidth: 1
                },
                {
                  label: "Jumlah Surat Keluar",
                  data: chartData.keluar,
                  backgroundColor: "rgb(168, 101, 35)",
                  borderColor: "rgb(82, 28, 13)",
                  borderWidth: 1
                }, {
                  label: "Jumlah Tamu",
                  data: chartData.tamu,
                  backgroundColor: "rgb(117, 78, 26)",
                  borderColor: "rgb(82, 28, 13)",
                  borderWidth: 1
                }
              ]
            },
            options: {
              responsive: true,
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    stepSize: 1
                  }
                }
              }
            }
          });
        }
      </script>
    </div>
    <footer style="background-color: beige; color: maroon; text-align: left; padding: 15px 0; font-weight: bold;">
      &copy; <?php echo date('Y'); ?> 2025 By Apradita. All rights reserved.
    </footer>
  </main>
</body>

</html>