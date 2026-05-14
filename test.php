<?php

require_once('koneksi.php');

if($conn){
    echo "Koneksi berhasil";
}else{
    echo "Koneksi gagal";
}

?>