<?php
// Proses form jika sudah disubmit
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');

    // Validasi sederhana
    if (empty($nama)) $errors[] = "Nama Lengkap wajib diisi.";
    if (empty($nik) || !preg_match('/^\d{16}$/', $nik)) $errors[] = "Nomer NIK KTP harus 16 digit angka.";
    if (empty($alamat)) $errors[] = "Alamat wajib diisi.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid.";
    if (empty($telepon) || !preg_match('/^\d{10,13}$/', $telepon)) $errors[] = "No Telephone harus 10–13 digit.";
    if (!isset($_FILES['foto_ktp']) || $_FILES['foto_ktp']['error'] !== 0) $errors[] = "Foto KTP wajib diunggah.";

    if (empty($errors)) {
        // Di sini kamu bisa tambahkan koneksi database dan INSERT
        // Contoh:
        // $conn = new mysqli("localhost", "root", "", "gala_data");
        // $stmt = $conn->prepare("INSERT INTO pelanggan (nama, nik, alamat, email, telepon) VALUES (?,?,?,?,?)");
        // $stmt->bind_param("sssss", $nama, $nik, $alamat, $email, $telepon);
        // $stmt->execute();
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Daftar Pelanggan – Gala Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #0d1b2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Outer wrapper – dark gradient frame */
        .outer {
            background: linear-gradient(145deg, #0f2544 0%, #0a1628 100%);
            border-radius: 20px;
            padding: 32px;
            width: 100%;
            max-width: 860px;
            box-shadow: 0 30px 80px rgba(0,0,0,.6);
        }

        /* White card */
        .card {
            background: #f4f6fb;
            border-radius: 16px;
            padding: 36px 40px 32px;
            position: relative;
        }

        /* Back arrow */
        .btn-back {
            position: absolute;
            top: 28px;
            left: 28px;
            background: none;
            border: none;
            cursor: pointer;
            color: #1a73e8;
            font-size: 22px;
            line-height: 1;
        }
        .btn-back:hover { color: #0d5bc7; }

        /* Logo area */
        .logo-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .logo-wrap img { height: 48px; }
        .logo-text { line-height: 1.1; }
        .logo-text strong {
            display: block;
            font-size: 22px;
            font-weight: 800;
            color: #1a73e8;
            letter-spacing: .5px;
        }
        .logo-text span {
            font-size: 10px;
            color: #888;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Step indicator */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 32px;
        }
        .step-circle {
            width: 52px; height: 52px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            font-weight: 700;
            flex-shrink: 0;
            border: 3px solid transparent;
        }
        .step-circle.active {
            background: #1a73e8;
            color: #fff;
            box-shadow: 0 4px 16px rgba(26,115,232,.4);
        }
        .step-circle.pending {
            background: #e4eaf5;
            color: #aab4c8;
            border-color: #d0d9ec;
        }
        .step-circle.done {
            background: #fff;
            color: #1a73e8;
            border-color: #d0d9ec;
        }
        .step-line {
            flex: 1;
            height: 3px;
            background: #d0d9ec;
            max-width: 140px;
        }
        .step-label {
            font-size: 12px;
            font-weight: 700;
            color: #1a73e8;
            text-align: center;
            margin-top: 6px;
        }
        .step-wrap { display: flex; flex-direction: column; align-items: center; }

        /* Form grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 24px;
        }
        .full { grid-column: 1 / -1; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1a73e8;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            background: #fff;
            border: 1.5px solid #dde3f0;
            border-radius: 10px;
            padding: 12px 14px;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            color: #222;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        textarea:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,.12);
        }
        textarea { resize: vertical; min-height: 90px; }

        /* File upload */
        .file-btn {
            display: inline-block;
            background: #1a73e8;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }
        .file-btn:hover { background: #0d5bc7; transform: translateY(-1px); }
        input[type="file"] { display: none; }
        .file-name {
            display: inline-block;
            margin-left: 10px;
            font-size: 12px;
            color: #666;
        }

        /* Error / success */
        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .alert-error { background: #fde8e8; color: #c0392b; border: 1px solid #f5c6c6; }
        .alert-error ul { padding-left: 18px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }

        /* Footer row */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 24px;
        }
        .note { font-size: 12px; color: #aaa; }

        .btn-lanjut {
            background: #1a73e8;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 800;
            padding: 12px 36px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            clip-path: polygon(10% 0%, 100% 0%, 100% 100%, 0% 100%);
            letter-spacing: .5px;
            transition: background .2s, transform .1s;
        }
        .btn-lanjut:hover { background: #0d5bc7; transform: translateY(-1px); }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: 1; }
            .card { padding: 24px 18px; }
            .outer { padding: 16px; }
            .step-line { max-width: 60px; }
        }
    </style>
</head>
<body>

<div class="outer">
    <div class="card">

        <!-- Tombol kembali -->
        <button class="btn-back" onclick="history.back()" title="Kembali">&#8592;</button>

        <!-- Logo -->
        <div class="logo-wrap">
            <!-- Jika punya file logo, ganti src di bawah ini -->
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
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

        <!-- Step indicator -->
        <div class="steps">
    <div class="step-wrap">
        <div class="step-line"></div>
        <div class="step-circle active">&#128100;</div>
        <span class="step-label">Data Diri</span>  
    </div>
    <div class="step-line"></div>
    <div class="step-wrap">
        <div class="step-circle pending">&#127760;</div>
    </div>
    <div class="step-line"></div>
    <div class="step-wrap">
        <div class="step-circle done">&#10003;</div>
    </div>
</div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                &#10003; Data berhasil dikirim! Kami akan menghubungi Anda segera.
            </div>
        <?php elseif (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-grid">

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama"
                           placeholder="Nama Lengkap"
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"/>
                </div>

                <!-- NIK KTP -->
                <div>
                    <label for="nik">Nomer NIK KTP *</label>
                    <input type="text" id="nik" name="nik"
                           placeholder="No. KTP (16 digit)"
                           maxlength="16"
                           value="<?= htmlspecialchars($_POST['nik'] ?? '') ?>"/>
                </div>

                <!-- Foto KTP -->
                <div class="full">
                    <label>Foto KTP *</label>
                    <label for="foto_ktp" class="file-btn">Kirim</label>
                    <input type="file" id="foto_ktp" name="foto_ktp" accept="image/*,.pdf"
                           onchange="document.getElementById('nama-file').textContent = this.files[0]?.name || ''"/>
                    <span class="file-name" id="nama-file">Belum ada file dipilih</span>
                </div>

                <!-- Alamat -->
                <div class="full">
                    <label for="alamat">Alamat *</label>
                    <textarea id="alamat" name="alamat"
                              placeholder="Sesuai KTP/SIM/Pasport"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>

                <!-- Email -->
                <div>
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email"
                           placeholder="Email Anda"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
                </div>

                <!-- Telepon -->
                <div>
                    <label for="telepon">No Telephone *</label>
                    <input type="tel" id="telepon" name="telepon"
                           placeholder="No Telephone"
                           value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"/>
                </div>

            </div><!-- /form-grid -->

            <div class="form-footer">
                <span class="note">* Wajib diisi</span>
                <button type="submit" class="btn-lanjut">Lanjut</button>
            </div>
        </form>
        <?php endif; ?>

    </div><!-- /card -->
</div><!-- /outer -->

</body>
</html>
