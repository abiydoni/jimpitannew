<!-- modal_iuran.php -->
<div id="modalIuran" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg p-6 shadow-lg w-full max-w-md">
    <h2 class="text-xl font-bold mb-4">Tambah Pembayaran Iuran</h2>
    <form id="formIuran" method="post">
      <div class="mb-4">
        <label class="block mb-1">No KK</label>
        <select name="nokk" class="w-full border rounded p-2" required>
          <?php
          $stmt = $pdo->query("SELECT DISTINCT nokk FROM tb_warga ORDER BY nokk");
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo "<option value='{$row['nokk']}'>{$row['nokk']}</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Jenis Iuran</label>
        <select name="jenis_iuran" class="w-full border rounded p-2" required>
          <option value="wajib">Iuran Wajib</option>
          <option value="sosial">Iuran Sosial</option>
          <option value="17an">Iuran 17an</option>
          <option value="merti">Iuran Merti Desa</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Bulan</label>
        <select name="bulan" class="w-full border rounded p-2" required>
          <?php
          $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
          foreach ($bulan as $b) {
              echo "<option value='$b'>$b</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Tahun</label>
        <input type="number" name="tahun" class="w-full border rounded p-2" value="<?= date('Y') ?>" required>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Jumlah (Rp)</label>
        <input type="number" name="jumlah" class="w-full border rounded p-2" required>
      </div>

      <div class="text-right">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
      </div>
    </form>
  </div>
</div>
