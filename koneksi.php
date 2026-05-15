<?php
$host = "localhost";
$user = "root";
$pass = "r7e7z7a7";
$db   = "layanan_isp";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>