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


# =========================
# VERIFIKASI
# =========================
if(isset($_POST['verifikasi'])){

    $id_pendaftaran = $_POST['id_pendaftaran'];
    $status         = $_POST['status_verifikasi'];

    $query = mysqli_query($koneksi,"
    UPDATE pendaftaran_pemasangan
    SET 
        status_verifikasi='$status',
    WHERE id_pendaftaran='$id_pendaftaran'
    ");

    if($query){

        # =========================
        # VALIDASI NORMALISASI
        # HANYA YANG DISETUJUI
        # BOLEH MASUK PEMASANGAN
        # =========================
        if($status == 'disetujui'){

            $ambil = mysqli_query($koneksi,"
            SELECT * 
            FROM pendaftaran_pemasangan
            WHERE id_pendaftaran='$id_pendaftaran'
            ");

            $data = mysqli_fetch_assoc($ambil);

            mysqli_query($koneksi,"
            INSERT INTO pemasangan(
                id_pendaftaran,
                nama_lokasi,
                alamat_pemasangan,
                status_pemasangan
            )
            VALUES(
                '$id_pendaftaran',
                'Belum Ditentukan',
                'Belum Ditentukan',
                'proses'
            )
            ");
        }

        echo "
        <script>
            alert('Verifikasi berhasil');
            window.location='pendaftaran_pemasangan.php';
        </script>
        ";
    }
}

# =========================
# TAMPIL DATA
# =========================
$query = mysqli_query($koneksi,"
SELECT 
    pp.*,
    p.nama_pelanggan,
    p.no_nik,
    p.alamat_domisili,
    p.foto_ktp,
    pk.nama_paket
FROM pendaftaran_pemasangan pp
JOIN pelanggan p
ON pp.id_pelanggan = p.id_pelanggan
JOIN paket pk
ON pp.id_paket = pk.id_paket
ORDER BY pp.id_pendaftaran DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Pemasangan</title>

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

            <li>
                <i class="fa-solid fa-users"></i>
                <a href="pelanggan.php">Pelanggan</a>
            </li>

            <li class="active">
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
                <i class="fa-solid fa-gear"></i>
                <a href="setting_user.php">Pengaturan</a>
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
                <i class="fa-solid fa-users"></i>
                Pendaftaran Pemasangan
            </button>

        <form method="GET" class="search-form">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input 
                    type="text" 
                    name="cari"
                    placeholder="Cari Nama Pelanggan"
                    value="<?= $cari; ?>"
                >

            </div>

        </form>
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
        <div class="table-container">

            <table>

                <thead>

                <tr>
                    <th>ID Pendaftaran</th>
                    <th>Nama Pelanggan</th>
                    <th>Paket</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

                </thead>

                <tbody>

                <?php while($row = mysqli_fetch_assoc($query)) { ?>

                <tr>

                    <td><?= $row['id_pendaftaran']; ?></td>

                    <td><?= $row['nama_pelanggan']; ?></td>

                    <td><?= $row['nama_paket']; ?></td>

                    <td><?= $row['tanggal_pengajuan']; ?></td>

                    <td>

                        <?php
                        if($row['status_verifikasi']=='pending'){
                            echo "<span class='pending'>Pending</span>";
                        }
                        elseif($row['status_verifikasi']=='disetujui'){
                            echo "<span class='setuju'>Disetujui</span>";
                        }
                        else{
                            echo "<span class='tolak'>Ditolak</span>";
                        }
                        ?>

                    </td>

                    <td>

                        <button 
                        class="detail-btn"
                        onclick="openModal<?= $row['id_pendaftaran']; ?>()">
                        Detail
                        </button>

                    </td>

                </tr>

                <!-- MODAL -->
                <div class="modal" id="modal<?= $row['id_pendaftaran']; ?>">

                    <div class="modal-content">

                        <span class="close"
                        onclick="closeModal<?= $row['id_pendaftaran']; ?>()">
                        &times;
                        </span>

                        <h2>Detail Pengajuan</h2>

                        <div class="detail-box">

                            <img src="uploads/<?= $row['foto_ktp']; ?>">

                            <div class="detail-text">

                                <p>
                                <b>NIK :</b>
                                <?= $row['no_nik']; ?>
                                </p>

                                <p>
                                <b>Nama :</b>
                                <?= $row['nama_pelanggan']; ?>
                                </p>

                                <p>
                                <b>Alamat :</b>
                                <?= $row['alamat_domisili']; ?>
                                </p>

                                <p>
                                <b>Paket :</b>
                                <?= $row['nama_paket']; ?>
                                </p>

                            </div>

                        </div>

                        <!-- FORM VERIFIKASI -->
                        <form method="POST">

                            <input type="hidden"
                            name="id_pendaftaran"
                            value="<?= $row['id_pendaftaran']; ?>">

                            <label>Status Verifikasi</label>

                            <select name="status_verifikasi" required>

                                <option value="">
                                    -- Pilih Status --
                                </option>

                                <option value="disetujui">
                                    Disetujui
                                </option>

                                <option value="ditolak">
                                    Ditolak
                                </option>

                            </select>

                            <button 
                            type="submit"
                            name="verifikasi"
                            class="verif-btn">
                            Simpan Verifikasi
                            </button>

                        </form>

                    </div>

                </div>

                <script>

                    function openModal<?= $row['id_pendaftaran']; ?>(){
                        document.getElementById(
                        'modal<?= $row['id_pendaftaran']; ?>'
                        ).style.display='flex';
                    }

                    function closeModal<?= $row['id_pendaftaran']; ?>(){
                        document.getElementById(
                        'modal<?= $row['id_pendaftaran']; ?>'
                        ).style.display='none';
                    }

                </script>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>