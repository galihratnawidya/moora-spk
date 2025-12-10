        <h3><i class="fa fa-angle-right"></i> List Siswa</h3>
        <div class="row mb">
          <!-- page start-->
          <div class="content-panel">
            <div class="adv-table" style="padding: 15px;">
              <button id="btnHapusBatch" class="btn btn-danger" style="margin-bottom:10px;">
  <i class="fa fa-trash"></i> Hapus Terpilih
</button>

              <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="myDataTables">
                <thead>
                  <tr>
                    <th><input type="checkbox" id="checkAll"></th>
<th>Nama</th>
<th>Jen kel</th>
<th>Alamat</th>

          <?php
          $sqlNamakriteria = "SELECT * FROM tabel_kriteria ORDER BY id_kriteria ASC";
          $resultNamaKriteria = mysqli_query($koneksi, $sqlNamakriteria);
              while ($hasilNamaKriteria = mysqli_fetch_assoc($resultNamaKriteria)) {
          ?>
                    <th><?=$hasilNamaKriteria['kriteria']?></th>
          <?php
          }
          ?>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
          <?php
          $sql = "SELECT * FROM tabel_siswa";
          $result = mysqli_query($koneksi, $sql);
              while ($row = mysqli_fetch_assoc($result)) {
          ?>
                  <tr class="gradeX">
                    <td><input type="checkbox" class="checkItem" value="<?=$row['id_siswa']?>"></td>
<td><?=$row['nama']?></td>
<td><?=$row['jenis_kelamin']?></td>
<td><?=$row['alamat']?></td>

                    <td><?=$row['KPS']?></td>
                    <td><?=$row['PKH']?></td>
                    <td><?=$row['status']?></td>
                    <td><?=$row['penghasilan']?></td>
                    <td><?=$row['ekonomi']?></td>
                    <td class="hidden-phone">
                        <a href="index.php?module=update_siswa&id_siswa=<?=$row['id_siswa']?>"><button type="button" class="btn btn-warning"><i class="fa fa-cog"></i> Update</button></a>
                        <a href="index.php?module=hapus_siswa&id_siswa=<?=$row['id_siswa']?>"><button type="button" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</button></a>
                    </td>
                  </tr>
          <?php
              }
          ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- page end-->
        </div>
        <!-- /row -->


        <script>
  // Select All
  document.getElementById("checkAll").addEventListener("click", function() {
      let checkboxes = document.querySelectorAll(".checkItem");
      checkboxes.forEach(cb => cb.checked = this.checked);
  });

  // Tombol batch delete
  document.getElementById("btnHapusBatch").addEventListener("click", function() {
      let selected = [];
      document.querySelectorAll(".checkItem:checked").forEach(cb => {
          selected.push(cb.value);
      });

      if (selected.length === 0) {
          alert("Tidak ada data yang dipilih!");
          return;
      }

      if (confirm("Yakin ingin menghapus " + selected.length + " data terpilih?")) {
          // Kirim ke hapus_batch.php
          window.location.href = "hapus_batch.php?ids=" + selected.join(",");
      }
  });
</script>
