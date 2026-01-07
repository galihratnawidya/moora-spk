<h3><i class="fa fa-angle-right"></i> Tambah Kriteria Baru</h3>
<div class="row mt">
  <div class="col-lg-12">
    <div class="form-panel">
      <h4 class="mb"><i class="fa fa-angle-right"></i> Form Kriteria</h4>
      
      <form class="form-horizontal style-form" method="POST" action="pages/kriteria/aksi_tambah_kriteria.php">

        <div class="form-group">
          <label class="col-sm-2 col-sm-2 control-label">Nama Kriteria</label>
          <div class="col-sm-10">
            <input type="text" name="kriteria" class="form-control" placeholder="Contoh: Penghasilan Orang Tua" required>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 col-sm-2 control-label">Tipe Kriteria</label>
          <div class="col-sm-10">
            <div class="radio">
              <label>
                <input type="radio" name="type" value="benefit" checked> Benefit (Semakin besar semakin bagus)
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="type" value="cost"> Cost (Semakin kecil semakin bagus)
              </label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 col-sm-2 control-label" style="font-weight:bold; color:red;">Prioritas (Ranking)</label>
          <div class="col-sm-10">
            <input type="number" name="prioritas" class="form-control" placeholder="Ranking keberapa?" required>
            <span class="help-block">Masukkan angka ranking (1 = Terpenting). Bobot akan dihitung otomatis oleh sistem.</span>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12" style="text-align:right;">
             <button type="submit" class="btn btn-primary">Simpan</button>
             <a href="index.php?module=list_kriteria" class="btn btn-danger">Batal</a>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>