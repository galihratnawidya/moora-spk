<?php
include "../config/koneksi.php";
include "../function/roc.php";

/* =========================
   1. AMBIL DATA KRITERIA
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
   2. HITUNG BOBOT ROC
========================= */
$bobotROC = hitungBobotROC($kriteria);

/* =========================
   3. AMBIL DATA ALTERNATIF
========================= */
$qAlt = mysqli_query($koneksi, "SELECT id_alternatif, nama_alternatif FROM alternatif");
$alternatif = [];
while ($row = mysqli_fetch_assoc($qAlt)) {
    $alternatif[$row['id_alternatif']] = $row['nama_alternatif'];
}

/* =========================
   4. AMBIL NILAI MATRKS
========================= */
$matriks = [];
$qNilai = mysqli_query($koneksi, "SELECT * FROM penilaian");

while ($row = mysqli_fetch_assoc($qNilai)) {
    $matriks[$row['id_alternatif']][$row['id_kriteria']] = $row['nilai'];
}

/* =========================
   5. NORMALISASI MOORA
========================= */
$pembagi = [];
foreach ($kriteria as $k) {
    $idk = $k['id_kriteria'];
    $pembagi[$idk] = 0;

    foreach ($alternatif as $ida => $nama) {
        $pembagi[$idk] += pow($matriks[$ida][$idk], 2);
    }

    $pembagi[$idk] = sqrt($pembagi[$idk]);
}

/* =========================
   6. HITUNG Yi MOORA + ROC
========================= */
$Yi = [];

foreach ($alternatif as $ida => $nama) {
    $benefit = 0;
    $cost = 0;

    foreach ($kriteria as $k) {
        $idk = $k['id_kriteria'];
        $normal = $matriks[$ida][$idk] / $pembagi[$idk];
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
