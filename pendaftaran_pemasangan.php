<?php
include 'koneksi.php';

$cari = "";

if (isset($_GET['cari'])) {
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
}

/* ======================================
   STATISTIK CARD
====================================== */
$total_pelanggan = mysqli_num_rows(
    mysqli_query($koneksi, "SELECT * FROM pelanggan")
);

$total_pendaftaran = mysqli_num_rows(
    mysqli_query($koneksi, "SELECT * FROM pendaftaran_pemasangan")
);

$total_pending = mysqli_num_rows(
    mysqli_query($koneksi, "SELECT * FROM pendaftaran_pemasangan WHERE status_verifikasi='pending'")
);

$total_pemasangan = mysqli_num_rows(
    mysqli_query($koneksi, "SELECT * FROM pemasangan WHERE status_pemasangan='terpasang'")
);

$total_monitoring = mysqli_num_rows(
    mysqli_query($koneksi, "SELECT * FROM monitoring WHERE status_koneksi='gangguan'")
);

/* ======================================
   VERIFIKASI ACTION
====================================== */
if (isset($_POST['verifikasi'])) {
    $id_pendaftaran   = mysqli_real_escape_string($koneksi, $_POST['id_pendaftaran']);
    $status           = mysqli_real_escape_string($koneksi, $_POST['status_verifikasi']);

    $q = mysqli_query($koneksi, "
        UPDATE pendaftaran_pemasangan
        SET status_verifikasi = '$status'
        WHERE id_pendaftaran  = '$id_pendaftaran'
    ");

    if ($q) {
        if ($status == 'disetujui') {
            $cek_sudah = mysqli_fetch_assoc(mysqli_query($koneksi,
                "SELECT id_pemasangan FROM pemasangan WHERE id_pendaftaran='$id_pendaftaran'"
            ));

            if (!$cek_sudah) {
                mysqli_query($koneksi, "
                    INSERT INTO pemasangan (
                        id_pendaftaran,
                        alamat_pemasangan,
                        tanggal_pemasangan,
                        status_pemasangan
                    ) VALUES (
                        '$id_pendaftaran',
                        'Belum Ditentukan',
                        CURDATE(),
                        'proses'
                    )
                ");
            }
        }

        echo "<script>alert('Verifikasi berhasil'); window.location='pendaftaran_pemasangan.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}

/* ======================================
   TAMPIL DATA
====================================== */
$filter_nama = $cari ? "AND p.nama_pelanggan LIKE '%$cari%'" : "";

$query_tabel = mysqli_query($koneksi, "
    SELECT
        pp.id_pendaftaran,
        pp.tanggal_pengajuan,
        pp.status_verifikasi,
        p.nama_pelanggan,
        p.no_nik,
        p.no_hp,
        p.alamat_domisili,
        p.foto_ktp,
        pk.nama_paket
    FROM pendaftaran_pemasangan pp
    JOIN pelanggan p  ON pp.id_pelanggan = p.id_pelanggan
    JOIN paket    pk  ON pp.id_paket     = pk.id_paket
    WHERE 1=1 $filter_nama
    ORDER BY pp.id_pendaftaran DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Pemasangan – Gala Data</title>

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>

    <style>
        /* Menggunakan style modal yang setipe dengan pemasangan.php */
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

        span.pending   { background: #fef9c3; color: #b45309; border: 1px solid #fde68a; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        span.setuju    { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        span.tolak     { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        
        .note-box { border-radius: 8px; padding: 12px; font-size: 13px; font-weight: 500; }
        .note-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
        .note-danger { background: #fff1f2; border: 1px solid #fca5a5; color: #dc2626; }
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
            <li class="active"><i class="fa-solid fa-user-plus"></i><a href="pendaftaran_pemasangan.php">Pendaftaran Pemasangan</a></li>
            <li><i class="fa-solid fa-hammer"></i><a href="pemasangan.php">Pemasangan</a></li>
            <li><i class="fa-solid fa-clipboard-check"></i><a href="monitoring.php">Monitoring</a></li>
            <li><i class="fa-solid fa-globe"></i><a href="setting_paket.php">Setting Paket</a></li>
            <li><i class="fa-solid fa-envelope"></i><a href="contact.php">Pesan Masuk</a></li>
            <li><i class="fa-solid fa-gear"></i><a href="setting_admin.php">Pengaturan</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </div>
    </div>

    <div class="main-content">

        <div class="topbar">
            <div class="topbar-row">
                <button class="dashboard-btn">
                    <i class="fa-solid fa-user-plus"></i> Pendaftaran Pemasangan
                </button>
                <form method="GET" class="search-form">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="cari" placeholder="Cari Nama Pelanggan" value="<?= htmlspecialchars($cari); ?>">
                    </div>
                </form>
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
                <div><h3>Terpasang</h3><h2><?= $total_pemasangan; ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-wifi"></i></div>
                <div><h3>Gangguan</h3><h2><?= $total_monitoring; ?></h2></div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>No HP</th>
                        <th>Paket</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $jumlah = mysqli_num_rows($query_tabel);
                while ($row = mysqli_fetch_assoc($query_tabel)):
                    $id = $row['id_pendaftaran'];
                    $st = $row['status_verifikasi'];
                    
                    // Cek relasi antrian pemasangan
                    $sudah_pemasangan = mysqli_fetch_assoc(mysqli_query($koneksi,
                        "SELECT id_pemasangan FROM pemasangan WHERE id_pendaftaran='$id'"
                    ));
                ?>
                <tr>
                    <td><?= $id; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                    <td><?= htmlspecialchars($row['no_hp'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_paket']); ?></td>
                    <td><?= $row['tanggal_pengajuan']; ?></td>
                    <td>
                        <?php if ($st == 'pending'): ?>
                            <span class="pending"><i class="fa-solid fa-spinner"></i> Pending</span>
                        <?php elseif ($st == 'disetujui'): ?>
                            <span class="setuju"><i class="fa-solid fa-circle-check"></i> Disetujui</span>
                        <?php else: ?>
                            <span class="tolak"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="detail-btn" onclick="openModal<?= $id; ?>()">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>

                <div class="modal" id="modal<?= $id; ?>">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal<?= $id; ?>()">&times;</span>
                        <h2><i class="fa-solid fa-circle-info"></i> Detail Pengajuan</h2>

                        <?php if(!empty($row['foto_ktp']) && file_exists('uploads/' . $row['foto_ktp'])): ?>
                            <img src="uploads/<?= $row['foto_ktp']; ?>" class="foto-ktp" alt="Foto KTP">
                        <?php endif; ?>

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
                            <div class="detail-item">
                                <label>No HP</label>
                                <p><?= htmlspecialchars($row['no_hp'] ?? '-'); ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Paket Pilihan</label>
                                <p><?= htmlspecialchars($row['nama_paket']); ?></p>
                            </div>
                            <div class="detail-item full">
                                <label>Alamat Domisili</label>
                                <p><?= htmlspecialchars($row['alamat_domisili']); ?></p>
                            </div>
                        </div>

                        <hr class="divider">

                        <p class="section-title"><i class="fa-solid fa-pen-to-square"></i> Keputusan Verifikasi</p>
                        
                        <?php if ($st == 'pending'): ?>
                            <form method="POST">
                                <input type="hidden" name="id_pendaftaran" value="<?= $id; ?>">
                                <label class="form-label">Ubah Status</label>
                                <select name="status_verifikasi" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                                <button type="submit" name="verifikasi" class="btn-simpan">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan Verifikasi
                                </button>
                            </form>
                        <?php elseif ($st == 'disetujui'): ?>
                            <div class="note-box note-success">
                                <i class="fa-solid fa-circle-check"></i> Pengajuan ini telah <strong>Disetujui</strong> dan dialihkan ke data pemasangan.
                                <?php if($sudah_pemasangan): ?>
                                    (ID Pasang: #<?= $sudah_pemasangan['id_pemasangan']; ?>)
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="note-box note-danger">
                                <i class="fa-solid fa-circle-xmark"></i> Pengajuan ini telah <strong>Ditolak</strong>.
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <script>
                    function openModal<?= $id; ?>(){
                        document.getElementById('modal<?= $id; ?>').style.display='flex';
                    }
                    function closeModal<?= $id; ?>(){
                        document.getElementById('modal<?= $id; ?>').style.display='none';
                    }
                </script>

                <?php endwhile; ?>

                <?php if($jumlah == 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color:#aaa; padding:32px;">
                        <i class="fa-solid fa-box-open" style="font-size:24px; margin-bottom:8px; display:block;"></i>
                        Belum ada data pendaftaran.
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