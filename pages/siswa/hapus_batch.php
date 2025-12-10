<?php
include 'koneksi.php';

if (isset($_GET['ids'])) {
    $ids = $_GET['ids']; 
    $idArray = explode(",", $ids);
    $idList = implode("','", $idArray);

    $sql = "DELETE FROM tabel_siswa WHERE id_siswa IN ('$idList')";
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Berhasil menghapus data terpilih');window.location='index.php?module=list_siswa';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
