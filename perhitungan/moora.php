<?php
include "../config/koneksi.php";
include "../function/roc.php";

/* =========================
   1. DATA KRITERIA
========================= */
$qKriteria = mysqli_query($koneksi, "
    SELECT id_kriteria, nama_kriteria, tipe, prioritas
    FROM kriteria
    ORDER BY prioritas ASC
");

$kriteria = [];
while ($row = mysqli_fetch_assoc($qKriteria)) {
    $kriteria[] = $row;
}

/* =========================
   2. BOBOT ROC
========================= */
$bobotROC = hitungBobotROC($kriteria);

/* =========================
   3. DATA ALTERNATIF
========================= */
$qAlt = mysqli_query($koneksi, "SELECT id_alternatif, nama_alternatif FROM alternatif");
$alternatif = [];
while ($row = mysqli_fetch_assoc($qAlt)) {
    $alternatif[$row['id_alternatif']] = $row['nama_alternatif'];
}

/* =========================
   4. MATRKS KEPUTUSAN
========================= */
$matriks = [];
$qNilai = mysqli_query($koneksi, "SELECT * FROM penilaian");
while ($row = mysqli_fetch_assoc($qNilai)) {
    $matriks[$row['id_alternatif']][$row['id_kriteria']] = $row['nilai'];
}

/* =========================
   5. NORMALISASI
========================= */
$pembagi = [];
foreach ($kriteria as $k) {
    $idk = $k['id_kriteria'];
    $pembagi[$idk] = 0;

    foreach ($alternatif as $ida => $nama) {
        $nilaiAwal = isset($matriks[$ida][$idk]) ? $matriks[$ida][$idk] : 0;
        $pembagi[$idk] += pow($nilaiAwal, 2);
    }

    $pembagi[$idk] = sqrt($pembagi[$idk]);
}

/* =========================
   6. NILAI Yi
========================= */
$Yi = [];

foreach ($alternatif as $ida => $nama) {
    $benefit = 0;
    $cost = 0;

    foreach ($kriteria as $k) {
        $idk = $k['id_kriteria'];
        $nilaiAwal = isset($matriks[$ida][$idk]) ? $matriks[$ida][$idk] : 0;

        if ($pembagi[$idk] != 0) {
            $normal = $nilaiAwal / $pembagi[$idk];
        } else {
            $normal = 0;
        }

        $nilai = $normal * $bobotROC[$idk];

        if ($k['tipe'] == 'benefit') {
            $benefit += $nilai;
        } else {
            $cost += $nilai;
        }
    }

    $Yi[$ida] = $benefit - $cost;
}

/* =========================
   7. RANKING
========================= */
arsort($Yi);

/* =========================
   OUTPUT
========================= */
echo "<h3>Hasil Perangkingan MOORA + ROC</h3>";
$rank = 1;
foreach ($Yi as $id => $nilai) {
    echo $rank . ". " . $alternatif[$id] . " = " . round($nilai, 4) . "<br>";
    $rank++;
}
