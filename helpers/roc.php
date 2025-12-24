<?php
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
