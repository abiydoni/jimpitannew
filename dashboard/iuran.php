<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';

$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$kode_tarif = isset($_GET['kode_tarif']) ? $_GET['kode_tarif'] : null;
$nikk = isset($_GET['nikk']) ? $_GET['nikk'] : null;

// Proses pembayaran
$notif = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'bayar') {
    $nikk_bayar = $_POST['nikk'];
    $kode_tarif_bayar = $_POST['kode_tarif'];
    $periode = $_POST['periode'];
    $jumlah_bayar = intval($_POST['jumlah']); // input user, untuk jml_bayar
    if (strpos($periode, '-') !== false) {
        [$bulan, $tahun_bayar] = explode('-', $periode);
    } else {
        $tahun_bayar = $periode;
        // Untuk tahunan, bulan diisi nama bulan saat pembayaran
        $bulanList = [
            'January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni',
            'July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'
        ];
        $bulan = $bulanList[date('F')];
    }
    // Ambil tarif tagihan dari tb_tarif
    $jumlah_tagihan = isset($tarif_map[$kode_tarif_bayar]) ? intval($tarif_map[$kode_tarif_bayar]['tarif']) : 0;
    try {
        $stmt = $pdo->prepare("INSERT INTO tb_iuran (nikk, kode_tarif, bulan, tahun, jumlah, jml_bayar, status, tgl_bayar) VALUES (?, ?, ?, ?, ?, ?, 'Cicil', NOW())");
        $stmt->execute([$nikk_bayar, $kode_tarif_bayar, $bulan, $tahun_bayar, $jumlah_tagihan, $jumlah_bayar]);
        $notif = ['type' => 'success', 'msg' => 'Pembayaran berhasil disimpan!'];
    } catch (Exception $e) {
        $notif = ['type' => 'error', 'msg' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()];
    }
}

// Ambil data tarif
$tarif = $pdo->query("SELECT * FROM tb_tarif ORDER BY kode_tarif")->fetchAll(PDO::FETCH_ASSOC);

// Filter tarif, hilangkan Jimpitan (TR001)
$tarif = array_filter($tarif, function($t) { return $t['kode_tarif'] !== 'TR001'; });
$tarif_map = [];
foreach ($tarif as $t) {
    $tarif_map[$t['kode_tarif']] = $t;
}

// Buat array bulanan dan tahunan berdasarkan metode
$bulanan = [];
$tahunan = [];
foreach ($tarif as $t) {
    if ($t['metode'] == '1') {
        $bulanan[] = $t['kode_tarif'];
    } elseif ($t['metode'] == '2') {
        $tahunan[] = $t['kode_tarif'];
    }
}

