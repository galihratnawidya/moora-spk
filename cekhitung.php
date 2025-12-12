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
    die('Connect Error (' . $db->connect_errno . ') ' . $db->connect_error);
}

// =============================
// 1. AMBIL DATA KRITERIA
// =============================
$kriteria = [];
$sql = "SELECT id_kriteria, kriteria, type, bobot FROM tabel_kriteria";
$result = $db->query($sql);

if (!$result) {
    die("Query kriteria gagal: " . $db->error);
}

while ($row = $result->fetch_assoc()) {
    $id = (int)$row['id_kriteria'];
    $kriteria[$id] = [
        'nama'       => $row['kriteria'],
        'type'       => strtolower($row['type']),   // benefit / cost
        'bobot_awal' => (float)$row['bobot'],       // skor awal (sebelum algoritma)
        'bobot'      => 0.0                         // bobot hasil algoritma
    ];
}

// =============================
// 2. ALGORITMA PENENTUAN BOBOT
//    (NORMALISASI BOBOT_AWAL)
// =============================

$totalBobot = 0.0;
foreach ($kriteria as $id => $k) {
    $totalBobot += $k['bobot_awal'];
}

if ($totalBobot > 0) {
    // normalisasi: wj = bobot_awal_j / Σ bobot_awal
    foreach ($kriteria as $id => $k) {
        $kriteria[$id]['bobot'] = $k['bobot_awal'] / $totalBobot;
    }
} else {
    // fallback: kalau semua bobot_awal = 0, bagi rata
    $jumlahKriteria = count($kriteria);
    if ($jumlahKriteria > 0) {
        foreach ($kriteria as $id => $k) {
            $kriteria[$id]['bobot'] = 1 / $jumlahKriteria;
        }
    }
}

// =============================
// 3. AMBIL DATA ALTERNATIF (SISWA)
// =============================
$alternatif = [];
$sql = "SELECT * FROM tabel_siswa ORDER BY id_siswa";
$result = $db->query($sql);

if (!$result) {
    die("Query siswa gagal: " . $db->error);
}

while ($row = $result->fetch_assoc()) {
    $id = (int)$row['id_siswa'];
    // simpan seluruh data siswa, minimal butuh 'nama'
    $alternatif[$id] = $row;
}

// =============================
// 4. AMBIL NILAI (MATRIX KEPUTUSAN)
// =============================
$sample = [];
$sql = "SELECT id_siswa, id_kriteria, nilai 
        FROM tabel_nilai 
        WHERE id_kriteria <> 0
        ORDER BY id_siswa, id_kriteria";

$result = $db->query($sql);

if (!$result) {
    die("Query nilai gagal: " . $db->error);
}

while ($row = $result->fetch_assoc()) {
    $idSiswa    = (int)$row['id_siswa'];
    $idKriteria = (int)$row['id_kriteria'];
    $nilai      = (float)$row['nilai'];

    if (!isset($sample[$idSiswa])) {
        $sample[$idSiswa] = [];
    }
    $sample[$idSiswa][$idKriteria] = $nilai;
}

// pastikan setiap alternatif & kriteria punya nilai (kalau kosong, isi 0)
foreach ($alternatif as $idSiswa => $alt) {
    if (!isset($sample[$idSiswa])) {
        $sample[$idSiswa] = [];
    }
    foreach ($kriteria as $idKriteria => $k) {
        if (!isset($sample[$idSiswa][$idKriteria])) {
            $sample[$idSiswa][$idKriteria] = 0.0;
        }
    }
}

// =============================
// 5. NORMALISASI MATRIKS (MOORA)
//    r_ij = x_ij / sqrt( Σ x_ij^2 )
// =============================
$normal = [];

// inisialisasi array normal
foreach ($alternatif as $idSiswa => $alt) {
    $normal[$idSiswa] = [];
}

foreach ($kriteria as $idKriteria => $k) {
    $pembagi = 0.0;

    // hitung penyebut (akar dari jumlah kuadrat)
    foreach ($alternatif as $idSiswa => $alt) {
        $nilai = $sample[$idSiswa][$idKriteria];
        $pembagi += pow($nilai, 2);
    }

    $akar = $pembagi > 0 ? sqrt($pembagi) : 1;

    // normalisasi tiap alternatif untuk kriteria ini
    foreach ($alternatif as $idSiswa => $alt) {
        $nilai = $sample[$idSiswa][$idKriteria];
        $normal[$idSiswa][$idKriteria] = $nilai / $akar;
    }
}

// =============================
// 6. HITUNG NILAI OPTIMASI MOORA
//    Yi = Σ(benefit) - Σ(cost)
//    dengan bobot dari algoritma di atas
// =============================
$optimasi = [];

foreach ($alternatif as $idSiswa => $alt) {
    $score = 0.0;

    foreach ($kriteria as $idKriteria => $k) {
        $nij   = $normal[$idSiswa][$idKriteria];
        $bobot = $k['bobot'];              // bobot hasil algoritma
        $sign  = ($k['type'] === 'benefit') ? 1 : -1;

        $score += $nij * $bobot * $sign;
    }

    $optimasi[$idSiswa] = $score;
}

// urutkan dari terbesar ke terkecil
arsort($optimasi);

// =============================
// 7. TAMPILKAN HASIL DALAM HTML
// =============================
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perhitungan MOORA - Penentuan Bobot Otomatis</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        h2 { margin-top: 30px; }
        table { border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 8px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Daftar Kriteria (setelah normalisasi bobot)</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nama Kriteria</th>
        <th>Tipe</th>
        <th>Bobot Awal</th>
        <th>Bobot Terhitung</th>
    </tr>
    <?php foreach ($kriteria as $idKriteria => $k): ?>
        <tr>
            <td><?php echo $idKriteria; ?></td>
            <td><?php echo htmlspecialchars($k['nama']); ?></td>
            <td><?php echo htmlspecialchars($k['type']); ?></td>
            <td><?php echo $k['bobot_awal']; ?></td>
            <td><?php echo $k['bobot']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Matriks Normalisasi</h2>
<table>
    <tr>
        <th>Alternatif</th>
        <?php foreach ($kriteria as $idKriteria => $k): ?>
            <th>K<?php echo $idKriteria; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($alternatif as $idSiswa => $alt): ?>
        <tr>
            <td><?php echo htmlspecialchars($alt['nama']) . ' (ID ' . $idSiswa . ')'; ?></td>
            <?php foreach ($kriteria as $idKriteria => $k): ?>
                <td>
                    <?php 
                    echo isset($normal[$idSiswa][$idKriteria]) 
                        ? $normal[$idSiswa][$idKriteria] 
                        : 0; 
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Nilai Optimasi & Peringkat</h2>
<table>
    <tr>
        <th>Ranking</th>
        <th>ID Siswa</th>
        <th>Nama</th>
        <th>Nilai MOORA</th>
        <th>Status</th>
    </tr>
    <?php
    $rank = 1;
    foreach ($optimasi as $idSiswa => $nilai):
        // di sini top 5 dikasih status "MASUK", sisanya "TOLAK"
        $status = ($rank <= 5) ? 'MASUK' : 'TOLAK';
    ?>
        <tr>
            <td><?php echo $rank; ?></td>
            <td><?php echo $idSiswa; ?></td>
            <td><?php echo htmlspecialchars($alternatif[$idSiswa]['nama']); ?></td>
            <td><?php echo $nilai; ?></td>
            <td><?php echo $status; ?></td>
        </tr>
    <?php
        $rank++;
    endforeach;
    ?>
</table>

</body>
</html>
<?php
// tutup koneksi
$db->close();
?>
