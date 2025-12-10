<?php
if (!isset($_GET['confirm'])) {
    echo "<script>
        if (confirm('Yakin ingin menghapus SEMUA data siswa?')) {
            window.location.href='?confirm=yes';
        } else {
            alert('Penghapusan dibatalkan');
            window.location='index.php?module=list_siswa';
        }
    </script>";
    exit;
}

$sql = "DELETE FROM tabel_siswa";
if ($koneksi->query($sql) === TRUE) {
    echo "<script>alert('HAPUS berhasil');window.location = 'index.php?module=list_siswa';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $koneksi->error;
}
?>
