<?php
// =======================
// KONEKSI DATABASE
// =======================
$db = new mysqli("localhost", "root", "", "moora");
if ($db->connect_error) {
    die("Koneksi gagal: " . $db->connect_error);
}

// =======================
// FUNGSI ROC (BENAR)
// =======================
function hitungBobotROC($kriteria)
{
    // Urutkan berdasarkan ranking (1 = paling penting)
    usort($kriteria, function ($a, $b) {
        return $a['ranking'] <=> $b['ranking'];
    });

    $n = count($kriteria);
    $bobot = [];

    for ($i = 0; $i < $n; $i++) {
        $rank = $i + 1;
        $total = 0;
        for ($j = $rank; $j <= $n; $j++) {
            $total += 1 / $j;
        }
        $bobot[] = $total / $n;
    }

    return $bobot;
}

// =======================
// AMBIL DATA KRITERIA
// =======================
$sql = "SELECT * FROM tabel_kriteria ORDER BY bobot ASC";
$result = $db->query($sql);

$kriteria = [];
while ($row = $result->fetch_assoc()) {
    $kriteria[$row['id_kriteria']] = [
        'nama'    => $row['kriteria'],
        'type'    => $row['type'],      // benefit / cost
        'ranking' => (int)$row['bobot'] // INI RANKING
    ];
}

// =======================
// HITUNG BOBOT ROC
// =======================
$bobotROC = hitungBobotROC(array_values($kriteria));

$i = 0;
foreach ($kriteria as $id => $k) {
    $kriteria[$id]['bobot'] = $bobotROC[$i];
    $i++;
}

// =======================
// AMBIL DATA ALTERNATIF
// =======================
$sql = "SELECT * FROM tabel_siswa";
$result = $db->query($sql);

$alternatif = [];
while ($row = $result->fetch_assoc()) {
    $alternatif[$row['id_siswa']] = $row['nama'];
}

// =======================
// AMBIL DATA NILAI
// =======================
$sql = "SELECT * FROM tabel_nilai ORDER BY id_siswa, id_kriteria";
$result = $db->query($sql);

$nilai = [];
while ($row = $result->fetch_assoc()) {
    $nilai[$row['id_siswa']][$row['id_kriteria']] = $row['nilai'];
}

// =======================
// NORMALISASI MOORA
// =======================
$normalisasi = [];

foreach ($kriteria as $id_kriteria => $k) {

    $pembagi = 0;
    foreach ($nilai as $n) {
        if (isset($n[$id_kriteria])) {
            $pembagi += pow($n[$id_kriteria], 2);
        }
    }
    $pembagi = sqrt($pembagi);

    foreach ($nilai as $id_siswa => $n) {
        $normalisasi[$id_siswa][$id_kriteria] =
            ($pembagi == 0) ? 0 : $n[$id_kriteria] / $pembagi;
    }
}

// =======================
// HITUNG YI (MOORA + ROC)
// =======================
$hasil = [];

foreach ($alternatif as $id_siswa => $nama) {
    $hasil[$id_siswa] = 0;

    foreach ($kriteria as $id_kriteria => $k) {
        $nilaiNormal = $normalisasi[$id_siswa][$id_kriteria];
        $nilaiBobot  = $nilaiNormal * $k['bobot'];

        $hasil[$id_siswa] +=
            ($k['type'] == 'benefit') ? $nilaiBobot : -$nilaiBobot;
    }
}

// =======================
// RANKING
// =======================
arsort($hasil);

// =======================
// SIMPAN HASIL
// =======================
$db->query("TRUNCATE TABLE tabel_hasil");

$jumlahTerima = isset($_POST['jsiswa']) ? (int)$_POST['jsiswa'] : 0;
$tanggal = date("Y-m-d H:i:s");
$rank = 1;

foreach ($hasil as $id_siswa => $nilaiAkhir) {

    $nama = $alternatif[$id_siswa];
    $status = ($rank <= $jumlahTerima)
        ? "rekomendasi"
        : "tidak rekomendasi";

    $db->query("
        INSERT INTO tabel_hasil (nama, nilai, tanggal, status)
        VALUES ('$nama', '$nilaiAkhir', '$tanggal', '$status')
    ");

    $rank++;
}

// =======================
// SELESAI
// =======================
echo "<script>
    alert('Perhitungan MOORA + ROC berhasil');
    window.location='../../index.php?module=list_hasil';
</script>";
?>
