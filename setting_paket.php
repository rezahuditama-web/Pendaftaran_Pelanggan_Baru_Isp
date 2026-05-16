<?php
include 'koneksi.php';

$cari = "";
if(isset($_GET['cari'])){
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
}

/* ======================================
   TAMBAH PAKET
====================================== */
if(isset($_POST['tambah'])){
    $nama_paket   = mysqli_real_escape_string($koneksi, $_POST['nama_paket']);
    $jenis_paket  = mysqli_real_escape_string($koneksi, $_POST['jenis_paket']);
    $kecepatan    = mysqli_real_escape_string($koneksi, $_POST['kecepatan']);
    $harga        = mysqli_real_escape_string($koneksi, $_POST['harga']);

    $query = mysqli_query($koneksi,"
        INSERT INTO paket(nama_paket, jenis_paket, kecepatan, harga)
        VALUES('$nama_paket','$jenis_paket','$kecepatan','$harga')
    ");

    if($query){
        echo "<script>alert('Paket berhasil ditambahkan'); window.location='setting_paket.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan paket');</script>";
    }
}

/* ======================================
   EDIT PAKET
====================================== */
if(isset($_POST['edit'])){
    $id_paket    = $_POST['id_paket'];
    $nama_paket  = mysqli_real_escape_string($koneksi, $_POST['nama_paket']);
    $jenis_paket = mysqli_real_escape_string($koneksi, $_POST['jenis_paket']);
    $kecepatan   = mysqli_real_escape_string($koneksi, $_POST['kecepatan']);
    $harga       = mysqli_real_escape_string($koneksi, $_POST['harga']);

    $query = mysqli_query($koneksi,"
        UPDATE paket
        SET 
            nama_paket='$nama_paket',
            jenis_paket='$jenis_paket',
            kecepatan='$kecepatan',
            harga='$harga'
        WHERE id_paket='$id_paket'
    ");

    if($query){
        echo "<script>alert('Paket berhasil diupdate'); window.location='setting_paket.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate paket');</script>";
    }
}

/* ======================================
   HAPUS PAKET
====================================== */
if(isset($_GET['hapus'])){
    $id_paket = $_GET['hapus'];
    $query = mysqli_query($koneksi,"DELETE FROM paket WHERE id_paket='$id_paket'");
    if($query){
        echo "<script>alert('Paket berhasil dihapus'); window.location='setting_paket.php';</script>";
    }
}

/* ======================================
   STATISTIK CARD
====================================== */
$total_pelanggan = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pelanggan"));
$total_pendaftaran = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan"));
$total_pending = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan WHERE status_verifikasi='pending'"));
$total_pemasangan = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pemasangan WHERE status_pemasangan='selesai'"));
$total_monitoring = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM monitoring WHERE status_koneksi='gangguan'"));

/* ======================================
   AMBIL DATA PAKET
====================================== */
$query = mysqli_query($koneksi,"
    SELECT * FROM paket
    WHERE nama_paket LIKE '%$cari%'
    OR jenis_paket LIKE '%$cari%'
    ORDER BY id_paket DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setting Paket – Gala Data</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>
    <style>
        /* ===== MODAL ===== */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #fff;
            border-radius: 14px;
            padding: 32px 36px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            position: relative;
        }
        .modal-content h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1a73e8;
            margin-bottom: 20px;
        }
        .close {
            position: absolute;
            top: 16px; right: 20px;
            font-size: 22px;
            cursor: pointer;
            color: #888;
        }
        .close:hover { color: #333; }

        /* ===== FORM DALAM MODAL ===== */
        .modal-content label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #555;
            margin: 12px 0 4px;
        }
        .modal-content input,
        .modal-content select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #dde3f0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: border-color .2s;
        }
        .modal-content input:focus,
        .modal-content select:focus {
            border-color: #1a73e8;
        }
        .modal-content .btn-simpan {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #1a73e8;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }
        .modal-content .btn-simpan:hover { background: #0d5bc7; }

        /* ===== TOMBOL TAMBAH ===== */
        .btn-tambah {
            background: #1a73e8;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background .2s;
        }
        .btn-tambah:hover { background: #0d5bc7; }

        /* ===== TOPBAR ROW ===== */
        .topbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0;
        }

        /* ===== BADGE JENIS PAKET ===== */
        .badge-basic   { background:#dbeafe; color:#1a73e8; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-premium { background:#ede9fe; color:#7c3aed; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }

        /* ===== TOMBOL AKSI ===== */
        .btn-edit {
            background: #fef3c7;
            color: #d97706;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            margin-right: 6px;
            transition: background .2s;
        }
        .btn-edit:hover { background: #fde68a; }

        .btn-hapus {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-hapus:hover { background: #fca5a5; }

        /* ===== HARGA ===== */
        .harga-text { font-weight: 600; color: #1a73e8; }
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
            <li><i class="fa-solid fa-clipboard-check"></i><a href="monitoring.php">Monitoring</a></li>
            <li class="active"><i class="fa-solid fa-globe"></i><a href="setting_paket.php">Setting Paket</a></li>
            <li><i class="fa-solid fa-gear"></i><a href="setting_user.php">Pengaturan</a></li>
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
            <div class="topbar-row">
                <button class="dashboard-btn">
                    <i class="fa-solid fa-globe"></i>
                    Setting Paket
                </button>
                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <form method="GET" class="search-form">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" name="cari" placeholder="Cari Nama / Jenis Paket" value="<?= $cari; ?>">
                        </div>
                    </form>
                    <button class="btn-tambah" onclick="openModalTambah()">
                        <i class="fa-solid fa-plus"></i> Tambah Paket
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
                <div><h3>Pemasangan</h3><h2><?= $total_pemasangan; ?></h2></div>
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
                        <th>No</th>
                        <th>Nama Paket</th>
                        <th>Jenis Paket</th>
                        <th>Kecepatan</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                while($row = mysqli_fetch_assoc($query)):
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_paket']); ?></td>
                    <td>
                        <?php if(strtolower($row['jenis_paket']) == 'rumah'): ?>
                            <span class="badge-basic">Rumah</span>
                        <?php else: ?>
                            <span class="badge-premium">Bisnis</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['kecepatan']); ?> Mbps</td>
                    <td class="harga-text">Rp <?= number_format($row['harga'], 0, ',', '.'); ?>,-</td>
                    <td>
                        <button class="btn-edit"
                            onclick="openModalEdit(
                                '<?= $row['id_paket']; ?>',
                                '<?= htmlspecialchars($row['nama_paket']); ?>',
                                '<?= htmlspecialchars($row['jenis_paket']); ?>',
                                '<?= htmlspecialchars($row['kecepatan']); ?>',
                                '<?= $row['harga']; ?>'
                            )">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                        <button class="btn-hapus"
                            onclick="konfirmasiHapus('<?= $row['id_paket']; ?>', '<?= htmlspecialchars($row['nama_paket']); ?>')">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($query) == 0 && $no == 1): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#aaa; padding:24px;">
                        Belum ada data paket.
                    </td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /main-content -->
</div><!-- /container -->

<!-- ============================
     MODAL TAMBAH PAKET
============================= -->
<div class="modal" id="modalTambah">
    <div class="modal-content">
        <span class="close" onclick="closeModalTambah()">&times;</span>
        <h2><i class="fa-solid fa-plus" style="margin-right:8px;"></i>Tambah Paket</h2>
        <form method="POST">
            <label>Nama Paket *</label>
            <input type="text" name="nama_paket" placeholder="Contoh: Regular Silver" required/>

            <label>Jenis Paket *</label>
            <select name="jenis_paket" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="basic">Basic Home</option>
                <option value="premium">Premium Bisnis</option>
            </select>

            <label>Kecepatan (Mbps) *</label>
            <input type="number" name="kecepatan" placeholder="Contoh: 20" min="1" required/>

            <label>Harga (Rp) *</label>
            <input type="number" name="harga" placeholder="Contoh: 89000" min="0" required/>

            <button type="submit" name="tambah" class="btn-simpan">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
        </form>
    </div>
</div>

<!-- ============================
     MODAL EDIT PAKET
============================= -->
<div class="modal" id="modalEdit">
    <div class="modal-content">
        <span class="close" onclick="closeModalEdit()">&times;</span>
        <h2><i class="fa-solid fa-pen" style="margin-right:8px;"></i>Edit Paket</h2>
        <form method="POST">
            <input type="hidden" name="id_paket" id="edit_id_paket"/>

            <label>Nama Paket *</label>
            <input type="text" name="nama_paket" id="edit_nama_paket" required/>

            <label>Jenis Paket *</label>
            <select name="jenis_paket" id="edit_jenis_paket" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="basic">Basic Home</option>
                <option value="premium">Premium Bisnis</option>
            </select>

            <label>Kecepatan (Mbps) *</label>
            <input type="number" name="kecepatan" id="edit_kecepatan" min="1" required/>

            <label>Harga (Rp) *</label>
            <input type="number" name="harga" id="edit_harga" min="0" required/>

            <button type="submit" name="edit" class="btn-simpan">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
    // ===== MODAL TAMBAH =====
    function openModalTambah(){
        document.getElementById('modalTambah').style.display = 'flex';
    }
    function closeModalTambah(){
        document.getElementById('modalTambah').style.display = 'none';
    }

    // ===== MODAL EDIT =====
    function openModalEdit(id, nama, jenis, kecepatan, harga){
        document.getElementById('edit_id_paket').value   = id;
        document.getElementById('edit_nama_paket').value = nama;
        document.getElementById('edit_jenis_paket').value = jenis;
        document.getElementById('edit_kecepatan').value  = kecepatan;
        document.getElementById('edit_harga').value      = harga;
        document.getElementById('modalEdit').style.display = 'flex';
    }
    function closeModalEdit(){
        document.getElementById('modalEdit').style.display = 'none';
    }

    // ===== HAPUS =====
    function konfirmasiHapus(id, nama){
        if(confirm('Yakin ingin menghapus paket "' + nama + '"?')){
            window.location = 'setting_paket.php?hapus=' + id;
        }
    }

    // Tutup modal jika klik di luar
    window.onclick = function(e){
        if(e.target.classList.contains('modal')){
            e.target.style.display = 'none';
        }
    }
</script>

</body>
</html>
