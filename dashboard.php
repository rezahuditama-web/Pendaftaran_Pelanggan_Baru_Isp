<?php

session_start();

include 'koneksi.php';


/* =========================================
   CEK LOGIN
========================================= */

if(!isset($_SESSION['username'])){

    header("Location: login.php");
    exit;

}


/* =========================================
   TOTAL PELANGGAN
========================================= */

$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan"
);

$total_pelanggan = mysqli_num_rows(
    $query_pelanggan
);


/* =========================================
   TOTAL PENDAFTARAN
========================================= */

$query_pendaftaran = mysqli_query(
    $koneksi,
    "SELECT * FROM pendaftaran_pemasangan"
);

$total_pendaftaran = mysqli_num_rows(
    $query_pendaftaran
);


/* =========================================
   TOTAL PENDING
========================================= */

$query_pending = mysqli_query(
    $koneksi,
    "SELECT * FROM pendaftaran_pemasangan
    WHERE status_verifikasi='pending'"
);

$total_pending = mysqli_num_rows(
    $query_pending
);


/* =========================================
   TOTAL PEMASANGAN
========================================= */

$query_pemasangan = mysqli_query(
    $koneksi,
    "SELECT * FROM pemasangan
    WHERE status_pemasangan='terpasang'"
);

$total_pemasangan = mysqli_num_rows(
    $query_pemasangan
);


/* =========================================
   TOTAL MONITORING OFFLINE
========================================= */

$query_monitoring = mysqli_query(
    $koneksi,
    "SELECT * FROM monitoring
    WHERE status_koneksi='offline'"
);

$total_monitoring = mysqli_num_rows(
    $query_monitoring
);

?>

<?php

$queryChart = mysqli_query($koneksi, "
SELECT 
    paket.nama_paket,
    COUNT(pendaftaran_pemasangan.id_paket) AS total
FROM pendaftaran_pemasangan
JOIN paket 
ON pendaftaran_pemasangan.id_paket = paket.id_paket
GROUP BY paket.id_paket
ORDER BY total DESC
");

$label_paket = [];
$data_paket = [];

while($row = mysqli_fetch_assoc($queryChart)) {

    $label_paket[] = $row['nama_paket'];
    $data_paket[] = $row['total'];

}

?>

<!DOCTYPE html>
<html lang="id">


<head>

    <meta charset="UTF-8">

    <title>Dashboard Admin ISP</title>

    <link rel="stylesheet" href="style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <script src="https://kit.fontawesome.com/4ad0d5a3b2.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>

<body>
<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="logo">

            <img src="asset/logo ISP.svg" alt="Logo ISP" style= ''>

            <h2>GALA DATA</h2>

            <p>BEST SOLUTION FAST INTERNET</p>

        </div>

        <ul>

            <li class="active">
                <i class="fa-solid fa-table-columns"></i>
                <a href="dashboard.php">Dashboard</a>
            </li>

            <li >
                <i class="fa-solid fa-users"></i>
                <a href="pelanggan.php">Pelanggan</a>
            </li>

            <li>
                <i class="fa-solid fa-user-plus"></i>
                <a href="pendaftaran_pemasangan.php">Pendaftaran Pemasangan</a>
            </li>

            <li>
                <i class="fa-solid fa-hammer"></i>
                <a href="pemasangan.php">Pemasangan</a>
            </li>

            <li>
                <i class="fa-solid fa-clipboard-check"></i>
                <a href="monitoring.php">Monitoring</a>
            </li>

            <li>
                <i class="fa-solid fa-globe"></i>
                <a href="setting_paket.php">Setting Paket</a>
            </li>

            <li>
                <i class="fa-solid fa-gear"></i>
                <a href="setting_user.php">Pengaturan</a>
            </li>

        </ul>

        <div class="logout">

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </div>

    </div>



    <!-- MAIN -->
    <div class="main-content">


        <!-- TOPBAR -->
     <div class="topbar">

    <div class="top-left">

        <button class="dashboard-btn">

            <i class="fa-solid fa-table-columns"></i>

            Dashboard

        </button>

        <div class="search-box">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input type="text" placeholder="Cari Pelanggan">

        </div>

    </div>

    <div class="profile">

        <img src="https://i.pravatar.cc/40" alt="">

    </div>

</div>


        <!-- CARD -->
        <div class="card-container">


            <!-- PELANGGAN -->
            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div>

                    <h3>Pelanggan</h3>

                    <h2><?= $total_pelanggan; ?></h2>

                </div>

            </div>



            <!-- PENDAFTARAN -->
            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>

                <div>

                    <h3>Pendaftaran</h3>

                    <h2><?= $total_pendaftaran; ?></h2>

                </div>

            </div>



            <!-- PENDING -->
            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>

                    <h3>Pending</h3>

                    <h2><?= $total_pending; ?></h2>

                </div>

            </div>



            <!-- PEMASANGAN -->
            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-hammer"></i>
                </div>

                <div>

                    <h3>Pemasangan</h3>

                    <h2><?= $total_pemasangan; ?></h2>

                </div>

            </div>



            <!-- MONITORING -->
            <div class="card">

                <div class="icon">
                    <i class="fa-solid fa-wifi"></i>
                </div>

                <div>

                    <h3>Gangguan</h3>

                    <h2><?= $total_monitoring; ?></h2>

                </div>

            </div>

        </div>



        <!-- DASHBOARD BOX -->
        <div class="dashboard-box">

            <div class="mini-box-container">


                <div class="mini-box">

                    <h4>Pelanggan Online</h4>

                    <h1>
                        <?= $total_pelanggan - $total_monitoring; ?>
                    </h1>

                </div>


                <div class="mini-box">

                    <h4>Pengajuan Pemasangan</h4>

                    <h1>
                        <?= $total_pendaftaran; ?>
                    </h1>

                </div>


                <div class="mini-box">

                    <h4>Pemasangan Selesai</h4>

                    <h1>
                        <?= $total_pemasangan; ?>
                    </h1>

                </div>


                <div class="mini-box">

                    <h4>Pelanggan Offline</h4>

                    <h1>
                        <?= $total_monitoring; ?>
                    </h1>

                </div>


                <div class="mini-box">

                    <h4>Pengajuan Pending</h4>

                    <h1>
                        <?= $total_pending; ?>
                    </h1>

                </div>


                <div class="mini-box">

                    <h4>Total Pelanggan</h4>

                    <h1>
                        <?= $total_pelanggan; ?>
                    </h1>

                </div>

            </div>



            <!-- CHART -->
                   <!-- CHART -->
<div class="chart-box">

    <h3>Statistik Paket Terlaris</h3>

    <canvas id="paketChart" height="120"></canvas>

</div>

        </div>

    </div>

</div>


<script>

const ctx = document.getElementById('paketChart');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: <?php echo json_encode($label_paket); ?>,

        datasets: [{

            label: 'Jumlah Pelanggan',

            data: <?php echo json_encode($data_paket); ?>,

            borderWidth: 1

        }]
    },

    options: {

        responsive: true,

        scales: {

            y: {
                beginAtZero: true
            }

        }

    }

});

</script>

            </div>

        </div>

    </div>

</div>

</body>
</html>