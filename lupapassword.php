<?php

include 'koneksi.php';

$message = "";

if(isset($_POST['cek'])){

    $username = $_POST['username'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM admin
        WHERE username='$username'"
    );

    $cek = mysqli_num_rows($query);

    if($cek > 0){

        header("Location: resetpassword.php?username=$username");
        exit;

    }else{

        $message = "Username tidak ditemukan!";

    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Lupa Password</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;

    background:
    linear-gradient(
        135deg,
        #00152e,
        #002b59
    );

    display:flex;
    justify-content:center;
    align-items:center;

    padding:20px;
}

.container{
    width:100%;
    max-width:430px;

    background:white;

    border-radius:25px;

    padding:45px 35px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.2);
}

.logo{
    text-align:center;
    margin-bottom:25px;
}

.logo img{
    width:90px;
}

.logo h2{
    color:#1a73e8;
    margin-top:10px;
}

.logo p{
    color:#777;
    font-size:12px;
}

.container h1{
    text-align:center;
    margin-bottom:10px;
    color:#001b4d;
}

.desc{
    text-align:center;
    color:#666;
    margin-bottom:30px;
    line-height:1.7;
    font-size:14px;
}

.input-box{
    margin-bottom:20px;
}

.input-box label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#001b4d;
}

.input-box input{
    width:100%;
    padding:15px;

    border:1px solid #ccc;
    border-radius:12px;

    outline:none;

    transition:0.3s;
}

.input-box input:focus{
    border-color:#13b0ff;
    box-shadow:
    0 0 5px rgba(19,176,255,0.5);
}

.btn{
    width:100%;

    border:none;

    padding:15px;

    border-radius:12px;

    background:
    linear-gradient(
        90deg,
        #1a73e8,
        #13b0ff
    );

    color:white;
    font-size:16px;
    font-weight:600;

    cursor:pointer;

    transition:0.3s;
}

.btn:hover{
    transform:translateY(-2px);
}

.message{
    margin-top:20px;
    text-align:center;
    color:red;
    font-size:14px;
}

.back{
    text-align:center;
    margin-top:25px;
}

.back a{
    color:#1a73e8;
    font-weight:600;
    text-decoration:none;
}

</style>
</head>

<body>

<div class="container">

    <div class="logo">
        <img src="asset/logo ISP.svg">

        <h2>GALA DATA</h2>

        <p>BEST SOLUTION FAST INTERNET</p>
    </div>

    <h1>Lupa Password</h1>

    <p class="desc">
        Masukkan username admin untuk melanjutkan proses reset password.
    </p>

    <form method="POST">

        <div class="input-box">

            <label>Username Admin</label>

            <input
                type="text"
                name="username"
                placeholder="Masukkan Username"
                required
            >

        </div>

        <button type="submit" name="cek" class="btn">
            Lanjut
        </button>

    </form>

    <div class="message">
        <?= $message; ?>
    </div>

    <div class="back">
        <a href="loginadmin.php">
            ← Kembali ke Login
        </a>
    </div>

</div>

</body>
</html>