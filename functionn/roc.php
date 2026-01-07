<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_moora");

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
<?php
function hitungBobotROC($kriteria) {
    $n = count($kriteria);
    $bobot = [];

    foreach ($kriteria as $krit) {
        $j = $krit['prioritas'];
        $sum = 0;

        for ($k = $j; $k <= $n; $k++) {
            $sum += 1 / $k;
        }

        $bobot[$krit['id_kriteria']] = $sum / $n;
    }

    return $bobot;
}
