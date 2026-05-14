<?php

include "koneksi.php";

$nik = $_POST['nik'];
$password = $_POST['Password'];

$query = mysqli_query($koneksi,
"SELECT * FROM pelanggan 
WHERE no_nik='$nik' 
AND password='$password'");

$cek = mysqli_num_rows($query);

if($cek > 0){

    header("Location: dashboard.php");

}else{

    echo "
    <script>
        alert('NIK atau Password salah');
        window.location='login.php';
    </script>
    ";

}

?>