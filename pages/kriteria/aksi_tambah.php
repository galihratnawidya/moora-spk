<?php
// Sesuaikan path koneksi Anda
include "../../../lib/koneksi.php";

$kriteria  = $_POST['kriteria'];
$type      = $_POST['type'];
$prioritas = $_POST['prioritas'];

// Masukkan data ke database
// Kolom 'bobot' kita isi manual dengan '0' karena akan dihitung otomatis nanti
$sql = "INSERT INTO tabel_kriteria (kriteria, type, bobot, prioritas) 
        VALUES ('$kriteria', '$type', '0', '$prioritas')";

if (mysqli_query($koneksi, $sql)) {
    // Redirect ke list_kriteria
    // Di halaman list inilah perhitungan ROC akan berjalan otomatis
    header("location:../../index.php?module=list_kriteria");
} else {
    echo "Gagal Menambah Data: " . mysqli_error($koneksi);
}
?>