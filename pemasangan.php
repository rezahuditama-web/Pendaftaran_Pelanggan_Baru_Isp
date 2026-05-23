<?php
include 'koneksi.php';

$cari = "";
if(isset($_GET['cari'])){
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
}

/* ======================================
   UPDATE STATUS PEMASANGAN
====================================== */
if(isset($_POST['update_status'])){
    $id_pemasangan     = $_POST['id_pemasangan'];
    $status_pemasangan = $_POST['status_pemasangan'];

    $query_update = mysqli_query($koneksi,"
        UPDATE pemasangan
        SET status_pemasangan='$status_pemasangan'
        WHERE id_pemasangan='$id_pemasangan'
    ");

    if($query_update){
        echo "<script>alert('Status pemasangan berhasil diupdate'); window.location='pemasangan.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}

/* ======================================
   STATISTIK CARD
====================================== */
$total_pelanggan   = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pelanggan"));
$total_pendaftaran = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan"));
$total_pending     = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan WHERE status_verifikasi='pending'"));
$total_selesai     = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pemasangan WHERE status_pemasangan='terpasang'"));
$total_monitoring  = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM monitoring WHERE status_koneksi='gangguan'"));

/* ======================================
   AMBIL DATA PEMASANGAN
   Sesuai kolom database:
   id_pemasangan, id_pendaftaran,
   alamat_pemasangan, tanggal_pemasangan,
   status_pemasangan
====================================== */
$query = mysqli_query($koneksi,"
    SELECT
        pm.id_pemasangan,
        pm.id_pendaftaran,
        pm.alamat_pemasangan,
        pm.tanggal_pemasangan,
        pm.status_pemasangan,
        p.nama_pelanggan,
        p.no_nik,
        p.alamat_domisili,
        p.foto_ktp,
        pk.nama_paket,
        pk.kecepatan,
        pk.harga,
        pk.jenis_paket,
        pp.tanggal_pengajuan,
        pp.status_verifikasi
    FROM pemasangan pm
    JOIN pendaftaran_pemasangan pp ON pm.id_pendaftaran = pp.id_pendaftaran
    JOIN pelanggan p ON pp.id_pelanggan = p.id_pelanggan
    JOIN paket pk ON pp.id_paket = pk.id_paket
    WHERE p.nama_pelanggan LIKE '%$cari%'
    OR pm.alamat_pemasangan LIKE '%$cari%'
    ORDER BY pm.id_pemasangan DESC
");

if(!$query){
    die("Error query: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemasangan – Gala Data</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>
    <style>
        .status-terpasang { background:#dcfce7; color:#16a34a; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
        .status-proses    { background:#fef3c7; color:#d97706; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
        .status-batal     { background:#fee2e2; color:#dc2626; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }

        .badge-rumah  { background:#dbeafe; color:#1a73e8; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-bisnis { background:#ede9fe; color:#7c3aed; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }

        .modal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-content {
            background: #fff; border-radius: 14px;
            padding: 32px 36px; width: 100%; max-width: 580px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2); position: relative;
        }
        .modal-content h2 {
            font-size: 18px; font-weight: 600;
            color: #1a73e8; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }
        .close { position: absolute; top: 16px; right: 20px; font-size: 22px; cursor: pointer; color: #888; }
        .close:hover { color: #333; }

        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
        .detail-item { background: #f8faff; border-radius: 10px; padding: 12px 14px; border: 1px solid #e8edf5; }
        .detail-item.full { grid-column: 1 / -1; }
        .detail-item label { display: block; font-size: 11px; font-weight: 600; color: #1a73e8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .detail-item p { font-size: 14px; font-weight: 500; color: #333; margin: 0; }

        .foto-ktp { width: 100%; border-radius: 10px; object-fit: cover; max-height: 160px; border: 1px solid #dde3f0; margin-bottom: 16px; }
        .divider { border: none; border-top: 1px solid #e8edf5; margin: 16px 0; }
        .section-title { font-size: 13px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px; }

        .histori-box { background: #f8faff; border-radius: 10px; padding: 14px; border: 1px solid #e8edf5; font-size: 13px; color: #555; }
        .histori-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e8edf5; }
        .histori-row:last-child { border-bottom: none; }
        .histori-row span:first-child { color: #888; }
        .histori-row span:last-child { font-weight: 600; color: #333; }

        .modal-content select {
            width: 100%; padding: 10px 14px; border: 1.5px solid #dde3f0;
            border-radius: 8px; font-family: 'Poppins', sans-serif;
            font-size: 14px; color: #333; outline: none; margin-bottom: 12px;
        }
        .modal-content select:focus { border-color: #1a73e8; }
        .modal-content label.form-label { display: block; font-size: 13px; font-weight: 500; color: #555; margin-bottom: 6px; }

        .btn-simpan {
            width: 100%; padding: 12px; background: #1a73e8; color: #fff;
            border: none; border-radius: 8px; font-family: 'Poppins', sans-serif;
            font-size: 15px; font-weight: 600; cursor: pointer; transition: background .2s;
        }
        .btn-simpan:hover { background: #0d5bc7; }

        .detail-btn {
            background: #1a73e8; color: #fff; border: none;
            padding: 6px 16px; border-radius: 6px; font-size: 13px;
            font-weight: 500; cursor: pointer; transition: background .2s;
        }
        .detail-btn:hover { background: #0d5bc7; }

        .topbar-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    </style>
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
            <li><i class="fa-solid fa-table-columns"></i><a href="dashboard.php">Dashboard</a></li>
            <li><i class="fa-solid fa-users"></i><a href="pelanggan.php">Pelanggan</a></li>
            <li><i class="fa-solid fa-user-plus"></i><a href="pendaftaran_pemasangan.php">Pendaftaran Pemasangan</a></li>
            <li class="active"><i class="fa-solid fa-hammer"></i><a href="pemasangan.php">Pemasangan</a></li>
            <li><i class="fa-solid fa-clipboard-check"></i><a href="monitoring.php">Monitoring</a></li>
            <li><i class="fa-solid fa-globe"></i><a href="setting_paket.php">Setting Paket</a></li>
            <li> <i class="fa-solid fa-envelope"></i><a href="contact.php">Pesan Masuk</a></li>
            <li><i class="fa-solid fa-gear"></i><a href="setting_admin.php">Pengaturan</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </div>
    </div>

    <!-- MAIN -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-row">
                <button class="dashboard-btn">
                    <i class="fa-solid fa-hammer"></i> Pemasangan
                </button>
                <form method="GET" class="search-form">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="cari"
                               placeholder="Cari Nama Pelanggan / Alamat"
                               value="<?= $cari; ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- CARD STATISTIK -->
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
                <div><h3>Terpasang</h3><h2><?= $total_selesai; ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-wifi"></i></div>
                <div><h3>Gangguan</h3><h2><?= $total_monitoring; ?></h2></div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat Pemasangan</th>
                        <th>Paket</th>
                        <th>Tgl Pemasangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $jumlah = mysqli_num_rows($query);
                while($row = mysqli_fetch_assoc($query)):
                ?>
                <tr>
                    <td><?= $row['id_pemasangan']; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                    <td><?= htmlspecialchars($row['alamat_pemasangan']); ?></td>
                    <td><?= htmlspecialchars($row['nama_paket']); ?></td>
                    <td>
                        <?= $row['tanggal_pemasangan']
                            ? date('d M Y', strtotime($row['tanggal_pemasangan']))
                            : '<span style="color:#aaa;">-</span>'; ?>
                    </td>
                    <td>
                        <?php
                        $st = $row['status_pemasangan'];
                        if($st == 'terpasang')
                            echo "<span class='status-terpasang'><i class='fa-solid fa-circle-check'></i> Terpasang</span>";
                        elseif($st == 'proses')
                            echo "<span class='status-proses'><i class='fa-solid fa-spinner'></i> Proses</span>";
                        else
                            echo "<span class='status-batal'><i class='fa-solid fa-circle-xmark'></i> Batal</span>";
                        ?>
                    </td>
                    <td>
                        <button class="detail-btn" onclick="openModal<?= $row['id_pemasangan']; ?>()">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>

                <!-- MODAL DETAIL -->
                <div class="modal" id="modal<?= $row['id_pemasangan']; ?>">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal<?= $row['id_pemasangan']; ?>()">&times;</span>
                        <h2><i class="fa-solid fa-circle-info"></i> Detail Pemasangan</h2>

                        <!-- Foto KTP -->
                        <?php if(!empty($row['foto_ktp'])): ?>
                        <img src="uploads/<?= $row['foto_ktp']; ?>" class="foto-ktp" alt="Foto KTP">
                        <?php endif; ?>

                        <!-- DATA PELANGGAN -->
                        <p class="section-title"><i class="fa-solid fa-user"></i> Data Pelanggan</p>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>NIK</label>
                                <p><?= htmlspecialchars($row['no_nik']); ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Nama Pelanggan</label>
                                <p><?= htmlspecialchars($row['nama_pelanggan']); ?></p>
                            </div>
                            <div class="detail-item full">
                                <label>Alamat Domisili</label>
                                <p><?= htmlspecialchars($row['alamat_domisili']); ?></p>
                            </div>
                        </div>

                        <hr class="divider">

                        <!-- LOKASI PEMASANGAN -->
                        <p class="section-title"><i class="fa-solid fa-location-dot"></i> Lokasi Pemasangan</p>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Tanggal Pemasangan</label>
                                <p><?= $row['tanggal_pemasangan']
                                    ? date('d M Y', strtotime($row['tanggal_pemasangan']))
                                    : 'Belum ditentukan'; ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Status</label>
                                <p><?= htmlspecialchars(ucfirst($row['status_pemasangan'])); ?></p>
                            </div>
                            <div class="detail-item full">
                                <label>Alamat Pemasangan</label>
                                <p><?= htmlspecialchars($row['alamat_pemasangan']); ?></p>
                            </div>
                        </div>

                        <hr class="divider">

                        <!-- PAKET INTERNET -->
                        <p class="section-title"><i class="fa-solid fa-wifi"></i> Paket Internet</p>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Nama Paket</label>
                                <p><?= htmlspecialchars($row['nama_paket']); ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Jenis</label>
                                <p>
                                <?php if(strtolower($row['jenis_paket']) == 'rumah'): ?>
                                    <span class="badge-rumah">Rumah</span>
                                <?php else: ?>
                                    <span class="badge-bisnis">Bisnis</span>
                                <?php endif; ?>
                                </p>
                            </div>
                            <div class="detail-item">
                                <label>Kecepatan</label>
                                <p><?= htmlspecialchars($row['kecepatan']); ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Harga</label>
                                <p style="color:#1a73e8; font-weight:700;">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>,-
                                </p>
                            </div>
                        </div>

                        <hr class="divider">

                        <!-- HISTORI PENGAJUAN -->
                        <p class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Histori Pengajuan</p>
                        <div class="histori-box">
                            <div class="histori-row">
                                <span>ID Pendaftaran</span>
                                <span>#<?= $row['id_pendaftaran']; ?></span>
                            </div>
                            <div class="histori-row">
                                <span>Tanggal Pengajuan</span>
                                <span><?= date('d M Y', strtotime($row['tanggal_pengajuan'])); ?></span>
                            </div>
                            <div class="histori-row">
                                <span>Status Verifikasi</span>
                                <span>
                                <?php
                                $sv = $row['status_verifikasi'];
                                if($sv == 'disetujui')      echo "<span style='color:#16a34a;'>✓ Disetujui</span>";
                                elseif($sv == 'ditolak')    echo "<span style='color:#dc2626;'>✗ Ditolak</span>";
                                else                        echo "<span style='color:#d97706;'>⏳ Pending</span>";
                                ?>
                                </span>
                            </div>
                            <div class="histori-row">
                                <span>Status Pemasangan</span>
                                <span>
                                <?php
                                $sp = $row['status_pemasangan'];
                                if($sp == 'terpasang')  echo "<span style='color:#16a34a;'>✓ Terpasang</span>";
                                elseif($sp == 'batal')  echo "<span style='color:#dc2626;'>✗ Batal</span>";
                                else                    echo "<span style='color:#d97706;'>⏳ Proses</span>";
                                ?>
                                </span>
                            </div>
                        </div>

                        <hr class="divider">

                        <!-- FORM UPDATE STATUS -->
                        <p class="section-title"><i class="fa-solid fa-pen-to-square"></i> Update Status Pemasangan</p>
                        <form method="POST">
                            <input type="hidden" name="id_pemasangan" value="<?= $row['id_pemasangan']; ?>">
                            <label class="form-label">Status Pemasangan</label>
                            <select name="status_pemasangan" required>
                                <option value="proses"     <?= $row['status_pemasangan']=='proses'     ? 'selected':'' ?>>Proses</option>
                                <option value="terpasang"  <?= $row['status_pemasangan']=='terpasang'  ? 'selected':'' ?>>Terpasang</option>
                                <option value="batal"      <?= $row['status_pemasangan']=='batal'      ? 'selected':'' ?>>Batal</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-simpan">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Status
                            </button>
                        </form>

                    </div>
                </div>

                <script>
                    function openModal<?= $row['id_pemasangan']; ?>(){
                        document.getElementById('modal<?= $row['id_pemasangan']; ?>').style.display='flex';
                    }
                    function closeModal<?= $row['id_pemasangan']; ?>(){
                        document.getElementById('modal<?= $row['id_pemasangan']; ?>').style.display='none';
                    }
                </script>

                <?php endwhile; ?>

                <?php if($jumlah == 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color:#aaa; padding:32px;">
                        <i class="fa-solid fa-box-open" style="font-size:24px; margin-bottom:8px; display:block;"></i>
                        Belum ada data pemasangan.
                    </td>
                </tr>
                <?php endif; ?>

                </tbody> 
            </table>
        </div>

    </div>
</div>

<script>
    window.onclick = function(e){
        if(e.target.classList.contains('modal')){
            e.target.style.display = 'none';
        }
    }
</script>

</body>
</html>
