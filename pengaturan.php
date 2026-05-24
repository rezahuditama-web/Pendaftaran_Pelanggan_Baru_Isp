<?php
include 'koneksi.php';
session_start();

$id_admin = $_SESSION['id_admin'] ?? 1;

/* ======================================
   AMBIL DATA ADMIN
====================================== */
$data_admin = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM admin WHERE id_admin='$id_admin'"
));

$pesan_sukses = "";
$pesan_error  = "";

/* ======================================
   UPDATE PROFILE
====================================== */
if (isset($_POST['update_profile'])) {
    $nama_admin = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    $username   = mysqli_real_escape_string($koneksi, $_POST['username']);
    $no_telepon = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);

    if (empty($nama_admin) || empty($username) || empty($no_telepon)) {
        $pesan_error = "Semua kolom wajib diisi.";
    } else {
        $cek_user = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT id_admin FROM admin WHERE username='$username' AND id_admin != '$id_admin'"
        ));
        if ($cek_user) {
            $pesan_error = "Username sudah digunakan oleh admin lain.";
        } else {
            $q = mysqli_query($koneksi, "
                UPDATE admin SET
                    nama_admin = '$nama_admin',
                    username   = '$username',
                    no_telepon = '$no_telepon'
                WHERE id_admin = '$id_admin'
            ");

            if ($q) {
                $pesan_sukses = "Data admin berhasil diupdate!";
                $data_admin = mysqli_fetch_assoc(mysqli_query($koneksi,
                    "SELECT * FROM admin WHERE id_admin='$id_admin'"
                ));
            } else {
                $pesan_error = "Gagal update: " . mysqli_error($koneksi);
            }
        }
    }
}

/* ======================================
   GANTI PASSWORD
====================================== */
if (isset($_POST['ganti_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi    = $_POST['konfirmasi_password'];

    $cek = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM admin WHERE id_admin='$id_admin' AND password='$password_lama'"
    ));

    if (!$cek) {
        $pesan_error = "Password lama tidak sesuai.";
    } elseif ($password_baru !== $konfirmasi) {
        $pesan_error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password_baru) < 6) {
        $pesan_error = "Password baru minimal 6 karakter.";
    } else {
        $q = mysqli_query($koneksi,
            "UPDATE admin SET password='$password_baru' WHERE id_admin='$id_admin'"
        );
        if ($q) $pesan_sukses = "Password berhasil diubah!";
        else    $pesan_error  = "Gagal mengubah password.";
    }
}

