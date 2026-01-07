<h3><i class="fa fa-angle-right"></i> List Kriteria (Prioritas ROC)</h3>
<div class="row mb">
  <a type="button" class="btn btn-theme03" href="index.php?module=tambah_kriteria">Tambah Kriteria</a>
  <hr>
  <div class="content-panel">
    <div class="adv-table" style="padding: 15px;">
      <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="myDataTables">
        <thead>
          <tr>
            <th>Prioritas</th> <th>Nama Kriteria</th>
            <th>Tipe</th>
            <th>Bobot ROC</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
  <?php
  // 1. QUERY PENTING: Urutkan berdasarkan kolom 'prioritas' ASC (1, 2, 3...)
  $sql = "SELECT * FROM tabel_kriteria ORDER BY prioritas ASC";
  $result = mysqli_query($koneksi, $sql);

  if (!$result) { die("Query Error: " . mysqli_error($koneksi)); }

  $data_kriteria = [];
  while ($row = mysqli_fetch_assoc($result)) {
      $data_kriteria[] = $row;
  }

  $n = count($data_kriteria); 
  $rank = 1; // Counter ranking dimulai dari 1

  if ($n > 0) {
      foreach ($data_kriteria as $row) {
          
          // --- LOGIKA ROC ---
          $sum_harmonic = 0;
          for ($i = $rank; $i <= $n; $i++) {
              $sum_harmonic += (1 / $i);
          }
          $bobot_roc = $sum_harmonic / $n;
          $bobot_fix = number_format($bobot_roc, 4);

          // --- AUTO UPDATE BOBOT KE DATABASE ---
          // Update bobot saja, jangan merubah prioritas di sini
          $id_nya = $row['id_kriteria'];
          mysqli_query($koneksi, "UPDATE tabel_kriteria SET bobot = '$bobot_fix' WHERE id_kriteria = '$id_nya'");
          
  ?>
          <tr class="gradeX">
            <td><span class="badge bg-primary">Ranking <?=$row['prioritas']?></span></td> 
            <td><?=$row['kriteria']?></td>
            <td><?=$row['type']?></td>
            <td><strong><?=$bobot_fix?></strong></td>
            <td class="hidden-phone">
                <a href="index.php?module=update_kriteria&id_kriteria=<?=$row['id_kriteria']?>"><button type="button" class="btn btn-warning"><i class="fa fa-cog"></i> Ubah Prioritas</button></a>
            </td>
          </tr>
  <?php
          $rank++; // Naikkan counter untuk perhitungan ROC baris berikutnya
      }
  } else {
      echo "<tr><td colspan='5' class='text-center'>Data Kosong</td></tr>";
  }
  ?>
        </tbody>
      </table>
    </div>
  </div>
</div>