// Ambil semua KK
$kk = $pdo->query("SELECT nikk, nama FROM tb_warga WHERE hubungan='Kepala Keluarga' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua pembayaran tahun ini
$pembayaran = $pdo->query("SELECT * FROM tb_iuran WHERE tahun='$tahun'")->fetchAll(PDO::FETCH_ASSOC);
$pembayaran_map = [];
foreach ($pembayaran as $p) {
    $periode = $p['bulan'] ? $p['bulan'].'-'.$p['tahun'] : $p['tahun'];
    $pembayaran_map[$p['nikk']][$p['kode_tarif']][$periode][] = $p;
}
$tahun_opsi = range(date('Y')-2, date('Y')+2);

// Icon untuk tiap jenis iuran (tanpa Jimpitan)
$icon_map = [
    'TR002' => '🏠', // Wajib
    'TR003' => '🤝', // Sosial
    'TR004' => '🎉', // 17an
    'TR005' => '🌾', // Merti Du
    'TR006' => '💵', // Kas
];

// Jika kode_tarif=TR001 di URL, redirect ke halaman utama iuran.php
if ($kode_tarif === 'TR001') {
    header('Location: iuran.php');
    exit;
}
?>

<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Iuran Warga</h1>
    <form method="GET" class="flex items-center gap-2">
      <?php if($kode_tarif): ?>
        <input type="hidden" name="kode_tarif" value="<?= htmlspecialchars($kode_tarif) ?>">
      <?php endif; ?>
      <label for="tahun" class="font-semibold">Tahun:</label>
      <select name="tahun" id="tahun" class="border rounded p-1" onchange="this.form.submit()">
        <?php foreach($tahun_opsi as $th): ?>
          <option value="<?= $th ?>" <?= $th==$tahun?'selected':'' ?>><?= $th ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <?php if(!$kode_tarif): ?>
    <!-- Pilihan Jenis Iuran: Menu Box Besar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-8">
      <?php foreach($tarif as $t): ?>
        <a href="?kode_tarif=<?= urlencode($t['kode_tarif']) ?>&tahun=<?= $tahun ?>" class="block bg-blue-50 border border-blue-200 rounded-lg shadow hover:shadow-lg hover:bg-blue-100 transition p-6 text-center cursor-pointer">
          <div class="text-5xl mb-2"><?= $icon_map[$t['kode_tarif']] ?? '💳' ?></div>
          <div class="text-lg font-bold mb-1"><?= htmlspecialchars($t['nama_tarif']) ?></div>
          <div class="text-gray-600">Rp<?= number_format($t['tarif'],0,',','.') ?><?= $t['metode'] == '1' ? '/bulan' : '/tahun' ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php elseif(!$nikk): ?>
    <!-- Tabel Rekap per KK untuk Jenis Iuran Terpilih -->
    <div class="mb-4">
      <a href="iuran.php?tahun=<?= $tahun ?>" class="text-blue-600 hover:underline">&larr; Kembali ke menu iuran</a>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-2 py-1 border">No KK</th>
            <th class="px-2 py-1 border">Nama KK</th>
            <th class="px-2 py-1 border">Total Tagihan</th>
            <th class="px-2 py-1 border">Sudah Bayar</th>
            <th class="px-2 py-1 border">Sisa Hutang</th>
            <th class="px-2 py-1 border">Status</th>
            <th class="px-2 py-1 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $is_bulanan = $tarif_map[$kode_tarif]['metode'] == '1';
          $periode_list = $is_bulanan ? [
            'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
          ] : [$tahun];
          foreach($kk as $w):
            $total_tagihan = 0;
            $total_bayar = 0;
            foreach($periode_list as $periode) {
              $periode_key = $is_bulanan ? $periode.'-'.$tahun : $tahun;
              $tarif_nom = intval($tarif_map[$kode_tarif]['tarif']);
              $total_tagihan += $tarif_nom;
              if (isset($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key])) {
                foreach ($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key] as $p) {
                  $total_bayar += intval($p['jml_bayar']);
                }
              }
            }
            $sisa = $total_tagihan - $total_bayar;
            $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
          ?>
          <tr class="hover:bg-gray-100">
            <td class="px-2 py-1 border"><?= htmlspecialchars($w['nikk']) ?></td>
            <td class="px-2 py-1 border"><?= htmlspecialchars($w['nama']) ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format($total_tagihan,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format($total_bayar,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format(max($sisa,0),0,',','.') ?></td>
            <td class="px-2 py-1 border font-semibold <?= $status=='Lunas'?'text-green-600':'text-red-600' ?>"><?= $status ?></td>
            <td class="px-2 py-1 border">
              <a href="?kode_tarif=<?= urlencode($kode_tarif) ?>&tahun=<?= $tahun ?>&nikk=<?= urlencode($w['nikk']) ?>" class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Detail</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <!-- Tabel Detail per Bulan/Tahun untuk KK Terpilih -->
    <div class="mb-4 flex items-center gap-2">
      <a href="?kode_tarif=<?= urlencode($kode_tarif) ?>&tahun=<?= $tahun ?>" class="text-blue-600 hover:underline">&larr; Kembali ke rekap KK</a>
      <span class="font-semibold">|
        <?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?> -
        <?= htmlspecialchars($nikk) ?>
        (<?= htmlspecialchars($pdo->query("SELECT nama FROM tb_warga WHERE nikk='$nikk' AND hubungan='Kepala Keluarga' LIMIT 1")->fetchColumn()) ?>)
      </span>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-2 py-1 border">Periode</th>
            <th class="px-2 py-1 border">Tarif</th>
            <th class="px-2 py-1 border">Sudah Bayar</th>
            <th class="px-2 py-1 border">Sisa Hutang</th>
            <th class="px-2 py-1 border">Status</th>
            <th class="px-2 py-1 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $is_bulanan = $tarif_map[$kode_tarif]['metode'] == '1';
          $periode_list = $is_bulanan ? [
            'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
          ] : [$tahun];
          foreach($periode_list as $periode) {
            $periode_key = $is_bulanan ? $periode.'-'.$tahun : $tahun;
            $tarif_nom = intval($tarif_map[$kode_tarif]['tarif']);
            $total_bayar = 0;
            if (isset($pembayaran_map[$nikk][$kode_tarif][$periode_key])) {
              foreach ($pembayaran_map[$nikk][$kode_tarif][$periode_key] as $p) {
                $total_bayar += intval($p['jml_bayar']);
              }
            }
            $sisa = $tarif_nom - $total_bayar;
            $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
          ?>
          <tr class="hover:bg-gray-100">
            <td class="px-2 py-1 border"><?= $is_bulanan ? $periode.' '.$tahun : $tahun ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format($tarif_nom,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format($total_bayar,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format(max($sisa,0),0,',','.') ?></td>
            <td class="px-2 py-1 border font-semibold <?= $status=='Lunas'?'text-green-600':'text-red-600' ?>"><?= $status ?></td>
            <td class="px-2 py-1 border">
              <?php if($status=='Belum Lunas'): ?>
                <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs" onclick="openBayarModal('<?= $nikk ?>','<?= $kode_tarif ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>',<?= $sisa ?>)">Bayar</button>
              <?php else: ?>
                <span class="text-gray-400">-</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
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