<?php
// =======================================================
// KONFIGURASI DATABASE
// =======================================================
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'moora';

$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($db->connect_error) {
    die('Connect Error (' . $db->connect_errno . ') ' . $db->connect_error);
}

// =======================================================
// FUNGSI ROC (PEMBOTOTAN KRITERIA)
// =======================================================
function hitungROC($jumlahKriteria)
{
    $bobot = [];
    for ($k = 1; $k <= $jumlahKriteria; $k++) {
        $total = 0;
        for ($i = $k; $i <= $jumlahKriteria; $i++) {
            $total += 1 / $i;
        }
        $bobot[] = $total / $jumlahKriteria;
    }
    return $bobot;
}

// =======================================================
// AMBIL DATA KRITERIA
// =======================================================
$sql = "SELECT * FROM tabel_kriteria ORDER BY id_kriteria";
$result = $db->query($sql);

$kriteria = [];
foreach ($result as $row) {
    $kriteria[$row['id_kriteria']] = [
        'nama' => $row['kriteria'],
        'type' => $row['type'] // benefit / cost
    ];
}

// HITUNG BOBOT ROC
$bobotROC = hitungROC(count($kriteria));

// MASUKKAN BOBOT ROC KE ARRAY KRITERIA
$no = 0;
foreach ($kriteria as $id => $val) {
    $kriteria[$id]['bobot'] = $bobotROC[$no];
    $no++;
}

// =======================================================
// AMBIL DATA SISWA (ALTERNATIF)
// =======================================================
$sql = "SELECT * FROM tabel_siswa";
$result = $db->query($sql);

$alternatif = [];
foreach ($result as $row) {
    $alternatif[$row['id_siswa']] = $row['nama'];
}

// =======================================================
// AMBIL NILAI PENILAIAN
// =======================================================
$sql = "SELECT * FROM tabel_nilai ORDER BY id_siswa, id_kriteria";
$result = $db->query($sql);

$sample = [];
foreach ($result as $row) {
    $sample[$row['id_siswa']][$row['id_kriteria']] = $row['nilai'];
}

// =======================================================
// NORMALISASI MOORA
// =======================================================
$normal = $sample;

foreach ($kriteria as $id_kriteria => $k) {
    $pembagi = 0;
    foreach ($sample as $id_siswa => $nilai) {
        $pembagi += pow($sample[$id_siswa][$id_kriteria], 2);
    }
    $pembagi = sqrt($pembagi);

    foreach ($sample as $id_siswa => $nilai) {
        $normal[$id_siswa][$id_kriteria] =
            $sample[$id_siswa][$id_kriteria] / $pembagi;
    }
}

// =======================================================
// HITUNG NILAI OPTIMASI (MOORA + ROC)
// =======================================================
$optimasi = [];

foreach ($alternatif as $id_siswa => $nama) {
    $optimasi[$id_siswa] = 0;
    foreach ($kriteria as $id_kriteria => $k) {
        $nilai = $normal[$id_siswa][$id_kriteria] * $k['bobot'];
        if ($k['type'] == 'benefit') {
            $optimasi[$id_siswa] += $nilai;
        } else {
            $optimasi[$id_siswa] -= $nilai;
        }
    }
}

// =======================================================
// RANKING
// =======================================================
arsort($optimasi);

// =======================================================
// SIMPAN HASIL
// =======================================================
$terima = $_POST['jsiswa'];
$tanggal = date("Y-m-d H:i:s");
$rank = 1;

foreach ($optimasi as $id_siswa => $nilai) {
    $nama = $alternatif[$id_siswa];
    $status = ($rank <= $terima) ? 'rekomendasi' : 'tidak rekomendasi';

    $sql = "INSERT INTO tabel_hasil (nama, nilai, tanggal, status)
            VALUES ('$nama', '$nilai', '$tanggal', '$status')";
    $db->query($sql);

    $rank++;
}

// =======================================================
// SELESAI
// =======================================================
echo "<script>
alert('Perhitungan MOORA + ROC berhasil');
window.location='../../index.php?module=list_hasil';
</script>";
?>
