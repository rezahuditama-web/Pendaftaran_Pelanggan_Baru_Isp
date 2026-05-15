<?php

session_start();

include 'koneksi.php';

/* =========================================
   PROSES LOGIN
========================================= */

if (isset($_POST['username'])) {

    // Mengambil input dari form
    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = mysqli_real_escape_string(
        $koneksi,
        $_POST['password']
    );

    // Query cek username dan password
    $query = mysqli_query(

        $koneksi,

        "SELECT * FROM admin
        WHERE username='$username'
        AND password='$password'"

    );

    // Menghitung jumlah data ditemukan
    $cek = mysqli_num_rows($query);

    // Jika login berhasil
    if ($cek > 0) {

        // Mengambil data admin
        $data = mysqli_fetch_assoc($query);

        // Menyimpan session
        $_SESSION['id_admin']   = $data['id_admin'];
        $_SESSION['username']   = $data['username'];
        $_SESSION['nama_admin'] = $data['nama_admin'];

        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit;

    } else {

        // Jika login gagal
        echo "
        <script>
            alert('Username atau Password salah!');
        </script>
        ";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin - Gala Data</title>

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
            min-height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;

            overflow:hidden;
        }

        a{
            text-decoration:none;
        }

        .container{
            width:100%;
            max-width:1200px;

            display:flex;
            align-items:center;
            justify-content:space-between;

            gap:50px;
            padding:40px;
        }

        /* LEFT */
        .left{
            flex:1;
            color:white;
        }

        .logo{
            display:flex;
            align-items:center;
            gap:15px;
            margin-bottom:40px;
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

        .logo-text h2{
            font-size:32px;
        }

        .logo-text p{
            font-size:13px;
            color:#cfcfcf;
            letter-spacing:2px;
        }

        .left h1{
            font-size:70px;
            line-height:1.2;
            margin-bottom:25px;
        }

        .left p{
            color:#d7d7d7;
            line-height:1.9;
            max-width:520px;
        }

        /* RIGHT */
        .right{
            flex:1;

            display:flex;
            justify-content:center;
        }

        .login-box{
            width:100%;
            max-width:430px;

            background:white;
            border-radius:25px;

            padding:50px 40px;

            box-shadow:0 10px 30px rgba(0,0,0,0.2);
        }

        .login-box h2{
            text-align:center;
            margin-bottom:35px;
            color:#001b3b;
            font-size:32px;
        }

        .input-box{
            margin-bottom:25px;
        }

        .input-box label{
            display:block;
            margin-bottom:10px;
            font-weight:600;
            color:#001b3b;
        }

        .input-box input{
            width:100%;
            padding:15px 18px;

            border:1px solid #ccc;
            border-radius:10px;

            font-size:15px;
            outline:none;

            transition:0.3s;
        }

        .input-box input:focus{
            border-color:#13b0ff;
            box-shadow:0 0 5px rgba(19,176,255,0.5);
        }

        .remember{
            display:flex;
            justify-content:space-between;
            align-items:center;

            margin-bottom:30px;

            font-size:14px;
        }

        .remember a{
            color:#1a73e8;
            font-weight:600;
        }

        .btn-login{
            width:100%;

            padding:15px;

            border:none;
            border-radius:10px;

            background:linear-gradient(90deg,#1a73e8,#13b0ff);

            color:white;
            font-size:16px;
            font-weight:700;

            cursor:pointer;

            transition:0.3s;
        }

        .btn-login:hover{
            transform:translateY(-2px);
        }

        .bottom-text{
            text-align:center;
            margin-top:25px;
            font-size:14px;
            color:#666;
        }

        .bottom-text a{
            color:#1a73e8;
            font-weight:600;
        }

        /* RESPONSIVE */
        @media(max-width:900px){

            body{
                overflow:auto;
                padding:40px 0;
            }

            .container{
                flex-direction:column;
                text-align:center;
            }

            .logo{
                justify-content:center;
            }

            .left h1{
                font-size:45px;
            }

            .left p{
                margin:auto;
            }
        }

    </style>
</head>

<body>


    <div class="container">


        <!-- LEFT -->
        <div class="left">

            <div class="logo">

                <div class="logo-circle">
                    <img src="asset/logo ISP.svg" alt="Logo ISP" style="width: 80px; height: 80px;, margin-top: 100px;">
                </div>

                <div class="logo-text">
                    <h2>GALA DATA</h2>
                    <p>BEST SOLUTION FAST INTERNET</p>
                </div>

            </div>


            <h1>
                Login <br>
                Administrator
            </h1>

            <p>
                Kelola data pelanggan, paket internet,
                pemasangan, dan seluruh layanan ISP
                dengan sistem admin Gala Data.
            </p>

        </div>



        <!-- RIGHT -->
        <div class="right">

            <div class="login-box">

                <h2>Login Admin</h2>


                <form action="" method="POST">
            

                    <div class="input-box">
                        <label>Username</label>

                        <input 
                            type="text" 
                            name="username"
                            placeholder="Masukkan username"
                            required
                        >
                    </div>


                    <div class="input-box">
                        <label>Password</label>

                        <input 
                            type="password" 
                            name="password"
                            placeholder="Masukkan password"
                            required
                        >
                    </div>


                    <div class="remember">

                        <label>
                            <input type="checkbox">
                            Remember Me
                        </label>

                        <a href="#">
                            Lupa Password?
                        </a>

                    </div>


                    <button type="submit" class="btn-login">
                        Login Sekarang
                    </button>

                </form>


                <div class="bottom-text">
                    Kembali ke 
                    <a href="index.php">
                        Homepage
                    </a>
                </div>

            </div>

        </div>

    </div>

</body>
</html>