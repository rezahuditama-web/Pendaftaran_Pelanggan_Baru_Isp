<?php
include 'koneksi.php';

$cari = "";

if(isset($_GET['cari'])){
    $cari = mysqli_real_escape_string(
        $koneksi,
        $_GET['cari']
    );
}

/* ======================================
   QUERY PELANGGAN
====================================== */

$query = mysqli_query($koneksi, "
SELECT * FROM pelanggan
WHERE no_nik LIKE '%$cari%'
ORDER BY id_pelanggan DESC
");

/* ======================================
   TOTAL PELANGGAN
====================================== */

$total_pelanggan = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM pelanggan")
);

/* ======================================
   TOTAL PENDAFTARAN
====================================== */

$total_pendaftaran = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM pendaftaran_pemasangan")
);

/* ======================================
   TOTAL PENDING
====================================== */

$total_pending = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM pendaftaran_pemasangan
    WHERE status_verifikasi='pending'")
);

/* ======================================
   TOTAL PEMASANGAN
====================================== */

$total_pemasangan = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM pemasangan
    WHERE status_pemasangan='selesai'")
);

/* ======================================
   TOTAL GANGGUAN
====================================== */

$total_monitoring = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM monitoring
    WHERE status_koneksi='gangguan'")
);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pelanggan</title>

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
            <img src="asset/logo ISP.svg" alt="">
            <h2>GALA DATA</h2>
            <p>BEST SOLUTION FAST INTERNET</p>
        </div>

        <ul>

            <li>
                <i class="fa-solid fa-table-columns"></i>
                <a href="dashboard.php">Dashboard</a>
            </li>

            <li class="active">
                <i class="fa-solid fa-users"></i>
                <a href="pelanggan.php">Pelanggan</a>
            </li>

            <li>
                <i class="fa-solid fa-user-plus"></i>
                <a href="pendaftaran_pemasangan.php">Pendaftaran Pemasangan</a>
            </li>

            <li>
                <i class="fa-solid fa-hammer"></i>
                <a href="pemasangan.php">Pemasangan</a>
            </li>

            <li>
                <i class="fa-solid fa-clipboard-check"></i>
                <a href="monitoring.php">Monitoring</a>
            </li>

            <li>
                <i class="fa-solid fa-globe"></i>
                <a href="setting_paket.php">Setting Paket</a>
            </li>

            <li>
                <i class="fa-solid fa-envelope"></i>
                <a href="contact.php">Pesan Masuk</a>
            </li>

            <li>
                <i class="fa-solid fa-gear"></i>
                <a href="setting_admin.php">Pengaturan</a>
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

    <div style="display:flex; align-items:center; gap:15px;">

        <button class="dashboard-btn">

            <i class="fa-solid fa-users"></i>

            Pelanggan

        </button>

        <form method="GET" class="search-form">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input 
                    type="text" 
                    name="cari"
                    placeholder="Cari NIK Pelanggan"
                    value="<?= $cari; ?>"
                >

            </div>

        </form>

    </div>

    <div class="profile">

        <img src="https://i.pravatar.cc/40" alt="">

    </div>

</div>

        <!-- CARD -->
        <div class="card-container">

            <?php

            $total = mysqli_num_rows($query);

            ?>

            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div>
                    <h3>Pelanggan</h3>
                    <h2><?= $total; ?></h2>
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
    
  <!-- PENDING -->
            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>

                    <h3>Pending</h3>

                    <h2><?= $total_pending; ?></h2>

                </div>

            </div>



            <!-- PEMASANGAN -->
            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-hammer"></i>
                </div>

                <div>

                    <h3>Pemasangan</h3>

                    <h2><?= $total_pemasangan; ?></h2>

                </div>

            </div>



            <!-- MONITORING -->
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

        <!-- TABLE -->
        <div class="table-box">

            <table>

                <thead>

                    <tr>
                        <th>ID Pelanggan</th>
                        <th>NIK</th>
                        <th>Nama Pelanggan</th>
                        <th>No HP</th>
                        <th>Alamat Domisili</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                <?php while($data = mysqli_fetch_assoc($query)) { ?>

                    <tr>

                        <td>
                            <?= $data['id_pelanggan']; ?>
                        </td>

                        <td>
                            <?= $data['no_nik']; ?>
                        </td>

                        <td>
                            <?= $data['nama_pelanggan']; ?>
                        </td>

                        <td>
                            <?= $data['no_hp']; ?>
                        </td>

                        <td>
                            <?= $data['alamat_domisili']; ?>
                        </td>

                        <td>

                            <a href="pelanggan_detail.php?id=<?= $data['id_pelanggan']; ?>" 
                            class="detail-btn">

                                Detail

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>