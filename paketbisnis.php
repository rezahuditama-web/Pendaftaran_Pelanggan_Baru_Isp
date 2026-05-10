<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wifi Premium Bisnis - Gala Data</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Poppins',sans-serif;
            background:#001b3b;
            color:white;
            padding-top:100px;
        }

        a{
            text-decoration:none;
        }

        /* NAVBAR */
        .navbar{
            background:white;
            padding:20px 8%;
            display:flex;
            justify-content:space-between;
            align-items:center;

            position:fixed;
            top:0;
            left:0;
            width:100%;

            z-index:1000;

            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        .logo h2{
            color:#1a73e8;
            font-size:28px;
        }

        .menu{
            display:flex;
            gap:30px;
        }

        .menu a{
            color:#111;
            font-weight:600;
        }

        .btn-login{
            background:linear-gradient(90deg,#1a73e8,#13b0ff);
            color:white;
            padding:12px 30px;
            border-radius:8px;
            font-weight:600;
        }

        /* HERO */
        .hero{
            text-align:center;
            padding:80px 20px 40px;
        }

        .hero h1{
            font-size:70px;
            color:#13b0ff;
        }

        .hero h2{
            font-size:50px;
            margin-bottom:20px;
        }

        .hero p{
            max-width:750px;
            margin:auto;
            line-height:1.8;
            color:#d4d4d4;
        }

        .fitur{
            display:flex;
            justify-content:center;
            gap:25px;
            flex-wrap:wrap;
            margin-top:50px;
        }

        .fitur-box{
            border:2px solid #13b0ff;
            padding:16px 28px;
            border-radius:10px;
            font-weight:600;
        }

        /* CARD */
        .paket-container{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:40px;
            padding:60px 8%;
        }

        .paket-card{
            border:2px solid #13b0ff;
            border-radius:15px;
            padding:35px 25px;
            text-align:center;
            background:rgba(255,255,255,0.03);
        }

        .speed-circle{
            width:180px;
            height:180px;
            border:3px solid #13b0ff;
            border-radius:50%;
            margin:auto;
            margin-bottom:30px;

            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
        }

        .speed-circle h3{
            font-size:18px;
            color:#d0d0d0;
            margin-bottom:10px;
        }

        .speed-circle h1{
            font-size:58px;
        }

        .speed-circle span{
            font-size:22px;
        }

        .benefit{
            margin:25px 0;
            text-align:left;
            line-height:2.2;
            padding-left:15px;
        }

        .benefit li{
            list-style:none;
        }

        .benefit li::before{
            content:'✔';
            color:#13b0ff;
            margin-right:12px;
        }

        .harga{
            margin:25px 0;
            border-top:1px solid #777;
            padding-top:20px;
            font-size:22px;
            font-weight:700;
        }

        .harga strong{
            font-size:48px;
        }

        .btn-daftar{
            display:inline-block;
            background:linear-gradient(90deg,#1a73e8,#13b0ff);
            color:white;
            padding:12px 30px;
            border-radius:10px;
            font-weight:700;
        }

        .catatan{
            text-align:center;
            color:#d4d4d4;
            margin-bottom:70px;
        }

        footer{
            background:#00142b;
            padding:70px 8%;
        }

        .footer-box{
            background:linear-gradient(90deg,#001d3d,#13b0ff);
            padding:40px;
            border-radius:20px;

            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:20px;
        }

        .footer-box a{
            background:white;
            color:#1a73e8;
            padding:15px 30px;
            border-radius:10px;
            font-weight:700;
        }

        @media(max-width:768px){

            .navbar{
                flex-direction:column;
                gap:20px;
            }

            .menu{
                flex-wrap:wrap;
                justify-content:center;
            }

            .hero h1{
                font-size:45px;
            }

            .hero h2{
                font-size:35px;
            }
        }

    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">

        <div class="logo">
            <h2>GALA DATA</h2>
        </div>

        <div class="menu">
            <a href="index.php">Home</a>
            <a href="#">Paket Harga</a>
            <a href="#">Bantuan</a>
            <a href="#">Hubungi Kami</a>
        </div>

        <a href="loginadmin.php" class="btn-login">Login Admin</a>

    </nav>



    <!-- HERO -->
    <section class="hero">

        <h1>Wifi Premium Bisnis</h1>

        <h2>Internet Unlimited</h2>

        <p>
            Internet premium untuk kantor, usaha, cafe,
            sekolah, dan bisnis dengan koneksi cepat,
            stabil, dan support prioritas.
        </p>


        <div class="fitur">

            <div class="fitur-box">
                Dedicated Support
            </div>

            <div class="fitur-box">
                Unlimited Bandwidth
            </div>

            <div class="fitur-box">
                Gratis Instalasi
            </div>

        </div>

    </section>



    <!-- PAKET -->
    <section class="paket-container">


        <!-- BRONZE -->
        <div class="paket-card">

            <div class="speed-circle">
                <h3>Premium Bronze</h3>
                <h1>50</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Unlimited Internet</li>
                <li>Support Prioritas</li>
            </ul>

            <div class="harga">
                Rp. <strong>229</strong>.000 /bulan
            </div>

            <a href="login_pelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>

        </div>



        <!-- GOLD -->
        <div class="paket-card">

            <div class="speed-circle">
                <h3>Premium Gold</h3>
                <h1>80</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Unlimited Internet</li>
                <li>Support Prioritas</li>
            </ul>

            <div class="harga">
                Rp. <strong>329</strong>.000 /bulan
            </div>

            <a href="login_pelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>

        </div>



        <!-- BISNIS -->
        <div class="paket-card">

            <div class="speed-circle">
                <h3>Premium Bisnis</h3>
                <h1>120</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Unlimited Internet</li>
                <li>Support Prioritas</li>
            </ul>

            <div class="harga">
                Rp. <strong>419</strong>.000 /bulan
            </div>

            <a href="login_pelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>

        </div>



        <!-- PLATINUM -->
        <div class="paket-card">

            <div class="speed-circle">
                <h3>Premium Platinum</h3>
                <h1>200</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Unlimited Internet</li>
                <li>Support Prioritas</li>
            </ul>

            <div class="harga">
                Rp. <strong>579</strong>.000 /bulan
            </div>

            <a href="login_pelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>

        </div>

    </section>



    <div class="catatan">
        * Harga sudah termasuk PPN 10%
    </div>



    <!-- FOOTER -->
    <footer>

        <div class="footer-box">

            <div>
                <h2>Apakah anda butuh bantuan?</h2>

                <p>
                    Kami akan memberikan informasi detail
                    tentang layanan internet terbaik kami.
                </p>
            </div>

            <a href="#">
                Hubungi Kami
            </a>

        </div>

    </footer>

</body>
</html>