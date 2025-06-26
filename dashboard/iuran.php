<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';

// Handle tambah iuran
$notif = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $nikk = $_POST['nikk'];
    $jenis = $_POST['jenis_iuran'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $jumlah = $_POST['jumlah'];
    try {
        $stmt = $pdo->prepare("REPLACE INTO tb_iuran (nikk, jenis_iuran, bulan, tahun, jumlah, status, tgl_bayar) VALUES (?, ?, ?, ?, ?, 'Lunas', NOW())");
        $stmt->execute([$nikk, $jenis, $bulan, $tahun, $jumlah]);
        $notif = ['type' => 'success', 'msg' => 'Data iuran berhasil disimpan!'];
    } catch (Exception $e) {
        $notif = ['type' => 'error', 'msg' => 'Gagal menyimpan data: ' . $e->getMessage()];
    }
}

// Ambil data rekap per KK
$sql = "SELECT 
          i.nikk,
          (SELECT nama FROM tb_warga w WHERE w.nikk = i.nikk AND w.hubungan = 'Kepala Keluarga' LIMIT 1) AS kepala_keluarga,
          i.tahun,
          GROUP_CONCAT(DISTINCT i.jenis_iuran) AS jenis_ikut,
          SUM(i.jumlah) AS total_bayar
        FROM tb_iuran i
        GROUP BY i.nikk, i.tahun
        ORDER BY i.tahun DESC";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil detail jika ada request detail
$detail = null;
if (isset($_GET['detail_nikk'], $_GET['detail_tahun'])) {
    $dnikk = $_GET['detail_nikk'];
    $dtahun = $_GET['detail_tahun'];
    $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nikk = ? AND tahun = ? ORDER BY bulan");
    $stmt->execute([$dnikk, $dtahun]);
    $detail = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $kepala = $pdo->prepare("SELECT nama FROM tb_warga WHERE nikk = ? AND hubungan = 'Kepala Keluarga' LIMIT 1");
    $kepala->execute([$dnikk]);
    $kepala = $kepala->fetchColumn();
}
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Rekap Iuran per Kartu Keluarga</h1>
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" onclick="openModalTambah()">+ Tambah Iuran</button>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-4 py-2 border">No KK</th>
          <th class="px-4 py-2 border">Kepala Keluarga</th>
          <th class="px-4 py-2 border">Tahun</th>
          <th class="px-4 py-2 border">Jenis Iuran</th>
          <th class="px-4 py-2 border">Total Bayar</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data as $row): ?>
        <tr class="hover:bg-gray-100">
          <td class="px-4 py-2 border"><?= htmlspecialchars($row['nikk']) ?></td>
          <td class="px-4 py-2 border"><?= htmlspecialchars($row['kepala_keluarga']) ?></td>
          <td class="px-4 py-2 border"><?= $row['tahun'] ?></td>
          <td class="px-4 py-2 border"><?= $row['jenis_ikut'] ?></td>
          <td class="px-4 py-2 border font-semibold">Rp<?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
          <td class="px-4 py-2 border">
            <a href="?detail_nikk=<?= urlencode($row['nikk']) ?>&detail_tahun=<?= urlencode($row['tahun']) ?>#detailModal" class="text-blue-600 hover:underline">Detail</a>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Iuran (Tailwind) -->
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg p-6 shadow-lg w-full max-w-md">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Tambah Pembayaran Iuran</h2>
      <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">&times;</button>
    </div>
    <form method="post">
      <input type="hidden" name="aksi" value="tambah">
      <div class="mb-4">
        <label class="block mb-1">No KK</label>
        <select id="select-nikk" name="nikk" class="w-full border rounded p-2" required></select>
      </div>
        <div class="mb-2">
            <label class="block mb-1">No KK</label>
            <input type="text" id="nikkAuto" name="nikk" class="border rounded w-full p-1 bg-gray-100" readonly required x-model="selectedOption ? selectedOption.nikk : ''">
        </div>
        <div class="mb-2">
            <label class="block mb-1">Nama KK</label>
            <input type="text" id="kkNameAuto" name="kk_name" class="border rounded w-full p-1 bg-gray-100" readonly required x-model="selectedOption ? selectedOption.kk_name : ''">
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
      <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Detail Iuran (Tailwind) -->
