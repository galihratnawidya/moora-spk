<?php
// PAKAI koneksi utama
include "lib/koneksi.php";

echo "<h2>Perhitungan Metode ROC</h2>";
echo "<hr>";

// ambil data kriteria
$sql = "SELECT * FROM tabel_kriteria ORDER BY id_kriteria";
$result = mysqli_query($koneksi, $sql);

$kriteria = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kriteria[] = $row['kriteria'];
}

$n = count($kriteria);

// hitung bobot ROC
$bobotROC = [];
for ($i = 1; $i <= $n; $i++) {
    $sum = 0;
    for ($j = $i; $j <= $n; $j++) {
        $sum += 1 / $j;
    }
    $bobotROC[$i] = $sum / $n;
}

// tampilkan hasil
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Rank</th><th>Kriteria</th><th>Bobot ROC</th></tr>";

$i = 1;
foreach ($kriteria as $k) {
    echo "<tr>";
    echo "<td>$i</td>";
    echo "<td>$k</td>";
    echo "<td>".round($bobotROC[$i],4)."</td>";
    echo "</tr>";
    $i++;
}

echo "</table>";
?>
