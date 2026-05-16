<?php

$host = "localhost";
$user = "root";
$password = "r7e7z7a7";
$database = "layanan_isp";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>