
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gala Data Internet</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #111;
        }

        a {
            text-decoration: none;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            width: 100%;
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo h2 {
            font-size: 24px;
            color: #1a73e8;
            font-weight: 800;
        }

        .logo span {
            font-size: 10px;
            color: #777;
            display: block;
        }
        .logo-circle img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    display: block;
}

        .nav-menu {
            display: flex;
            gap: 35px;
        }

        .nav-menu a {
            color: #111;
            font-size: 15px;
            font-weight: 600;
            transition: .3s;
        }

        .nav-menu a:hover {
            color: #1a73e8;
        }

        .btn-login {
            background: linear-gradient(90deg, #1a73e8, #13b0ff);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }

        /* ================= HERO ================= */
        .hero {
            width: 100%;
            min-height: 90vh;
            background: linear-gradient(135deg, #00152e, #002b59);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 80px 8%;
            color: white;
            gap: 40px;
        }

        .hero-text {
            flex: 1;
        }

        .hero-text h5 {
            font-size: 20px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .hero-text h1 {
            font-size: 68px;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .hero-text p {
            font-size: 18px;
            color: #d8d8d8;
            line-height: 1.8;
            margin-bottom: 30px;
            max-width: 600px;
        }

        .hero-text small {
            display: block;
            margin-bottom: 35px;
            color: #c5c5c5;
        }

        .btn-daftar {
            background: linear-gradient(90deg, #1a73e8, #13b0ff);
            color: white;
            padding: 15px 35px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            transition: .3s;
            display: inline-block;
        }

        .btn-daftar:hover {
            transform: translateY(-3px);
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .circle {
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .circle img {
            width: 70%;
        }

        /* ================= FITUR ================= */
        .fitur {
            padding: 80px 8%;
            background: #ffffff;
        }

        .fitur-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 70px;
        }

        .card {
            background: #001d3d;
            color: white;
            padding: 35px;
            border-radius: 20px;
            transition: .3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #13b0ff;
        }

        .fitur-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .fitur-title h2 {
            font-size: 50px;
            margin-bottom: 20px;
        }

        .fitur-title p {
            color: #666;
            max-width: 850px;
            margin: auto;
            line-height: 1.8;
        }

        .fitur-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 50px;
        }

        .fitur-item {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
        }

        .fitur-icon {
            width: 50px;
            height: 50px;
            background: #001d3d;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* ================= PAKET ================= */
        .paket {
            background: linear-gradient(135deg, #00152e, #002b59);
            color: white;
            padding: 90px 8%;
        }

        .paket-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .paket-text {
            flex: 1;
        }

        .paket-text h2 {
            font-size: 54px;
            margin-bottom: 20px;
        }

        .paket-text p {
            line-height: 1.8;
            color: #d9d9d9;
            margin-bottom: 20px;
        }

        .paket-text h4 {
            margin-bottom: 25px;
            color: #13b0ff;
        }

        .btn-detail {
            background: #1a73e8;
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
        }

        .paket-image {
            flex: 1;
            text-align: center;
        }

        .paket-image img {
            width: 350px;
            max-width: 100%;
        }

        /* ================= FOOTER ================= */
        footer {
            background: #00152e;
            color: white;
            padding: 70px 8% 40px;
        }

        .footer-top {
            background: linear-gradient(90deg, #001d3d, #13b0ff);
            padding: 40px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 60px;
            gap: 20px;
        }

        .footer-top h2 {
            margin-bottom: 10px;
        }

        .footer-btn {
            background: white;
            color: #1a73e8;
            padding: 15px 35px;
            border-radius: 10px;
            font-weight: 700;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .footer-grid h4 {
            margin-bottom: 15px;
            color: #13b0ff;
        }

        .footer-grid p,
        .footer-grid a {
            color: #d0d0d0;
            line-height: 1.9;
            display: block;
        }

        .copyright {
            text-align: center;
            margin-top: 50px;
            color: #aaa;
            font-size: 14px;
        }

        /* ================= RESPONSIVE ================= */
        @media(max-width: 900px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 48px;
            }

            .circle {
                width: 320px;
                height: 320px;
            }

            .navbar {
                flex-direction: column;
                gap: 20px;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .fitur-title h2,
            .paket-text h2 {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <div class="logo-circle">
    <img src="asset/logo ISP.svg" alt="Logo ISP">
</div>
            <div>
                <h2>GALA DATA</h2>
                <span>BEST SOLUTION FAST INTERNET</span>
            </div>
        </div>

        <div class="nav-menu">
    <a href="index.php">Home</a>
    <a href="index.php#paket">Paket Harga</a>
    <a href="index.php#bantuan">Bantuan</a>
    <a href="hubungi_kami.php">Hubungi Kami</a>
</div>

        <a href="loginadmin.php" class="btn-login">Login Admin</a>
    </nav>


    <!-- HERO -->
    <section class="hero">
        <div class="hero-text">
            <h5>GRATIS PEMASANGAN</h5>

            <h1>
                Solusi Terbaik <br>
                Internet Cepat!
            </h1>

            <p>
                Yuk, daftar di GALA DATA sekarang! Rasakan internet super cepat
                dengan harga yang ramah di kantong.
            </p>

            <small>
                Jam kerja: 09.00 WIB – 17.00 WIB. <br>
                Pendaftaran malam akan dipasang besok pagi.
            </small>

            <!-- Tombol menuju form pendaftaran -->
            <a href="loginpelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>
        </div>

        <div class="hero-image">
            <div class="circle">
                <img src="asset/inde.png">
            </div>
        </div>
    </section>


    <!-- FITUR -->
    <section class="fitur" id="bantuan">

        <div class="fitur-box">
            <div class="card">
                <h3>Paket Harga</h3>
                <p>Pilihan paket internet cepat sesuai kebutuhan rumah dan bisnis.</p>
            </div>

            <div class="card">
                <h3>Bantuan</h3>
                <p>Layanan bantuan pelanggan aktif untuk membantu kebutuhan Anda.</p>
            </div>

            <div class="card">
                <h3>Hubungi Kami</h3>
                <p>Hubungi customer service kami untuk informasi pemasangan.</p>
            </div>
        </div>


        <div class="fitur-title">
            <h2>Terkoneksi Dengan Internet Yang Tepat & Cepat</h2>

            <p>
                Kami provider internet dan wifi rumah yang memahami kebutuhan Anda.
                Kini bukan hanya internet yang cepat tapi juga dengan fitur lengkap
                dan harga yang murah.
            </p>
        </div>


        <div class="fitur-list">
            <div class="fitur-item">
                <div class="fitur-icon">🌐</div>
                <span>100% Fiber Optic</span>
            </div>

            <div class="fitur-item">
                <div class="fitur-icon">📶</div>
                <span>Modern Wi-Fi</span>
            </div>

            <div class="fitur-item">
                <div class="fitur-icon">∞</div>
                <span>Tanpa Kuota</span>
            </div>

            <div class="fitur-item">
                <div class="fitur-icon">🎬</div>
                <span>Premium Channel</span>
            </div>

            <div class="fitur-item">
                <div class="fitur-icon">📺</div>
                <span>TV Apps</span>
            </div>

            <div class="fitur-item">
                <div class="fitur-icon">▶</div>
                <span>Video On Demand</span>
            </div>
        </div>
    </section>


    <!-- WIFI BASIC -->
    <section class="paket" id="paket">
        <div class="paket-content">
            <div class="paket-text">
                <h2>WIFI BASIC HOME</h2>

                <p>
                    Internet anti lelet untuk keluarga dengan WiFi Basic Home.
                    Belajar online lancar, scrolling medsos makin kencang.
                </p>

                <h4>#Gratis Pemasangan — Internet Unlimited</h4>

                <a href="pakethome.php" class="btn-detail">Lihat Detail</a>
            </div>

            <div class="paket-image">
                <img src="asset/homepaket.svg">
            </div>
        </div>
    </section>


    <!-- WIFI PREMIUM -->
    <section class="paket">
        <div class="paket-content">
            <div class="paket-text">
                <h2>WIFI PREMIUM BISNIS</h2>

                <p>
                    Koneksi stabil adalah kunci profesionalitas.
                    WiFi Premium Bisnis hadir dengan layanan Dedicated Speed
                    dan bantuan teknis 24/7.
                </p>

                <h4>#Gratis Pemasangan — Internet Unlimited</h4>

                <a href="paketbisnis.php" class="btn-detail">Lihat Detail</a>
            </div>

            <div class="paket-image">
                <img src="asset/basic.svg">
            </div>
        </div>
    </section>


    <!-- FOOTER -->
    <footer id="kontak">

        <div class="footer-top">
            <div>
                <h2>Apakah anda butuh bantuan?</h2>
                <p>
                    Kami akan memberikan informasi detail tentang layanan dan produk unggulan kami.
                </p>
            </div>

            <a href="#" class="footer-btn">Hubungi Kami</a>
        </div>


        <div class="footer-grid">
            <div>
                <h4>PAKET & HARGA</h4>
                <a href="#">Stream</a>
                <a href="#">Stream+</a>
                <a href="#">Installation</a>
            </div>

            <div>
                <h4>PELANGGAN</h4>
                <a href="#">Daftar</a>
                <a href="#">Masuk</a>
                <a href="#">Hubungi Kami</a>
            </div>

            <div>
                <h4>INFO</h4>
                <a href="#">Promo</a>
               <!-- <a href="#">Info Cara Bayar</a> -->
                <a href="#">FAQ</a>
            </div>

            <div>
                <h4>ALAMAT</h4>
                <p>
                    Banyuwangi, Jawa Timur <br>
                    Indonesia
                </p>
            </div>
        </div>

        <div class="copyright">
            © 2025 GalaData.ID Home
        </div>
    </footer>

</body>
</html>