/* ======================================
   STATISTIK CARD
====================================== */
$total_pelanggan   = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pelanggan"));
$total_pendaftaran = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pendaftaran_pemasangan"));
$total_pending     = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pendaftaran_pemasangan WHERE status_verifikasi='pending'"));
$total_terpasang   = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pemasangan WHERE status_pemasangan='terpasang'"));
$total_gangguan    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM monitoring WHERE status_koneksi='gangguan'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengaturan – Gala Data</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>
    <style>
        /* CSS Tambahan khusus agar form di dalam table-container rapi layaknya tabel */
        .box-pengaturan {
            background: #fff;
            padding: 24px;
            border-radius: 8px;
        }
        .form-tabel {
            width: 100%;
            max-width: 650px;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .form-tabel td {
            padding: 10px 6px;
            vertical-align: middle;
            font-family: 'Poppins', sans-serif;
        }
        .form-tabel td label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        .form-tabel td input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #dde3f0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            /* DIUBAH KE 16px: Mencegah iOS/Android auto-zoom saat user klik/ketik di input form */
            font-size: 16px; 
            color: #333;
            outline: none;
            box-sizing: border-box;
        }
        .form-tabel td input:focus {
            border-color: #1a73e8;
        }
        .btn-aksi {
            background: #1a73e8;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-aksi:hover { background: #0d5bc7; }
        
        .alert {
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 15px;
            max-width: 650px;
        }
        .alert-success { background: #dcfce7; color: #16a34a; }
        .alert-error { background: #fee2e2; color: #dc2626; }
        
        .sub-judul {
            font-size: 16px;
            font-weight: 600;
            color: #1a73e8;
            margin-top: 10px;
            margin-bottom: 5px;
            border-bottom: 2px solid #e8edf5;
            padding-bottom: 6px;
            max-width: 650px;
        }
        .topbar-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    </style>
</head>
<body>

<div class="container">

    <div class="sidebar">
        <div class="logo">
            <img src="asset/logo ISP.svg" alt="">
            <h2>GALA DATA</h2>
            <p>BEST SOLUTION FAST INTERNET</p>
        </div>
        <ul>
            <li><i class="fa-solid fa-table-columns"></i><a href="dashboard.php">Dashboard</a></li>
            <li><i class="fa-solid fa-users"></i><a href="pelanggan.php">Pelanggan</a></li>
            <li><i class="fa-solid fa-user-plus"></i><a href="pendaftaran_pemasangan.php">Pendaftaran Pemasangan</a></li>
            <li><i class="fa-solid fa-hammer"></i><a href="pemasangan.php">Pemasangan</a></li>
            <li><i class="fa-solid fa-clipboard-check"></i><a href="monitoring.php">Monitoring</a></li>
            <li><i class="fa-solid fa-globe"></i><a href="setting_paket.php">Setting Paket</a></li>
            <li><i class="fa-solid fa-envelope"></i><a href="contact.php">Pesan Masuk</a></li>
            <li class="active"><i class="fa-solid fa-gear"></i><a href="setting_admin.php">Pengaturan</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> <a href="logout.php" style="color: inherit; text-decoration: none;">Logout</a>
        </div>
    </div>
    <div class="main-content">

        <div class="topbar">
            <div class="topbar-row">
                <button class="dashboard-btn">
                    <i class="fa-solid fa-gear"></i> Pengaturan
                </button>
            </div>
        </div>

        <div class="card-container">
            <div class="card">
                <div class="icon"><i class="fa-solid fa-users"></i></div>
                <div><h3>Pelanggan</h3><h2><?= $total_pelanggan; ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-user-plus"></i></div>
                <div><h3>Pendaftaran</h3><h2><?= $total_pendaftaran; ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-clock"></i></div>
                <div><h3>Pending</h3><h2><?= $total_pending; ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-hammer"></i></div>
                <div><h3>Terpasang</h3><h2><?= $total_terpasang; ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-wifi"></i></div>
                <div><h3>Gangguan</h3><h2><?= $total_gangguan; ?></h2></div>
            </div>
        </div>

        <div class="table-container" style="padding: 20px;">
            <div class="box-pengaturan">
                
                <?php if (!empty($pesan_sukses)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($pesan_sukses); ?></div>
                <?php elseif (!empty($pesan_error)): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($pesan_error); ?></div>
                <?php endif; ?>

                <div class="sub-judul"><i class="fa-solid fa-user-gear"></i> Kelola Data Admin</div>
                <form method="POST">
                    <table class="form-tabel">
                        <tr>
                            <td style="width: 160px;"><label for="nama_admin">Nama Admin</label></td>
                            <td><input type="text" id="nama_admin" name="nama_admin" value="<?= htmlspecialchars($data_admin['nama_admin'] ?? ''); ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="username">Username</label></td>
                            <td><input type="text" id="username" name="username" value="<?= htmlspecialchars($data_admin['username'] ?? ''); ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="no_telepon">No Telepon</label></td>
                            <td><input type="text" id="no_telepon" name="no_telepon" value="<?= htmlspecialchars($data_admin['no_telepon'] ?? ''); ?>" required></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><button type="submit" name="update_profile" class="btn-aksi"><i class="fa-solid fa-floppy-disk"></i> Simpan Data</button></td>
                        </tr>
                    </table>
                </form>

                <div style="margin-top: 30px;"></div>

                <div class="sub-judul"><i class="fa-solid fa-key"></i> Keamanan &amp; Ganti Password</div>
                <form method="POST">
                    <table class="form-tabel">
                        <tr>
                            <td style="width: 160px;"><label for="password_lama">Password Lama</label></td>
                            <td><input type="password" id="password_lama" name="password_lama" placeholder="Masukkan password lama" required></td>
                        </tr>
                        <tr>
                            <td><label for="password_baru">Password Baru</label></td>
                            <td><input type="password" id="password_baru" name="password_baru" placeholder="Minimal 6 karakter" required></td>
                        </tr>
                        <tr>
                            <td><label for="konfirmasi_password">Konfirmasi Password</label></td>
                            <td><input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password baru" required></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><button type="submit" name="ganti_password" class="btn-aksi" style="background: #dc2626;"><i class="fa-solid fa-shield"></i> Perbarui Password</button></td>
                        </tr>
                    </table>
                </form>

            </div>
        </div>

    </div>
</div>

</body>
</html>