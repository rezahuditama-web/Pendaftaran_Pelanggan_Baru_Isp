
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
        }

        a{
            text-decoration:none;
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

        }
    </style>
</head>
<body>
<?php include 'navbar.php'; 

include 'koneksi.php';

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM paket 
     WHERE jenis_paket='Bisnis'"
);
?>


    <section class="hero">
        <h1>Wifi Premium Bisnis</h1>
        <h2>Internet Unlimited</h2>

        <p>
            Internet premium untuk kantor, usaha, cafe,
            sekolah, dan bisnis dengan koneksi cepat,
            stabil, dan support prioritas.
        </p>

        <div class="fitur">
            <div class="fitur-box">Dedicated Support</div>
            <div class="fitur-box">Unlimited Bandwidth</div>
            <div class="fitur-box">Gratis Instalasi</div>
        </div>
    </section>


       <section class="paket-container">

<?php while($data = mysqli_fetch_array($query)) { ?>

    <div class="paket-card">

        <div class="speed-circle">

            <h3>
                <?php echo $data['nama_paket']; ?>
            </h3>

            <h1>
                <?php echo str_replace(' Mbps', '', $data['kecepatan']); ?>
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

<?php } ?>

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

            <a href="hubungi_kami.php">Hubungi Kami</a>
        </div>
    </footer>

</body>
</html>