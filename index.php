<?php
session_start();
include "lib/koneksi.php";

if (empty($_SESSION['namauser'])) {
    echo "<script>alert('Anda harus login terlebih dahulu');window.location='login.html';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>SPK - SD Negeri 1 Kudu</title>

  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
</head>

<body>
<section id="container">

<!-- ================= HEADER ================= -->
<header class="header black-bg">
  <div class="sidebar-toggle-box">
    <div class="fa fa-bars"></div>
  </div>
  <a href="index.php" class="logo">
    <b>SISTEM PENDUKUNG <span>KEPUTUSAN</span></b>
  </a>
  <div class="top-menu">
    <ul class="nav pull-right top-menu">
      <li><a class="logout" href="logout.php">Logout</a></li>
    </ul>
  </div>
</header>

<!-- ================= SIDEBAR ================= -->
<aside>
  <div id="sidebar" class="nav-collapse">
    <ul class="sidebar-menu">

      <li>
        <a href="index.php?module=home">
          <i class="fa fa-home"></i> <span>Home</span>
        </a>
      </li>

      <li>
        <a href="index.php?module=list_kriteria">
          <i class="fa fa-file"></i> <span>Kriteria</span>
        </a>
      </li>

      <li>
        <a href="index.php?module=list_siswa">
          <i class="fa fa-users"></i> <span>Siswa</span>
        </a>
      </li>

      <li>
        <a href="index.php?module=list_hasil">
          <i class="fa fa-list"></i> <span>Hasil</span>
        </a>
      </li>

      <li>
        <a href="index.php?module=hitung">
          <i class="fa fa-calculator"></i> <span>Hitung MOORA</span>
        </a>
      </li>


    </ul>
  </div>
</aside>

<!-- ================= CONTENT ================= -->
<section id="main-content">
<section class="wrapper">

<?php
$module = isset($_GET['module']) ? $_GET['module'] : 'home';

switch ($module) {

    case 'home':
        include "pages/home/home.php";
        break;

    // --- MANAJEMEN KRITERIA ---
    case 'list_kriteria':
        include "pages/kriteria/list_kriteria.php";
        break;
        
    case 'tambah_kriteria': // Tambahkan ini
        include "pages/kriteria/tambah_kriteria.php";
        break;

    case 'update_kriteria': // Tambahkan ini agar tidak lari ke Home
        include "pages/kriteria/update_kriteria.php";
        break;

    // --- MANAJEMEN SISWA ---
    case 'list_siswa':
        include "pages/siswa/list_siswa.php";
        break;

    case 'tambah_siswa': // Sebaiknya tambahkan juga jika ada fiturnya
        include "pages/siswa/tambah_siswa.php";
        break;

    case 'update_siswa': // Sebaiknya tambahkan juga jika ada fiturnya
        include "pages/siswa/update_siswa.php";
        break;

    // --- HASIL & HITUNG ---
    case 'list_hasil':
        include "pages/hasil/list_hasil.php";
        break;

    case 'hitung':
        include "pages/hasil/hitung.php";
        break;

    case 'roc':
        include "roc.php";
        break;

    default:
        include "pages/home/home.php";
        break;
}
?>

</section>
</section>
<!-- ================= FOOTER ================= -->
<footer class="site-footer">
  <div class="text-center">
    © SD Negeri 1 Kudu
  </div>
</footer>

</section>

<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
<script src="lib/common-scripts.js"></script>

</body>
</html>
