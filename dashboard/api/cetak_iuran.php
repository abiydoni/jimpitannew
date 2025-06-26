<?php
// cetak_iuran.php
include 'db.php';

$nokk = $_GET['nokk'] ?? '';
$tahun = $_GET['tahun'] ?? date('Y');

$stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nokk = ? AND tahun = ?");
$stmt->execute([$nokk, $tahun]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("SELECT nama FROM tb_warga WHERE nokk = ? AND hubungan = 'Kepala Keluarga' LIMIT 1");
$stmt2->execute([$nokk]);
$kepala = $stmt2->fetchColumn();

header("Content-type: text/html");
echo "<html><head><title>Cetak Iuran</title>
<style>
  body { font-family: Arial, sans-serif; margin: 20px; }
  h2 { text-align: center; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #000; padding: 8px; text-align: left; }
  th { background-color: #eee; }
  .total { font-weight: bold; }
</style>
</head><body onload='window.print()'>";

echo "<h2>Rekap Iuran - $kepala<br><small>No KK: $nokk | Tahun: $tahun</small></h2>";

echo "<table><thead><tr>
  <th>Bulan</th>
  <th>Jenis Iuran</th>
  <th>Jumlah</th>
  <th>Status</th>
  <th>Tanggal Bayar</th>
</tr></thead><tbody>";

$total = 0;
foreach ($data as $row) {
  $tgl = $row['tgl_bayar'] ? date('d-m-Y', strtotime($row['tgl_bayar'])) : '-';
  echo "<tr>
    <td>{$row['bulan']}</td>
    <td>{$row['jenis_iuran']}</td>
    <td>Rp" . number_format($row['jumlah'], 0, ',', '.') . "</td>
    <td>{$row['status']}</td>
    <td>$tgl</td>
  </tr>";
  $total += $row['jumlah'];
}

echo "<tr class='total'>
  <td colspan='2'>Total</td>
  <td colspan='3'>Rp" . number_format($total, 0, ',', '.') . "</td>
</tr>";

echo "</tbody></table></body></html>";
