<?php
session_start();
include "koneksi.php";

$step = $_POST['step'] ?? 1;
$errors = [];
$success = false;

// ==========================================
// AMBIL DATA PAKET DARI DATABASE
// ==========================================

$query_paket = mysqli_query($koneksi, "
    SELECT * FROM paket
    ORDER BY jenis_paket ASC, harga ASC
");

$paket_basic = [];
$paket_premium = [];

while ($row = mysqli_fetch_assoc($query_paket)) {

    if ($row['jenis_paket'] == 'basic') {

        $paket_basic[] = $row;

    } else if ($row['jenis_paket'] == 'premium') {

        $paket_premium[] = $row;
    }
}

// ==========================================
// STEP 1 - DATA DIRI
// ==========================================

if ($step == 1 && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama      = trim($_POST['nama'] ?? '');
    $nik       = trim($_POST['nik'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $telepon   = trim($_POST['telepon'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    if (empty($nama)) {
        $errors[] = "Nama lengkap wajib diisi.";
    }

    if (empty($nik) || !preg_match('/^\d{16}$/', $nik)) {
        $errors[] = "NIK harus 16 digit.";
    }

    if (empty($alamat)) {
        $errors[] = "Alamat wajib diisi.";
    }

    if (empty($telepon) || !preg_match('/^\d{10,13}$/', $telepon)) {
        $errors[] = "Nomor HP harus 10-13 digit.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    if (!isset($_FILES['foto_ktp']) || $_FILES['foto_ktp']['error'] != 0) {
        $errors[] = "Foto KTP wajib diupload.";
    }

    // ==========================================
    // CEK NIK SUDAH ADA / BELUM
    // ==========================================

    $cek_nik = mysqli_query($koneksi, "
        SELECT * FROM pelanggan
        WHERE no_nik='$nik'
    ");

    if (mysqli_num_rows($cek_nik) > 0) {
        $errors[] = "NIK sudah terdaftar.";
    }

    // ==========================================
    // JIKA TIDAK ADA ERROR
    // ==========================================

    if (empty($errors)) {

        $folder = "upload/";

        if (!is_dir($folder)) {
            mkdir($folder);
        }

        $nama_file = time() . "_" . $_FILES['foto_ktp']['name'];

        $tmp = $_FILES['foto_ktp']['tmp_name'];

        $path = $folder . $nama_file;

        move_uploaded_file($tmp, $path);

        $_SESSION['data_diri'] = [

            'nama'       => $nama,
            'nik'        => $nik,
            'alamat'     => $alamat,
            'telepon'    => $telepon,
            'password'   => $password,
            'foto_ktp'   => $path
        ];

        $step = 2;
    }
}

// ==========================================
// STEP 2 - PILIH PAKET
// ==========================================

if ($step == 2 && isset($_POST['id_paket'])) {

    $id_paket = $_POST['id_paket'];

    if (empty($id_paket)) {

        $errors[] = "Pilih paket internet.";
    }

    if (empty($errors)) {

        $data = $_SESSION['data_diri'];

        $nama       = $data['nama'];
        $nik        = $data['nik'];
        $alamat     = $data['alamat'];
        $telepon    = $data['telepon'];
        $password   = $data['password'];
        $foto_ktp   = $data['foto_ktp'];

        // ==========================================
        // INSERT KE TABEL PELANGGAN
        // ==========================================

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

        // AMBIL ID PELANGGAN TERBARU
        $id_pelanggan = mysqli_insert_id($koneksi);

        // ==========================================
        // INSERT KE TABEL PENDAFTARAN
        // ==========================================

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

        if ($query1 && $query2) {

            $success = true;

            session_destroy();

        } else {

            $errors[] = "Data gagal disimpan : " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Daftar Pelanggan – Gala Data</title>

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
            letter-spacing:1.5px;
            text-transform:uppercase;
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

        .step-circle.pending{
            background:#fff;
            color:#aab4c8;
            border:2px solid #d0d9ec;
        }

        .step-line{
            flex:1;
            height:3px;
            max-width:140px;
            background:#d0d9ec;
        }

        .step-line.active-line{
            background:#1a73e8;
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
            grid-template-columns:1fr 1fr;
            gap:18px 24px;
        }

        .full{
            grid-column:1 / -1;
        }

        label{
            display:block;
            font-size:13px;
            font-weight:700;
            color:#1a73e8;
            margin-bottom:6px;
        }

        input[type="text"],
        input[type="password"],
        input[type="tel"],
        textarea{
            width:100%;
            background:#fff;
            border:1.5px solid #dde3f0;
            border-radius:10px;
            padding:12px 14px;
            font-size:14px;
            outline:none;
        }

        textarea{
            resize:vertical;
            min-height:90px;
        }

        .file-btn{
            display:inline-block;
            background:#1a73e8;
            color:#fff;
            padding:10px 24px;
            border-radius:10px;
            cursor:pointer;
        }

        input[type="file"]{
            display:none;
        }

        .file-name{
            display:inline-block;
            margin-left:10px;
            font-size:12px;
            color:#666;
        }

        .paket-buttons{
            display:flex;
            gap:20px;
            justify-content:center;
            margin:24px 0 8px;
            flex-wrap:wrap;
        }

        .btn-paket{
            padding:16px 40px;
            border-radius:12px;
            font-size:16px;
            font-weight:800;
            cursor:pointer;
            border:2px solid #1a73e8;
            background:#fff;
            color:#1a73e8;
        }

        .btn-paket.active{
            background:#1a73e8;
            color:#fff;
        }

        .paket-list{
            margin-top:24px;
            display:none;
        }

        .paket-list.show{
            display:block;
        }

        .paket-item{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:10px 4px;
            border-bottom:1px solid #e8edf5;
        }

        .harga{
            font-weight:700;
        }

        .form-footer{
            display:flex;
            justify-content:space-between;
            margin-top:24px;
        }

        .btn-lanjut{
            background:#1a73e8;
            color:#fff;
            padding:12px 36px;
            border:none;
            border-radius:12px;
            cursor:pointer;
            font-weight:800;
        }

    </style>
</head>

<body>

<div class="outer">
<div class="card">

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

<?php if(!$success): ?>

<?php if($step == 1): ?>

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
Upload
</label>

<input
type="file"
id="foto_ktp"
name="foto_ktp"
accept="image/*"
onchange="document.getElementById('nama-file').textContent=this.files[0]?.name || ''"
>

<span class="file-name" id="nama-file">
Belum ada file
</span>

</div>

<div class="full">

<label>Alamat *</label>

<textarea name="alamat"></textarea>

</div>

<div class="full">

<label>No HP *</label>

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

<?php if($step == 2): ?>

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