<?php
require 'db.php';
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=data_warga.xls");

$stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
echo "<table border='1'>
<tr>
  <th>Nama</th><th>NIK</th><th>HP</th><th>Alamat</th><th>Kota</th><th>Propinsi</th>
</tr>";
while ($row = $stmt->fetch()) {
  echo "<tr>
    <td>{$row['nama']}</td>
    <td>{$row['nik']}</td>
    <td>{$row['hp']}</td>
    <td>{$row['alamat']}</td>
    <td>{$row['kota']}</td>
    <td>{$row['propinsi']}</td>
  </tr>";
}
echo "</table>";