<?php if ($detail): ?>
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-6 shadow-lg w-full max-w-3xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Detail Iuran - <?= htmlspecialchars($kepala) ?> (<?= htmlspecialchars($dnikk) ?>)</h2>
      <a href="iuran.php" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</a>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded shadow">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2 border">Bulan</th>
            <th class="px-4 py-2 border">Jenis Iuran</th>
            <th class="px-4 py-2 border">Jumlah</th>
            <th class="px-4 py-2 border">Status</th>
            <th class="px-4 py-2 border">Tanggal Bayar</th>
            <th class="px-4 py-2 border">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($detail as $row): ?>
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 border"><?= $row['bulan'] ?></td>
            <td class="px-4 py-2 border"><?= $row['jenis_iuran'] ?></td>
            <td class="px-4 py-2 border">Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td class="px-4 py-2 border"><?= $row['status'] ?></td>
            <td class="px-4 py-2 border"><?= $row['tgl_bayar'] ? date('d-m-Y H:i', strtotime($row['tgl_bayar'])) : '-' ?></td>
            <td class="px-4 py-2 border"><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="flex justify-end mt-4">
      <a href="iuran.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Tutup</a>
    </div>
  </div>
</div>
<?php endif; ?>

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
    // Modal Tambah Data KK dari Warga (NIKK)
    function openAddNikkModal() {
        toggleModal('addModalNikk');
        loadNikkDropdown();
    }

    function loadNikkDropdown() {
        console.log('loadNikkDropdown called');
        fetch('api/get_nikk_group.php')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('nikkDropdown');
                if ($(select).hasClass('select2-hidden-accessible')) {
                    $(select).select2('destroy');
                }
                select.innerHTML = '';
                // Tambahkan option kosong di awal
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = 'Pilih No KK disini...';
                select.appendChild(emptyOpt);
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.nikk;
                    opt.textContent = item.nikk + ' - ' + item.kk_name;
                    opt.setAttribute('data-nikk', item.nikk);
                    opt.setAttribute('data-kk_name', item.kk_name);
                    select.appendChild(opt);
                });
                // Reset value ke kosong
                select.value = '';
                document.getElementById('kkNameAuto').value = '';
                document.getElementById('nikkAuto').value = '';
                $(select).select2({
                    dropdownParent: $('#addModalNikk'),
                    width: '100%',
                    placeholder: 'Pilih No KK disini...',
                    matcher: function(params, data) {
                        console.log('Matcher called:', params.term, data.text, data.element ? $(data.element).attr('data-kk_name') : '');
                        if ($.trim(params.term) === '') {
                            return data;
                        }
                        if (typeof data.text === 'undefined') {
                            return null;
                        }
                        var term = params.term.toLowerCase();
                        var text = data.text.toLowerCase();
                        var kkName = '';
                        if (data.element) {
                            kkName = $(data.element).attr('data-kk_name') ? $(data.element).attr('data-kk_name').toLowerCase() : '';
                        }
                        if (text.indexOf(term) > -1 || kkName.indexOf(term) > -1) {
                            return data;
                        }
                        return null;
                    }
                });
                console.log('Dropdown options:', select.innerHTML);
                console.log('Select2 status:', typeof $.fn.select2);
            });
    }
    document.addEventListener('DOMContentLoaded', function() {
        var nikkDropdown = document.getElementById('nikkDropdown');
        if (nikkDropdown) {
            nikkDropdown.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const text = selected.textContent.split(' - ');
                document.getElementById('kkNameAuto').value = text[1] || '';
                document.getElementById('nokkAuto').value = selected.getAttribute('data-nokk') || '';
            });
        }
    function kkDropdownSearch() {
        return {
            search: '',
            open: false,
            options: [],
            selectedOption: null,
            get filteredOptions() {
                if (!Array.isArray(this.options)) return [];
                if (!this.search) return this.options;
                const term = this.search.toLowerCase();
                return this.options.filter(kk =>
                    kk.nikk.toLowerCase().includes(term) ||
                    kk.kk_name.toLowerCase().includes(term)
                );
            },
            selectOption(kk) {
                this.selectedOption = kk;
                this.search = kk.nikk + ' - ' + kk.kk_name;
                this.open = false;
            },
            async init() {
                const res = await fetch('api/get_nikk_group.php');
                this.options = await res.json();
                console.log('NIKK options loaded:', this.options);
            }
        }
    }
        
    function openModalTambah() {
        document.getElementById('modalTambah').classList.remove('hidden');
        loadNikkDropdown();
    }
</script>

<?php include 'footer.php'; ?> 