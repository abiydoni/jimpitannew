<?php
// iuran_detail.php
include 'header.php';
include 'db.php';

$nokk = $_GET['nokk'] ?? '';
$tahun = $_GET['tahun'] ?? date('Y');

$stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nokk = ? AND tahun = ?");
$stmt->execute([$nokk, $tahun]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("SELECT nama FROM tb_warga WHERE nokk = ? AND hubungan = 'Kepala Keluarga' LIMIT 1");
$stmt2->execute([$nokk]);
$kepala = $stmt2->fetchColumn();
?>

<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold">Detail Iuran - <?= htmlspecialchars($kepala) ?> (<?= htmlspecialchars($nokk) ?>)</h2>
    <a href="iuran.php" class="text-blue-600 hover:underline">&larr; Kembali</a>
  </div>

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
      <?php foreach ($data as $row): ?>
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

<?php include 'footer.php'; ?>
