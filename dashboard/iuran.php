<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';
include 'api/db.php';

// Handle tambah iuran
$notif = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $nokk = $_POST['nokk'];
    $jenis = $_POST['jenis_iuran'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $jumlah = $_POST['jumlah'];
    try {
        $stmt = $pdo->prepare("REPLACE INTO tb_iuran (nokk, jenis_iuran, bulan, tahun, jumlah, status, tgl_bayar) VALUES (?, ?, ?, ?, ?, 'Lunas', NOW())");
        $stmt->execute([$nokk, $jenis, $bulan, $tahun, $jumlah]);
        $notif = ['type' => 'success', 'msg' => 'Data iuran berhasil disimpan!'];
    } catch (Exception $e) {
        $notif = ['type' => 'error', 'msg' => 'Gagal menyimpan data: ' . $e->getMessage()];
    }
}

// Ambil data rekap per KK
$sql = "SELECT 
          i.nokk,
          (SELECT nama FROM tb_warga w WHERE w.nokk = i.nokk AND w.hubungan = 'Kepala Keluarga' LIMIT 1) AS kepala_keluarga,
          i.tahun,
          GROUP_CONCAT(DISTINCT i.jenis_iuran) AS jenis_ikut,
          SUM(i.jumlah) AS total_bayar
        FROM tb_iuran i
        GROUP BY i.nokk, i.tahun
        ORDER BY i.tahun DESC";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil detail jika ada request detail
$detail = null;
if (isset($_GET['detail_nokk'], $_GET['detail_tahun'])) {
    $dnokk = $_GET['detail_nokk'];
    $dtahun = $_GET['detail_tahun'];
    $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nokk = ? AND tahun = ? ORDER BY bulan");
    $stmt->execute([$dnokk, $dtahun]);
    $detail = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $kepala = $pdo->prepare("SELECT nama FROM tb_warga WHERE nokk = ? AND hubungan = 'Kepala Keluarga' LIMIT 1");
    $kepala->execute([$dnokk]);
    $kepala = $kepala->fetchColumn();
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Rekap Iuran per Kartu Keluarga</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Iuran</button>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>No KK</th>
          <th>Kepala Keluarga</th>
          <th>Tahun</th>
          <th>Jenis Iuran</th>
          <th>Total Bayar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['nokk']) ?></td>
          <td><?= htmlspecialchars($row['kepala_keluarga']) ?></td>
          <td><?= $row['tahun'] ?></td>
          <td><?= $row['jenis_ikut'] ?></td>
          <td><b>Rp<?= number_format($row['total_bayar'], 0, ',', '.') ?></b></td>
          <td>
            <a href="?detail_nokk=<?= urlencode($row['nokk']) ?>&detail_tahun=<?= urlencode($row['tahun']) ?>#detailModal" class="btn btn-sm btn-info">Detail</a>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Iuran -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="aksi" value="tambah">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pembayaran Iuran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">No KK</label>
            <select name="nokk" class="form-select" required>
              <option value="">Pilih No KK</option>
              <?php
              $stmt = $pdo->query("SELECT DISTINCT nokk FROM tb_warga ORDER BY nokk");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<option value='{$row['nokk']}'>{$row['nokk']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Iuran</label>
            <select name="jenis_iuran" class="form-select" required>
              <option value="wajib">Iuran Wajib</option>
              <option value="sosial">Iuran Sosial</option>
              <option value="17an">Iuran 17an</option>
              <option value="merti">Iuran Merti Desa</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select" required>
              <?php
              $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
              foreach ($bulan as $b) {
                  echo "<option value='$b'>$b</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Iuran -->
<?php if ($detail): ?>
<div class="modal fade show" id="detailModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Iuran - <?= htmlspecialchars($kepala) ?> (<?= htmlspecialchars($dnokk) ?>)</h5>
        <a href="iuran.php" class="btn-close"></a>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Bulan</th>
              <th>Jenis Iuran</th>
              <th>Jumlah</th>
              <th>Status</th>
              <th>Tanggal Bayar</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detail as $row): ?>
            <tr>
              <td><?= $row['bulan'] ?></td>
              <td><?= $row['jenis_iuran'] ?></td>
              <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
              <td><?= $row['status'] ?></td>
              <td><?= $row['tgl_bayar'] ? date('d-m-Y H:i', strtotime($row['tgl_bayar'])) : '-' ?></td>
              <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <a href="iuran.php" class="btn btn-secondary">Tutup</a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
<?php include 'footer.php'; ?> 