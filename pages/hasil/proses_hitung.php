<?php
// =======================
// KONEKSI DATABASE
// =======================
$db = new mysqli("localhost", "root", "", "moora");
if ($db->connect_error) {
    die("Koneksi gagal: " . $db->connect_error);
}

// =======================
// FUNGSI ROC
// =======================
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

// =======================
// AMBIL KRITERIA
// =======================
$sql = "SELECT * FROM tabel_kriteria ORDER BY id_kriteria ASC";
$result = $db->query($sql);

$kriteria = [];
foreach ($result as $row) {
    $kriteria[$row['id_kriteria']] = [
        'nama'  => $row['kriteria'],
        'type'  => $row['type'] // benefit / cost
    ];
}

// =======================
// HITUNG BOBOT ROC
// =======================
$bobotROC = hitungROC(count($kriteria));
$i = 0;
foreach ($kriteria as $id => $k) {
    $kriteria[$id]['bobot'] = $bobotROC[$i];
    $i++;
}

// =======================
// AMBIL ALTERNATIF
// =======================
$sql = "SELECT * FROM tabel_siswa";
$result = $db->query($sql);

$alternatif = [];
foreach ($result as $row) {
    $alternatif[$row['id_siswa']] = $row['nama'];
}

// =======================
// AMBIL NILAI
// =======================
$sql = "SELECT * FROM tabel_nilai ORDER BY id_siswa, id_kriteria";
$result = $db->query($sql);

$sample = [];
foreach ($result as $row) {
    $sample[$row['id_siswa']][$row['id_kriteria']] = $row['nilai'];
}

// =======================
// NORMALISASI MOORA
// =======================
$normal = $sample;
foreach ($kriteria as $id_kriteria => $k) {
    $pembagi = 0;
    foreach ($sample as $nilai) {
        $pembagi += pow($nilai[$id_kriteria], 2);
    }
    $pembagi = sqrt($pembagi);

    foreach ($sample as $id_siswa => $nilai) {
        $normal[$id_siswa][$id_kriteria] /= $pembagi;
    }
}

// =======================
// HITUNG YI (MOORA + ROC)
// =======================
$optimasi = [];
foreach ($alternatif as $id_siswa => $nama) {
    $optimasi[$id_siswa] = 0;
    foreach ($kriteria as $id_kriteria => $k) {
        $nilai = $normal[$id_siswa][$id_kriteria] * $k['bobot'];
        $optimasi[$id_siswa] += ($k['type'] == 'benefit') ? $nilai : -$nilai;
    }
}

// =======================
// RANKING
// =======================
arsort($optimasi);

// =======================
// SIMPAN HASIL
// =======================
$db->query("TRUNCATE TABLE tabel_hasil");

$terima = isset($_POST['jsiswa']) ? (int)$_POST['jsiswa'] : 0;
$tanggal = date("Y-m-d H:i:s");
$rank = 1;

foreach ($optimasi as $id_siswa => $nilai) {
    $nama = $alternatif[$id_siswa];
    $status = ($rank <= $terima) ? "rekomendasi" : "tidak rekomendasi";

    $db->query("
        INSERT INTO tabel_hasil (nama, nilai, tanggal, status)
        VALUES ('$nama', '$nilai', '$tanggal', '$status')
    ");

    $rank++;
}

// =======================
// SELESAI
// =======================
echo "<script>alert('Perhitungan MOORA + ROC berhasil');window.location='../../index.php?module=list_hasil';</script>";
?>
