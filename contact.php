<?php
include 'koneksi.php';

$cari = "";
if(isset($_GET['cari'])){
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
}

/* ======================================
   HAPUS PESAN
====================================== */
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    $q  = mysqli_query($koneksi, "DELETE FROM contact WHERE id_contact='$id'");
    if($q){
        echo "<script>alert('Pesan berhasil dihapus'); window.location='contact.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus: " . mysqli_error($koneksi) . "');</script>";
    }
}

/* ======================================
   STATISTIK CARD (sama seperti monitoring)
====================================== */
$total_pelanggan   = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pelanggan"));
$total_pendaftaran = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan"));
$total_pending     = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pendaftaran_pemasangan WHERE status_verifikasi='pending'"));
$total_terpasang   = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pemasangan WHERE status_pemasangan='terpasang'"));
$total_pesan       = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM contact"));
$total_gangguan    = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM monitoring WHERE status_koneksi='gangguan'"));

/* ======================================
   AMBIL DATA PESAN
====================================== */
$query = mysqli_query($koneksi,"
    SELECT * FROM contact
    WHERE nama_pengirim LIKE '%$cari%'
    OR email LIKE '%$cari%'
    OR subjek LIKE '%$cari%'
    OR no_hp LIKE '%$cari%'
    ORDER BY id_contact DESC
");

if(!$query){
    die("Error query: " . mysqli_error($koneksi));
}

$jumlah = mysqli_num_rows($query);
$rows   = [];
while($r = mysqli_fetch_assoc($query)) $rows[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Masuk – Gala Data</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>
    <style>
        /* ===== MODAL ===== */
        .modal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-content {
            background: #fff; border-radius: 14px;
            padding: 32px 36px; width: 100%; max-width: 560px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2); position: relative;
        }
        .modal-content h2 {
            font-size: 18px; font-weight: 600;
            color: #1a73e8; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }
        .close {
            position: absolute; top: 16px; right: 20px;
            font-size: 22px; cursor: pointer; color: #888;
        }
        .close:hover { color: #333; }

        /* ===== DETAIL GRID ===== */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .detail-item { background: #f8faff; border-radius: 10px; padding: 12px 14px; border: 1px solid #e8edf5; }
        .detail-item.full { grid-column: 1 / -1; }
        .detail-item label { display: block; font-size: 11px; font-weight: 600; color: #1a73e8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .detail-item p { font-size: 14px; font-weight: 500; color: #333; margin: 0; line-height: 1.6; }
        .divider { border: none; border-top: 1px solid #e8edf5; margin: 16px 0; }
        .section-title { font-size: 13px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px; }

        /* ===== TOMBOL AKSI ===== */
        .detail-btn {
            background: #1a73e8; color: #fff; border: none;
            padding: 6px 14px; border-radius: 6px; font-size: 13px;
            font-weight: 500; cursor: pointer; margin-right: 4px;
            transition: background .2s; font-family: 'Poppins', sans-serif;
        }
        .detail-btn:hover { background: #0d5bc7; }

        .btn-wa {
            background: #dcfce7; color: #16a34a; border: none;
            padding: 6px 14px; border-radius: 6px; font-size: 13px;
            font-weight: 500; cursor: pointer; margin-right: 4px;
            transition: background .2s; font-family: 'Poppins', sans-serif;
        }
        .btn-wa:hover { background: #bbf7d0; }

        .btn-hapus {
            background: #fee2e2; color: #dc2626; border: none;
            padding: 6px 14px; border-radius: 6px; font-size: 13px;
            font-weight: 500; cursor: pointer;
            transition: background .2s; font-family: 'Poppins', sans-serif;
        }
        .btn-hapus:hover { background: #fca5a5; }

        /* ===== TOMBOL DI DALAM MODAL HUBUNGI ===== */
        .contact-actions {
            display: flex; gap: 12px; flex-wrap: wrap; margin-top: 18px;
        }
        .btn-copy {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #f0f4f8; color: #333; border: 1.5px solid #dde3f0;
            padding: 11px 16px; border-radius: 8px; font-size: 14px; font-weight: 500;
            cursor: pointer; font-family: 'Poppins', sans-serif; transition: all .2s;
        }
        .btn-copy:hover { background: #e0eaff; border-color: #1a73e8; color: #1a73e8; }
        .btn-wa-big {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #25d366; color: #fff; border: none;
            padding: 11px 16px; border-radius: 8px; font-size: 14px; font-weight: 600;
            cursor: pointer; font-family: 'Poppins', sans-serif; transition: background .2s;
            text-decoration: none;
        }
        .btn-wa-big:hover { background: #1ebe5a; }

        /* ===== TOPBAR ROW ===== */
        .topbar-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }

        /* ===== PESAN BOX ===== */
        .pesan-box {
            background: #f8faff; border: 1px solid #e8edf5;
            border-radius: 10px; padding: 16px;
        }
        .pesan-box p { font-size: 14px; color: #444; line-height: 1.75; margin: 0; }

        /* ===== COPY TOAST ===== */
        #copyToast {
            position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
            background: #0d5bc7; color: #fff; padding: 10px 24px;
            border-radius: 8px; font-size: 13px; font-weight: 600;
            display: none; z-index: 9999; box-shadow: 0 4px 16px rgba(0,0,0,.2);
        }
    </style>
</head>
<body>

<div class="container">

    <!-- ===== SIDEBAR ===== -->
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
            <li><i class="fa-solid fa-globe"></i><a href="setting_paket.php">Setting Paket</a></li>
            <li class="active"><i class="fa-solid fa-envelope"></i><a href="contact.php">Pesan Masuk</a></li>
            <li><i class="fa-solid fa-gear"></i><a href="setting_admin.php">Pengaturan</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </div>
    </div>

    <!-- ===== MAIN ===== -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-row">
                <button class="dashboard-btn">
                    <i class="fa-solid fa-envelope"></i> Pesan Masuk
                </button>
                <form method="GET" class="search-form">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="cari"
                               placeholder="Cari Nama / Email / Subjek"
                               value="<?= htmlspecialchars($cari) ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- CARD STATISTIK -->
        <div class="card-container">
            <div class="card">
                <div class="icon"><i class="fa-solid fa-users"></i></div>
                <div><h3>Pelanggan</h3><h2><?= $total_pelanggan ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-user-plus"></i></div>
                <div><h3>Pendaftaran</h3><h2><?= $total_pendaftaran ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-clock"></i></div>
                <div><h3>Pending</h3><h2><?= $total_pending ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-hammer"></i></div>
                <div><h3>Terpasang</h3><h2><?= $total_terpasang ?></h2></div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                <div><h3>Pesan Masuk</h3><h2><?= $total_pesan ?></h2></div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pengirim</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Subjek</th>
                        <th>Tanggal Kirim</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($jumlah == 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color:#aaa; padding:32px;">
                        <i class="fa-solid fa-inbox" style="font-size:24px; margin-bottom:8px; display:block;"></i>
                        Belum ada pesan masuk.
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach($rows as $row): ?>
                <tr>
                    <td><?= $row['id_contact'] ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_pengirim']) ?></strong></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                    <td><?= htmlspecialchars($row['subjek']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_kirim'])) ?></td>
                    <td>
                        <!-- Detail -->
                        <button class="detail-btn"
                            onclick="bukaDetail(
                                '<?= $row['id_contact'] ?>',
                                '<?= htmlspecialchars($row['nama_pengirim'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['no_hp'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['subjek'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['isi_pesan'], ENT_QUOTES) ?>',
                                '<?= date('d M Y, H:i', strtotime($row['tanggal_kirim'])) ?>'
                            )">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>

                        <!-- Hubungi -->
                        <button class="btn-wa"
                            onclick="bukaHubungi(
                                '<?= htmlspecialchars($row['nama_pengirim'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['no_hp'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>'
                            )">
                            <i class="fa-brands fa-whatsapp"></i> Hubungi
                        </button>

                        <!-- Hapus -->
                        <button class="btn-hapus" onclick="konfirmasiHapus('<?= $row['id_contact'] ?>')">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div><!-- end table-container -->

    </div><!-- end main-content -->
</div><!-- end container -->

<!-- ===========================
     MODAL DETAIL PESAN
=========================== -->
<div class="modal" id="modalDetail">
    <div class="modal-content">
        <span class="close" onclick="tutupModal('modalDetail')">&times;</span>
        <h2><i class="fa-solid fa-circle-info"></i> Detail Pesan</h2>

        <p class="section-title"><i class="fa-solid fa-user"></i> Data Pengirim</p>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Nama Pengirim</label>
                <p id="d_nama">—</p>
            </div>
            <div class="detail-item">
                <label>Tanggal Kirim</label>
                <p id="d_tanggal">—</p>
            </div>
            <div class="detail-item">
                <label>Email</label>
                <p id="d_email">—</p>
            </div>
            <div class="detail-item">
                <label>No. HP</label>
                <p id="d_hp">—</p>
            </div>
            <div class="detail-item full">
                <label>Subjek</label>
                <p id="d_subjek">—</p>
            </div>
        </div>

        <hr class="divider">

        <p class="section-title"><i class="fa-solid fa-comment-dots"></i> Isi Pesan</p>
        <div class="pesan-box">
            <p id="d_pesan">—</p>
        </div>
    </div>
</div>

<!-- ===========================
     MODAL HUBUNGI PELANGGAN
=========================== -->
<div class="modal" id="modalHubungi">
    <div class="modal-content">
        <span class="close" onclick="tutupModal('modalHubungi')">&times;</span>
        <h2><i class="fa-solid fa-phone"></i> Hubungi Pelanggan</h2>

        <p class="section-title"><i class="fa-solid fa-user"></i> Data Kontak</p>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Nama</label>
                <p id="h_nama">—</p>
            </div>
            <div class="detail-item">
                <label>No. HP</label>
                <p id="h_hp">—</p>
            </div>
            <div class="detail-item full">
                <label>Email</label>
                <p id="h_email">—</p>
            </div>
        </div>

        <div class="contact-actions">
            <button class="btn-copy" onclick="copyHP()">
                <i class="fa-solid fa-copy"></i> Copy No. HP
            </button>
            <a id="wa_link" href="#" target="_blank" class="btn-wa-big">
                <i class="fa-brands fa-whatsapp"></i> Buka WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- TOAST COPY -->
<div id="copyToast">✓ Nomor HP berhasil disalin!</div>

<script>
    let currentHP = '';

    // Buka modal detail
    function bukaDetail(id, nama, email, hp, subjek, pesan, tanggal){
        document.getElementById('d_nama').textContent    = nama;
        document.getElementById('d_email').textContent   = email;
        document.getElementById('d_hp').textContent      = hp;
        document.getElementById('d_subjek').textContent  = subjek;
        document.getElementById('d_pesan').textContent   = pesan;
        document.getElementById('d_tanggal').textContent = tanggal;
        document.getElementById('modalDetail').style.display = 'flex';
    }

    // Buka modal hubungi
    function bukaHubungi(nama, hp, email){
        currentHP = hp;
        document.getElementById('h_nama').textContent  = nama;
        document.getElementById('h_hp').textContent    = hp;
        document.getElementById('h_email').textContent = email;

        // Buat link WA — ubah 08xx → 628xx
        let noWA = hp.replace(/^0/, '62').replace(/\D/g,'');
        document.getElementById('wa_link').href =
            'https://wa.me/' + noWA + '?text=' +
            encodeURIComponent('Halo ' + nama + ', kami dari Gala Data ingin menindaklanjuti pesan Anda. Terima kasih.');

        document.getElementById('modalHubungi').style.display = 'flex';
    }

    // Tutup modal
    function tutupModal(id){
        document.getElementById(id).style.display = 'none';
    }

    // Copy nomor HP
    function copyHP(){
        navigator.clipboard.writeText(currentHP).then(() => {
            const toast = document.getElementById('copyToast');
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 2000);
        });
    }

    // Hapus dengan konfirmasi
    function konfirmasiHapus(id){
        if(confirm('Yakin ingin menghapus pesan ini? Tindakan tidak dapat dibatalkan.')){
            window.location = 'contact.php?hapus=' + id;
        }
    }

    // Tutup modal jika klik di luar
    window.onclick = function(e){
        if(e.target.classList.contains('modal')){
            e.target.style.display = 'none';
        }
    }

    // Escape key
    document.addEventListener('keydown', e => {
        if(e.key === 'Escape'){
            document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        }
    });
</script>

</body>
</html>
