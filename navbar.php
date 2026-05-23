
    <style>
       .navbar{
    width:100%;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:20px 8%;
    position:sticky;
    top:0;
    z-index:1000;
}

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
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
        .logo-circle img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    display: block;
}

       .nav-menu{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}
.menu-center{
    display:flex;
    align-items:center;
    gap:35px;
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

        .btn-login{
    position:absolute;
    right:0;

    background: linear-gradient(90deg, #1a73e8, #13b0ff);
    color:white !important;
    padding:12px 30px;
    border-radius:8px;
    font-weight:600;
}

        /* ================= DROPDOWN ================= */
        .dropdown{
            position:relative;
             padding:10px 0;
        }

        .dropdown-menu{
            position:absolute;
            top:35px;
            left:0;
            background:#fff;
            min-width:200px;
            border-radius:10px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
            padding:10px 0;
            display:none;
        }

        .dropdown-menu a{
            display:block;
            padding:12px 20px;
            font-size:14px;
        }

        .dropdown-menu a:hover{
            background:#f3f7ff;
        }

       @media(min-width:901px){

    .dropdown:hover .dropdown-menu{
        display:block;
    }

}

        /* ================= HAMBURGER ================= */
.hamburger{
    display:none;
    flex-direction:column;
    cursor:pointer;
    gap:5px;
}

.hamburger span{
    width:28px;
    height:3px;
    background:#111;
    border-radius:5px;
    transition:0.3s;
}

       
        /* ================= RESPONSIVE ================= */
       @media(max-width:900px){

.navbar{
    padding:20px 5%;
    flex-wrap:wrap;
    position:relative;
}

/* hamburger */
.hamburger{
    display:flex;
    margin-left:auto;
}

/* menu mobile */
.nav-menu{

    display:none;
    flex-direction:column;

    position:absolute;
    top:100%;
    left:0;

    width:100%;

    background:#fff;

    padding:20px;

    box-shadow:0 5px 15px rgba(0,0,0,.1);

    justify-content:flex-start;
    align-items:flex-start;

    z-index:999;
}

.nav-menu.active{
    display:flex;
}

.menu-center{

    width:100%;

    display:flex;
    flex-direction:column;

    gap:20px;

    align-items:flex-start;
}

.nav-menu a{
    width:100%;
    padding:10px 0;
}

/* dropdown */
.dropdown{
    width:100%;
}

.dropdown-menu{

    position:static;

    display:none;

    width:100%;

    box-shadow:none;

    background:#f7f9fc;

    border-radius:8px;

    margin-top:5px;

    padding-left:15px;
}

.dropdown.active .dropdown-menu{
    display:block;
}

.btn-login{

    position:static;

    width:100%;

    text-align:center;

    margin-top:10px;
}



        }

    </style>

    <!-- NAVBAR -->
   <nav class="navbar">

    <!-- LOGO -->
    <div class="logo">
        <div class="logo-circle">
            <img src="asset/logo ISP.svg" alt="Logo ISP">
        </div>

        <div>
            <h2>GALA DATA</h2>
            <span>BEST SOLUTION FAST INTERNET</span>
        </div>
    </div>

    <!-- HAMBURGER -->
    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- MENU -->
    <div class="nav-menu" id="navmenu">

        <!-- WRAPPER MENU TENGAH -->
        <div class="menu-center">

            <a href="index.php">Home</a>

            <div class="dropdown">
                <a href="#">Paket Harga ▾</a>

                <div class="dropdown-menu">
                    <a href="pakethome.php">Paket Rumah</a>
                    <a href="paketbisnis.php">Paket Bisnis</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#">Bantuan ▾</a>

                <div class="dropdown-menu">
                    <a href="FAQ.php">FAQ</a>
                </div>
            </div>

            <a href="hubungi_kami.php">Hubungi Kami</a>

        </div>

        <!-- LOGIN -->
        <a href="loginadmin.php" class="btn-login">
            Login Admin
        </a>

    </div>

</nav>

    <script>

/* ================= HAMBURGER ================= */
const hamburger = document.getElementById("hamburger");
const navMenu = document.getElementById("navmenu");

hamburger.addEventListener("click", () => {
    navMenu.classList.toggle("active");
});

/* ================= DROPDOWN MOBILE ================= */
const dropdowns = document.querySelectorAll(".dropdown");

dropdowns.forEach(dropdown => {
    dropdown.addEventListener("click", function(e){

        // hanya untuk mobile
        if(window.innerWidth <= 900){

            e.stopPropagation();

            dropdowns.forEach(item => {
                if(item !== dropdown){
                    item.classList.remove("active");
                }
            });

            dropdown.classList.toggle("active");
        }
    });
});

</script>


