<?php
$id_kriteria = $_GET['id_kriteria'];
$sql = "SELECT * FROM tabel_kriteria WHERE id_kriteria = $id_kriteria";
$result = mysqli_query($koneksi, $sql);
$row = mysqli_fetch_assoc($result);
?>

            <div class="form-panel">
  <h4 class="mb"><i class="fa fa-angle-right"></i> Tambah/Ubah Kriteria</h4>
  
  <form class="form-horizontal style-form" method="POST" action="pages/kriteria/aksi_update.php">
    
    <input type="hidden" name="id_kriteria" value="<?=$row['id_kriteria']?>">
    <div class="form-group">
      <label class="col-sm-2 col-sm-2 control-label">Nama Kriteria</label>
      <div class="col-sm-10">
        <input type="text" class="form-control round-form" name="kriteria" value="<?=$row['kriteria']?>" required>
      </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 col-sm-2 control-label">Type Kriteria</label>
        <div class="col-sm-10">
        <div class="form-check-inline">
                  <label class="form-check-label">
                    <input type="radio" name="type" value="benefit" <?php if($row['type']=='benefit') echo 'checked'; ?>> Benefit
                    <input type="radio" name="type" value="cost" <?php if($row['type']=='cost') echo 'checked'; ?>> Cost
                  </label>
             </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 col-sm-2 control-label">Prioritas (Ranking)</label>
        <div class="col-sm-10">
            <input type="number" name="prioritas" class="form-control" value="<?php echo $row['prioritas']; ?>" required>
            <span class="help-block">Masukkan angka 1 untuk prioritas tertinggi, dst.</span>
        </div>
    </div>

    <div class="form-group">
      <div class="col-sm-12" style="text-align: center;">
        <button type="submit" class="btn btn-theme03">Simpan Perubahan</button>
        <a href="index.php?module=list_kriteria" class="btn btn-theme04">Batal</a>
      </div>
    </div>

  </form>
</div>