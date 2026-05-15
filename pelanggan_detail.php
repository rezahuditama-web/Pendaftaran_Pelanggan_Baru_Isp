<?php
include 'koneksi.php';

$id = $_GET['id'];

# =========================
# DATA PELANGGAN
# =========================
$pelanggan = mysqli_query($koneksi, "
SELECT * FROM pelanggan
WHERE id_pelanggan = '$id'
");

$data = mysqli_fetch_assoc($pelanggan);

# =========================
# HISTORI PEMASANGAN
# =========================
$riwayat = mysqli_query($koneksi, "
SELECT 
    pemasangan.alamat_pemasangan,
    pemasangan.status_pemasangan,
    paket.nama_paket,
    paket.kecepatan

FROM pendaftaran_pemasangan

JOIN pemasangan
ON pemasangan.id_pendaftaran = pendaftaran_pemasangan.id_pendaftaran

JOIN paket
ON paket.id_paket = pendaftaran_pemasangan.id_paket

WHERE pendaftaran_pemasangan.id_pelanggan = '$id'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pelanggan</title>

    <link rel="stylesheet" href="style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<div class="detail-container">

    <h1>Detail Pelanggan</h1>

    <!-- IDENTITAS -->
    <div class="detail-card">

        <h2>Data Identitas</h2>

        <table class="detail-table">

            <tr>
                <td>ID Pelanggan</td>
                <td>: <?= $data['id_pelanggan']; ?></td>
            </tr>

            <tr>
                <td>NIK</td>
                <td>: <?= $data['no_nik']; ?></td>
            </tr>

            <tr>
                <td>Nama</td>
                <td>: <?= $data['nama_pelanggan']; ?></td>
            </tr>

            <tr>
                <td>No HP</td>
                <td>: <?= $data['no_hp']; ?></td>
            </tr>

            <tr>
                <td>Alamat Domisili</td>
                <td>: <?= $data['alamat_domisili']; ?></td>
            </tr>

        </table>

    </div>

    <!-- HISTORI -->
    <div class="detail-card">

        <h2>Riwayat Pemasangan</h2>

        <table>

            <thead>

                <tr>
                    <th>Nama Lokasi</th>
                    <th>Alamat Pemasangan</th>
                    <th>Paket</th>
                    <th>Kecepatan</th>
                    <th>Status</th>
                </tr>

            </thead>

            <tbody>

            <?php while($r = mysqli_fetch_assoc($riwayat)) { ?>

                <tr>

                    <td><?= $r['alamat_pemasangan']; ?></td>

                    <td><?= $r['nama_paket']; ?></td>

                    <td><?= $r['kecepatan']; ?></td>

                    <td><?= $r['status_pemasangan']; ?></td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>