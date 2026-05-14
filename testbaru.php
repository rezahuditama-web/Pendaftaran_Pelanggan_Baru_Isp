<?php
session_start();
include "koneksi.php";

$step = $_POST['step'] ?? 1;
$errors = [];

/* =========================
   STEP 1 - DATA DIRI
========================= */

if ($step == 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama      = trim($_POST['nama'] ?? '');
    $nik       = trim($_POST['nik'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telepon   = trim($_POST['telepon'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    if (empty($nama)) {
        $errors[] = "Nama lengkap wajib diisi.";
    }

    if (empty($nik) || !preg_match('/^\d{16}$/', $nik)) {
        $errors[] = "NIK harus 16 digit angka.";
    }

    if (empty($alamat)) {
        $errors[] = "Alamat wajib diisi.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }

    if (empty($telepon) || !preg_match('/^\d{10,13}$/', $telepon)) {
        $errors[] = "Nomor telepon harus 10-13 digit.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    if (!isset($_FILES['foto_ktp']) || $_FILES['foto_ktp']['error'] != 0) {
        $errors[] = "Foto KTP wajib diupload.";
    }

    if (empty($errors)) {

        $folderUpload = "upload/";

        if (!is_dir($folderUpload)) {
            mkdir($folderUpload);
        }

        $namaFile = time() . "_" . $_FILES['foto_ktp']['name'];
        $tmpFile  = $_FILES['foto_ktp']['tmp_name'];

        $pathUpload = $folderUpload . $namaFile;

        move_uploaded_file($tmpFile, $pathUpload);

        $_SESSION['data_diri'] = [
            'nama'      => $nama,
            'nik'       => $nik,
            'alamat'    => $alamat,
            'email'     => $email,
            'telepon'   => $telepon,
            'password'  => $password,
            'foto_ktp'  => $pathUpload
        ];

        $step = 2;
    }
}

/* =========================
   STEP 2 - PILIH PAKET
========================= */

$success = false;

if ($step == 2 && isset($_POST['jenis_paket'])) {

    $jenis_paket   = $_POST['jenis_paket'] ?? '';
    $paket_pilihan = $_POST['paket_pilihan'] ?? '';

    if (empty($jenis_paket)) {
        $errors[] = "Pilih jenis paket.";
    }

    if (empty($paket_pilihan)) {
        $errors[] = "Pilih paket internet.";
    }

    if (empty($errors)) {

        $data = $_SESSION['data_diri'];

        $nama      = $data['nama'];
        $nik       = $data['nik'];
        $alamat    = $data['alamat'];
        $email     = $data['email'];
        $telepon   = $data['telepon'];
        $password  = $data['password'];
        $foto_ktp  = $data['foto_ktp'];

        $paket = $jenis_paket . " - " . $paket_pilihan;

        mysqli_query($koneksi,

        "INSERT INTO pelanggan
        (
            nama_pelanggan,
            nik,
            alamat,
            email,
            telepon,
            password,
            foto_ktp,
            paket
        )

        VALUES
        (
            '$nama',
            '$nik',
            '$alamat',
            '$email',
            '$telepon',
            '$password',
            '$foto_ktp',
            '$paket'
        )"

        );

        $success = true;

        session_destroy();
    }
}

/* =========================
   DATA PAKET
========================= */

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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Daftar Pelanggan - Gala Data</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Nunito',sans-serif;
    background:#0d1b2e;
    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    padding:24px;
}

.outer{
    width:100%;
    max-width:900px;

    background:white;

    border-radius:20px;

    padding:40px;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.full{
    grid-column:1 / -1;
}

label{
    display:block;
    margin-bottom:8px;

    color:#1a73e8;
    font-weight:700;
}

input,
textarea{
    width:100%;

    padding:12px;

    border:1px solid #ccc;
    border-radius:10px;

    outline:none;
}

textarea{
    resize:none;
    height:100px;
}

input:focus,
textarea:focus{
    border-color:#1a73e8;
}

.file-btn{
    display:inline-block;

    background:#1a73e8;
    color:white;

    padding:12px 20px;

    border-radius:10px;

    cursor:pointer;

    font-weight:700;
}

input[type="file"]{
    display:none;
}

.file-name{
    display:block;
    margin-top:10px;

    font-size:13px;
    color:#666;
}

.btn-lanjut{
    margin-top:30px;

    background:#1a73e8;
    color:white;

    border:none;
    border-radius:10px;

    padding:14px 30px;

    font-size:16px;
    font-weight:700;

    cursor:pointer;
}

.btn-lanjut:hover{
    background:#0d5bc7;
}

.alert{
    margin-bottom:20px;

    background:#fde8e8;
    color:#c0392b;

    padding:15px;

    border-radius:10px;
}

.success{
    background:#e8f5e9;
    color:#2e7d32;
}

.paket-buttons{
    display:flex;
    gap:20px;

    justify-content:center;

    margin-top:30px;
}

.btn-paket{
    padding:16px 30px;

    border:2px solid #1a73e8;
    border-radius:12px;

    background:white;
    color:#1a73e8;

    cursor:pointer;

    font-weight:700;
}

.btn-paket.selected{
    background:#1a73e8;
    color:white;
}

.paket-list{
    margin-top:30px;
}

.paket-item{
    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:12px 0;

    border-bottom:1px solid #ddd;
}

</style>
</head>

<body>

<div class="outer">

<?php if ($success): ?>

<div class="alert success">
    Pendaftaran berhasil dan data sudah masuk database.
</div>

<?php else: ?>

<?php if (!empty($errors)): ?>

<div class="alert">
    <ul>
        <?php foreach($errors as $e): ?>
            <li><?= $e ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<?php endif; ?>

<?php if ($step == 1): ?>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="step" value="1">

<div class="form-grid">

    <div>
        <label>Nama Lengkap *</label>

        <input 
            type="text"
            name="nama"
            placeholder="Nama Lengkap"
            required
        >
    </div>

    <div>
        <label>NIK *</label>

        <input 
            type="text"
            name="nik"
            maxlength="16"
            placeholder="16 digit NIK"
            required
        >
    </div>

    <div class="full">
        <label>Alamat *</label>

        <textarea 
            name="alamat"
            placeholder="Alamat lengkap"
            required
        ></textarea>
    </div>

    <div>
        <label>Email *</label>

        <input 
            type="email"
            name="email"
            placeholder="Email"
            required
        >
    </div>

    <div>
        <label>No Telephone *</label>

        <input 
            type="text"
            name="telepon"
            placeholder="No Telephone"
            required
        >
    </div>

    <div>
        <label>Password *</label>

        <input 
            type="password"
            name="password"
            placeholder="Buat Password"
            required
        >
    </div>

    <div>
        <label>Foto KTP *</label>

        <label for="foto_ktp" class="file-btn">
            Kirim Gambar
        </label>

        <input 
            type="file"
            id="foto_ktp"
            name="foto_ktp"
            accept="image/*"
            onchange="document.getElementById('nama-file').textContent = this.files[0]?.name || ''"
        >

        <span class="file-name" id="nama-file">
            Belum ada file dipilih
        </span>
    </div>

</div>

<button type="submit" class="btn-lanjut">
    Lanjut
</button>

</form>

<?php elseif ($step == 2): ?>

<form method="POST">

<input type="hidden" name="step" value="2">

<input type="hidden" name="jenis_paket" id="jenis_paket">

<div class="paket-buttons">

<button 
    type="button"
    class="btn-paket"
    onclick="pilihJenis('basic',this)"
>
    Wifi Basic Home
</button>

<button 
    type="button"
    class="btn-paket"
    onclick="pilihJenis('premium',this)"
>
    Wifi Premium Bisnis
</button>

</div>

<div class="paket-list" id="list-basic" style="display:none;">

<?php foreach($paket_basic as $nama => $harga): ?>

<div class="paket-item">

<label>
<input type="radio" name="paket_pilihan" value="<?= $nama ?>">
<?= $nama ?>
</label>

<span><?= $harga ?></span>

</div>

<?php endforeach; ?>

</div>

<div class="paket-list" id="list-premium" style="display:none;">

<?php foreach($paket_premium as $nama => $harga): ?>

<div class="paket-item">

<label>
<input type="radio" name="paket_pilihan" value="<?= $nama ?>">
<?= $nama ?>
</label>

<span><?= $harga ?></span>

</div>

<?php endforeach; ?>

</div>

<button type="submit" class="btn-lanjut">
    Daftar Sekarang
</button>

</form>

<?php endif; ?>

<?php endif; ?>

</div>

<script>

function pilihJenis(jenis,el){

    document.getElementById('jenis_paket').value = jenis;

    document.querySelectorAll('.btn-paket').forEach(btn=>{
        btn.classList.remove('selected');
    });

    el.classList.add('selected');

    document.getElementById('list-basic').style.display='none';
    document.getElementById('list-premium').style.display='none';

    document.getElementById('list-'+jenis).style.display='block';
}

</script>

</body>
</html>