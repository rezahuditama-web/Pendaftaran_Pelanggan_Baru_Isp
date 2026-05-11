<?php
session_start();

$step = $_POST['step'] ?? 1;
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
// PROSES STEP 2 - PILIH PAKET
// =====================
$success = false;

if ($step == 2 && isset($_POST['jenis_paket']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $jenis_paket   = $_POST['jenis_paket'] ?? '';
    $paket_pilihan = $_POST['paket_pilihan'] ?? '';

    if (empty($jenis_paket)) {
        $errors[] = "Silakan pilih jenis paket.";
    }

    if (empty($paket_pilihan)) {
        $errors[] = "Silakan pilih paket berlangganan.";
    }

    if (empty($errors)) {

        // SIMPAN DATABASE DISINI
        // contoh:
        // $alamat = $_SESSION['alamat_pemasangan'];

        $success = true;

        session_destroy();
    }
}

// =====================
// DATA PAKET
// =====================
$paket_basic = [
    "Regular Silver"   => "Rp. 99.000,-",
    "Regular Gold"     => "Rp. 129.000,-",
    "Regular Gamer"    => "Rp. 189.000,-",
    "Regular Platinum" => "Rp. 229.000,-",
];

$paket_premium = [
    "Premium Bronze"   => "Rp. 229.000,-",
    "Premium Gold"     => "Rp. 329.000,-",
    "Premium Bisnis"   => "Rp. 419.000,-",
    "Premium Platinum" => "Rp. 579.000,-",
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Tambah Lokasi Pemasangan</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>

<style>

*, *::before, *::after{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    font-family:'Nunito',sans-serif;
    background:#0d1b2e;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
}

.outer{
    background:linear-gradient(145deg,#0f2544 0%,#0a1628 100%);
    border-radius:20px;
    padding:32px;
    width:100%;
    max-width:860px;
    box-shadow:0 30px 80px rgba(0,0,0,.6);
}

.card{
    background:#f4f6fb;
    border-radius:16px;
    padding:36px 40px 32px;
    position:relative;
}

.btn-back{
    position:absolute;
    top:28px;
    left:28px;
    background:none;
    border:none;
    cursor:pointer;
    color:#1a73e8;
    font-size:22px;
}

.logo-wrap{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-bottom:28px;
}

.logo-text strong{
    display:block;
    font-size:22px;
    font-weight:800;
    color:#1a73e8;
}

.logo-text span{
    font-size:10px;
    color:#888;
}

.steps{
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:8px;
}

.step-wrap{
    display:flex;
    flex-direction:column;
    align-items:center;
}

.step-circle{
    width:52px;
    height:52px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    font-weight:700;
}

.step-circle.active{
    background:#1a73e8;
    color:#fff;
}

.step-circle.done-blue{
    background:#1a73e8;
    color:#fff;
}

.step-circle.pending{
    background:#fff;
    color:#aab4c8;
    border:3px solid #d0d9ec;
}

.step-line{
    flex:1;
    height:3px;
    max-width:140px;
}

.active-line{
    background:#1a73e8;
}

.inactive-line{
    background:#d0d9ec;
}

.step-label{
    font-size:12px;
    font-weight:700;
    color:#1a73e8;
}

.alert{
    border-radius:10px;
    padding:12px 16px;
    font-size:13px;
    font-weight:600;
    margin-bottom:20px;
}

.alert-error{
    background:#fde8e8;
    color:#c0392b;
}

.alert-success{
    background:#e8f5e9;
    color:#2e7d32;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:20px;
}

label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:#1a73e8;
    margin-bottom:6px;
}

textarea{
    width:100%;
    background:#fff;
    border:1.5px solid #dde3f0;
    border-radius:10px;
    padding:12px 14px;
    font-family:'Nunito',sans-serif;
    font-size:14px;
    min-height:120px;
}

textarea:focus{
    border-color:#1a73e8;
    outline:none;
}

.form-footer{
    display:flex;
    justify-content:space-between;
    margin-top:24px;
}

.note{
    font-size:12px;
    color:#999;
}

.btn-lanjut{
    background:#1a73e8;
    color:#fff;
    font-size:15px;
    font-weight:800;
    padding:12px 36px;
    border:none;
    border-radius:12px;
    cursor:pointer;
}

.paket-buttons{
    display:flex;
    gap:20px;
    justify-content:center;
    margin:24px 0;
    flex-wrap:wrap;
}

.btn-paket{
    padding:16px 40px;
    border-radius:12px;
    font-size:16px;
    font-weight:800;
    cursor:pointer;
    border:2px solid #1a73e8;
}

.selected{
    background:#1a73e8;
    color:white;
}

.not-selected{
    background:white;
    color:#1a73e8;
}

.paket-list{
    display:none;
    margin-top:20px;
}

.paket-list.show{
    display:block;
}

.paket-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 0;
    border-bottom:1px solid #ddd;
}

.harga{
    font-weight:700;
}

</style>
</head>

<body>

<div class="outer">
<div class="card">

<?php if ($step == 2 && !$success): ?>

<form method="POST">
    <input type="hidden" name="step" value="1">
    <button type="submit" class="btn-back">&#8592;</button>
</form>

<?php else: ?>

<button class="btn-back" onclick="history.back()">&#8592;</button>

<?php endif; ?>

<div class="logo-wrap">

<div class="logo-text">
<strong>GALA DATA</strong>
<span>Best Solution Fast Internet</span>
</div>

</div>

<div class="steps">

<div class="step-wrap">
<div class="step-circle <?= $step == 1 ? 'active' : 'done-blue' ?>">&#128205;</div>
</div>

<div class="step-line <?= $step == 2 ? 'active-line' : 'inactive-line' ?>"></div>

<div class="step-wrap">
<div class="step-circle <?= $step == 2 ? 'active' : 'pending' ?>">&#128246;</div>
</div>

</div>

<div style="display:flex; justify-content:center; margin-bottom:20px; gap:120px;">
<span class="step-label">Alamat</span>
<span class="step-label"><?= $step == 2 ? 'Pilih Paket' : '' ?></span>
</div>

<?php if ($success): ?>

<div class="alert alert-success">
&#10003;Pendaftaran berhasil! Kami akan segera menghubungi Anda
</div>

<div style="text-align:center; margin-top:20px;">

    <a href="index.php" class="btn-lanjut"
       style="text-decoration:none; display:inline-block;">
        Kembali ke Homepage
    </a>

</div>

<?php elseif (!empty($errors)): ?>

<div class="alert alert-error">
<ul>

<?php foreach($errors as $e): ?>

<li><?= htmlspecialchars($e) ?></li>

<?php endforeach; ?>

</ul>
</div>

<?php endif; ?>

<?php if (!$success): ?>

<?php if ($step == 1): ?>

<!-- STEP 1 -->

<form method="POST">

<input type="hidden" name="step" value="1">

<div class="form-grid">

<div>
<label>Alamat Pemasangan Baru *</label>

<textarea
name="alamat_pemasangan"
placeholder="Masukkan alamat pemasangan baru"><?= htmlspecialchars($_POST['alamat_pemasangan'] ?? '') ?></textarea>

</div>

</div>

<div class="form-footer">

<span class="note">* Wajib diisi</span>

<button type="submit" class="btn-lanjut">
Lanjut
</button>

</div>

</form>

<?php elseif ($step == 2): ?>

<!-- STEP 2 -->

<form method="POST">

<input type="hidden" name="step" value="2">

<input type="hidden"
name="jenis_paket"
id="jenis_paket"
value="<?= htmlspecialchars($_POST['jenis_paket'] ?? '') ?>">

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

<div class="paket-list <?= (($_POST['jenis_paket'] ?? '') === 'basic') ? 'show' : '' ?>" id="list-basic">

<?php foreach ($paket_basic as $nama => $harga): ?>

<div class="paket-item">

<label>
<input type="radio"
name="paket_pilihan"
value="<?= htmlspecialchars($nama) ?>">

<?= htmlspecialchars($nama) ?>

</label>

<span class="harga"><?= $harga ?></span>

</div>

<?php endforeach; ?>

</div>

<div class="paket-list <?= (($_POST['jenis_paket'] ?? '') === 'premium') ? 'show' : '' ?>" id="list-premium">

<?php foreach ($paket_premium as $nama => $harga): ?>

<div class="paket-item">

<label>
<input type="radio"
name="paket_pilihan"
value="<?= htmlspecialchars($nama) ?>">

<?= htmlspecialchars($nama) ?>

</label>

<span class="harga"><?= $harga ?></span>

</div>

<?php endforeach; ?>

</div>

<div class="form-footer">

<span class="note">* Wajib pilih</span>

<button type="submit" class="btn-lanjut">
Selesai
</button>

</div>

</form>

<?php endif; ?>

<?php endif; ?>

</div>
</div>

<script>

function pilihJenis(jenis, el){

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