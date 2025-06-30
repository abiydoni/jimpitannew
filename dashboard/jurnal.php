<?php
include 'header.php';
require_once 'api/db.php';

date_default_timezone_set('Asia/Jakarta');
$tanggalSekarang = date('Y-m-d');

// Ambil filter tanggal dan reff jika ada
$tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$reff_filter = isset($_GET['reff']) ? $_GET['reff'] : '';

// Query data kas_sub
$where = [];
$params = [];
if ($tanggal_filter) {
    $where[] = 'date_trx = ?';
    $params[] = $tanggal_filter;
}
if ($reff_filter) {
    $where[] = 'reff = ?';
    $params[] = $reff_filter;
}
$sql = 'SELECT * FROM kas_sub';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY date_trx DESC, id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total debet, kredit, saldo
$total_debet = 0;
$total_kredit = 0;
foreach ($data as $row) {
    $total_debet += floatval($row['debet']);
    $total_kredit += floatval($row['kredit']);
}
$saldo = $total_debet - $total_kredit;

// Ambil daftar kode tarif untuk dropdown
$tarif_list = $pdo->query('SELECT kode_tarif, nama_tarif FROM tb_tarif ORDER BY nama_tarif')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Jurnal Kas Sub</h1>
    <div class="flex gap-2 items-center">
      <form method="GET" class="flex items-center gap-2">
        <label for="tanggal" class="font-semibold">Tanggal:</label>
        <input type="date" name="tanggal" id="tanggal" class="border rounded p-1" value="<?= htmlspecialchars($tanggal_filter) ?>">
        <label for="reff" class="font-semibold ml-2">Jenis Iuran:</label>
        <select name="reff" id="reff" class="border rounded p-1">
          <option value="">Semua</option>
          <?php foreach($tarif_list as $t): ?>
            <option value="<?= htmlspecialchars($t['kode_tarif']) ?>" <?= $reff_filter == $t['kode_tarif'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nama_tarif']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Filter</button>
      </form>
      <button type="button" onclick="openJurnalModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">Tambah Jurnal</button>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm" id="tableJurnal">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-2 py-1 border">Kode COA</th>
          <th class="px-2 py-1 border">Tanggal</th>
          <th class="px-2 py-1 border">Reff</th>
          <th class="px-2 py-1 border">Keterangan</th>
          <th class="px-2 py-1 border text-right">Debet</th>
          <th class="px-2 py-1 border text-right">Kredit</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($data): foreach ($data as $row): ?>
        <tr class="hover:bg-gray-100">
          <td class="px-2 py-1 border"><?= htmlspecialchars($row['coa_code']) ?></td>
          <td class="px-2 py-1 border"><?= htmlspecialchars($row['date_trx']) ?></td>
          <td class="px-2 py-1 border"><?= htmlspecialchars($row['reff']) ?></td>
          <td class="px-2 py-1 border"><?= htmlspecialchars($row['desc_trx']) ?></td>
          <td class="px-2 py-1 border text-right">Rp <?= number_format($row['debet'], 0, ',', '.') ?></td>
          <td class="px-2 py-1 border text-right">Rp <?= number_format($row['kredit'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center py-4">Tidak ada data jurnal.</td></tr>
        <?php endif; ?>
      </tbody>
      <tfoot class="bg-gray-100 font-bold">
        <tr>
          <td colspan="4" class="text-right">Total</td>
          <td class="px-2 py-1 border text-right text-blue-700">Rp <?= number_format($total_debet, 0, ',', '.') ?></td>
          <td class="px-2 py-1 border text-right text-red-700">Rp <?= number_format($total_kredit, 0, ',', '.') ?></td>
        </tr>
        <tr>
          <td colspan="4" class="text-right">Saldo</td>
          <td colspan="2" class="px-2 py-1 border text-right text-green-700">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
<!-- Modal Input Jurnal -->
<div id="jurnalModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
  <div class="bg-white p-4 rounded shadow-lg w-full max-w-sm">
    <h2 class="text-lg font-bold mb-2">Input Jurnal ke kas_sub</h2>
    <form id="formJurnal">
      <input type="hidden" name="coa_code" value="100-002">
      <input type="hidden" name="date_trx" id="jurnalDateTrx">
      <div class="mb-2">
        <label class="block mb-1">Reff (Kode Tarif)</label>
        <input type="text" name="reff" id="jurnalReff" class="w-full border rounded p-1" required value="<?= htmlspecialchars($reff_filter) ?>">
      </div>
      <div class="mb-2">
        <label class="block mb-1">Keterangan (desc_trx)</label>
        <input type="text" name="desc_trx" id="jurnalDescTrx" class="w-full border rounded p-1" required>
      </div>
      <div class="mb-2">
        <label class="block mb-1">Debet</label>
        <input type="number" name="debet" id="jurnalDebet" class="w-full border rounded p-1" min="0" value="0" required>
      </div>
      <div class="mb-2">
        <label class="block mb-1">Kredit</label>
        <input type="number" name="kredit" id="jurnalKredit" class="w-full border rounded p-1" min="0" value="0" required>
      </div>
      <div class="flex justify-end">
        <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('jurnalModal')">Tutup</button>
        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
function openJurnalModal() {
  // Set tanggal otomatis hari ini
  const now = new Date();
  const yyyy = now.getFullYear();
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const dd = String(now.getDate()).padStart(2, '0');
  document.getElementById('jurnalDateTrx').value = `${yyyy}-${mm}-${dd}`;
  document.getElementById('jurnalDescTrx').value = '';
  document.getElementById('jurnalDebet').value = 0;
  document.getElementById('jurnalKredit').value = 0;
  document.getElementById('jurnalReff').value = '<?= htmlspecialchars($reff_filter) ?>';
  toggleModal('jurnalModal');
}

// Modal toggle
function toggleModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.classList.toggle('hidden');
}

// Submit form jurnal
const formJurnal = document.getElementById('formJurnal');
if (formJurnal) {
  formJurnal.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(formJurnal);
    fetch('api/add_jurnal.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Sukses',
          text: data.message,
          timer: 1500,
          timerProgressBar: true,
          showConfirmButton: false,
          position: 'top-end',
          toast: true
        });
        toggleModal('jurnalModal');
        setTimeout(() => { window.location.reload(); }, 1000);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: data.message,
          timer: 1500,
          timerProgressBar: true,
          showConfirmButton: false,
          position: 'top-end',
          toast: true
        });
      }
    })
    .catch(error => {
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Gagal menyimpan jurnal: ' + error.message,
        timer: 1500,
        timerProgressBar: true,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
      });
    });
  });
}
</script>
<?php include 'footer.php'; ?> 