<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// iuran.php
include 'header.php';
include 'api/db.php';

// Ambil data rekap per KK
$sql = "SELECT 
          i.nokk,
          (SELECT nama FROM tb_warga w WHERE w.nikk = i.nokk AND w.hubungan = 'Kepala Keluarga' LIMIT 1) AS kepala_keluarga,
        --   (SELECT nikk FROM tb_warga w WHERE w.nikk = i.nokk AND w.hubungan = 'Kepala Keluarga' LIMIT 1) AS nikk_kepala,
          i.tahun,
          GROUP_CONCAT(DISTINCT i.jenis_iuran) AS jenis_ikut,
          SUM(i.jumlah) AS total_bayar
        FROM tb_iuran i
        GROUP BY i.nokk, i.tahun
        ORDER BY i.tahun DESC";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Rekap Iuran per Kartu Keluarga</h1>
    <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Iuran</button>
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
          <td class="px-4 py-2 border"><?= htmlspecialchars($row['nokk']) ?></td>
          <td class="px-4 py-2 border"><?= htmlspecialchars($row['kepala_keluarga']) ?></td>
          <td class="px-4 py-2 border"><?= $row['tahun'] ?></td>
          <td class="px-4 py-2 border"><?= $row['jenis_ikut'] ?></td>
          <td class="px-4 py-2 border font-semibold">Rp<?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
          <td class="px-4 py-2 border">
            <button onclick="lihatDetail('<?= $row['nokk'] ?>', <?= $row['tahun'] ?>)" class="text-blue-600 hover:underline">Detail</button>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'api/modal_iuran.php'; ?>
<script src="js/iuran.js"></script>
<?php include 'footer.php'; ?>
