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
   VERIFIKASI
   ALUR: pending → disetujui → masuk tabel pemasangan
         pending → ditolak   → tidak masuk pemasangan
====================================== */
if (isset($_POST['verifikasi'])) {
    $id_pendaftaran   = mysqli_real_escape_string($koneksi, $_POST['id_pendaftaran']);
    $status           = mysqli_real_escape_string($koneksi, $_POST['status_verifikasi']);

    // FIX: hapus koma trailing sebelum WHERE
    $q = mysqli_query($koneksi, "
        UPDATE pendaftaran_pemasangan
        SET status_verifikasi = '$status'
        WHERE id_pendaftaran  = '$id_pendaftaran'
    ");

    if ($q) {
        // Jika disetujui → masukkan ke tabel pemasangan (cek dulu belum ada)
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
   JOIN pelanggan (ambil no_hp) + paket
====================================== */
$filter_nama = $cari ? "AND p.nama_pelanggan LIKE '%$cari%'" : "";

$query = mysqli_query($koneksi, "
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pemasangan – Gala Data</title>

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>

    <style>
        /* ===== MODAL OVERLAY ===== */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.open { display: flex; }

        .modal-content {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            width: 560px;
            max-width: 95vw;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 8px 40px rgba(0,0,0,.18);
            animation: modalIn .2s ease;
        }

        @keyframes modalIn {
            from { transform: translateY(24px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        .modal-content h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1a1d23;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-content h2 i { color: #1a73e8; }

        .close-btn {
            position: absolute;
            top: 16px; right: 20px;
            background: #f1f3f8;
            border: none;
            width: 32px; height: 32px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #555;
            transition: background .15s;
        }
        .close-btn:hover { background: #e0e5f0; }

        /* ===== DETAIL BOX ===== */
        .detail-box {
            display: flex;
            gap: 20px;
            background: #f8faff;
            border: 1px solid #dde3f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        /* FOTO KTP */
        .foto-ktp-wrap {
            flex-shrink: 0;
            width: 130px;
        }
        .foto-ktp-wrap img {
            width: 130px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            border: 1.5px solid #dde3f0;
            display: block;
        }
        .foto-ktp-wrap .foto-label {
            font-size: 11px;
            color: #888;
            text-align: center;
            margin-top: 5px;
        }
        .foto-ktp-wrap .no-foto {
            width: 130px; height: 90px;
            background: #e8edf5;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            color: #aaa;
            font-size: 12px;
            gap: 4px;
            border: 1.5px dashed #cdd3e0;
        }
        .foto-ktp-wrap .no-foto i { font-size: 24px; }

        /* INFO PELANGGAN */
        .detail-info { flex: 1; }

        .info-row {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            align-items: flex-start;
            font-size: 13px;
        }
        .info-row .label {
            font-weight: 600;
            color: #555;
            min-width: 90px;
            flex-shrink: 0;
        }
        .info-row .value {
            color: #1a1d23;
            flex: 1;
        }
        .info-row .value.hp {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-row .value.hp i { color: #1a73e8; font-size: 13px; }

        /* BADGE STATUS */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.pending  { background: #fef9c3; color: #b45309; border: 1px solid #fde68a; }
        .badge.disetujui{ background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge.ditolak  { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }

        /* DIVIDER */
        .modal-divider {
            border: none;
            border-top: 1.5px solid #e8edf5;
            margin: 18px 0;
        }

        /* FORM VERIFIKASI */
        .verif-label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
            display: block;
        }
        .verif-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #dde3f0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #333;
            background: #f8faff;
            outline: none;
            margin-bottom: 16px;
            transition: border-color .2s;
        }
        .verif-select:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,.1);
        }

        .verif-btn {
            background: #1a73e8;
            color: #fff;
            border: none;
            padding: 11px 28px;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background .2s;
        }
        .verif-btn:hover { background: #0d5bc7; }

        /* STATUS BADGE IN TABLE */
        span.pending   { background: #fef9c3; color: #b45309; border: 1px solid #fde68a; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        span.setuju    { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        span.tolak     { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }

        /* ALREADY VERIFIED NOTE */
        .verified-note {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #16a34a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .rejected-note {
            background: #fff1f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- ===== SIDEBAR ===== -->
    <div class="sidebar">
        <div class="logo">
            <img src="asset/logo ISP.svg" alt="Logo Gala Data">
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
            <li><i class="fa-solid fa-gear"></i><a href="setting_user.php">Pengaturan</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">
            <button class="dashboard-btn">
                <i class="fa-solid fa-user-plus"></i>
                Pendaftaran Pemasangan
            </button>
            <form method="GET" class="search-form">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="text"
                        name="cari"
                        placeholder="Cari Nama Pelanggan"
                        value="<?= htmlspecialchars($cari); ?>"
                    >
                </div>
            </form>
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
                <div><h3>Terpasang</h3><h2><?= $total_pemasangan; ?></h2></div>
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
                        <th>No HP</th>
                        <th>Paket</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($query)):
                    $id  = $row['id_pendaftaran'];
                    $st  = $row['status_verifikasi'];

                    // Cek apakah sudah masuk pemasangan
                    $sudah_pemasangan = mysqli_fetch_assoc(mysqli_query($koneksi,
                        "SELECT id_pemasangan FROM pemasangan WHERE id_pendaftaran='$id'"
                    ));
                ?>
                <tr>
                    <td><?= $id; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                    <td>
                        <i class="fa-solid fa-phone" style="color:#1a73e8; font-size:12px;"></i>
                        <?= htmlspecialchars($row['no_hp'] ?? '-'); ?>
                    </td>
                    <td><?= htmlspecialchars($row['nama_paket']); ?></td>
                    <td><?= $row['tanggal_pengajuan']; ?></td>
                    <td>
                        <?php if ($st == 'pending'): ?>
                            <span class="pending">Pending</span>
                        <?php elseif ($st == 'disetujui'): ?>
                            <span class="setuju">Disetujui</span>
                        <?php else: ?>
                            <span class="tolak">Ditolak</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="detail-btn" onclick="bukaModal(<?= $id; ?>)">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>

                <!-- ===== MODAL DETAIL ===== -->
                <div class="modal" id="modal-<?= $id; ?>">
                    <div class="modal-content">

                        <button class="close-btn" onclick="tutupModal(<?= $id; ?>)" title="Tutup">&times;</button>

                        <h2>
                            <i class="fa-solid fa-file-lines"></i>
                            Detail Pengajuan #<?= $id; ?>
                        </h2>

                        <!-- INFO PELANGGAN + FOTO KTP -->
                        <div class="detail-box">

                            <!-- FOTO KTP -->
                            <div class="foto-ktp-wrap">
                                <?php
                                $foto_path = 'uploads/' . $row['foto_ktp'];
                                if (!empty($row['foto_ktp']) && file_exists($foto_path)):
                                ?>
                                    <img src="<?= $foto_path; ?>" alt="Foto KTP <?= htmlspecialchars($row['nama_pelanggan']); ?>">
                                    <div class="foto-label"><i class="fa-solid fa-id-card"></i> Foto KTP</div>
                                <?php else: ?>
                                    <div class="no-foto">
                                        <i class="fa-regular fa-image"></i>
                                        <span>Foto KTP<br>tidak tersedia</span>
                                    </div>
                                    <div class="foto-label">Foto KTP</div>
                                <?php endif; ?>
                            </div>

                            <!-- DATA PELANGGAN -->
                            <div class="detail-info">
                                <div class="info-row">
                                    <span class="label"><i class="fa-solid fa-hashtag" style="color:#aaa; font-size:11px;"></i> NIK</span>
                                    <span class="value"><?= htmlspecialchars($row['no_nik']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fa-solid fa-user" style="color:#aaa; font-size:11px;"></i> Nama</span>
                                    <span class="value"><?= htmlspecialchars($row['nama_pelanggan']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fa-solid fa-phone" style="color:#aaa; font-size:11px;"></i> No HP</span>
                                    <span class="value hp">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                        <?= htmlspecialchars($row['no_hp'] ?? '-'); ?>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fa-solid fa-location-dot" style="color:#aaa; font-size:11px;"></i> Alamat</span>
                                    <span class="value"><?= htmlspecialchars($row['alamat_domisili']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fa-solid fa-wifi" style="color:#aaa; font-size:11px;"></i> Paket</span>
                                    <span class="value"><?= htmlspecialchars($row['nama_paket']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fa-solid fa-calendar" style="color:#aaa; font-size:11px;"></i> Tanggal</span>
                                    <span class="value"><?= $row['tanggal_pengajuan']; ?></span>
                                </div>
                            </div>
                        </div><!-- /detail-box -->

                        <hr class="modal-divider">

                        <!-- FORM VERIFIKASI (hanya tampil jika masih pending) -->
                        <?php if ($st == 'pending'): ?>
                            <form method="POST">
                                <input type="hidden" name="id_pendaftaran" value="<?= $id; ?>">

                                <label class="verif-label">
                                    <i class="fa-solid fa-circle-check" style="color:#1a73e8;"></i>
                                    Keputusan Verifikasi
                                </label>

                                <select name="status_verifikasi" class="verif-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="disetujui">✅ Disetujui – lanjut ke pemasangan</option>
                                    <option value="ditolak">❌ Ditolak</option>
                                </select>

                                <button type="submit" name="verifikasi" class="verif-btn">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Simpan Verifikasi
                                </button>
                            </form>

                        <?php elseif ($st == 'disetujui'): ?>
                            <div class="verified-note">
                                <i class="fa-solid fa-circle-check"></i>
                                Pengajuan ini telah <strong>disetujui</strong> dan sudah masuk ke antrian pemasangan.
                                <?php if ($sudah_pemasangan): ?>
                                    (ID Pemasangan: #<?= $sudah_pemasangan['id_pemasangan']; ?>)
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <div class="rejected-note">
                                <i class="fa-solid fa-circle-xmark"></i>
                                Pengajuan ini telah <strong>ditolak</strong>.
                            </div>
                        <?php endif; ?>

                    </div><!-- /modal-content -->
                </div><!-- /modal -->

                <?php endwhile; ?>
                </tbody>
            </table>
        </div><!-- /table-container -->

    </div><!-- /main-content -->
</div><!-- /container -->

<script>
    function bukaModal(id) {
        document.getElementById('modal-' + id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function tutupModal(id) {
        document.getElementById('modal-' + id).classList.remove('open');
        document.body.style.overflow = '';
    }

    // Tutup modal kalau klik di luar area konten
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    });

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.open').forEach(function(m) {
                m.classList.remove('open');
            });
            document.body.style.overflow = '';
        }
    });
</script>

</body>
</html>
