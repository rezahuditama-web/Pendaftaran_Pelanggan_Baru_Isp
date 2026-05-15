<?php
session_start();
include "koneksi.php";

$step = $_POST['step'] ?? 1;
$errors = [];
$success = false;

// =====================================
// AMBIL DATA PAKET DARI DATABASE
// =====================================

$query_paket = mysqli_query($koneksi, "
    SELECT * FROM paket
    ORDER BY jenis_paket ASC, harga ASC
");

$paket_basic = [];
$paket_premium = [];

while($row = mysqli_fetch_assoc($query_paket)){

    if($row['jenis_paket'] == 'basic'){

        $paket_basic[] = $row;

    }else if($row['jenis_paket'] == 'premium'){

        $paket_premium[] = $row;
    }
}

// =====================================
// STEP 1
// =====================================

if($step == 1 && $_SERVER['REQUEST_METHOD'] == 'POST'){

    $nama      = trim($_POST['nama'] ?? '');
    $nik       = trim($_POST['nik'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $telepon   = trim($_POST['telepon'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    if(empty($nama)){
        $errors[] = "Nama lengkap wajib diisi.";
    }

    if(empty($nik) || !preg_match('/^\d{16}$/', $nik)){
        $errors[] = "NIK harus 16 digit.";
    }

    if(empty($alamat)){
        $errors[] = "Alamat wajib diisi.";
    }

    if(empty($telepon) || !preg_match('/^\d{10,13}$/', $telepon)){
        $errors[] = "Nomor HP harus 10-13 digit.";
    }

    if(strlen($password) < 6){
        $errors[] = "Password minimal 6 karakter.";
    }

    if(!isset($_FILES['foto_ktp']) || $_FILES['foto_ktp']['error'] != 0){
        $errors[] = "Foto KTP wajib diupload.";
    }

    // =====================================
    // CEK NIK DUPLIKAT
    // =====================================

    $cek_nik = mysqli_query($koneksi, "
        SELECT * FROM pelanggan
        WHERE no_nik='$nik'
    ");

    if(mysqli_num_rows($cek_nik) > 0){
        $errors[] = "NIK sudah terdaftar.";
    }

    // =====================================
    // JIKA TIDAK ADA ERROR
    // =====================================

    if(empty($errors)){

        $folder = "upload/";

        if(!is_dir($folder)){
            mkdir($folder);
        }

        $nama_file = time() . "_" . $_FILES['foto_ktp']['name'];

        $tmp = $_FILES['foto_ktp']['tmp_name'];

        $path = $folder . $nama_file;

        move_uploaded_file($tmp, $path);

        $_SESSION['data_diri'] = [

            'nama'      => $nama,
            'nik'       => $nik,
            'alamat'    => $alamat,
            'telepon'   => $telepon,
            'password'  => $password,
            'foto_ktp'  => $path
        ];

        $step = 2;
    }
}

// =====================================
// STEP 2
// =====================================

if($step == 2 && isset($_POST['id_paket'])){

    $id_paket = $_POST['id_paket'];

    if(empty($id_paket)){
        $errors[] = "Pilih paket internet.";
    }

    if(empty($errors)){

        $data = $_SESSION['data_diri'];

        $nama       = $data['nama'];
        $nik        = $data['nik'];
        $alamat     = $data['alamat'];
        $telepon    = $data['telepon'];
        $password   = $data['password'];
        $foto_ktp   = $data['foto_ktp'];

        // =====================================
        // INSERT KE TABEL PELANGGAN
        // =====================================

        $query1 = mysqli_query($koneksi, "

            INSERT INTO pelanggan
            (
                no_nik,
                nama_pelanggan,
                no_hp,
                alamat_domisili,
                foto_ktp,
                password
            )

            VALUES
            (
                '$nik',
                '$nama',
                '$telepon',
                '$alamat',
                '$foto_ktp',
                '$password'
            )

        ");

        $id_pelanggan = mysqli_insert_id($koneksi);

        // =====================================
        // INSERT KE TABEL PENDAFTARAN
        // =====================================

        $query2 = mysqli_query($koneksi, "

            INSERT INTO pendaftaran_pemasangan
            (
                id_pelanggan,
                id_admin,
                id_paket,
                status_verifikasi,
                tanggal_pengajuan
            )

            VALUES
            (
                '$id_pelanggan',
                NULL,
                '$id_paket',
                'pending',
                CURDATE()
            )

        ");

        if($query1 && $query2){

            $success = true;

            session_destroy();

        }else{

            $errors[] = "Data gagal disimpan : " . mysqli_error($koneksi);
        }
    }
}
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
    background:#e9eaee;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.outer{

    width:100%;
    max-width:900px;
}

.card{

    background:#f5f5f7;
    border-radius:18px;
    padding:40px;
    position:relative;
}

/* ======================================
LOGO
====================================== */

.logo-wrap{

    display:flex;
    align-items:center;
    justify-content:center;
    gap:14px;
    margin-bottom:35px;
}

.logo-icon{

    width:54px;
    height:54px;
    border-radius:14px;
    background:#dce8ff;

    display:flex;
    justify-content:center;
    align-items:center;
}

.logo-text strong{

    display:block;
    font-size:20px;
    color:#1a73e8;
    font-weight:800;
}

.logo-text span{

    font-size:11px;
    letter-spacing:2px;
    color:#888;
}

/* ======================================
STEP
====================================== */

.steps{

    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:10px;
}

.step-wrap{

    display:flex;
    flex-direction:column;
    align-items:center;
}

.step-circle{

    width:56px;
    height:56px;
    border-radius:50%;

    display:flex;
    justify-content:center;
    align-items:center;

    font-size:24px;
    font-weight:bold;
}

.step-circle.active{

    background:#1a73e8;
    color:#fff;

    box-shadow:0 4px 15px rgba(26,115,232,.4);
}

.step-circle.pending{

    background:#f5f5f7;
    color:#b0b7c6;

    border:4px solid #cfd7e7;
}

.step-line{

    width:180px;
    height:4px;
    background:#cfd7e7;
}

.step-line.active-line{

    background:#1a73e8;
}

.steps-label{

    display:flex;
    justify-content:flex-start;
    margin-left:5px;
    margin-bottom:30px;
}

.steps-label span{

    font-size:13px;
    font-weight:700;
    color:#1a73e8;
}

/* ======================================
ALERT
====================================== */

.alert{

    padding:14px 18px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
}

.alert-error{

    background:#ffdede;
    color:#c0392b;
}

.alert-success{

    background:#dff5e3;
    color:#2e7d32;
}

/* ======================================
FORM
====================================== */

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
    font-size:13px;
    font-weight:700;
}

input[type="text"],
input[type="password"],
input[type="tel"],
textarea{

    width:100%;
    padding:13px 14px;

    border-radius:10px;
    border:1px solid #d7dce7;

    outline:none;
    font-family:'Nunito',sans-serif;
}

textarea{

    min-height:90px;
    resize:vertical;
}

.file-btn{

    background:#1a73e8;
    color:#fff;

    padding:10px 20px;

    border-radius:10px;
    display:inline-block;
    cursor:pointer;
}

input[type="file"]{

    display:none;
}

.file-name{

    margin-left:10px;
    font-size:12px;
    color:#777;
}

/* ======================================
PAKET
====================================== */

.paket-buttons{

    display:flex;
    justify-content:center;
    gap:20px;
    margin-bottom:20px;
}

.btn-paket{

    padding:15px 35px;

    border-radius:12px;
    border:2px solid #1a73e8;

    background:#fff;
    color:#1a73e8;

    cursor:pointer;
    font-weight:800;
}

.paket-list{

    display:none;
}

.paket-list.show{

    display:block;
}

.paket-item{

    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:14px 0;

    border-bottom:1px solid #ddd;
}

.harga{

    font-weight:bold;
}

/* ======================================
BUTTON
====================================== */

.form-footer{

    margin-top:30px;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.btn-lanjut{

    background:#1a73e8;
    color:#fff;

    border:none;
    padding:12px 35px;

    border-radius:12px;

    cursor:pointer;
    font-weight:800;
}

@media(max-width:700px){

    .form-grid{

        grid-template-columns:1fr;
    }

    .step-line{

        width:70px;
    }
}

</style>

</head>

<body>

<div class="outer">

<div class="card">

<!-- LOGO -->

<div class="logo-wrap">

<div class="logo-icon">

🌐

</div>

<div class="logo-text">

<strong>GALA DATA</strong>
<span>BEST SOLUTION FAST INTERNET</span>

</div>

</div>

<!-- STEP -->

<div class="steps">

<div class="step-wrap">

<div class="step-circle <?= $step == 1 ? 'active' : 'pending'; ?>">

👤

</div>

</div>

<div class="step-line <?= $step == 2 ? 'active-line' : ''; ?>"></div>

<div class="step-wrap">

<div class="step-circle <?= $step == 2 ? 'active' : 'pending'; ?>">

🌐

</div>

</div>

<div class="step-line"></div>

<div class="step-wrap">

<div class="step-circle pending">

✓

</div>

</div>

</div>

<div class="steps-label">

<span>Data Diri</span>

</div>

<!-- ALERT -->

<?php if($success): ?>

<div class="alert alert-success">

Pendaftaran berhasil dilakukan.

</div>

<?php endif; ?>

<?php if(!empty($errors)): ?>

<div class="alert alert-error">

<ul>

<?php foreach($errors as $e): ?>

<li><?= $e; ?></li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>

<!-- STEP 1 -->

<?php if(!$success && $step == 1): ?>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="step" value="1">

<div class="form-grid">

<div>

<label>Nama Lengkap *</label>

<input type="text" name="nama">

</div>

<div>

<label>NIK *</label>

<input type="text" name="nik" maxlength="16">

</div>

<div>

<label>Password *</label>

<input type="password" name="password">

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
onchange="document.getElementById('nama-file').textContent=this.files[0]?.name || ''"
>

<span class="file-name" id="nama-file">

Belum ada file dipilih

</span>

</div>

<div class="full">

<label>Alamat *</label>

<textarea name="alamat"></textarea>

</div>

<div class="full">

<label>No Telephone *</label>

<input type="tel" name="telepon">

</div>

</div>

<div class="form-footer">

<span>* wajib diisi</span>

<button type="submit" class="btn-lanjut">

Lanjut

</button>

</div>

</form>

<?php endif; ?>

<!-- STEP 2 -->

<?php if(!$success && $step == 2): ?>

<form method="POST">

<input type="hidden" name="step" value="2">

<div class="paket-buttons">

<button
type="button"
class="btn-paket"
onclick="pilihJenis('basic')"
>

Wifi Basic Home

</button>

<button
type="button"
class="btn-paket"
onclick="pilihJenis('premium')"
>

Wifi Premium Bisnis

</button>

</div>

<!-- BASIC -->

<div class="paket-list" id="list-basic">

<?php foreach($paket_basic as $p): ?>

<div class="paket-item">

<label>

<input
type="radio"
name="id_paket"
value="<?= $p['id_paket']; ?>"
>

<?= $p['nama_paket']; ?>

</label>

<span class="harga">

Rp <?= number_format($p['harga']); ?>

</span>

</div>

<?php endforeach; ?>

</div>

<!-- PREMIUM -->

<div class="paket-list" id="list-premium">

<?php foreach($paket_premium as $p): ?>

<div class="paket-item">

<label>

<input
type="radio"
name="id_paket"
value="<?= $p['id_paket']; ?>"
>

<?= $p['nama_paket']; ?>

</label>

<span class="harga">

Rp <?= number_format($p['harga']); ?>

</span>

</div>

<?php endforeach; ?>

</div>

<div class="form-footer">

<span>* pilih paket</span>

<button type="submit" class="btn-lanjut">

Daftar

</button>

</div>

</form>

<?php endif; ?>

</div>
</div>

<script>

function pilihJenis(jenis){

    document.getElementById('list-basic').classList.remove('show');
    document.getElementById('list-premium').classList.remove('show');

    document.getElementById('list-' + jenis).classList.add('show');
}

</script>

</body>
</html>