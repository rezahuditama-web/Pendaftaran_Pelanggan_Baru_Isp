<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login Pelanggan - Gala Data</title>

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

    padding:30px;
}

.container{
    width:100%;
    max-width:1100px;

    display:grid;
    grid-template-columns:1fr 1fr;

    background:white;
    border-radius:25px;

    overflow:hidden;
}

.left{
    background:linear-gradient(135deg,#001b3b,#0d4ea6);

    color:white;

    padding:60px 50px;

    display:flex;
    flex-direction:column;
    justify-content:center;
}

.left h1{
    font-size:48px;
    margin-bottom:20px;
}

.left p{
    line-height:1.8;
    color:#d6d6d6;
}

.right{
    padding:60px 50px;
}

.logo{
    color:#1a73e8;
    font-size:32px;
    font-weight:800;

    margin-bottom:40px;
}

.form-box h2{
    color:#001b3b;
    margin-bottom:10px;
}

.form-box p{
    color:#666;
    margin-bottom:35px;
}

.input-box{
    margin-bottom:22px;
}

.input-box label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

.input-box input{
    width:100%;
    padding:15px;

    border:1px solid #ccc;
    border-radius:10px;

    outline:none;
}

.input-box input:focus{
    border-color:#1a73e8;
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

    margin-top:10px;
}

.btn-login:hover{
    opacity:0.9;
}

.daftar-baru{
    text-align:center;
    margin-top:25px;
}

.daftar-baru a{
    color:#1a73e8;
    font-weight:700;
    text-decoration:none;
}

.info-box{
    margin-top:30px;

    background:#eef5ff;

    padding:20px;
    border-radius:12px;

    font-size:14px;
    line-height:1.8;
}

@media(max-width:900px){

    .container{
        grid-template-columns:1fr;
    }

    .left{
        text-align:center;
    }

    .left h1{
        font-size:38px;
    }

}

</style>
</head>
<body>

<div class="container">

    <div class="left">

        <h1>
            Login Pelanggan
        </h1>

        <p>
            Jika sebelumnya Anda sudah pernah mendaftar,
            silakan login menggunakan password yang anda buat sebelumnya dan nomor NIK yang terdaftar pada saat registrasi awal
            untuk melakukan pendaftaran lokasi pemasangan baru.
        </p>

    </div>


    <div class="right">

        <div class="logo">
            GALA DATA
        </div>

        <div class="form-box">

            <h2>Selamat Datang</h2>

            <p>
                Masuk Pelanggan
            </p>

            <form action="proseslogin.php" method="POST">

                <div class="input-box">
                    <label>Password</label>

                    <input 
                        type="password" 
                        name="Password"
                        placeholder="Password pada saat registrasi"
                        required
                    >
                </div>

                <div class="input-box">
                    <label>NIK</label>

                    <input 
                        type="text" 
                        name="nik"
                        placeholder="Masukkan NIK pada saat registrasi"
                        required
                    >
                </div>

<button type="submit" class="btn-login">
    Login
</button>

            </form>

            <div class="daftar-baru">

                Belum pernah daftar?

                <a href="isidatapelangganbaru.php">
                    Daftar Pelanggan Baru
                </a>

            </div>


            <div class="info-box">

                <strong>Pelanggan Lama:</strong><br>
                Login untuk menambah alamat pemasangan baru
                tanpa perlu mengisi ulang data diri lengkap.

            </div>

        </div>

    </div>

</div>

</body>
</html>