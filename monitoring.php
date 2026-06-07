<?php
include 'koneksi.php';

$cari = "";
if(isset($_GET['cari'])){
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
}

/* ======================================
   TAMBAH MONITORING
====================================== */
if(isset($_POST['tambah'])){
    $id_pemasangan  = mysqli_real_escape_string($koneksi, $_POST['id_pemasangan']);
    $id_admin       = mysqli_real_escape_string($koneksi, $_POST['id_admin']);
    $tanggal_cek    = mysqli_real_escape_string($koneksi, $_POST['tanggal_cek']);
    $status_koneksi = mysqli_real_escape_string($koneksi, $_POST['status_koneksi']);
    $keterangan     = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    $q = mysqli_query($koneksi,"
        INSERT INTO monitoring(id_admin, id_pemasangan, tanggal_cek, status_koneksi, keterangan)
        VALUES('$id_admin','$id_pemasangan','$tanggal_cek','$status_koneksi','$keterangan')
    ");

    if($q){
        echo "<script>alert('Data monitoring berhasil ditambahkan'); window.location='monitoring.php';</script>";
    } else {
        echo "<script>alert('Gagal tambah: " . addslashes(mysqli_error($koneksi)) . "');</script>";
    }
    exit;
}

/* ======================================
   UPDATE MONITORING  ← PERBAIKAN: pakai POST action
====================================== */
if(isset($_POST['update'])){
    $id_monitoring  = mysqli_real_escape_string($koneksi, $_POST['id_monitoring']);
    $id_pemasangan  = mysqli_real_escape_string($koneksi, $_POST['id_pemasangan']);
    $id_admin       = mysqli_real_escape_string($koneksi, $_POST['id_admin']);
    $status_koneksi = mysqli_real_escape_string($koneksi, $_POST['status_koneksi']);
    $keterangan     = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $tanggal_cek    = mysqli_real_escape_string($koneksi, $_POST['tanggal_cek']);

    $q = mysqli_query($koneksi,"
        UPDATE monitoring
        SET
            id_pemasangan  = '$id_pemasangan',
            id_admin       = '$id_admin',
            status_koneksi = '$status_koneksi',
            keterangan     = '$keterangan',
            tanggal_cek    = '$tanggal_cek'
        WHERE id_monitoring = '$id_monitoring'
    ");

    if($q){
        echo "<script>alert('Data monitoring berhasil diupdate'); window.location='monitoring.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . addslashes(mysqli_error($koneksi)) . "');</script>";
    }
    exit;
}

/* ======================================
   HAPUS MONITORING  ← PERBAIKAN: pakai POST
====================================== */
if(isset($_POST['hapus'])){
    $id_monitoring = mysqli_real_escape_string($koneksi, $_POST['id_monitoring']);
    $q = mysqli_query($koneksi,"DELETE FROM monitoring WHERE id_monitoring='$id_monitoring'");
    if($q){
        echo "<script>alert('Data monitoring berhasil dihapus'); window.location='monitoring.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . addslashes(mysqli_error($koneksi)) . "');</script>";
    }
    exit;
}

/* ======================================
   STATISTIK CARD
====================================== */
$total_pelanggan   = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pelanggan"));
$total_pendaftaran = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan"));
$total_pending     = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan WHERE status_verifikasi='pending'"));
$total_terpasang   = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pemasangan WHERE status_pemasangan='terpasang'"));
$total_gangguan    = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM monitoring WHERE status_koneksi='offline'"));

/* ======================================
   AMBIL DATA MONITORING
====================================== */
$query = mysqli_query($koneksi,"
    SELECT
        m.*,
        p.nama_pelanggan,
        p.alamat_domisili,
        pk.nama_paket,
        pp.alamat_pemasangan,
        pm.status_pemasangan
    FROM monitoring m
    JOIN pemasangan pm ON m.id_pemasangan = pm.id_pemasangan
    JOIN pendaftaran_pemasangan pp ON pm.id_pendaftaran = pp.id_pendaftaran
    JOIN pelanggan p ON pp.id_pelanggan = p.id_pelanggan
    JOIN paket pk ON pp.id_paket = pk.id_paket
    WHERE p.nama_pelanggan LIKE '%$cari%'
    OR m.status_koneksi LIKE '%$cari%'
    OR m.keterangan LIKE '%$cari%'
    ORDER BY m.id_monitoring DESC
");

if(!$query){
    die("Error query: " . mysqli_error($koneksi));
}

/* ======================================
   DROPDOWN PEMASANGAN & ADMIN
====================================== */
$list_pemasangan = mysqli_query($koneksi,"
    SELECT pm.id_pemasangan, p.nama_pelanggan, pp.alamat_pemasangan
    FROM pemasangan pm
    JOIN pendaftaran_pemasangan pp ON pm.id_pendaftaran = pp.id_pendaftaran
    JOIN pelanggan p ON pp.id_pelanggan = p.id_pelanggan
    ORDER BY pm.id_pemasangan DESC
");

$list_admin = mysqli_query($koneksi,"SELECT * FROM admin ORDER BY id_admin ASC");

/* Simpan rows ke array supaya bisa dipakai ulang di modal edit */
$rows_monitoring = [];
while($r = mysqli_fetch_assoc($query)){
    $rows_monitoring[] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring – Gala Data</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>
    <style>
        /* STATUS BADGE */
        .status-online  { background:#dcfce7; color:#16a34a; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
        .status-offline { background:#fee2e2; color:#dc2626; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }

        /* MODAL */
        .modal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: #fff; border-radius: 14px;
            padding: 32px 36px; width: 100%; max-width: 520px;
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

        /* FORM MODAL */
        .modal-content label.form-label {
            display: block; font-size: 13px; font-weight: 500;
            color: #555; margin: 12px 0 4px;
        }
        .modal-content input,
        .modal-content select,
        .modal-content textarea {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid #dde3f0; border-radius: 8px;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            color: #333; outline: none; transition: border-color .2s;
            box-sizing: border-box;
        }
        .modal-content input:focus,
        .modal-content select:focus,
        .modal-content textarea:focus { border-color: #1a73e8; }
        .modal-content textarea { resize: vertical; min-height: 80px; }

        .btn-simpan {
            margin-top: 16px; width: 100%; padding: 12px;
            background: #1a73e8; color: #fff; border: none;
            border-radius: 8px; font-family: 'Poppins', sans-serif;
            font-size: 15px; font-weight: 600; cursor: pointer; transition: background .2s;
        }
        .btn-simpan:hover { background: #0d5bc7; }

        /* DETAIL BOX */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .detail-item { background: #f8faff; border-radius: 10px; padding: 12px 14px; border: 1px solid #e8edf5; }
        .detail-item.full { grid-column: 1 / -1; }
        .detail-item label { display: block; font-size: 11px; font-weight: 600; color: #1a73e8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .detail-item p { font-size: 14px; font-weight: 500; color: #333; margin: 0; }
        .divider { border: none; border-top: 1px solid #e8edf5; margin: 16px 0; }
        .section-title { font-size: 13px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px; }

        /* TOMBOL AKSI */
        .detail-btn {
            background: #1a73e8; color: #fff; border: none;
            padding: 6px 12px; border-radius: 6px; font-size: 12px;
            font-weight: 500; cursor: pointer; margin-right: 3px; transition: background .2s;
        }
        .detail-btn:hover { background: #0d5bc7; }
        .btn-edit {
            background: #fef3c7; color: #d97706; border: none;
            padding: 6px 12px; border-radius: 6px; font-size: 12px;
            font-weight: 500; cursor: pointer; margin-right: 3px; transition: background .2s;
        }
        .btn-edit:hover { background: #fde68a; }
        .btn-hapus {
            background: #fee2e2; color: #dc2626; border: none;
            padding: 6px 12px; border-radius: 6px; font-size: 12px;
            font-weight: 500; cursor: pointer; transition: background .2s;
        }
        .btn-hapus:hover { background: #fca5a5; }

        /* TAMBAH BUTTON */
        .btn-tambah {
            background: #1a73e8; color: #fff; border: none;
            padding: 10px 20px; border-radius: 8px;
            font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500;
            cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background .2s;
        }
        .btn-tambah:hover { background: #0d5bc7; }

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
            <li><i class="fa-solid fa-hammer"></i><a href="pemasangan.php">Pemasangan</a></li>
            <li class="active"><i class="fa-solid fa-clipboard-check"></i><a href="monitoring.php">Monitoring</a></li>
            <li><i class="fa-solid fa-globe"></i><a href="setting_paket.php">Setting Paket</a></li>
            <li><i class="fa-solid fa-envelope"></i><a href="contact.php">Pesan Masuk</a></li>
            <li><i class="fa-solid fa-gear"></i><a href="setting_admin.php">Pengaturan</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-row">
                <button class="dashboard-btn">
                    <i class="fa-solid fa-clipboard-check"></i> Monitoring
                </button>
                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <form method="GET" class="search-form">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" name="cari"
                                   placeholder="Cari Nama Pelanggan / Status"
                                   value="<?= htmlspecialchars($cari); ?>">
                        </div>
                    </form>
                    <button type="button" class="btn-tambah" onclick="openModalTambah()">
                        <i class="fa-solid fa-plus"></i> Tambah Monitoring
                    </button>
                </div>
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
                <div><h3>Terpasang</h3><h2><?= $total_terpasang; ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-wifi"></i></div>
                <div><h3>Gangguan</h3><h2><?= $total_gangguan; ?></h2></div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Paket</th>
                        <th>Tanggal Cek</th>
                        <th>Status Koneksi</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($rows_monitoring) == 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color:#aaa; padding:32px;">
                        <i class="fa-solid fa-box-open" style="font-size:24px; margin-bottom:8px; display:block;"></i>
                        Belum ada data monitoring.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach($rows_monitoring as $row): ?>
                <tr>
                    <td><?= $row['id_monitoring']; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                    <td><?= htmlspecialchars($row['nama_paket']); ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_cek'])); ?></td>
                    <td>
                        <?php if($row['status_koneksi'] == 'online'): ?>
                            <span class="status-online"><i class="fa-solid fa-circle-check"></i> Online</span>
                        <?php else: ?>
                            <span class="status-offline"><i class="fa-solid fa-circle-xmark"></i> Offline</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['keterangan']); ?></td>
                    <td style="white-space:nowrap;">
                        <!-- DETAIL -->
                        <button type="button" class="detail-btn"
                            onclick="openModalDetail(
                                '<?= $row['id_monitoring']; ?>',
                                '<?= htmlspecialchars($row['nama_pelanggan'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($row['nama_paket'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($row['alamat_pemasangan'], ENT_QUOTES); ?>',
                                '<?= $row['tanggal_cek']; ?>',
                                '<?= $row['status_koneksi']; ?>',
                                '<?= htmlspecialchars($row['keterangan'], ENT_QUOTES); ?>',
                                '<?= $row['id_pemasangan']; ?>',
                                '<?= $row['id_admin']; ?>'
                            )">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                        <!-- EDIT -->
                        <button type="button" class="btn-edit"
                            onclick="openModalEdit(
                                '<?= $row['id_monitoring']; ?>',
                                '<?= $row['id_pemasangan']; ?>',
                                '<?= $row['id_admin']; ?>',
                                '<?= $row['tanggal_cek']; ?>',
                                '<?= $row['status_koneksi']; ?>',
                                '<?= htmlspecialchars($row['keterangan'], ENT_QUOTES); ?>'
                            )">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                        <!-- HAPUS via form POST -->
                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('Yakin ingin menghapus data monitoring ini?')">
                            <input type="hidden" name="id_monitoring" value="<?= $row['id_monitoring']; ?>">
                            <button type="submit" name="hapus" class="btn-hapus">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================
     MODAL DETAIL (1 modal, diisi JS)
============================= -->
<div class="modal" id="modalDetail">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalDetail')">&times;</span>
        <h2><i class="fa-solid fa-circle-info"></i> Detail Monitoring</h2>

        <p class="section-title"><i class="fa-solid fa-user"></i> Data Pelanggan</p>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Nama Pelanggan</label>
                <p id="d_nama"></p>
            </div>
            <div class="detail-item">
                <label>Paket</label>
                <p id="d_paket"></p>
            </div>
            <div class="detail-item full">
                <label>Alamat Pemasangan</label>
                <p id="d_alamat"></p>
            </div>
        </div>

        <hr class="divider">

        <p class="section-title"><i class="fa-solid fa-wifi"></i> Status Koneksi</p>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Tanggal Cek</label>
                <p id="d_tanggal"></p>
            </div>
            <div class="detail-item">
                <label>Status Koneksi</label>
                <p id="d_status"></p>
            </div>
            <div class="detail-item full">
                <label>Keterangan</label>
                <p id="d_keterangan"></p>
            </div>
            <div class="detail-item">
                <label>ID Pemasangan</label>
                <p id="d_id_pemasangan"></p>
            </div>
            <div class="detail-item">
                <label>ID Admin</label>
                <p id="d_id_admin"></p>
            </div>
        </div>
    </div>
</div>

<!-- ============================
     MODAL TAMBAH MONITORING
============================= -->
<div class="modal" id="modalTambah">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalTambah')">&times;</span>
        <h2><i class="fa-solid fa-plus"></i> Tambah Monitoring</h2>
        <form method="POST">
            <label class="form-label">Pelanggan / Pemasangan *</label>
            <select name="id_pemasangan" required>
                <option value="">-- Pilih Pemasangan --</option>
                <?php
                mysqli_data_seek($list_pemasangan, 0);
                while($pm = mysqli_fetch_assoc($list_pemasangan)):
                ?>
                <option value="<?= $pm['id_pemasangan']; ?>">
                    #<?= $pm['id_pemasangan']; ?> – <?= htmlspecialchars($pm['nama_pelanggan']); ?> (<?= htmlspecialchars($pm['alamat_pemasangan']); ?>)
                </option>
                <?php endwhile; ?>
            </select>

            <label class="form-label">Admin *</label>
            <select name="id_admin" required>
                <option value="">-- Pilih Admin --</option>
                <?php
                mysqli_data_seek($list_admin, 0);
                while($adm = mysqli_fetch_assoc($list_admin)):
                ?>
                <option value="<?= $adm['id_admin']; ?>">
                    <?= htmlspecialchars($adm['nama_admin'] ?? $adm['username'] ?? 'Admin #'.$adm['id_admin']); ?>
                </option>
                <?php endwhile; ?>
            </select>

            <label class="form-label">Tanggal Cek *</label>
            <input type="date" name="tanggal_cek" value="<?= date('Y-m-d'); ?>" required>

            <label class="form-label">Status Koneksi *</label>
            <select name="status_koneksi" required>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
            </select>

            <label class="form-label">Keterangan *</label>
            <textarea name="keterangan" placeholder="Contoh: Koneksi stabil, tidak ada gangguan" required></textarea>

            <button type="submit" name="tambah" class="btn-simpan">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
        </form>
    </div>
</div>

<!-- ============================
     MODAL EDIT MONITORING  (1 modal)
============================= -->
<div class="modal" id="modalEdit">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalEdit')">&times;</span>
        <h2><i class="fa-solid fa-pen"></i> Edit Monitoring</h2>
        <form method="POST">
            <input type="hidden" name="id_monitoring" id="edit_id_monitoring">

            <label class="form-label">Pelanggan / Pemasangan *</label>
            <select name="id_pemasangan" id="edit_id_pemasangan" required>
                <option value="">-- Pilih Pemasangan --</option>
                <?php
                mysqli_data_seek($list_pemasangan, 0);
                while($pm = mysqli_fetch_assoc($list_pemasangan)):
                ?>
                <option value="<?= $pm['id_pemasangan']; ?>">
                    #<?= $pm['id_pemasangan']; ?> – <?= htmlspecialchars($pm['nama_pelanggan']); ?>
                </option>
                <?php endwhile; ?>
            </select>

            <label class="form-label">Admin *</label>
            <select name="id_admin" id="edit_id_admin" required>
                <option value="">-- Pilih Admin --</option>
                <?php
                mysqli_data_seek($list_admin, 0);
                while($adm = mysqli_fetch_assoc($list_admin)):
                ?>
                <option value="<?= $adm['id_admin']; ?>">
                    <?= htmlspecialchars($adm['nama_admin'] ?? $adm['username'] ?? 'Admin #'.$adm['id_admin']); ?>
                </option>
                <?php endwhile; ?>
            </select>

            <label class="form-label">Tanggal Cek *</label>
            <input type="date" name="tanggal_cek" id="edit_tanggal_cek" required>

            <label class="form-label">Status Koneksi *</label>
            <select name="status_koneksi" id="edit_status_koneksi" required>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
            </select>

            <label class="form-label">Keterangan *</label>
            <textarea name="keterangan" id="edit_keterangan" required></textarea>

            <button type="submit" name="update" class="btn-simpan">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
/* ── Helper tutup modal ── */
function closeModal(id){
    document.getElementById(id).style.display = 'none';
}

/* ── MODAL TAMBAH ── */
function openModalTambah(){
    document.getElementById('modalTambah').style.display = 'flex';
}

/* ── MODAL DETAIL ── */
function openModalDetail(id, nama, paket, alamat, tgl, status, ket, idPem, idAdm){
    document.getElementById('d_nama').textContent        = nama;
    document.getElementById('d_paket').textContent       = paket;
    document.getElementById('d_alamat').textContent      = alamat;
    document.getElementById('d_tanggal').textContent     = tgl;
    document.getElementById('d_keterangan').textContent  = ket;
    document.getElementById('d_id_pemasangan').textContent = '#' + idPem;
    document.getElementById('d_id_admin').textContent    = '#' + idAdm;

    var statusEl = document.getElementById('d_status');
    if(status === 'online'){
        statusEl.innerHTML = "<span class='status-online'>✓ Online</span>";
    } else {
        statusEl.innerHTML = "<span class='status-offline'>✗ Offline</span>";
    }

    document.getElementById('modalDetail').style.display = 'flex';
}

/* ── MODAL EDIT ── */
function openModalEdit(id, idPem, idAdm, tgl, status, ket){
    document.getElementById('edit_id_monitoring').value   = id;
    document.getElementById('edit_id_pemasangan').value   = idPem;
    document.getElementById('edit_id_admin').value        = idAdm;
    document.getElementById('edit_tanggal_cek').value     = tgl;
    document.getElementById('edit_status_koneksi').value  = status;
    document.getElementById('edit_keterangan').value      = ket;
    document.getElementById('modalEdit').style.display    = 'flex';
}

/* ── Tutup modal jika klik di luar ── */
window.addEventListener('click', function(e){
    if(e.target.classList.contains('modal')){
        e.target.style.display = 'none';
    }
});
</script>

</body>
</html>