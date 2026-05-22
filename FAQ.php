<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>FAQ – Gala Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: #fff;
            padding: 0 60px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .nav-logo-text strong {
            display: block; font-size: 18px;
            font-weight: 800; color: #1a73e8;
        }
        .nav-logo-text span {
            font-size: 9px; color: #888;
            letter-spacing: 1.5px; text-transform: uppercase;
        }
        .nav-links {
            display: flex; align-items: center; gap: 32px;
            list-style: none;
        }
        .nav-links a {
            text-decoration: none; color: #444;
            font-size: 14px; font-weight: 500;
            transition: color .2s;
        }
        .nav-links a:hover,
        .nav-links a.active { color: #1a73e8; }
        .nav-links .dropdown { position: relative; }
        .nav-links .dropdown-menu {
            display: none; position: absolute;
            top: 100%; left: 0; background: #fff;
            border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.12);
            min-width: 160px; padding: 8px 0; z-index: 200;
        }
        .nav-links .dropdown:hover .dropdown-menu { display: block; }
        .nav-links .dropdown-menu a {
            display: block; padding: 10px 18px;
            font-size: 13px;
        }
        .nav-links .dropdown-menu a:hover { background: #f0f7ff; }
        .nav-btns { display: flex; gap: 10px; }
        .btn-daftar {
            background: #1a73e8; color: #fff;
            border: none; padding: 9px 22px;
            border-radius: 8px; font-family: 'Poppins', sans-serif;
            font-size: 14px; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: background .2s;
        }
        .btn-daftar:hover { background: #0d5bc7; }
        .btn-masuk {
            background: #fff; color: #1a73e8;
            border: 2px solid #1a73e8; padding: 9px 22px;
            border-radius: 8px; font-family: 'Poppins', sans-serif;
            font-size: 14px; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: all .2s;
        }
        .btn-masuk:hover { background: #f0f7ff; }

        /* ===== HERO ===== */
        .hero {
            background: linear-gradient(135deg, #0d1b2e 0%, #0f2f5a 60%, #1a73e8 100%);
            padding: 70px 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(26,115,232,.15);
            border-radius: 50%;
            top: -150px; right: -100px;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(26,115,232,.1);
            border-radius: 50%;
            bottom: -100px; left: -80px;
        }
        .hero-icon {
            width: 72px; height: 72px;
            background: rgba(26,115,232,.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px; color: #fff;
            border: 2px solid rgba(26,115,232,.5);
        }
        .hero h1 {
            font-size: 36px; font-weight: 800;
            color: #fff; margin-bottom: 12px;
        }
        .hero p {
            font-size: 15px; color: #b0c4de;
            max-width: 500px; margin: 0 auto;
            line-height: 1.7;
        }

        /* ===== FAQ SECTION ===== */
        .faq-section {
            max-width: 820px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        /* Kategori label */
        .faq-category {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            margin-top: 40px;
        }
        .faq-category:first-of-type { margin-top: 0; }
        .faq-category-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #1a73e8, #0d5bc7);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; color: #fff;
            flex-shrink: 0;
        }
        .faq-category h2 {
            font-size: 16px; font-weight: 700;
            color: #1a1a1a;
        }

        /* Accordion item */
        .faq-item {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1.5px solid #e8edf5;
            overflow: hidden;
            transition: box-shadow .2s;
        }
        .faq-item:hover {
            box-shadow: 0 4px 16px rgba(26,115,232,.1);
        }
        .faq-item.open {
            border-color: #1a73e8;
            box-shadow: 0 4px 20px rgba(26,115,232,.15);
        }

        .faq-question {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            font-family: 'Poppins', sans-serif;
            gap: 16px;
        }
        .faq-question span {
            font-size: 14px; font-weight: 600;
            color: #1a1a1a; flex: 1;
            transition: color .2s;
        }
        .faq-item.open .faq-question span { color: #1a73e8; }
        .faq-chevron {
            width: 28px; height: 28px;
            background: #f0f4f8;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; color: #888;
            flex-shrink: 0;
            transition: background .2s, transform .3s, color .2s;
        }
        .faq-item.open .faq-chevron {
            background: #1a73e8;
            color: #fff;
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height .35s ease, padding .3s ease;
            padding: 0 22px;
        }
        .faq-item.open .faq-answer {
            max-height: 400px;
            padding-bottom: 18px;
        }
        .faq-answer p {
            font-size: 13.5px;
            color: #555;
            line-height: 1.75;
            border-top: 1px solid #f0f4f8;
            padding-top: 14px;
        }

        /* ===== CTA BAWAH ===== */
        .cta-section {
            background: linear-gradient(135deg, #0d1b2e 0%, #0f2f5a 60%, #1a73e8 100%);
            padding: 60px 20px;
            text-align: center;
        }
        .cta-section h2 {
            font-size: 26px; font-weight: 800;
            color: #fff; margin-bottom: 12px;
        }
        .cta-section p {
            font-size: 14px; color: #b0c4de;
            margin-bottom: 28px; line-height: 1.7;
        }
        .btn-hubungi {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #1a73e8;
            color: #fff;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 10px;
            font-size: 15px; font-weight: 700;
            font-family: 'Poppins', sans-serif;
            transition: background .2s, transform .1s;
            box-shadow: 0 4px 20px rgba(26,115,232,.4);
        }
        .btn-hubungi:hover {
            background: #0d5bc7;
            transform: translateY(-2px);
        }

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
            .navbar { padding: 0 20px; }
            .nav-links, .nav-btns { display: none; }
            .hero { padding: 50px 20px; }
            .hero h1 { font-size: 26px; }
            .faq-section { padding: 40px 16px; }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <a href="index.php" class="nav-logo">
        <svg width="38" height="38" viewBox="0 0 48 48" fill="none">
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
        <div class="nav-logo-text">
            <strong>GALA DATA</strong>
            <span>Best Solution Fast Internet</span>
        </div>
    </a>

    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li class="dropdown">
            <a href="#">Paket Harga <i class="fa-solid fa-chevron-down" style="font-size:11px;"></i></a>
            <div class="dropdown-menu">
                <a href="#">Paket Rumah</a>
                <a href="#">Paket Bisnis</a>
            </div>
        </li>
        <li class="dropdown">
            <a href="#" class="active">Bantuan <i class="fa-solid fa-chevron-down" style="font-size:11px;"></i></a>
            <div class="dropdown-menu">
                <a href="faq.php" style="color:#1a73e8;">FAQ</a>
                <a href="hubungi_kami.php">Hubungi Kami</a>
            </div>
        </li>
    </ul>

    <div class="nav-btns">
        <a href="daftar.php" class="btn-daftar">Daftar</a>
        <a href="login.php"  class="btn-masuk">Masuk</a>
    </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-icon">
        <i class="fa-solid fa-circle-question"></i>
    </div>
    <h1>Pertanyaan yang Sering Ditanyakan</h1>
    <p>Temukan jawaban atas pertanyaan umum seputar layanan internet Gala Data di sini.</p>
</section>

<!-- ===== FAQ LIST ===== -->
<div class="faq-section">

    <?php
    $faq_data = [
        [
            'kategori' => 'Pendaftaran & Pemasangan',
            'icon'     => 'fa-solid fa-user-plus',
            'items'    => [
                [
                    'q' => 'Bagaimana cara mendaftar layanan internet Gala Data?',
                    'a' => 'Anda bisa mendaftar melalui website kami dengan klik tombol "Daftar" di pojok kanan atas, atau hubungi kami via WhatsApp. Tim kami akan memandu proses pendaftaran hingga pemasangan selesai.'
                ],
                [
                    'q' => 'Berapa lama proses pemasangan setelah mendaftar?',
                    'a' => 'Proses pemasangan biasanya memakan waktu 1–3 hari kerja setelah pendaftaran dikonfirmasi dan area Anda telah terverifikasi dalam jangkauan layanan kami.'
                ],
                [
                    'q' => 'Apakah ada biaya pemasangan?',
                    'a' => 'Biaya pemasangan tergantung paket yang dipilih. Beberapa paket sudah termasuk gratis biaya pasang. Silakan cek halaman Paket Harga untuk detail lebih lengkap.'
                ],
                [
                    'q' => 'Apakah area saya sudah terjangkau layanan Gala Data?',
                    'a' => 'Untuk mengecek ketersediaan layanan di area Anda, silakan hubungi kami via WhatsApp atau isi form Hubungi Kami. Tim kami akan segera mengonfirmasi jangkauan layanan di lokasi Anda.'
                ],
            ]
        ],
        [
            'kategori' => 'Paket & Harga',
            'icon'     => 'fa-solid fa-tags',
            'items'    => [
                [
                    'q' => 'Paket internet apa saja yang tersedia?',
                    'a' => 'Gala Data menyediakan paket Rumah dan paket Bisnis dengan berbagai pilihan kecepatan. Silakan kunjungi halaman Paket Harga untuk melihat detail kecepatan dan harga masing-masing paket.'
                ],
                [
                    'q' => 'Apakah ada kontrak atau ikatan berlangganan?',
                    'a' => 'Kami menawarkan sistem berlangganan bulanan tanpa kontrak jangka panjang. Anda bebas melanjutkan atau berhenti kapan saja sesuai kebutuhan.'
                ],
                [
                    'q' => 'Apakah ada promo atau diskon untuk pelanggan baru?',
                    'a' => 'Ya! Kami sering mengadakan promo untuk pelanggan baru. Pantau terus halaman utama kami atau hubungi tim kami untuk info promo terkini.'
                ],
            ]
        ],
        [
            'kategori' => 'Gangguan & Teknis',
            'icon'     => 'fa-solid fa-wrench',
            'items'    => [
                [
                    'q' => 'Apa yang harus dilakukan jika internet saya tiba-tiba mati?',
                    'a' => 'Coba langkah berikut: (1) Restart modem dengan cabut listrik selama 30 detik lalu pasang kembali. (2) Periksa semua kabel sudah terpasang dengan benar. (3) Jika masih bermasalah, hubungi tim teknis kami via WhatsApp.'
                ],
                [
                    'q' => 'Kecepatan internet saya terasa lambat, apa penyebabnya?',
                    'a' => 'Beberapa penyebab umum: banyak perangkat terhubung sekaligus, posisi router yang tidak optimal, atau gangguan jaringan di area Anda. Coba restart modem terlebih dahulu. Jika masalah berlanjut, hubungi kami untuk pengecekan lebih lanjut.'
                ],
                [
                    'q' => 'Apakah ada layanan teknisi ke rumah jika ada gangguan?',
                    'a' => 'Ya, kami menyediakan layanan kunjungan teknisi ke rumah. Hubungi tim kami via WhatsApp atau form Hubungi Kami, dan kami akan jadwalkan kunjungan teknisi sesegera mungkin.'
                ],
            ]
        ],
        [
            'kategori' => 'Pembayaran',
            'icon'     => 'fa-solid fa-credit-card',
            'items'    => [
                [
                    'q' => 'Metode pembayaran apa saja yang diterima?',
                    'a' => 'Kami menerima pembayaran melalui transfer bank, e-wallet (GoPay, OVO, Dana), dan pembayaran tunai di kantor kami. Detail rekening dan metode pembayaran akan dikirimkan saat tagihan diterbitkan.'
                ],
                [
                    'q' => 'Kapan tagihan dikirimkan setiap bulannya?',
                    'a' => 'Tagihan dikirimkan setiap awal bulan melalui email atau WhatsApp yang didaftarkan. Batas pembayaran adalah tanggal 10 setiap bulannya.'
                ],
                [
                    'q' => 'Apa yang terjadi jika saya terlambat membayar?',
                    'a' => 'Jika pembayaran melewati batas waktu, layanan internet Anda akan ditangguhkan sementara. Layanan akan aktif kembali dalam 1x24 jam setelah pembayaran dikonfirmasi.'
                ],
            ]
        ],
    ];
    ?>

    <?php foreach($faq_data as $cat_index => $kategori): ?>

        <!-- Kategori -->
        <div class="faq-category">
            <div class="faq-category-icon">
                <i class="<?= $kategori['icon'] ?>"></i>
            </div>
            <h2><?= $kategori['kategori'] ?></h2>
        </div>

        <!-- Item FAQ -->
        <?php foreach($kategori['items'] as $i => $item): ?>
        <div class="faq-item" id="faq-<?= $cat_index ?>-<?= $i ?>">
            <button class="faq-question" onclick="toggleFaq('faq-<?= $cat_index ?>-<?= $i ?>')">
                <span><?= htmlspecialchars($item['q']) ?></span>
                <div class="faq-chevron">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </button>
            <div class="faq-answer">
                <p><?= htmlspecialchars($item['a']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endforeach; ?>

</div>

<!-- ===== CTA ===== -->
<section class="cta-section">
    <h2>Tidak menemukan jawaban yang Anda cari?</h2>
    <p>Tim kami siap membantu Anda. Kirimkan pertanyaan Anda langsung kepada kami!</p>
    <a href="hubungi_kami.php" class="btn-hubungi">
        <i class="fa-solid fa-paper-plane"></i> Hubungi Kami
    </a>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <p>&copy; 2026 <span>Gala Data</span> – Best Solution Fast Internet. All rights reserved.</p>
</footer>

<script>
function toggleFaq(id) {
    const item = document.getElementById(id);
    const isOpen = item.classList.contains('open');

    // Tutup semua yang terbuka
    document.querySelectorAll('.faq-item.open').forEach(el => {
        el.classList.remove('open');
    });

    // Buka yang diklik (kalau sebelumnya tertutup)
    if (!isOpen) {
        item.classList.add('open');
    }
}
</script>

</body>
</html>