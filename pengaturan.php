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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Pengaturan – Gala Data</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>
    <style>
        /* ===== GLOBAL RESET HP ANTI-ZOOM ===== */
        html, body {
            max-width: 100% !important;
            overflow-x: hidden !important;
            -webkit-text-size-adjust: 100% !important;
        }

        /* ===== TAB NAVIGATION ===== */
        .tab-nav {
            display: flex;
            border-bottom: 2px solid #e8edf5;
            margin-bottom: 28px;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 12px 24px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #888;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .tab-btn:hover { color: #1a73e8; }
        .tab-btn.active {
            color: #1a73e8;
            border-bottom-color: #1a73e8;
            font-weight: 600;
        }

        /* ===== TAB CONTENT ===== */
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* ===== FORM GRID ===== */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 28px;
        }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }
        
        /* PAKSA UKURAN INPUT MINIMAL 16PX DI MOBILE AGAR TIDAK AUTO ZOOM */
        .form-group input {
            padding: 11px 14px;
            border: 1.5px solid #dde3f0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px !important; 
            color: #333;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            background: #f8faff;
            width: 100%;
            box-sizing: border-box;
            -webkit-appearance: none;
        }
        .form-group input:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,.1);
            background: #fff;
            font-size: 16px !important;
        }
        .form-group input::placeholder { color: #aab0c0; }

        /* INPUT DENGAN PREFIX +62 */
        .input-prefix {
            display: flex;
            align-items: center;
            border: 1.5px solid #dde3f0;
            border-radius: 10px;
            overflow: hidden;
            background: #f8faff;
            transition: border-color .2s, box-shadow .2s;
            width: 100%;
            box-sizing: border-box;
        }
        .input-prefix:focus-within {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,.1);
            background: #fff;
        }
        .prefix-label {
            padding: 11px 14px;
            background: #e8edf5;
            color: #555;
            font-size: 14px;
            font-weight: 600;
            border-right: 1.5px solid #dde3f0;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .input-prefix input {
            border: none;
            background: transparent;
            padding: 11px 14px;
            flex: 1;
            box-shadow: none !important;
            width: 100%;
            font-size: 16px !important;
        }
        .input-prefix input:focus { outline: none; }

        /* DIVIDER */
        .setting-divider {
            border: none;
            border-top: 1.5px solid #e8edf5;
            margin: 28px 0;
        }

        /* TOMBOL */
        .btn-update {
            background: #1a73e8;
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
            margin-right: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-update:hover { background: #0d5bc7; transform: translateY(-1px); }
        .btn-update:active { transform: translateY(0); }

        .btn-reset {
            background: none;
            color: #888;
            border: 1.5px solid #dde3f0;
            padding: 12px 24px;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
        }
        .btn-reset:hover { border-color: #aaa; color: #555; }

        /* ALERT */
        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        /* ADMIN INFO HEADER */
        .admin-header {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f0f4ff;
            border: 1.5px solid #dde3f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .admin-avatar-circle {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: #1a73e8;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            text-transform: uppercase;
        }
        .admin-info .admin-name {
            font-size: 15px; font-weight: 600; color: #333;
        }
        .admin-info .admin-role {
            font-size: 12px; color: #888; margin-top: 2px;
        }
        .badge-aktif {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600;
            background: #dcfce7; color: #16a34a;
            border: 1px solid #bbf7d0;
            padding: 3px 10px; border-radius: 20px;
            margin-top: 5px;
        }

        /* INFO BOX */
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #1e40af;
        }
        .info-box i { margin-top: 1px; flex-shrink: 0; }

        /* PASSWORD GRID */
        .password-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            max-width: 480px;
        }

        /* TOPBAR */
        .topbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* ======================================
           MEDIA QUERIES HP
        ====================================== */
        @media (max-width: 768px) {
            .table-container { padding: 16px !important; }
            .admin-header { flex-direction: row; padding: 12px 16px; }
            .tab-btn { padding: 10px 14px; font-size: 13px; }
            .container, .main-content, .table-container {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                overflow-x: hidden !important;
            }
        }

        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; gap: 16px; }
            .form-group.full { grid-column: 1; }
            .btn-update, .btn-reset {
                width: 100%;
                justify-content: center;
                margin-bottom: 10px;
            }
            .btn-update { margin-right: 0; }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="sidebar">
        <div class="logo">
            <img src="asset/logo ISP.svg" alt="Logo Gala Data">
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
            <i class="fa-solid fa-right-from-bracket"></i>
            <a href="logout.php">Logout</a>
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

        <div class="table-container" style="padding: 28px;">

            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?= htmlspecialchars($pesan_sukses); ?>
                </div>
            <?php elseif (!empty($pesan_error)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <?= htmlspecialchars($pesan_error); ?>
                </div>
            <?php endif; ?>

            <div class="tab-nav">
                <button class="tab-btn active" onclick="switchTab('account', this)">
                    <i class="fa-solid fa-user"></i> Data Admin
                </button>
                <button class="tab-btn" onclick="switchTab('security', this)">
                    <i class="fa-solid fa-lock"></i> Login &amp; Keamanan
                </button>
            </div>

            <div class="tab-content active" id="tab-account">
                <div class="admin-header">
                    <div class="admin-avatar-circle" id="avatarCircle">
                        <?= strtoupper(substr($data_admin['nama_admin'] ?? 'AD', 0, 2)); ?>
                    </div>
                    <div class="admin-info">
                        <div class="admin-name" id="previewNama">
                            <?= htmlspecialchars($data_admin['nama_admin'] ?? 'Admin Sistem'); ?>
                        </div>
                        <div class="admin-role">Administrator</div>
                        <div class="badge-aktif">
                            <i class="fa-solid fa-shield-halved"></i> Admin Aktif
                        </div>
                    </div>
                </div>

                <div class="info-box">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Halaman ini digunakan untuk <strong>mengelola data admin sistem</strong>. Pastikan informasi sudah benar sebelum disimpan.</span>
                </div>

                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nama_admin">Nama Admin</label>
                            <input type="text" id="nama_admin" name="nama_admin" style="font-size: 16px !important;" placeholder="Nama lengkap admin" value="<?= htmlspecialchars($data_admin['nama_admin'] ?? ''); ?>" oninput="updatePreview(this.value)" required />
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" style="font-size: 16px !important;" placeholder="Masukkan username" value="<?= htmlspecialchars($data_admin['username'] ?? ''); ?>" required />
                        </div>

                        <div class="form-group">
                            <label for="no_telepon">No HP</label>
                            <div class="input-prefix">
                                <span class="prefix-label"><i class="fa-solid fa-mobile-screen"></i> +62</span>
                                <input type="text" id="no_telepon" name="no_telepon" style="font-size: 16px !important;" placeholder="Nomor HP" value="<?= htmlspecialchars($data_admin['no_telepon'] ?? ''); ?>" required />
                            </div>
                        </div>
                    </div>

                    <hr class="setting-divider">
                    <button type="submit" name="update_profile" class="btn-update"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                    <button type="reset" class="btn-reset">Reset</button>
                </form>
            </div>

            <div class="tab-content" id="tab-security">
                <form method="POST">
                    <h4 style="font-size: 15px; font-weight: 600; color: #333; margin-bottom: 20px;"><i class="fa-solid fa-lock" style="color: #1a73e8;"></i> Ganti Password</h4>
                    <div class="password-grid">
                        <div class="form-group">
                            <label for="password_lama">Password Lama</label>
                            <input type="password" id="password_lama" name="password_lama" style="font-size: 16px !important;" placeholder="Password lama" required />
                        </div>
                        <div class="form-group">
                            <label for="password_baru">Password Baru</label>
                            <input type="password" id="password_baru" name="password_baru" style="font-size: 16px !important;" placeholder="Minimal 6 karakter" required />
                        </div>
                        <div class="form-group">
                            <label for="konfirmasi_password">Konfirmasi Password Baru</label>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" style="font-size: 16px !important;" placeholder="Ulangi password" required />
                        </div>
                    </div>
                    <hr class="setting-divider">
                    <button type="submit" name="ganti_password" class="btn-update"><i class="fa-solid fa-key"></i> Simpan Password</button>
                    <button type="reset" class="btn-reset">Batal</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function switchTab(tab, el) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        el.classList.add('active');
    }

    function updatePreview(val) {
        val = val.trim();
        const words = val.split(' ').filter(Boolean);
        const initials = words.length >= 2 ? (words[0][0] + words[1][0]).toUpperCase() : (val.substring(0, 2) || 'AD').toUpperCase();
        document.getElementById('avatarCircle').textContent = initials;
        document.getElementById('previewNama').textContent = val || 'Admin Sistem';
    }

    /* === JS FORCE DISABLE AUTO-ZOOM === */
    document.querySelectorAll('input').forEach(function(el) {
        el.addEventListener('touchend', function(e) {
            const now = (new Date()).getTime();
            const lastTouch = el.getAttribute('data-last-touch') || 0;
            if (now - lastTouch < 300) { e.preventDefault(); }
            el.setAttribute('data-last-touch', now);
        });
    });

    const metaVp = document.querySelector('meta[name="viewport"]');
    if (metaVp) {
        document.querySelectorAll('input').forEach(function(el) {
            el.addEventListener('focus', function() {
                metaVp.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
            });
            el.addEventListener('blur', function() {
                metaVp.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
            });
        });
    }
</script>

</body>
</html>