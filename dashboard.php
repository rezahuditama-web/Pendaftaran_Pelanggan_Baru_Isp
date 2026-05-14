<?php
session_start();
include 'koneksi.php';
# =========================
# TOTAL PELANGGAN
# =========================
$q_pelanggan = mysqli_query($conn, 
"SELECT COUNT(*) as total FROM pelanggan");

$data_pelanggan = mysqli_fetch_assoc($q_pelanggan);
$total_pelanggan = $data_pelanggan['total'];

# =========================
# TOTAL PENDAFTARAN
# =========================
$q_pendaftaran = mysqli_query($conn, 
"SELECT COUNT(*) as total FROM pendaftaran_pemasangan");

$data_pendaftaran = mysqli_fetch_assoc($q_pendaftaran);
$total_pendaftaran = $data_pendaftaran['total'];

# =========================
# PENGAJUAN PENDING
# =========================
$q_pending = mysqli_query($conn,
"SELECT COUNT(*) as total 
FROM pendaftaran_pemasangan
WHERE status_verifikasi='pending'");

$data_pending = mysqli_fetch_assoc($q_pending);
$total_pending = $data_pending['total'];

# =========================
# PEMASANGAN SELESAI
# =========================
$q_pemasangan = mysqli_query($conn,
"SELECT COUNT(*) as total 
FROM pemasangan
WHERE status_pemasangan='selesai'");

$data_pemasangan = mysqli_fetch_assoc($q_pemasangan);
$total_pemasangan = $data_pemasangan['total'];

# =========================
# MONITORING GANGGUAN
# =========================
$q_monitoring = mysqli_query($conn,
"SELECT COUNT(*) as total 
FROM monitoring
WHERE status_koneksi='gangguan'");

$data_monitoring = mysqli_fetch_assoc($q_monitoring);
$total_monitoring = $data_monitoring['total'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin ISP</title>

    <link rel="stylesheet" href="style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>
</head>

<body>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="logo">
            <img src="assets/logo.png" alt="">
            <h2>GALA DATA</h2>
            <p>BEST SOLUTION FAST INTERNET</p>
        </div>

        <ul>
            <li class="active">
                <i class="fa-solid fa-table-columns"></i>
                Dashboard
            </li>

            <li>
                <i class="fa-solid fa-users"></i>
                Pelanggan
            </li>

            <li>
                <i class="fa-solid fa-user-plus"></i>
                Pendaftaran Pemasangan
            </li>

            <li>
                <i class="fa-solid fa-hammer"></i>
                Pemasangan
            </li>

            <li>
                <i class="fa-solid fa-clipboard-check"></i>
                Monitoring
            </li>

            <li>
                <i class="fa-solid fa-globe"></i>
                Setting Paket
            </li>

            <li>
                <i class="fa-solid fa-gear"></i>
                Pengaturan
            </li>
        </ul>

        <div class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </div>

    </div>

    <!-- MAIN -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">

            <button class="dashboard-btn">
                <i class="fa-solid fa-table-columns"></i>
                Dashboard
            </button>

            <div class="top-right">

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari Pelanggan">
                </div>

                <div class="profile">
                    <img src="https://i.pravatar.cc/40" alt="">
                </div>

            </div>

        </div>

        <!-- CARD -->
        <div class="card-container">

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div>
                    <h3>Pelanggan</h3>
                    <h2><?= $total_pelanggan; ?></h2>
                </div>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>

                <div>
                    <h3>Pendaftaran</h3>
                    <h2><?= $total_pendaftaran; ?></h2>
                </div>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>
                    <h3>Pending</h3>
                    <h2><?= $total_pending; ?></h2>
                </div>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-hammer"></i>
                </div>

                <div>
                    <h3>Pemasangan</h3>
                    <h2><?= $total_pemasangan; ?></h2>
                </div>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-wifi"></i>
                </div>

                <div>
                    <h3>Gangguan</h3>
                    <h2><?= $total_monitoring; ?></h2>
                </div>
            </div>

        </div>

        <!-- DASHBOARD BOX -->
        <div class="dashboard-box">

            <div class="mini-box-container">

                <div class="mini-box">
                    <h4>Pelanggan Online</h4>
                    <h1><?= $total_pelanggan - $total_monitoring; ?></h1>
                </div>

                <div class="mini-box">
                    <h4>Pengajuan Pemasangan</h4>
                    <h1><?= $total_pendaftaran; ?></h1>
                </div>

                <div class="mini-box">
                    <h4>Pemasangan Selesai</h4>
                    <h1><?= $total_pemasangan; ?></h1>
                </div>

                <div class="mini-box">
                    <h4>Pelanggan Offline</h4>
                    <h1><?= $total_monitoring; ?></h1>
                </div>

                <div class="mini-box">
                    <h4>Pengajuan Pending</h4>
                    <h1><?= $total_pending; ?></h1>
                </div>

                <div class="mini-box">
                    <h4>Total Pelanggan</h4>
                    <h1><?= $total_pelanggan; ?></h1>
                </div>

            </div>

            <!-- CHART -->
            <div class="chart-box">

                <h3>Activity</h3>

                <div class="bar-container">

                    <div class="bar" style="height:40%"></div>
                    <div class="bar" style="height:50%"></div>
                    <div class="bar" style="height:65%"></div>
                    <div class="bar" style="height:75%"></div>
                    <div class="bar" style="height:55%"></div>
                    <div class="bar" style="height:70%"></div>
                    <div class="bar" style="height:85%"></div>
                    <div class="bar" style="height:90%"></div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>