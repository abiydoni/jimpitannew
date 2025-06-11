<?php
require 'db.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) die('Data tidak ditemukan.');

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Biodata Warga</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    td { padding: 8px; vertical-align: top; }
    .label { width: 200px; font-weight: bold; }
    img { max-width: 150px; margin-top: 10px; }
  </style>
</head>
<body>
  <h2>Biodata Warga</h2>
  <table>
    <tr><td class="label">Nama</td><td>: <?= $data['nama'] ?></td></tr>
    <tr><td class="label">NIK</td><td>: <?= $data['nik'] ?></td></tr>
    <tr><td class="label">NIKK</td><td>: <?= $data['nikk'] ?></td></tr>
    <tr><td class="label">Hubungan</td><td>: <?= $data['hubungan'] ?></td></tr>
    <tr><td class="label">Jenis Kelamin</td><td>: <?= $data['jenkel'] ?></td></tr>
    <tr><td class="label">Tempat, Tanggal Lahir</td><td>: <?= $data['tpt_lahir'] ?>, <?= $data['tgl_lahir'] ?></td></tr>
    <tr><td class="label">Alamat</td><td>: <?= $data['alamat'] ?> RT <?= $data['rt'] ?>/RW <?= $data['rw'] ?></td></tr>
    <tr><td class="label">Wilayah</td><td>: <?= $data['kelurahan'] ?>, <?= $data['kecamatan'] ?>, <?= $data['kota'] ?>, <?= $data['propinsi'] ?>, <?= $data['negara'] ?></td></tr>
    <tr><td class="label">Agama</td><td>: <?= $data['agama'] ?></td></tr>
    <tr><td class="label">Status</td><td>: <?= $data['status'] ?></td></tr>
    <tr><td class="label">Pekerjaan</td><td>: <?= $data['pekerjaan'] ?></td></tr>
    <tr><td class="label">No HP</td><td>: <?= $data['hp'] ?></td></tr>
    <?php if (!empty($data['foto']) && file_exists($data['foto'])): ?>
      <tr><td class="label">Foto</td><td><img src="<?= $data['foto'] ?>" alt="Foto Warga"></td></tr>
    <?php endif; ?>
  </table>
</body>
</html>