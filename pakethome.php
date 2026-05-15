
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wifi Basic Home - Gala Data</title>

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

                padding-top:90px;
        }

        a{
            text-decoration:none;
        }

        /* NAVBAR */
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
            gap: 10px;
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
        .logo-circle{
            width:60px;
            height:60px;
            border-radius:50%;
           
            display:flex;
            justify-content:center;
            align-items:center;

            font-size:26px;
            font-weight:700;
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

        /* HERO */
        .hero{
            text-align:center;
            padding:80px 20px 40px;
        }

        .hero h1{
            font-size:72px;
            color:#13b0ff;
        }

        .hero h2{
            font-size:52px;
            margin-bottom:25px;
        }

        .hero p{
            color:#d4d4d4;
            max-width:700px;
            margin:auto;
            line-height:1.8;
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

        /* CARD PAKET */
        .paket-container{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:40px;
            padding:60px 10%;
        }

        .paket-card{
            border:2px solid #13b0ff;
            border-radius:10px;
            padding:35px 25px;
            text-align:center;
            background:rgba(255,255,255,0.02);
        }

        .speed-circle{
            width:239px;
            height:239px;
            border:3px solid #13b0ff;
            border-radius:50%;
            margin:0 auto 30px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
        }

        .speed-circle h3{
            font-size:18px;
            margin-bottom:10px;
            color:#c7c7c7;
        }

        .speed-circle h1{
            font-size:60px;
        }

        .speed-circle span{
            font-size:22px;
        }

        .benefit{
            margin:25px 0;
            text-align:left;
            line-height:2.3;
            padding-left:20px;
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
            font-size:22px;
            font-weight:700;
            border-top:1px solid #888;
            padding-top:20px;
        }

        .harga strong{
            font-size:50px;
        }

        .btn-daftar{
            display:inline-block;
            background:linear-gradient(90deg,#1a73e8,#13b0ff);
            color:white;
            padding:12px 25px;
            border-radius:8px;
            font-weight:700;
        }

        .catatan{
            text-align:center;
            margin-bottom:70px;
            color:#d6d6d6;
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
            .hero h1{
                font-size:45px;
            }

            .hero h2{
                font-size:35px;
            }

            .navbar{
                flex-direction:column;
                gap:20px;
            }

            .menu{
                flex-wrap:wrap;
                justify-content:center;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <div class="logo-circle">
                    <img src="asset/logo ISP.svg" alt="Logo ISP" style="width: 60px; height: 60px;, margin-top: 100px;">
                </div>
            <div>
                <h2>GALA DATA</h2>
                <span>BEST SOLUTION FAST INTERNET</span>
            </div>
        </div>

        <div class="nav-menu">
            <a href="#">Home</a>
            <a href="#paket">Paket Harga</a>
            <a href="#bantuan">Bantuan</a>
            <a href="#kontak">Hubungi Kami</a>
        </div>

        <a href="loginadmin.php" class="btn-login">Login Admin</a>
    </nav>


    <section class="hero">
        <h1>Wifi Basic Home</h1>
        <h2>Internet Unlimited</h2>

        <p>
            WiFi rumah murah & stabil? Pilih Basic Home.
            Solusi internet andalan keluarga Indonesia.
        </p>

        <div class="fitur">
            <div class="fitur-box">Kuota Unlimited</div>
            <div class="fitur-box">Include Wifi Modem</div>
            <div class="fitur-box">Gratis Instalasi</div>
        </div>
    </section>


    <section class="paket-container">

        <div class="paket-card">
            <div class="speed-circle">
                <h3>Regular Silver</h3>
                <h1>12</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Kuota Unlimited</li>
                <li>Gratis Instalasi</li>
            </ul>

            <div class="harga">
                Rp. <strong>99</strong>.000 /bulan
            </div>

            <a href="loginpelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>
        </div>


        <div class="paket-card">
            <div class="speed-circle">
                <h3>Regular Gold</h3>
                <h1>20</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Kuota Unlimited</li>
                <li>Gratis Instalasi</li>
            </ul>

            <div class="harga">
                Rp. <strong>129</strong>.000 /bulan
            </div>

            <a href="loginpelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>
        </div>


        <div class="paket-card">
            <div class="speed-circle">
                <h3>Regular Gamer</h3>
                <h1>40</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Kuota Unlimited</li>
                <li>Gratis Instalasi</li>
            </ul>

            <div class="harga">
                Rp. <strong>189</strong>.000 /bulan
            </div>

            <a href="loginpelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>
        </div>


        <div class="paket-card">
            <div class="speed-circle">
                <h3>Regular Platinum</h3>
                <h1>70</h1>
                <span>Mbps</span>
            </div>

            <ul class="benefit">
                <li>WiFi Modem</li>
                <li>Kuota Unlimited</li>
                <li>Gratis Instalasi</li>
            </ul>

            <div class="harga">
                Rp. <strong>229</strong>.000 /bulan
            </div>

            <a href="loginpelanggan.php" class="btn-daftar">
                Daftar Sekarang!
            </a>
        </div>

    </section>

        <section class="paket-container">

<?php

session_start();
include 'koneksi.php';

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM paket 
     WHERE jenis_paket='home'"
);

while($data = mysqli_fetch_array($query)){

?>

    <div class="paket-card">

        <div class="speed-circle">

            <h3>
                <?php echo $data['nama_paket']; ?>
            </h3>

            <h1>
                <?php echo $data['kecepatan']; ?>
            </h1>

            <span>Mbps</span>

        </div>

        <ul class="benefit">

            <li>WiFi Modem</li>
            <li>Kuota Unlimited</li>
            <li>Gratis Instalasi</li>

        </ul>

        <div class="harga">

            Rp.
            <strong>
                <?php echo number_format($data['harga']/1000); ?>
            </strong>.000 /bulan

        </div>

        <a href="loginpelanggan.php?id_paket=<?php echo $data['id_paket']; ?>" 
           class="btn-daftar">

            Daftar Sekarang!

        </a>

    </div>

<?php
}
?>

</section>
    


    <div class="catatan">
        * Harga di atas sudah termasuk PPN 10%
    </div>


    <footer>
        <div class="footer-box">
            <div>
                <h2>Apakah anda butuh bantuan?</h2>
                <p>
                    Kami akan memberikan informasi detail tentang layanan dan produk unggulan kami.
                </p>
            </div>

            <a href="#">Hubungi Kami</a>
        </div>
    </footer>

</body>
</html>