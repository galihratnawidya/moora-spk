<?php
// =====================
// KONEKSI DATABASE
// =====================
$db = new mysqli("localhost","root","","db_moora");
if ($db->connect_error) die("Koneksi DB gagal");

// =====================
// AMBIL KRITERIA
// =====================
$kriteria = [];
$q = $db->query("SELECT * FROM tabel_kriteria ORDER BY id_kriteria");
while ($r = $q->fetch_assoc()) {
    $kriteria[$r['id_kriteria']] = [
        'type'  => $r['type'],   // benefit / cost
        'bobot' => $r['bobot']
    ];
}

// =====================
// NORMALISASI BOBOT
// =====================
$totalBobot = 0;
foreach ($kriteria as $k) $totalBobot += $k['bobot'];

foreach ($kriteria as $id => $k) {
    $kriteria[$id]['bobot'] = $k['bobot'] / $totalBobot;
}

// =====================
// AMBIL SISWA
// =====================
$siswa = [];
$q = $db->query("SELECT * FROM tabel_siswa");
while ($r = $q->fetch_assoc()) {
    $siswa[$r['id_siswa']] = $r['nama'];
}

// =====================
// AMBIL NILAI
// =====================
$nilai = [];
$q = $db->query("SELECT * FROM tabel_nilai WHERE id_kriteria <> 0");
while ($r = $q->fetch_assoc()) {
    $nilai[$r['id_siswa']][$r['id_kriteria']] = $r['nilai'];
}

// =====================
// NORMALISASI MOORA
// =====================
$normal = [];
foreach ($kriteria as $idK => $k) {
    $pembagi = 0;
    foreach ($siswa as $idS => $n) {
        $x = $nilai[$idS][$idK] ?? 0;
        $pembagi += $x * $x;
    }
    $pembagi = sqrt($pembagi);

    foreach ($siswa as $idS => $n) {
        $normal[$idS][$idK] = ($nilai[$idS][$idK] ?? 0) / $pembagi;
    }
}

// =====================
// HITUNG NILAI & RANKING
// =====================
$hasil = [];
foreach ($siswa as $idS => $nama) {
    $hasil[$idS] = 0;
    foreach ($kriteria as $idK => $k) {
        $sign = ($k['type'] == 'benefit') ? 1 : -1;
        $hasil[$idS] += $normal[$idS][$idK] * $k['bobot'] * $sign;
    }
}

// =====================
// SORT DESC (RANKING)
// =====================
arsort($hasil);
?>

<!DOCTYPE html>
<html>
<head>
<title>Perangkingan MOORA</title>
<style>
table { border-collapse: collapse; }
th, td { border: 1px solid #333; padding: 6px; }
</style>
</head>
<body>

<h3>Hasil Perangkingan Alternatif</h3>

<table>
<tr>
    <th>Ranking</th>
    <th>Nama</th>
    <th>Nilai</th>
    <th>Status</th>
</tr>

<?php
$rank = 1;
foreach ($hasil as $idS => $nilai){
    echo "<tr>";
    echo "<td>$rank</td>";
    echo "<td>".$siswa[$idS]."</td>";
    echo "<td>".round($nilai,4)."</td>";
    echo "<td>".($rank <= 5 ? 'MASUK' : 'TOLAK')."</td>";
    echo "</tr>";
    $rank++;
}
?>

</table>

</body>
</html>
