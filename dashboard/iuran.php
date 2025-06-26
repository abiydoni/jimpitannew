<?php include 'header.php'; ?>

<thead>
  <tr>
    <th>No KK</th>
    <th>Nama KK</th>
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
      <td>Rp<?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
      <td>
        <button onclick="lihatDetail('<?= $row['nokk'] ?>', <?= $row['tahun'] ?>)" class="text-blue-600 hover:underline">Detail</button>
      </td>
    </tr>
  <?php endforeach ?>
</tbody>

<?php include 'footer.php'; ?>