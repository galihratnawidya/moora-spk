<?php
// Pastikan path koneksi benar
include "../../lib/koneksi.php";

// Tangkap data dari POST (sekarang id_kriteria sudah ada isinya karena input hidden tadi)
$id_kriteria = $_POST['id_kriteria'];
$kriteria    = $_POST['kriteria'];
$type        = $_POST['type'];
$prioritas   = $_POST['prioritas'];

// Query Update
$sql = "UPDATE tabel_kriteria SET 
            kriteria = '$kriteria',
            type     = '$type',
            prioritas= '$prioritas' 
        WHERE id_kriteria = '$id_kriteria'";

// Eksekusi
if (mysqli_query($koneksi, $sql)) {
    // Redirect ke halaman list agar perhitungan ROC otomatis berjalan ulang
    header("location:../../index.php?module=list_kriteria");
} else {
    echo "Gagal Update: " . mysqli_error($koneksi);
    echo "<br><a href='../../index.php?module=list_kriteria'>Kembali</a>";
}
?>