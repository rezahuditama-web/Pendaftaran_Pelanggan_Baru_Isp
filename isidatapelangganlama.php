<?php
session_start();

// Kalau belum login, suruh login dulu
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: loginpelanggan.php");
    exit;
}

include 'koneksi.php';

$step   = $_POST['step'] ?? 1;
$errors = [];

// =====================
// PROSES STEP 1 - ALAMAT PEMASANGAN
// =====================
if ($step == 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat_pemasangan = trim($_POST['alamat_pemasangan'] ?? '');

    if (empty($alamat_pemasangan)) {
        $errors[] = "Alamat pemasangan wajib diisi.";
    }

    if (empty($errors)) {
        $_SESSION['alamat_pemasangan'] = $alamat_pemasangan;
        $step = 2;
    }
}

// =====================
// PROSES STEP 2 - PILIH PAKET & SIMPAN KE DATABASE
// =====================
$success = false;

if ($step == 2 && isset($_POST['jenis_paket']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_paket   = $_POST['jenis_paket']   ?? '';
    $paket_pilihan = trim($_POST['paket_pilihan'] ?? '');

    if (empty($jenis_paket)) {
        $errors[] = "Silakan pilih jenis paket.";
    }
    if (empty($paket_pilihan)) {
        $errors[] = "Silakan pilih paket berlangganan.";
    }

    if (empty($errors)) {
        $id_pelanggan      = (int) $_SESSION['id_pelanggan'];
        $alamat_pemasangan = mysqli_real_escape_string($koneksi, $_SESSION['alamat_pemasangan']);

        // Escape nama paket, trim spasi ekstra
        $nama_paket_esc = mysqli_real_escape_string($koneksi, $paket_pilihan);

        // Cari id_paket — TRIM di SQL untuk toleransi spasi
        $res_paket = mysqli_query($koneksi,
            "SELECT id_paket FROM paket WHERE TRIM(nama_paket) = TRIM('$nama_paket_esc') LIMIT 1"
        );
        $row_paket = $res_paket ? mysqli_fetch_assoc($res_paket) : null;

        if (!$row_paket) {
            $errors[] = "Paket \"$paket_pilihan\" tidak ditemukan. Hubungi admin.";
        } else {
            $id_paket = (int) $row_paket['id_paket'];

            // INSERT — 1 pelanggan boleh daftar berkali-kali (tidak ada cek duplikat)
            $q = mysqli_query($koneksi, "
                INSERT INTO pendaftaran_pemasangan
                    (id_pelanggan, id_paket, alamat_pemasangan, status_verifikasi, tanggal_pengajuan)
                VALUES
                    ('$id_pelanggan', '$id_paket', '$alamat_pemasangan', 'pending', CURDATE())
            ");

            if ($q) {
                $success = true;
                unset($_SESSION['alamat_pemasangan']); // bersihkan session alamat saja
            } else {
                $errors[] = "Gagal menyimpan: " . mysqli_error($koneksi);
            }
        }
    }

    if (!empty($errors)) {
        $step = 2;
    }
}

// =====================
// AMBIL DATA PAKET LANGSUNG DARI DATABASE
// =====================
$res_basic   = mysqli_query($koneksi, "SELECT * FROM paket WHERE jenis_paket='Rumah'  ORDER BY harga ASC");
$res_premium = mysqli_query($koneksi, "SELECT * FROM paket WHERE jenis_paket='Bisnis' ORDER BY harga ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Tambah Lokasi Pemasangan</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
body {
    font-family:'Nunito',sans-serif;
    background:#0d1b2e;
    min-height:100vh;
    display:flex; align-items:center; justify-content:center;
    padding:24px;
}
.outer {
    background:linear-gradient(145deg,#0f2544 0%,#0a1628 100%);
    border-radius:20px; padding:32px; width:100%; max-width:860px;
    box-shadow:0 30px 80px rgba(0,0,0,.6);
}
.card {
    background:#f4f6fb; border-radius:16px;
    padding:36px 40px 32px; position:relative;
}
.btn-back {
    position:absolute; top:28px; left:28px;
    background:none; border:none; cursor:pointer;
    color:#1a73e8; font-size:22px;
}
.logo-wrap { display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:28px; }
.logo-text strong { display:block; font-size:22px; font-weight:800; color:#1a73e8; }
.logo-text span   { font-size:10px; color:#888; }

/* STEPS */
.steps { display:flex; align-items:center; justify-content:center; margin-bottom:8px; }
.step-wrap { display:flex; flex-direction:column; align-items:center; }
.step-circle { width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; }
.step-circle.active    { background:#1a73e8; color:#fff; }
.step-circle.done-blue { background:#1a73e8; color:#fff; }
.step-circle.pending   { background:#fff; color:#aab4c8; border:3px solid #d0d9ec; }
.step-line { flex:1; height:3px; max-width:140px; }
.active-line   { background:#1a73e8; }
.inactive-line { background:#d0d9ec; }
.step-label { font-size:12px; font-weight:700; color:#1a73e8; }

/* ALERT */
.alert { border-radius:10px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:20px; }
.alert-error   { background:#fde8e8; color:#c0392b; }
.alert-success { background:#e8f5e9; color:#2e7d32; font-size:15px; text-align:center; padding:24px; }

/* FORM */
label { display:block; font-size:13px; font-weight:700; color:#1a73e8; margin-bottom:6px; }
textarea {
    width:100%; background:#fff; border:1.5px solid #dde3f0;
    border-radius:10px; padding:12px 14px;
    font-family:'Nunito',sans-serif; font-size:14px; min-height:120px;
}
textarea:focus { border-color:#1a73e8; outline:none; }
.form-footer { display:flex; justify-content:space-between; align-items:center; margin-top:24px; }
.note { font-size:12px; color:#999; }
.btn-lanjut {
    background:#1a73e8; color:#fff; font-size:15px; font-weight:800;
    padding:12px 36px; border:none; border-radius:12px; cursor:pointer; transition:opacity .2s;
}
.btn-lanjut:hover { opacity:.9; }

/* PAKET */
.paket-buttons { display:flex; gap:20px; justify-content:center; margin:24px 0; flex-wrap:wrap; }
.btn-paket { padding:16px 40px; border-radius:12px; font-size:16px; font-weight:800; cursor:pointer; border:2px solid #1a73e8; transition:.2s; font-family:'Nunito',sans-serif; }
.selected     { background:#1a73e8; color:white; }
.not-selected { background:white; color:#1a73e8; }
.paket-list { display:none; margin-top:20px; }
.paket-list.show { display:block; }
.paket-item {
    display:flex; justify-content:space-between; align-items:center;
    padding:14px 12px; border-bottom:1px solid #dde3f0;
    border-radius:8px; transition:background .15s;
}
.paket-item:hover { background:#eef5ff; }
.paket-item label { cursor:pointer; font-size:14px; font-weight:600; color:#333; display:flex; align-items:center; gap:10px; }
.paket-item label input[type=radio] { accent-color:#1a73e8; width:16px; height:16px; cursor:pointer; }
.harga { font-weight:800; color:#1a73e8; font-size:14px; }

/* INFO PELANGGAN */
.info-pelanggan {
    background:#eef5ff; border:1px solid #c5d9f7;
    border-radius:10px; padding:12px 18px;
    font-size:14px; margin-bottom:20px; color:#333;
}
.info-pelanggan strong { color:#1a73e8; }
</style>
</head>
<body>
<div class="outer">
<div class="card">

<?php if ($step == 2 && !$success): ?>
    <form method="POST" style="display:inline">
        <input type="hidden" name="step" value="1">
        <button type="submit" class="btn-back">&#8592;</button>
    </form>
<?php else: ?>
    <button class="btn-back" onclick="history.back()">&#8592;</button>
<?php endif; ?>

<!-- LOGO -->
<div class="logo-wrap">
    <div class="logo-text">
        <strong>GALA DATA</strong>
        <span>Best Solution Fast Internet</span>
    </div>
</div>

<!-- STEP INDICATOR -->
<div class="steps">
    <div class="step-wrap">
        <div class="step-circle <?= ($step == 1) ? 'active' : 'done-blue' ?>">&#128205;</div>
    </div>
    <div class="step-line <?= ($step == 2) ? 'active-line' : 'inactive-line' ?>"></div>
    <div class="step-wrap">
        <div class="step-circle <?= ($step == 2) ? 'active' : 'pending' ?>">&#128246;</div>
    </div>
</div>
<div style="display:flex;justify-content:center;margin-bottom:20px;gap:120px;">
    <span class="step-label">Alamat</span>
    <span class="step-label">Pilih Paket</span>
</div>

<!-- INFO PELANGGAN -->
<div class="info-pelanggan">
    Halo, <strong><?= htmlspecialchars($_SESSION['nama_pelanggan']) ?></strong>!
    Silakan lengkapi data pemasangan baru Anda.
</div>

<?php if ($success): ?>
<!-- ===== SUKSES ===== -->
<div class="alert alert-success">
    &#10003; Pendaftaran pemasangan berhasil!<br>
    <small style="font-weight:500">Status: <strong>Pending</strong> — Admin akan memverifikasi dan menghubungi Anda segera.</small>
</div>
<div style="display:flex;gap:12px;justify-content:center;margin-top:20px;flex-wrap:wrap;">
    <!-- <a href="isidatapelangganlama.php" class="btn-lanjut" style="text-decoration:none;background:#16a34a;">
        + Daftar Pemasangan Lagi
    </a> -->
    <a href="index.php" class="btn-lanjut" style="text-decoration:none;" >
        Kembali ke Homepage
    </a>
</div>

<?php else: ?>

<!-- ERROR -->
<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul style="padding-left:16px">
        <?php foreach($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if ($step == 1): ?>
<!-- ===== STEP 1: ALAMAT ===== -->
<form method="POST">
    <input type="hidden" name="step" value="1">
    <div>
        <label>Alamat Pemasangan Baru *</label>
        <textarea name="alamat_pemasangan"
            placeholder="Contoh: Jl. Mawar No.5, RT 02/RW 03, Banyuwangi"
        ><?= htmlspecialchars($_POST['alamat_pemasangan'] ?? '') ?></textarea>
    </div>
    <div class="form-footer">
        <span class="note">* Wajib diisi</span>
        <button type="submit" class="btn-lanjut">Lanjut &rarr;</button>
    </div>
</form>

<?php elseif ($step == 2): ?>
<!-- ===== STEP 2: PILIH PAKET ===== -->
<form method="POST">
    <input type="hidden" name="step" value="2">
    <input type="hidden" name="jenis_paket" id="jenis_paket"
           value="<?= htmlspecialchars($_POST['jenis_paket'] ?? '') ?>">

    <p style="text-align:center;font-weight:700;color:#555;margin-bottom:4px;font-size:14px;">
        Pilih Jenis Paket
    </p>

    <div class="paket-buttons">
        <button type="button"
            class="btn-paket <?= (($_POST['jenis_paket'] ?? '') === 'basic') ? 'selected' : 'not-selected' ?>"
            onclick="pilihJenis('basic', this)">
            Wifi Basic Home
        </button>
        <button type="button"
            class="btn-paket <?= (($_POST['jenis_paket'] ?? '') === 'premium') ? 'selected' : 'not-selected' ?>"
            onclick="pilihJenis('premium', this)">
            Wifi Premium Bisnis
        </button>
    </div>

    <!-- LIST PAKET RUMAH (Basic) — diambil dari database -->
    <div class="paket-list <?= (($_POST['jenis_paket'] ?? '') === 'basic') ? 'show' : '' ?>" id="list-basic">
        <?php
        mysqli_data_seek($res_basic, 0);
        while ($pk = mysqli_fetch_assoc($res_basic)):
        ?>
        <div class="paket-item">
            <label>
                <input type="radio" name="paket_pilihan"
                    value="<?= htmlspecialchars($pk['nama_paket']) ?>"
                    <?= (($_POST['paket_pilihan'] ?? '') === $pk['nama_paket']) ? 'checked' : '' ?>>
                <?= htmlspecialchars($pk['nama_paket']) ?>
                <small style="color:#888;font-weight:500">(<?= $pk['kecepatan'] ?>)</small>
            </label>
            <span class="harga">Rp <?= number_format($pk['harga'], 0, ',', '.') ?>,-</span>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- LIST PAKET BISNIS (Premium) — diambil dari database -->
    <div class="paket-list <?= (($_POST['jenis_paket'] ?? '') === 'premium') ? 'show' : '' ?>" id="list-premium">
        <?php
        mysqli_data_seek($res_premium, 0);
        while ($pk = mysqli_fetch_assoc($res_premium)):
        ?>
        <div class="paket-item">
            <label>
                <input type="radio" name="paket_pilihan"
                    value="<?= htmlspecialchars($pk['nama_paket']) ?>"
                    <?= (($_POST['paket_pilihan'] ?? '') === $pk['nama_paket']) ? 'checked' : '' ?>>
                <?= htmlspecialchars($pk['nama_paket']) ?>
                <small style="color:#888;font-weight:500">(<?= $pk['kecepatan'] ?>)</small>
            </label>
            <span class="harga">Rp <?= number_format($pk['harga'], 0, ',', '.') ?>,-</span>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="form-footer">
        <span class="note">* Wajib pilih paket</span>
        <button type="submit" class="btn-lanjut">Selesai &#10003;</button>
    </div>
</form>
<?php endif; ?>
<?php endif; ?>

</div>
</div>

<script>
function pilihJenis(jenis, el) {
    document.getElementById('jenis_paket').value = jenis;
    document.querySelectorAll('.btn-paket').forEach(b => {
        b.classList.remove('selected');
        b.classList.add('not-selected');
    });
    el.classList.add('selected');
    el.classList.remove('not-selected');
    document.getElementById('list-basic').classList.remove('show');
    document.getElementById('list-premium').classList.remove('show');
    document.getElementById('list-' + jenis).classList.add('show');
}
</script>
</body>
</html>
