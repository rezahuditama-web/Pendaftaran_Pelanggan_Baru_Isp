<?php
include 'koneksi.php';

$pesan_sukses = "";
$pesan_error  = "";

/* ======================================
   PROSES KIRIM PESAN
   Tabel: contact
   Kolom: id_contact, nama_pengirim, email, no_hp, subjek, isi_pesan, tanggal_kirim
====================================== */
if(isset($_POST['kirim'])){
    $subjek        = mysqli_real_escape_string($koneksi, trim($_POST['subjek']));
    $nama_pengirim = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email         = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $no_hp         = mysqli_real_escape_string($koneksi, trim($_POST['telepon']));
    $isi_pesan     = mysqli_real_escape_string($koneksi, trim($_POST['pesan']));

    // Validasi wajib isi
    if(empty($nama_pengirim) || empty($email) || empty($isi_pesan)){
        $pesan_error = "Nama, Email, dan Pesan wajib diisi.";
    }
    // Validasi format email
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $pesan_error = "Format email tidak valid. Contoh: nama@email.com";
    }
    // Validasi no HP hanya angka
    elseif(!empty($no_hp) && !preg_match('/^[0-9+\-\s]{8,15}$/', $no_hp)){
        $pesan_error = "Nomor HP hanya boleh berisi angka (8–15 digit).";
    }
    // Validasi panjang pesan
    elseif(strlen($isi_pesan) > 1000){
        $pesan_error = "Pesan terlalu panjang. Maksimal 1000 karakter.";
    }
    else {
        // Simpan ke tabel contact
        $q = mysqli_query($koneksi,"
            INSERT INTO contact (nama_pengirim, email, no_hp, subjek, isi_pesan, tanggal_kirim)
            VALUES ('$nama_pengirim','$email','$no_hp','$subjek','$isi_pesan', NOW())
        ");

        if($q){
            $pesan_sukses = "Pesan berhasil dikirim! Kami akan segera menghubungi Anda.";
            // Kosongkan input setelah berhasil
            $_POST = [];
        } else {
            $pesan_error = "Gagal menyimpan pesan: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Hubungi Kami – Gala Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            a { text-decoration: none; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            background: linear-gradient(135deg, #0d1b2e 0%, #0f2f5a 60%, #1a73e8 100%);
            padding: 60px 60px 70px;
            display: flex;
            align-items: center;
            gap: 40px;
            position: relative;
            overflow: hidden;
            min-height: 520px;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(26,115,232,.15);
            border-radius: 50%;
            top: -100px; right: -100px;
        }

        /* Ilustrasi kiri */
        .hero-illust {
            flex: 0 0 320px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }
        .illust-phone { width: 260px; position: relative; }
        .illust-phone svg { width: 100%; }

        /* Konten kanan */
        .hero-content { flex: 1; }

        .badge-hubungi {
            display: inline-block;
            background: #1a73e8;
            color: #fff;
            padding: 10px 28px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        .hero-sub {
            font-size: 14px;
            color: #b0c4de;
            margin-bottom: 32px;
            line-height: 1.7;
        }

        /* ===== ALERT ===== */
        .alert {
            border-radius: 10px; padding: 12px 16px;
            font-size: 13px; font-weight: 600; margin-bottom: 16px;
        }
        .alert-success { background: rgba(220,252,231,.9); color: #16a34a; border: 1px solid #bbf7d0; }
        .alert-error   { background: rgba(254,226,226,.9); color: #dc2626; border: 1px solid #fca5a5; }

        /* ===== FORM ===== */
        .contact-form { background: transparent; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }
        .form-row.three { grid-template-columns: 1fr 1fr 1fr; }
        .form-row.one   { grid-template-columns: 1fr; }

        .form-group { display: flex; flex-direction: column; }
        .form-group label {
            font-size: 13px; font-weight: 500;
            color: #b0c4de; margin-bottom: 6px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 14px;
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.2);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px; color: #fff;
            outline: none;
            transition: border-color .2s, background .2s;
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder { color: rgba(255,255,255,.4); }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #1a73e8;
            background: rgba(255,255,255,.18);
        }
        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23fff' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
            cursor: pointer;
        }
        .form-group select option { background: #0f2f5a; color: #fff; }
        .form-group textarea { resize: vertical; min-height: 120px; }

        .btn-kirim {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 8px;
        }
        .btn-kirim button {
            background: #1a73e8; color: #fff;
            border: none; padding: 13px 40px;
            border-radius: 10px; font-family: 'Poppins', sans-serif;
            font-size: 15px; font-weight: 700;
            cursor: pointer; transition: background .2s, transform .1s;
        }
        .btn-kirim button:hover { background: #0d5bc7; transform: translateY(-1px); }

        /* ===== INFO KONTAK ===== */
        .info-section {
            background: #fff;
            padding: 60px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }
        .info-card {
            text-align: center;
            padding: 32px 24px;
            border-radius: 14px;
            border: 1.5px solid #e8edf5;
            transition: box-shadow .2s, transform .2s;
        }
        .info-card:hover {
            box-shadow: 0 8px 24px rgba(26,115,232,.12);
            transform: translateY(-4px);
        }
        .info-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #1a73e8, #0d5bc7);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 22px; color: #fff;
        }
        .info-card h3 {
            font-size: 16px; font-weight: 700;
            color: #1a1a1a; margin-bottom: 8px;
        }
        .info-card p { font-size: 14px; color: #666; line-height: 1.6; }
        .info-card a { color: #1a73e8; text-decoration: none; font-weight: 600; }
        .info-card a:hover { text-decoration: underline; }

        /* ===== FOOTER ===== */
        .footer {
            background: #0d1b2e;
            color: #b0c4de;
            text-align: center;
            padding: 24px;
            font-size: 13px;
        }
        .footer span { color: #1a73e8; font-weight: 600; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {

            .hero { flex-direction: column; padding: 40px 20px; }
            .hero-illust { display: none; }
            .hero-title { font-size: 22px; }
            .form-row, .form-row.three { grid-template-columns: 1fr; }
            .info-section { grid-template-columns: 1fr; padding: 40px 20px; }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<!-- ===== HERO + FORM ===== -->
<section class="hero">

    <!-- Ilustrasi kiri -->
    <div class="hero-illust">
        <svg viewBox="0 0 260 320" fill="none" xmlns="http://www.w3.org/2000/svg" class="illust-phone">
            <!-- Awan -->
            <ellipse cx="130" cy="295" rx="110" ry="22" fill="white" opacity=".15"/>
            <ellipse cx="80"  cy="285" rx="70"  ry="18" fill="white" opacity=".2"/>
            <!-- Tangan -->
            <rect x="85" y="200" width="90" height="110" rx="20" fill="#f4a68a"/>
            <rect x="95" y="185" width="70" height="30"  rx="10" fill="#f4a68a"/>
            <!-- HP body -->
            <rect x="88" y="80"  width="84" height="145" rx="14" fill="#1e293b"/>
            <rect x="92" y="88"  width="76" height="130" rx="10" fill="#0f172a"/>
            <!-- Layar chat -->
            <rect x="100" y="100" width="60" height="16" rx="6" fill="#3b82f6" opacity=".8"/>
            <rect x="100" y="122" width="45" height="14" rx="6" fill="#475569"/>
            <rect x="115" y="142" width="45" height="14" rx="6" fill="#3b82f6" opacity=".6"/>
            <rect x="100" y="162" width="50" height="14" rx="6" fill="#475569"/>
            <!-- Notif -->
            <circle cx="178" cy="88"  r="10" fill="#ef4444"/>
            <text x="174" y="93" fill="white" font-size="10" font-weight="bold">!</text>
            <circle cx="82"  cy="100" r="8"  fill="#f59e0b"/>
            <text x="78" y="105" fill="white" font-size="10" font-weight="bold">!</text>
        </svg>
    </div>

    <!-- Konten + Form -->
    <div class="hero-content">
        <h1 class="hero-title">Bagaimana kami dapat<br>membantu Anda?</h1>
        <p class="hero-sub">
            Silahkan kirimkan pertanyaan atau pesan Anda melalui formulir berikut<br>
            dan kami akan segera menghubungi Anda kembali.
        </p>

        <?php if(!empty($pesan_sukses)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $pesan_sukses; ?></div>
        <?php elseif(!empty($pesan_error)): ?>
            <div class="alert alert-error"><i class="fa-solid fa-circle-xmark"></i> <?= $pesan_error; ?></div>
        <?php endif; ?>

        <form method="POST" class="contact-form">
            <!-- Baris 1: ID Pelanggan + Subjek -->
            <div class="form-row">
                <div class="form-group">
                    <label>Subjek</label>
                    <select name="subjek" required>
                        <option value="" disabled <?= empty($_POST['subjek']) ? 'selected':'' ?>>Paket/Promo</option>
                        <option value="Paket/Promo"      <?= ($_POST['subjek'] ?? '')==='Paket/Promo'      ? 'selected':'' ?>>Paket/Promo</option>
                        <option value="Gangguan Koneksi"  <?= ($_POST['subjek'] ?? '')==='Gangguan Koneksi' ? 'selected':'' ?>>Gangguan Koneksi</option>
                        <option value="Pendaftaran"       <?= ($_POST['subjek'] ?? '')==='Pendaftaran'      ? 'selected':'' ?>>Pendaftaran</option>
                        <option value="Pembayaran"        <?= ($_POST['subjek'] ?? '')==='Pembayaran'       ? 'selected':'' ?>>Pembayaran</option>
                        <option value="Lainnya"           <?= ($_POST['subjek'] ?? '')==='Lainnya'          ? 'selected':'' ?>>Lainnya</option>
                    </select>
                </div>
            </div>

            <!-- Baris 2: Nama + Email + Telepon -->
            <div class="form-row three">
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="nama" placeholder="Nama Lengkap"
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required/>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" placeholder="Email Anda"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
                </div>
                <div class="form-group">
                    <label>Telephone</label>
                    <input type="tel" name="telepon" placeholder="No. Telepon"
                           value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"/>
                </div>
            </div>

            <!-- Baris 3: Pesan -->
            <div class="form-row one">
                <div class="form-group">
                    <textarea name="pesan" placeholder="Pesan Anda" required><?= htmlspecialchars($_POST['pesan'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="btn-kirim">
                <button type="submit" name="kirim">
                    <i class="fa-solid fa-paper-plane"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</section>

<!-- ===== INFO KONTAK ===== -->
<section class="info-section">
    <div class="info-card">
        <div class="info-icon"><i class="fa-brands fa-whatsapp"></i></div>
        <h3>WhatsApp</h3>
        <p>Hubungi kami langsung<br>via WhatsApp</p>
        <a href="https://wa.me/6281231631284" target="_blank">+62 812-3163-1284</a>
    </div>
    <div class="info-card">
        <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
        <h3>Email</h3>
        <p>Kirimkan pertanyaan<br>melalui email kami</p>
        <a href="mailto:support@galadata.id">support@galadata.id</a>
    </div>
    <div class="info-card">
        <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
        <h3>Kantor</h3>
        <p> Ds.benelan kidul <br>Banyuwangi, Jawa Timur</p>
        <a href="https://maps.app.goo.gl/iKuhUm2RtTJCo2WU8" target="_blank">Lihat di Maps</a>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <p>&copy; 2026 <span>Gala Data</span> – Best Solution Fast Internet. All rights reserved.</p>
</footer>

</body>
</html>
