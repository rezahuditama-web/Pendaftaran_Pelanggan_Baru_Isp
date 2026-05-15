<?php
session_start();
include "koneksi.php";

$step = $_POST['step'] ?? 1;
$errors = [];

// =====================
// PROSES STEP 1 - Data Diri
// =====================
if ($step == 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama      = trim($_POST['nama'] ?? '');
    $nik       = trim($_POST['nik'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telepon   = trim($_POST['telepon'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    if (empty($nama))
        $errors[] = "Nama Lengkap wajib diisi.";

    if (empty($nik) || !preg_match('/^\d{16}$/', $nik))
        $errors[] = "Nomer NIK KTP harus 16 digit angka.";

    if (empty($alamat))
        $errors[] = "Alamat wajib diisi.";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Email tidak valid.";

    if (empty($telepon) || !preg_match('/^\d{10,13}$/', $telepon))
        $errors[] = "No Telephone harus 10–13 digit.";

    if (!isset($_FILES['foto_ktp']) || $_FILES['foto_ktp']['error'] !== 0)
        $errors[] = "Foto KTP wajib diunggah.";

    if (strlen($password) < 6)
        $errors[] = "Password minimal 6 karakter.";

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

// =====================
// PROSES STEP 2 - PILIH PAKET
// =====================
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

        // =====================
        // CARI PAKET
        // =====================

        if($jenis_paket == "basic"){

            $queryPaket = mysqli_query(
                $koneksi,
                "SELECT * FROM paket
                WHERE jenis_paket='Rumah'
                AND nama_paket='$paket_pilihan'"
            );

        }else{

            $queryPaket = mysqli_query(
                $koneksi,
                "SELECT * FROM paket
                WHERE jenis_paket='Bisnis'
                AND nama_paket='$paket_pilihan'"
            );

        }

        $dataPaket = mysqli_fetch_assoc($queryPaket);

        if (!$dataPaket) {

            $errors[] = "Paket tidak ditemukan.";

        } else {

            $id_paket = $dataPaket['id_paket'];

            // =====================
            // INSERT KE TABEL PELANGGAN
            // =====================

$query = mysqli_query($koneksi,

"INSERT INTO pelanggan
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
)"

);

if(!$query){
    die("Gagal insert pelanggan: " . mysqli_error($koneksi));
}

$id_pelanggan = mysqli_insert_id($koneksi);

// echo $id_pelanggan;
// exit;

            // =====================
            // INSERT KE TABEL PENDAFTARAN
            // =====================

            $query2 = mysqli_query($koneksi,

            "INSERT INTO pendaftaran_pemasangan
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
                'Pending',
                NOW()
            )"

            );

            // =====================
            // CEK BERHASIL / GAGAL
            // =====================

            if ($query && $query2) {

                $success = true;
                session_destroy();

            } else {

                $errors[] = "Data gagal disimpan: " . mysqli_error($koneksi);

            }
        }
    }
}

// =====================
// DATA PAKET
// =====================

$paket_basic = [
    "Bronze" => "Rp. 150.000,-",
    "Silver" => "Rp. 250.000,-",
    "Gold" => "Rp. 400.000,-",
];

$paket_premium = [
    "Platinum" => "Rp. 750.000,-",
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Daftar Pelanggan – Gala Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #0d1b2e;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }

        .outer {
            background: linear-gradient(145deg, #0f2544 0%, #0a1628 100%);
            border-radius: 20px; padding: 32px;
            width: 100%; max-width: 860px;
            box-shadow: 0 30px 80px rgba(0,0,0,.6);
        }

        .card {
            background: #f4f6fb; border-radius: 16px;
            padding: 36px 40px 32px; position: relative;
        }

        .btn-back {
            position: absolute; top: 28px; left: 28px;
            background: none; border: none;
            cursor: pointer; color: #1a73e8; font-size: 22px;
        }
        .btn-back:hover { color: #0d5bc7; }

        .logo-wrap {
            display: flex; align-items: center;
            justify-content: center; gap: 10px; margin-bottom: 28px;
        }
        .logo-text strong { display: block; font-size: 22px; font-weight: 800; color: #1a73e8; }
        .logo-text span { font-size: 10px; color: #888; letter-spacing: 1.5px; text-transform: uppercase; }

        .steps { display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }
        .step-wrap { display: flex; flex-direction: column; align-items: center; }
        .step-circle {
            width: 52px; height: 52px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 700; flex-shrink: 0;
            border: 3px solid transparent;
        }
        .step-circle.active    { background: #1a73e8; color: #fff; box-shadow: 0 4px 16px rgba(26,115,232,.4); }
        .step-circle.done-blue { background: #1a73e8; color: #fff; box-shadow: 0 4px 16px rgba(26,115,232,.4); }
        .step-circle.pending   { background: #fff; color: #aab4c8; border-color: #d0d9ec; }
        .step-line { flex: 1; height: 3px; max-width: 140px; }
        .step-line.active-line   { background: #1a73e8; }
        .step-line.inactive-line { background: #d0d9ec; }
        .step-label { font-size: 12px; font-weight: 700; color: #1a73e8; margin-top: 6px; }

        .alert { border-radius: 10px; padding: 12px 16px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
        .alert-error   { background: #fde8e8; color: #c0392b; border: 1px solid #f5c6c6; }
        .alert-error ul { padding-left: 18px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }

        /* Step 1 */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 24px; }
        .full { grid-column: 1 / -1; }
        label { display: block; font-size: 13px; font-weight: 700; color: #1a73e8; margin-bottom: 6px; }
        input[type="text"], input[type="email"], input[type="tel"], textarea {
            width: 100%; background: #fff; border: 1.5px solid #dde3f0;
            border-radius: 10px; padding: 12px 14px;
            font-family: 'Nunito', sans-serif; font-size: 14px; color: #222;
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        input[type="text"]:focus, input[type="email"]:focus,
        input[type="tel"]:focus, textarea:focus {
            border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,.12);
        }
        textarea { resize: vertical; min-height: 90px; }
        .file-btn {
            display: inline-block; background: #1a73e8; color: #fff;
            font-family: 'Nunito', sans-serif; font-size: 14px; font-weight: 700;
            padding: 10px 24px; border-radius: 10px; border: none; cursor: pointer;
        }
        .file-btn:hover { background: #0d5bc7; }
        input[type="file"] { display: none; }
        .file-name { display: inline-block; margin-left: 10px; font-size: 12px; color: #666; }

        /* Step 2 */
        .paket-buttons {
            display: flex; gap: 20px; justify-content: center;
            margin: 24px 0 8px; flex-wrap: wrap;
        }
        .btn-paket {
            padding: 16px 40px; border-radius: 12px;
            font-family: 'Nunito', sans-serif; font-size: 16px; font-weight: 800;
            cursor: pointer; border: 2.5px solid #1a73e8;
            transition: all .2s; min-width: 200px;
        }
        .btn-paket.selected     { background: #1a73e8; color: #fff; box-shadow: 0 6px 20px rgba(26,115,232,.35); }
        .btn-paket.not-selected { background: #fff; color: #1a73e8; }
        .btn-paket:hover { transform: translateY(-2px); }

        .paket-list { margin-top: 24px; display: none; }
        .paket-list.show { display: block; }
        .paket-list h3 {
            font-size: 15px; font-weight: 800; color: #1a73e8;
            border-bottom: 2px solid #1a73e8; padding-bottom: 8px; margin-bottom: 16px;
        }
        .paket-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 4px; border-bottom: 1px solid #e8edf5;
        }
        .paket-item:last-child { border-bottom: none; }
        .paket-item label {
            display: flex; align-items: center; gap: 12px;
            cursor: pointer; font-size: 14px; font-weight: 600; color: #333; margin: 0;
        }
        input[type="radio"] { width: 18px; height: 18px; accent-color: #1a73e8; cursor: pointer; }
        .harga { font-size: 14px; font-weight: 700; color: #333; }

        .form-footer {
            display: flex; align-items: center;
            justify-content: space-between; margin-top: 24px;
        }
        .note { font-size: 12px; color: #aaa; }
        .btn-lanjut {
            background: #1a73e8; color: #fff;
            font-family: 'Nunito', sans-serif; font-size: 15px; font-weight: 800;
            padding: 12px 36px; border: none; border-radius: 12px; cursor: pointer;
            clip-path: polygon(10% 0%, 100% 0%, 100% 100%, 0% 100%);
            transition: background .2s, transform .1s;
        }
        .btn-lanjut:hover { background: #0d5bc7; transform: translateY(-1px); }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: 1; }
            .card { padding: 24px 18px; }
            .outer { padding: 16px; }
            .step-line { max-width: 60px; }
            .btn-paket { min-width: 140px; padding: 14px 20px; font-size: 14px; }
        }
    </style>
</head>
<body>
<div class="outer">
<div class="card">

    <!-- Tombol kembali -->
    <?php if ($step == 2 && !$success): ?>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="step" value="1"/>
            <button type="submit" class="btn-back">&#8592;</button>
        </form>
    <?php else: ?>
        <button class="btn-back" onclick="history.back()">&#8592;</button>
    <?php endif; ?>

    <!-- Logo -->
    <div class="logo-wrap">
        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
            <circle cx="24" cy="24" r="24" fill="#1a73e8" opacity=".1"/>
            <circle cx="24" cy="24" r="6" fill="#1a73e8"/>
            <circle cx="12" cy="14" r="4" fill="#1a73e8" opacity=".7"/>
            <circle cx="36" cy="14" r="4" fill="#1a73e8" opacity=".7"/>
            <circle cx="12" cy="34" r="4" fill="#1a73e8" opacity=".7"/>
            <circle cx="36" cy="34" r="4" fill="#1a73e8" opacity=".7"/>
            <line x1="24" y1="24" x2="12" y2="14" stroke="#1a73e8" stroke-width="2"/>
            <line x1="24" y1="24" x2="36" y2="14" stroke="#1a73e8" stroke-width="2"/>
            <line x1="24" y1="24" x2="12" y2="34" stroke="#1a73e8" stroke-width="2"/>
            <line x1="24" y1="24" x2="36" y2="34" stroke="#1a73e8" stroke-width="2"/>
        </svg>
        <div class="logo-text">
            <strong>GALA DATA</strong>
            <span>Best Solution Fast Internet</span>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="steps">
        <div class="step-wrap">
            <div class="step-circle <?= $step == 1 ? 'active' : 'done-blue' ?>">&#128100;</div>
        </div>
        <div class="step-line <?= $step == 2 ? 'active-line' : 'inactive-line' ?>"></div>
        <div class="step-wrap">
            <div class="step-circle <?= $step == 2 ? 'active' : 'pending' ?>">&#127760;</div>
        </div>
        <div class="step-line inactive-line"></div>
        <div class="step-wrap">
            <div class="step-circle pending">&#10003;</div>
        </div>
    </div>
    <div class="steps-label-row" style="display:flex; justify-content:center; gap:0; margin-bottom:16px;">
    <div style="width:52px; text-align:center;">
        <span class="step-label">Data Diri</span>
    </div>
    <div style="flex:1; max-width:140px;"></div>
    <div style="width:52px; text-align:center;">
        <span class="step-label"><?= $step == 2 ? 'Pilih Paket' : '' ?></span>
    </div>
    <div style="flex:1; max-width:140px;"></div>
    <div style="width:52px;"></div>
</div>

    <!-- Alert -->
    <?php if ($success): ?>
        <div class="alert alert-success">&#10003; Pendaftaran berhasil! Kami akan segera menghubungi Anda.</div>
        <div style="text-align:center; margin-top:20px;">

    <a href="index.php" class="btn-lanjut"
       style="text-decoration:none; display:inline-block;">
        Kembali ke Homepage
    </a>

</div>
    <?php elseif (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>

    <?php if ($step == 1): ?>
    <!-- ======== STEP 1: DATA DIRI ======== -->
    <form method="POST" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="step" value="1"/>
        <div class="form-grid">
            <div>
                <label for="nama">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap"
                       value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"/>
            </div>
            <div>
                <label for="nik">Nomer NIK KTP *</label>
                <input type="text" id="nik" name="nik" placeholder="No. KTP (16 digit)" maxlength="16"
                       value="<?= htmlspecialchars($_POST['nik'] ?? '') ?>"/>
            </div>

<div>
    <label for="password">Password *</label>

    <input 
        type="password"
        id="password"
        name="password"
        placeholder="Buat password login"
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
            
            <div class="full">
                <label for="alamat">Alamat *</label>
                <textarea id="alamat" name="alamat" placeholder="Sesuai KTP/SIM/Pasport"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
            </div>
            <div>
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="Email Anda"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
            </div>
            <div>
                <label for="telepon">No Telephone *</label>
                <input type="tel" id="telepon" name="telepon" placeholder="No Telephone"
                       value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"/>
            </div>
        </div>
        <div class="form-footer">
            <span class="note">* Wajib diisi</span>
            <button type="submit" class="btn-lanjut">Lanjut</button>
        </div>
    </form>

    <?php elseif ($step == 2): ?>
    <!-- ======== STEP 2: PILIH PAKET ======== -->
    <form method="POST">
        <input type="hidden" name="step" value="2"/>
        <input type="hidden" name="jenis_paket" id="jenis_paket" value="<?= htmlspecialchars($_POST['jenis_paket'] ?? '') ?>"/>

        <div class="paket-buttons">
            <button type="button"
                    class="btn-paket <?= (($_POST['jenis_paket'] ?? '') === 'basic') ? 'selected' : 'not-selected' ?>"
                    onclick="pilihJenis('basic', this)">Wifi Basic Home</button>
            <button type="button"
                    class="btn-paket <?= (($_POST['jenis_paket'] ?? '') === 'premium') ? 'selected' : 'not-selected' ?>"
                    onclick="pilihJenis('premium', this)">Wifi Premium Bisnis</button>
        </div>

        <div class="paket-list <?= (($_POST['jenis_paket'] ?? '') === 'basic') ? 'show' : '' ?>" id="list-basic">
            <h3>Berlangganan Bulanan</h3>
            <?php foreach ($paket_basic as $nama => $harga): ?>
            <div class="paket-item">
                <label>
                    <input type="radio" name="paket_pilihan" value="<?= htmlspecialchars($nama) ?>"
                           <?= (($_POST['paket_pilihan'] ?? '') === $nama) ? 'checked' : '' ?>/>
                    <?= htmlspecialchars($nama) ?>
                </label>
                <span class="harga"><?= $harga ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="paket-list <?= (($_POST['jenis_paket'] ?? '') === 'premium') ? 'show' : '' ?>" id="list-premium">
            <h3>Berlangganan Bulanan</h3>
            <?php foreach ($paket_premium as $nama => $harga): ?>
            <div class="paket-item">
                <label>
                    <input type="radio" name="paket_pilihan" value="<?= htmlspecialchars($nama) ?>"
                           <?= (($_POST['paket_pilihan'] ?? '') === $nama) ? 'checked' : '' ?>/>
                    <?= htmlspecialchars($nama) ?>
                </label>
                <span class="harga"><?= $harga ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="form-footer">
            <span class="note">* Wajib pilih</span>
            <button type="submit" class="btn-lanjut">Lanjut</button>
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
    document.querySelectorAll('input[name="paket_pilihan"]').forEach(r => r.checked = false);
}
</script>

</body>
</html>
