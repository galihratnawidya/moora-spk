<?php
// =============================
// KONEKSI DATABASE
// =============================
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbName = 'db_moora';

$db = new mysqli($host, $user, $pass, $dbName);
if ($db->connect_error) {
    die('Koneksi database gagal: ' . $db->connect_error);
}

// =============================
// 1. AMBIL DATA KRITERIA
// =============================
$queryKriteria = $db->query("SELECT * FROM tabel_kriteria");

$kriteria      = [];
$totalBobot    = 0;

while ($row = $queryKriteria->fetch_assoc()) {
    $kriteria[$row['id_kriteria']] = $row;
    // bobot dianggap sebagai skor kepentingan
    $totalBobot += $row['bobot'];
}

// =============================
// 1A. NORMALISASI BOBOT KRITERIA
// Algoritma:
// wj = bobot_j / total_bobot
// =============================
$bobotNormalisasi = [];
foreach ($kriteria as $id => $k) {
    $bobotNormalisasi[$id] = $k['bobot'] / $totalBobot;
}

// =============================
// 2. AMBIL DATA SISWA
// =============================
$querySiswa = $db->query("SELECT * FROM tabel_siswa");
$siswa = [];
while ($row = $querySiswa->fetch_assoc()) {
    $siswa[$row['id_siswa']] = $row;
}

// =============================
// 3. AMBIL DATA NILAI
// =============================
$queryNilai = $db->query("SELECT * FROM tabel_nilai");

$nilai = [];
while ($row = $queryNilai->fetch_assoc()) {
    $nilai[$row['id_siswa']][$row['id_kriteria']] = $row['nilai'];
}

// =============================
// 4. NORMALISASI MATRIKS (MOORA)
// =============================
$penyebut = [];

foreach ($kriteria as $idKriteria => $k) {
    $jumlahKuadrat = 0;
    foreach ($nilai as $idSiswa => $nilaiSiswa) {
        $jumlahKuadrat += pow($nilaiSiswa[$idKriteria], 2);
    }
    $penyebut[$idKriteria] = sqrt($jumlahKuadrat);
}

// =============================
// 5. HITUNG NILAI OPTIMASI (Yi)
// =============================
$hasil = [];

foreach ($nilai as $idSiswa => $nilaiSiswa) {
    $nilaiOptimasi = 0;

    foreach ($nilaiSiswa as $idKriteria => $nilaiKriteria) {
        $nilaiNormalisasi = $nilaiKriteria / $penyebut[$idKriteria];
        $bobot = $bobotNormalisasi[$idKriteria];

        if ($kriteria[$idKriteria]['type'] == 'benefit') {
            $nilaiOptimasi += $nilaiNormalisasi * $bobot;
        } else {
            $nilaiOptimasi -= $nilaiNormalisasi * $bobot;
        }
    }

    $hasil[] = [
        'id_siswa' => $idSiswa,
        'nama'     => $siswa[$idSiswa]['nama_siswa'],
        'nilai'    => $nilaiOptimasi
    ];
}

// =============================
// 6. SORTING PERANGKINGAN
// =============================
usort($hasil, function ($a, $b) {
    return $b['nilai'] <=> $a['nilai'];
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hasil Perangkingan MOORA</title>
</head>
<body>

<h2>Hasil Perangkingan Beasiswa (Metode MOORA)</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Ranking</th>
        <th>Nama Siswa</th>
        <th>Nilai Optimasi</th>
        <th>Status</th>
    </tr>

<?php
$rank = 1;
foreach ($hasil as $row):
    $status = ($rank <= 3) ? 'Diterima' : 'Tidak Diterima';
?>
    <tr>
        <td><?= $rank ?></td>
        <td><?= $row['nama'] ?></td>
        <td><?= round($row['nilai'], 5) ?></td>
        <td><?= $status ?></td>
    </tr>
<?php
    $rank++;
endforeach;
?>
</table>

</body>
</html>

<?php
$db->close();
?>
