<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';

// Ambil tahun yang dipilih atau default tahun berjalan
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));

// Proses pembayaran
$notif = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'bayar') {
    $nikk = $_POST['nikk'];
    $kode_tarif = $_POST['kode_tarif'];
    $periode = $_POST['periode']; // format: "bulan" atau "tahun"
    $jumlah = intval($_POST['jumlah']);
    $bulan = null;
    if (strpos($periode, '-') !== false) {
        // Bulanan: format "bulan-tahun"
        [$bulan, $tahun_bayar] = explode('-', $periode);
    } else {
        // Tahunan: format "tahun"
        $tahun_bayar = $periode;
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO tb_iuran (nikk, kode_tarif, bulan, tahun, jumlah, status, tgl_bayar) VALUES (?, ?, ?, ?, ?, 'Cicil', NOW())");
        $stmt->execute([$nikk, $kode_tarif, $bulan, $tahun_bayar, $jumlah]);
        $notif = ['type' => 'success', 'msg' => 'Pembayaran berhasil disimpan!'];
    } catch (Exception $e) {
        $notif = ['type' => 'error', 'msg' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()];
    }
}

// Ambil data tarif
$tarif = $pdo->query("SELECT * FROM tb_tarif ORDER BY kode_tarif")->fetchAll(PDO::FETCH_ASSOC);
$tarif_map = [];
foreach ($tarif as $t) {
    $tarif_map[$t['kode_tarif']] = $t;
}
$bulanan = ['TR001','TR002','TR003'];
$tahunan = ['TR004','TR005','TR006'];

// Ambil semua KK
$kk = $pdo->query("SELECT nikk, nama FROM tb_warga WHERE hubungan='Kepala Keluarga' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua pembayaran tahun ini
$pembayaran = $pdo->query("SELECT * FROM tb_iuran WHERE tahun='$tahun'")->fetchAll(PDO::FETCH_ASSOC);
// Index pembayaran: [nikk][kode_tarif][periode] = total bayar
$pembayaran_map = [];
foreach ($pembayaran as $p) {
    $periode = $p['bulan'] ? $p['bulan'].'-'.$p['tahun'] : $p['tahun'];
    $pembayaran_map[$p['nikk']][$p['kode_tarif']][$periode][] = $p;
}

// Dropdown tahun
$tahun_opsi = range(date('Y')-2, date('Y')+2);
?>

<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Rekap Tagihan Iuran per Kartu Keluarga</h1>
    <form method="GET" class="flex items-center gap-2">
      <label for="tahun" class="font-semibold">Tahun:</label>
      <select name="tahun" id="tahun" class="border rounded p-1" onchange="this.form.submit()">
        <?php foreach($tahun_opsi as $th): ?>
          <option value="<?= $th ?>" <?= $th==$tahun?'selected':'' ?>><?= $th ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-2 py-1 border">No KK</th>
          <th class="px-2 py-1 border">Nama KK</th>
          <th class="px-2 py-1 border">Jenis Iuran</th>
          <th class="px-2 py-1 border">Periode</th>
          <th class="px-2 py-1 border">Tarif</th>
          <th class="px-2 py-1 border">Sudah Bayar</th>
          <th class="px-2 py-1 border">Sisa Hutang</th>
          <th class="px-2 py-1 border">Status</th>
          <th class="px-2 py-1 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($kk as $w): ?>
          <?php foreach($tarif as $t): ?>
            <?php
            $kode = $t['kode_tarif'];
            $is_bulanan = in_array($kode, $bulanan);
            $periode_list = $is_bulanan ? [
              'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
            ] : [$tahun];
            foreach ($periode_list as $periode) {
              $periode_key = $is_bulanan ? $periode.'-'.$tahun : $tahun;
              $total_bayar = 0;
              if (isset($pembayaran_map[$w['nikk']][$kode][$periode_key])) {
                foreach ($pembayaran_map[$w['nikk']][$kode][$periode_key] as $p) {
                  $total_bayar += intval($p['jumlah']);
                }
              }
              $tarif_nom = intval($t['tarif']);
              $sisa = $tarif_nom - $total_bayar;
              $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
            ?>
            <tr class="hover:bg-gray-100">
              <td class="px-2 py-1 border"><?= htmlspecialchars($w['nikk']) ?></td>
              <td class="px-2 py-1 border"><?= htmlspecialchars($w['nama']) ?></td>
              <td class="px-2 py-1 border"><?= htmlspecialchars($t['nama_tarif']) ?></td>
              <td class="px-2 py-1 border"><?= $is_bulanan ? $periode.' '.$tahun : $tahun ?></td>
              <td class="px-2 py-1 border">Rp<?= number_format($tarif_nom,0,',','.') ?></td>
              <td class="px-2 py-1 border">Rp<?= number_format($total_bayar,0,',','.') ?></td>
              <td class="px-2 py-1 border">Rp<?= number_format(max($sisa,0),0,',','.') ?></td>
              <td class="px-2 py-1 border font-semibold <?= $status=='Lunas'?'text-green-600':'text-red-600' ?>"><?= $status ?></td>
              <td class="px-2 py-1 border">
                <?php if($status=='Belum Lunas'): ?>
                  <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs" onclick="openBayarModal('<?= $w['nikk'] ?>','<?= $kode ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($t['nama_tarif']) ?>',<?= $sisa ?>)">Bayar</button>
                <?php else: ?>
                  <span class="text-gray-400">-</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php } ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Bayar -->
<div id="bayarModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
  <div class="bg-white p-4 rounded shadow-lg w-full max-w-sm">
    <h2 class="text-lg font-bold mb-2">Pembayaran Iuran</h2>
    <form method="POST" id="formBayar">
      <input type="hidden" name="aksi" value="bayar">
      <input type="hidden" name="nikk" id="modalNikk">
      <input type="hidden" name="kode_tarif" id="modalKodeTarif">
      <input type="hidden" name="periode" id="modalPeriode">
      <div class="mb-2">
        <label class="block mb-1">Nama Iuran</label>
        <input type="text" id="modalNamaTarif" class="w-full border rounded p-1 bg-gray-100" readonly>
      </div>
      <div class="mb-2">
        <label class="block mb-1">Jumlah Bayar (Rp)</label>
        <input type="number" name="jumlah" id="modalJumlah" class="w-full border rounded p-1" min="1" required>
      </div>
      <div class="flex justify-end">
        <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('bayarModal')">Tutup</button>
        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php if ($notif): ?>
<script>
  Swal.fire({
    icon: "<?= $notif['type'] ?>",
    title: "<?= $notif['type'] === 'success' ? 'Sukses' : 'Gagal' ?>",
    text: "<?= addslashes($notif['msg']) ?>",
    timer: 2000,
    showConfirmButton: false
  });
</script>
<?php endif; ?>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden');
}
function openBayarModal(nikk, kode_tarif, periode, nama_tarif, sisa) {
    document.getElementById('modalNikk').value = nikk;
    document.getElementById('modalKodeTarif').value = kode_tarif;
    document.getElementById('modalPeriode').value = periode;
    document.getElementById('modalNamaTarif').value = nama_tarif;
    document.getElementById('modalJumlah').value = sisa;
    document.getElementById('modalJumlah').max = sisa;
    toggleModal('bayarModal');
}
</script>

<?php include 'footer.php'; ?> 