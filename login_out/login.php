<?php
session_start();
date_default_timezone_set("Asia/Jakarta"); // <- Tambahan penting untuk WIB
require_once __DIR__ . '/../koneksi.php';

// Fungsi untuk menambahkan akun baru
function tambahAkun($conn, $name, $email, $password_plain, $role, $divisi = null)
{
    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT * FROM tb_login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        // Insert akun baru
        $stmt = $conn->prepare("INSERT INTO tb_login (nama, email, password, role, divisi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $role, $divisi);
        $stmt->execute();
    }
}

// Cek apakah tb_login kosong, baru tambah akun default
$result = $conn->query("SELECT COUNT(*) AS jumlah FROM tb_login");
$row = $result->fetch_assoc();
if ($row['jumlah'] == 0) {
    tambahAkun($conn, 'Admin Utama', 'admin@gmail.com', 'admin123', 'admin');
    tambahAkun($conn, 'Kepala Sekolah', 'kepalasekolah@gmail.com', 'kepsek123', 'atasan');
    tambahAkun($conn, 'Waka Kurikulum', 'kurikulum@gmail.com', 'kurikulum123', 'divisi', 'kurikulum');
    tambahAkun($conn, 'Waka Kesiswaan', 'kesiswaan@gmail.com', 'kesiswaan123', 'divisi', 'kesiswaan');
    tambahAkun($conn, 'Waka Sarpras', 'sarpras@gmail.com', 'sarpras123', 'divisi', 'sarpras');
    tambahAkun($conn, 'Waka Hubin', 'hubin@gmail.com', 'hubin123', 'divisi', 'hubin');
    tambahAkun($conn, 'Kapro', 'kapro@gmail.com', 'kapro123', 'divisi', 'kapro');
    tambahAkun($conn, 'Tata Usaha', 'tatausaha@gmail.com', 'tatausaha123', 'divisi', 'tatausaha');
    tambahAkun($conn, 'Bendahara', 'bendahara@gmail.com', 'bendahara123', 'divisi', 'bendahara');
}

// Inisialisasi error
$error = "";

// Proses login saat method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM tb_login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login berhasil: update last_login dengan WIB
            $now = date("Y-m-d H:i:s"); // Sudah dalam format Asia/Jakarta
            $update = $conn->prepare("UPDATE tb_login SET last_login = ? WHERE id = ?");
            $update->bind_param("si", $now, $user['id']);
            $update->execute();

            // Simpan session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_divisi'] = $user['divisi'];
            $_SESSION['email'] = $user['email'];



            // Simpan pesan sukses di session
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Login berhasil! Selamat datang, ' . htmlspecialchars($user['nama']) . '.'
            ];

            // Redirect sesuai role
            switch ($user['role']) {
                case 'admin':
                    header("Location: ../admin/admin_page.php");
                    break;
                case 'atasan':
                    header("Location: ../multiuser/atasan.php");
                    break;
                case 'divisi':
                    header("Location: ../multiuser/divisi.php");
                    break;
                default:
                    header("Location: user/beranda.php");
                    break;
            }
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- MDB UI BOOTSTRAP -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />
    <!-- Memuat file JavaScript dari MDB UI Kit -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .bg-image-vertical {
            position: relative;
            overflow: hidden;
            background-repeat: no-repeat;
            background-position: right center;
            background-size: auto 100%;
        }

        @media (min-width: 1025px) {
            .h-custom-2 {
                height: 100%;
            }
        }

        body {
            background-color: #6B2C29;
            color: #F5F0E6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 400;
            line-height: 1.5;
            letter-spacing: 0.03em;
        }

        .form-outline .form-label {
            color: #EADFC8;
            transition: all 0.2s ease-in-out;
            font-family: 'Georgia', serif;
            font-weight: 600;
            letter-spacing: 0.04em;
            font-size: 1rem;
        }

        .form-control::placeholder {
            color: rgba(234, 223, 200, 0.6);
            font-style: italic;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-outline .form-control:focus~.form-label,
        .form-outline .form-control:not(:placeholder-shown)~.form-label {
            transform: translateY(-2em) scale(0.8);
            color: #F9F4EF;
            font-weight: 700;
        }

        .form-outline .form-control {
            background-color: rgba(234, 223, 200, 0.15);
            color: #4A342E;
            border: 1px solid rgba(234, 223, 200, 0.5);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1rem;
            font-weight: 400;
            letter-spacing: 0.02em;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .form-outline .form-control:focus {
            background-color: rgba(234, 223, 200, 0.3);
            color: #3E2F28;
            border-color: #D1B57E;
            box-shadow: 0 0 8px 0 rgba(209, 181, 126, 0.6);
        }

        .btn-info {
            background-color: #D1B57E !important;
            border: none;
            color: #4A342E;
            font-family: 'Georgia', serif;
            font-weight: 700;
            letter-spacing: 0.1em;
            font-size: 1.1rem;
            text-transform: uppercase;
            padding: 0.75rem 1.5rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-info:hover {
            background-color: #B89C5D !important;
            color: #fff;
        }

        .text-muted {
            color: #D1B57E !important;
            font-family: 'Georgia', serif;
            font-weight: 600;
            letter-spacing: 0.05em;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #F2D1D1;
            color: #6B2C29;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            letter-spacing: 0.03em;
            font-size: 0.95rem;
        }

        h3.fw-normal.mb-3.pb-3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 2.5rem;
            color: #D1B57E;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            user-select: none;
        }

        @media (max-width: 576px) {
            form[method="POST"] {
                width: 100% !important;
                padding: 0 1rem;
            }
        }

        @media (min-width: 577px) and (max-width: 1024px) {
            form[method="POST"] {
                width: 28rem !important;
            }
        }
    </style>
</head>

<body>
    <section class="vh-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 text-black">
                    <div class="d-flex align-items-center px-5 ms-xl-4 mt-4">
                        <img src="../images/logo-smk.png" alt="Logo"
                            style="height: 80px; margin-left: 10px; margin-top: 30px;" />
                    </div>
                    <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">
                        <form method="POST" style="width: 23rem;">
                            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Log in</h3>
                            <!-- Error Massage -->
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <div data-mdb-input-init class="form-outline mb-5">
                                <input type="email" name="email" class="form-control form-control-lg pl" placeholder=" "
                                    required />
                                <label class="form-label">Alamat Email</label>
                            </div>
                            <div class="form-outline mb-5 position-relative">
                                <input type="password" id="password" name="password"
                                    class="form-control form-control-lg" placeholder=" " required />
                                <label class="form-label" for="password">Password</label>
                                <span class="position-absolute top-50 end-0 translate-middle-y pe-3"
                                    style="cursor: pointer;" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div class="pt-1 mb-4">
                                <button class="btn btn-info btn-lg btn-block" type="submit">Login</button>
                            </div>
                            <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#">Lupa password? Hubungi
                                    Admin</a></p>
                        </form>
                    </div>
                </div>
                <div class="col-sm-6 px-0 d-none d-sm-block">
                    <img src="../images/gdg-pasim.png" alt="Login image" class="w-100 vh-100"
                        style="object-fit: cover; object-position: left;" />
                </div>
            </div>
        </div>
    </section>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            // toggle icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');

            // toggle type input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
        });
    </script>
</body>

</html>